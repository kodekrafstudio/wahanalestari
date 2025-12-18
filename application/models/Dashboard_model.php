<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    // 1. STATISTIK KEUANGAN & ASET (BARIS 1 DASHBOARD)
    public function get_financial_kpi() {
        $month = date('m');
        $year  = date('Y');

        // A. Omzet Bulan Ini
        $this->db->select_sum('total_amount');
        $this->db->where('MONTH(order_date)', $month);
        $this->db->where('YEAR(order_date)', $year);
        $this->db->where('status !=', 'canceled');
        $omzet = $this->db->get('sales_orders')->row()->total_amount ?? 0;

        // B. Hitung HPP Bulan Ini (Modal Barang Terjual)
        // Rumus: Qty * Base Cost
        $this->db->select('SUM(soi.qty * p.base_cost) as total_hpp');
        $this->db->from('sales_order_items soi');
        $this->db->join('sales_orders so', 'so.id = soi.sales_order_id');
        $this->db->join('salt_products p', 'p.product_id = soi.product_id');
        $this->db->where('MONTH(so.order_date)', $month);
        $this->db->where('YEAR(so.order_date)', $year);
        $this->db->where('so.status !=', 'canceled');
        $hpp = $this->db->get()->row()->total_hpp ?? 0;

        // C. Biaya Operasional Bulan Ini
        $this->db->select_sum('amount');
        $this->db->where('MONTH(expense_date)', $month);
        $this->db->where('YEAR(expense_date)', $year);
        $expenses = $this->db->get('operational_expenses')->row()->amount ?? 0;

        // ESTIMASI NET PROFIT (Laba Bersih)
        $net_profit = $omzet - $hpp - $expenses;

        // D. Total Piutang (Unpaid) - Akumulasi Semua Waktu
        // Rumus: Total Tagihan - Total Yang Sudah Dibayar
        $this->db->select('SUM(total_amount - total_paid) as piutang');
        $this->db->where('status !=', 'canceled');
        // Hanya ambil yg belum lunas (ada sisa > 100 perak toleransi)
        $this->db->where('(total_amount - total_paid) >', 100); 
        $piutang = $this->db->get('sales_orders')->row()->piutang ?? 0;

        // E. VALUASI ASET GUDANG (Stok Fisik * Harga Beli)
        $this->db->select('SUM(ws.quantity * p.base_cost) as asset_value');
        $this->db->from('warehouse_stock ws');
        $this->db->join('salt_products p', 'p.product_id = ws.product_id');
        $asset_value = $this->db->get()->row()->asset_value ?? 0;

        return [
            'omzet'       => $omzet,
            'net_profit'  => $net_profit,
            'piutang'     => $piutang,
            'asset_value' => $asset_value
        ];
    }

    // 2. GRAFIK TREN BULANAN (OMZET VS PROFIT)
    public function get_monthly_trend($year) {
        $data = ['omzet' => [], 'profit' => []];
        
        for ($i=1; $i<=12; $i++) {
            // Ambil Omzet per bulan
            $this->db->select_sum('total_amount');
            $this->db->where('MONTH(order_date)', $i);
            $this->db->where('YEAR(order_date)', $year);
            $this->db->where('status !=', 'canceled');
            $omzet = $this->db->get('sales_orders')->row()->total_amount ?? 0;

            // Masukkan ke array
            $data['omzet'][] = $omzet;
            
            // Simulasi Profit Margin (Misal 30%) untuk grafik tren cepat
            // (Idealnya query detail seperti di atas, tapi akan berat jika di-loop 12x)
            $data['profit'][] = $omzet * 0.3; 
        }
        return $data;
    }

    // 3. SMART FOLLOW UP (Pelanggan yg sudah waktunya beli lagi)
    public function get_smart_followup() {
        // Cari pelanggan yang terakhir beli > 14 hari lalu
        $query = $this->db->query("
            SELECT c.name, c.phone, MAX(so.order_date) as last_order, 
            DATEDIFF(NOW(), MAX(so.order_date)) as days_since
            FROM customers c
            JOIN sales_orders so ON c.customer_id = so.customer_id
            WHERE c.status = 'active' AND so.status != 'canceled'
            GROUP BY c.customer_id
            HAVING days_since > 14
            ORDER BY days_since ASC
            LIMIT 5
        ");
        return $query->result();
    }

    // 4. TOP 5 CUSTOMER
    public function get_top_customers() {
        $month = date('m');
        $year  = date('Y');
        $this->db->select('c.name, SUM(so.total_amount) as total_beli');
        $this->db->from('sales_orders so');
        $this->db->join('customers c', 'c.customer_id = so.customer_id');
        $this->db->where('MONTH(so.order_date)', $month);
        $this->db->where('YEAR(so.order_date)', $year);
        $this->db->where('so.status !=', 'canceled');
        $this->db->group_by('so.customer_id');
        $this->db->order_by('total_beli', 'DESC');
        $this->db->limit(5);
        return $this->db->get()->result();
    }

    // 5. TOP PRODUK
    public function get_top_products() {
        $this->db->select('p.name, SUM(i.qty) as total_sold');
        $this->db->from('sales_order_items i');
        $this->db->join('sales_orders o', 'o.id = i.sales_order_id'); 
        $this->db->join('salt_products p', 'p.product_id = i.product_id');
        $this->db->where('o.status !=', 'canceled');
        $this->db->group_by('i.product_id');
        $this->db->order_by('total_sold', 'DESC');
        $this->db->limit(5);
        return $this->db->get()->result();
    }

    // 6. STOK KRITIS
    public function get_low_stock_items() {
        $this->db->select('p.name, p.unit, ws.quantity');
        $this->db->from('warehouse_stock ws');
        $this->db->join('salt_products p', 'p.product_id = ws.product_id');
        
        // UBAH 100 JADI 1000 DISINI
        $this->db->where('ws.quantity <', 1000); 
        
        $this->db->order_by('ws.quantity', 'ASC');
        $this->db->limit(5);
        return $this->db->get()->result();
    }
}