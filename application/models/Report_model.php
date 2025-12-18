<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {

    // =========================================================================
    // 1. LAPORAN PENJUALAN (SALES REPORT)
    // =========================================================================
    public function get_sales_report($start_date, $end_date, $status = 'all') {
        $this->db->select('so.*, c.name as customer_name, u.full_name as sales_name');
        $this->db->from('sales_orders so');
        $this->db->join('customers c', 'c.customer_id = so.customer_id', 'left');
        $this->db->join('users u', 'u.user_id = so.created_by', 'left'); // Atau salesman_id
        
        $this->db->where('DATE(so.order_date) >=', $start_date);
        $this->db->where('DATE(so.order_date) <=', $end_date);

        if($status != 'all') {
            $this->db->where('so.status', $status);
        }

        $this->db->order_by('so.order_date', 'DESC');
        return $this->db->get()->result();
    }

    // =========================================================================
    // 2. LAPORAN MUTASI STOK (STOCK MOVEMENT)
    // =========================================================================
    public function get_stock_report($start_date, $end_date, $product_id = 'all') {
        $this->db->select('sl.*, p.name as product_name, p.unit, u.full_name as user_name');
        $this->db->from('stock_logs sl');
        $this->db->join('salt_products p', 'p.product_id = sl.product_id', 'left');
        $this->db->join('users u', 'u.user_id = sl.user_id', 'left');

        $this->db->where('DATE(sl.created_at) >=', $start_date);
        $this->db->where('DATE(sl.created_at) <=', $end_date);

        if($product_id != 'all') {
            $this->db->where('sl.product_id', $product_id);
        }

        $this->db->order_by('sl.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // =========================================================================
    // 3. LABA RUGI (PROFIT & LOSS)
    // =========================================================================
    public function get_profit_loss($month, $year) {
        
        // A. PENDAPATAN (REVENUE)
        // Dari Sales Order yang valid (tidak cancel)
        $this->db->select_sum('grand_total'); // Pastikan nama kolom 'grand_total'
        $this->db->where('MONTH(order_date)', $month);
        $this->db->where('YEAR(order_date)', $year);
        $this->db->where('status !=', 'canceled');
        $query_rev = $this->db->get('sales_orders')->row();
        $revenue = $query_rev ? $query_rev->grand_total : 0;

        // B. HPP (COST OF GOODS SOLD)
        // Dari Sales Items (Qty * Cost Historis)
        // Gunakan FALSE agar tidak error backticks pada rumus perkalian
        $this->db->select('SUM(soi.qty * soi.cost) as total_hpp', FALSE);
        $this->db->from('sales_order_items soi');
        $this->db->join('sales_orders so', 'so.id = soi.sales_order_id');
        $this->db->where('MONTH(so.order_date)', $month);
        $this->db->where('YEAR(so.order_date)', $year);
        $this->db->where('so.status !=', 'canceled');
        $query_hpp = $this->db->get()->row();
        $hpp = $query_hpp ? $query_hpp->total_hpp : 0;

        // C. BIAYA OPERASIONAL (EXPENSES)
        $this->db->select_sum('amount');
        $this->db->where('MONTH(expense_date)', $month);
        $this->db->where('YEAR(expense_date)', $year);
        $query_exp = $this->db->get('operational_expenses')->row();
        $expenses = $query_exp ? $query_exp->amount : 0;

        // D. DETAIL LIST BIAYA (Untuk Tabel)
        $this->db->where('MONTH(expense_date)', $month);
        $this->db->where('YEAR(expense_date)', $year);
        $this->db->order_by('expense_date', 'ASC');
        $expense_list = $this->db->get('operational_expenses')->result();

        // E. PRODUK TERLARIS (Top 5)
        $this->db->select('p.name, SUM(soi.qty) as total_qty, SUM(soi.subtotal) as total_sales');
        $this->db->from('sales_order_items soi');
        $this->db->join('sales_orders so', 'so.id = soi.sales_order_id');
        $this->db->join('salt_products p', 'p.product_id = soi.product_id');
        $this->db->where('MONTH(so.order_date)', $month);
        $this->db->where('YEAR(so.order_date)', $year);
        $this->db->where('so.status !=', 'canceled');
        $this->db->group_by('soi.product_id');
        $this->db->order_by('total_qty', 'DESC');
        $this->db->limit(5);
        $top_products = $this->db->get()->result();

        return [
            'revenue'      => (float) $revenue,
            'hpp'          => (float) $hpp,
            'expenses'     => (float) $expenses,
            'expense_list' => $expense_list,
            'top_products' => $top_products
        ];
    }

    // =========================================================================
    // 4. NERACA KEUANGAN (BALANCE SHEET) - FIX COLUMN NAME
    // =========================================================================
    public function get_balance_sheet() {
        
        // --- A. ASET (ASSETS) ---
        
        // 1. Persediaan (Inventory Value)
        $this->db->select('SUM(ws.quantity * p.base_cost) as inventory_value', FALSE);
        $this->db->from('warehouse_stock ws');
        $this->db->join('salt_products p', 'p.product_id = ws.product_id');
        $q_inv = $this->db->get()->row();
        $inventory = $q_inv ? $q_inv->inventory_value : 0;

        // 2. Piutang (Receivables) - Dari Penjualan (Tabel sales_orders pakai grand_total)
        $this->db->select('SUM(grand_total - total_paid) as receivables', FALSE);
        $this->db->where('status !=', 'canceled');
        $this->db->where('payment_status !=', 'paid');
        $q_piutang = $this->db->get('sales_orders')->row();
        $piutang = $q_piutang ? $q_piutang->receivables : 0;

        // 3. Estimasi Kas (Cash on Hand)
        // Uang Masuk dari Penjualan
        $this->db->select_sum('total_paid');
        $q_in = $this->db->get('sales_orders')->row();
        $cash_in = $q_in ? $q_in->total_paid : 0;

        // Uang Keluar untuk Pembelian (Cek tabel purchases)
        $cash_out_buy = 0;
        // Kita cek manual apakah kolom total_paid ada
        if($this->db->field_exists('total_paid', 'purchases')) {
            $this->db->select_sum('total_paid');
            $q_out_buy = $this->db->get('purchases')->row();
            $cash_out_buy = $q_out_buy ? $q_out_buy->total_paid : 0;
        }

        // Uang Keluar untuk Operasional
        $this->db->select_sum('amount');
        $q_out_exp = $this->db->get('operational_expenses')->row();
        $cash_out_exp = $q_out_exp ? $q_out_exp->amount : 0;

        $cash_balance = $cash_in - ($cash_out_buy + $cash_out_exp);


        // --- B. KEWAJIBAN (LIABILITIES) ---

        // 1. Hutang Usaha (Payables) - Dari Pembelian
        // PERBAIKAN: Menggunakan 'total_amount' bukan 'grand_total'
        $hutang = 0;
        
        // Pastikan kolom total_paid sudah dibuat di database
        if($this->db->field_exists('total_paid', 'purchases')) {
            // Rumus: Total Tagihan (total_amount) - Sudah Dibayar (total_paid)
            $this->db->select('SUM(total_amount - total_paid) as payables', FALSE);
            $this->db->where('status !=', 'canceled');
            $q_hutang = $this->db->get('purchases')->row();
            $hutang = $q_hutang ? $q_hutang->payables : 0;
        }

        // --- C. EKUITAS (EQUITY) ---
        // Equity = Assets - Liabilities
        $total_assets      = $cash_balance + $inventory + $piutang;
        $total_liabilities = $hutang;
        $equity            = $total_assets - $total_liabilities;

        return [
            'cash'         => (float) $cash_balance,
            'inventory'    => (float) $inventory,
            'receivables'  => (float) $piutang,
            'payables'     => (float) $hutang,
            'equity'       => (float) $equity,
            'total_assets' => (float) $total_assets
        ];
    }
}