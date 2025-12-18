<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {

    // 1. LAPORAN PENJUALAN (SALES) - AMAN
    public function get_sales_report($start_date, $end_date, $status = 'all') {
        $this->db->select('so.*, c.name as customer_name, u.full_name as sales_name');
        $this->db->from('sales_orders so');
        $this->db->join('customers c', 'c.customer_id = so.customer_id', 'left');
        $this->db->join('users u', 'u.user_id = so.created_by', 'left');
        
        $this->db->where('DATE(so.order_date) >=', $start_date);
        $this->db->where('DATE(so.order_date) <=', $end_date);

        if($status != 'all') {
            $this->db->where('so.status', $status);
        }

        $this->db->order_by('so.order_date', 'DESC');
        return $this->db->get()->result();
    }

    // 2. LAPORAN MUTASI STOK (STOCK) - AMAN
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

    // 3. LABA RUGI (PROFIT & LOSS) - PERBAIKAN GROUP BY
    public function get_profit_loss($month, $year) {
        
        // A. PENDAPATAN
        $this->db->select_sum('grand_total');
        $this->db->where('MONTH(order_date)', $month);
        $this->db->where('YEAR(order_date)', $year);
        $this->db->where('status !=', 'canceled');
        $query_rev = $this->db->get('sales_orders')->row();
        $revenue = $query_rev ? $query_rev->grand_total : 0;

        // B. HPP (COGS)
        $this->db->select('SUM(soi.qty * soi.cost) as total_hpp', FALSE);
        $this->db->from('sales_order_items soi');
        $this->db->join('sales_orders so', 'so.id = soi.sales_order_id');
        $this->db->where('MONTH(so.order_date)', $month);
        $this->db->where('YEAR(so.order_date)', $year);
        $this->db->where('so.status !=', 'canceled');
        $query_hpp = $this->db->get()->row();
        $hpp = $query_hpp ? $query_hpp->total_hpp : 0;

        // C. BIAYA OPERASIONAL (EXPENSES)
        // Menggunakan tabel operational_expenses (sesuai database Anda)
        $this->db->select('ec.name as category, SUM(e.amount) as total');
        $this->db->from('operational_expenses e');
        $this->db->join('expense_categories ec', 'ec.category_id = e.category_id', 'left'); 
        $this->db->where('MONTH(e.expense_date)', $month);
        $this->db->where('YEAR(e.expense_date)', $year);
        
        // FIX: Group By harus konsisten
        $this->db->group_by('e.category_id, ec.name, e.category'); 
        $expense_list = $this->db->get()->result();

        $total_expense = 0;
        foreach($expense_list as $e) { $total_expense += $e->total; }

        // D. PRODUK TERLARIS (Top 5)
        // FIX: Group By p.name ditambahkan agar tidak error di MySQL modern
        $this->db->select('p.name, SUM(soi.qty) as total_qty, SUM(soi.subtotal) as total_sales');
        $this->db->from('sales_order_items soi');
        $this->db->join('sales_orders so', 'so.id = soi.sales_order_id');
        $this->db->join('salt_products p', 'p.product_id = soi.product_id');
        $this->db->where('MONTH(so.order_date)', $month);
        $this->db->where('YEAR(so.order_date)', $year);
        $this->db->where('so.status !=', 'canceled');
        $this->db->group_by('soi.product_id, p.name'); 
        $this->db->order_by('total_qty', 'DESC');
        $this->db->limit(5);
        $top_products = $this->db->get()->result();

        // Hitung Profit & Margin
        $gross_profit = $revenue - $hpp;
        $net_profit   = $gross_profit - $total_expense;
        $gross_margin = ($revenue > 0) ? ($gross_profit / $revenue) * 100 : 0;
        $net_margin   = ($revenue > 0) ? ($net_profit / $revenue) * 100 : 0;

        return [
            'revenue'       => (float) $revenue,
            'cogs'          => (float) $hpp,
            'gross_profit'  => (float) $gross_profit,
            'expenses_list' => $expense_list,
            'total_expense' => (float) $total_expense,
            'net_profit'    => (float) $net_profit,
            'top_products'  => $top_products,
            'gross_margin'  => $gross_margin,
            'net_margin'    => $net_margin
        ];
    }

    // 4. NERACA (BALANCE SHEET) - PERBAIKAN KOLOM
    public function get_balance_sheet() {
        
        // --- A. ASSETS (HARTA) ---
        
        // 1. Kas (Estimasi)
        $modal_awal = $this->db->select_sum('amount')->get('company_capital')->row();
        $val_modal  = $modal_awal ? $modal_awal->amount : 0;

        $sales_paid = $this->db->select_sum('total_paid')->get('sales_orders')->row();
        $val_sales  = $sales_paid ? $sales_paid->total_paid : 0;
        
        // FIX: Menggunakan kolom 'total_paid' dari tabel purchases (jika ada)
        // Jika belum ada kolom total_paid di purchases, jalankan SQL ALTER TABLE dulu.
        // Asumsi: sudah ada sesuai file sql.
        $purch_paid = $this->db->select_sum('total_paid')->get('purchases')->row();
        $val_purch  = $purch_paid ? $purch_paid->total_paid : 0;
        
        $exp_total  = $this->db->select_sum('amount')->get('operational_expenses')->row();
        $val_exp    = $exp_total ? $exp_total->amount : 0;

        $cash_on_hand = ($val_modal + $val_sales) - ($val_purch + $val_exp);

        // 2. Persediaan (Stok * HPP)
        $query_stock = $this->db->query("SELECT SUM(quantity * base_cost) as val FROM warehouse_stock ws JOIN salt_products p ON p.product_id = ws.product_id")->row();
        $inventory_value = $query_stock ? $query_stock->val : 0;

        // 3. Piutang (Sales - Paid)
        $query_ar = $this->db->query("SELECT SUM(grand_total - total_paid) as piutang FROM sales_orders WHERE payment_status != 'paid' AND status != 'canceled'")->row();
        $piutang = $query_ar ? $query_ar->piutang : 0;

        $total_assets = $cash_on_hand + $inventory_value + $piutang;

        // --- B. LIABILITIES (HUTANG) ---
        
        // 1. Hutang Dagang (Purchase - Paid)
        // FIX ERROR DISINI: Menggunakan 'total_cost' (bukan total_amount)
        $query_ap = $this->db->query("SELECT SUM(total_cost - total_paid) as hutang FROM purchases WHERE payment_status != 'paid' AND status != 'canceled'")->row();
        $hutang = $query_ap ? $query_ap->hutang : 0;

        // --- C. EQUITY (MODAL) ---
        
        $total_equity_paid = $val_modal;
        $retained_earnings = $total_assets - $hutang - $total_equity_paid;

        return [
            'assets' => [
                'cash'      => (float) $cash_on_hand,
                'inventory' => (float) $inventory_value,
                'piutang'   => (float) $piutang,
                'total'     => (float) $total_assets
            ],
            'liabilities' => [
                'hutang_dagang' => (float) $hutang,
                'total'         => (float) $hutang
            ],
            'equity' => [
                'modal_disetor' => (float) $total_equity_paid,
                'laba_ditahan'  => (float) $retained_earnings,
                'total'         => (float) ($total_equity_paid + $retained_earnings)
            ]
        ];
    }
}