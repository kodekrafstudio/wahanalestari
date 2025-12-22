<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    // 1. KPI UTAMA (Omzet, Laba, Aset, Piutang)
    public function get_financial_kpi($month, $year) {
        // Omzet
        $this->db->select_sum('total_amount');
        $this->db->where(['MONTH(order_date)'=>$month, 'YEAR(order_date)'=>$year, 'status !='=>'canceled']);
        $omzet = $this->db->get('sales_orders')->row()->total_amount ?? 0;

        // HPP (Modal)
        $this->db->select('SUM(soi.qty * p.base_cost) as total_hpp');
        $this->db->from('sales_order_items soi');
        $this->db->join('sales_orders so', 'so.id = soi.sales_order_id');
        $this->db->join('salt_products p', 'p.product_id = soi.product_id');
        $this->db->where(['MONTH(so.order_date)'=>$month, 'YEAR(so.order_date)'=>$year, 'so.status !='=>'canceled']);
        $hpp = $this->db->get()->row()->total_hpp ?? 0;

        // Expense (Biaya)
        $this->db->select_sum('amount');
        $this->db->where(['MONTH(expense_date)'=>$month, 'YEAR(expense_date)'=>$year]);
        $expenses = $this->db->get('operational_expenses')->row()->amount ?? 0;

        // Aset & Piutang (Akumulasi)
        $this->db->select('SUM(ws.quantity * p.base_cost) as asset_value');
        $this->db->from('warehouse_stock ws');
        $this->db->join('salt_products p', 'p.product_id = ws.product_id');
        $asset_value = $this->db->get()->row()->asset_value ?? 0;

        $this->db->select('SUM(total_amount - total_paid) as piutang');
        $this->db->where('status !=', 'canceled');
        $piutang = $this->db->get('sales_orders')->row()->piutang ?? 0;

        return [
            'omzet' => $omzet, 
            'net_profit' => $omzet - $hpp - $expenses,
            'asset_value' => $asset_value,
            'piutang' => $piutang
        ];
    }

    // 2. TREN BULANAN
    public function get_monthly_trend($year) {
        $data = ['omzet' => [], 'profit' => []];
        for ($i=1; $i<=12; $i++) {
            $this->db->select_sum('total_amount');
            $this->db->where(['MONTH(order_date)'=>$i, 'YEAR(order_date)'=>$year, 'status !='=>'canceled']);
            $omzet = $this->db->get('sales_orders')->row()->total_amount ?? 0;
            $data['omzet'][] = $omzet;
            $data['profit'][] = $omzet * 0.25; // Estimasi Profit 25% (Bisa diganti real query jika perlu)
        }
        return $data;
    }

    // 3. TOP LISTS
    public function get_top_customers($m, $y) {
        $this->db->select('c.name, SUM(so.total_amount) as total_beli');
        $this->db->from('sales_orders so');
        $this->db->join('customers c', 'c.customer_id = so.customer_id');
        $this->db->where(['MONTH(so.order_date)'=>$m, 'YEAR(so.order_date)'=>$y, 'so.status !='=>'canceled']);
        $this->db->group_by('so.customer_id')->order_by('total_beli', 'DESC')->limit(5);
        return $this->db->get()->result();
    }

    public function get_top_products($m, $y) {
        $this->db->select('p.name, SUM(i.qty) as total_sold');
        $this->db->from('sales_order_items i');
        $this->db->join('sales_orders o', 'o.id = i.sales_order_id');
        $this->db->join('salt_products p', 'p.product_id = i.product_id');
        $this->db->where(['MONTH(o.order_date)'=>$m, 'YEAR(o.order_date)'=>$y, 'o.status !='=>'canceled']);
        $this->db->group_by('i.product_id')->order_by('total_sold', 'DESC')->limit(5);
        return $this->db->get()->result();
    }

    // 4. NOTIFIKASI
    public function get_smart_followup() {
        return $this->db->query("SELECT c.name, c.phone, MAX(so.order_date) as last_order, DATEDIFF(NOW(), MAX(so.order_date)) as days_since FROM customers c JOIN sales_orders so ON c.customer_id = so.customer_id WHERE c.status='active' GROUP BY c.customer_id HAVING days_since > 14 ORDER BY days_since ASC LIMIT 5")->result();
    }

    public function get_low_stock_items() {
        return $this->db->select('p.name, p.unit, ws.quantity')->from('warehouse_stock ws')->join('salt_products p', 'p.product_id = ws.product_id')->where('ws.quantity <', 1000)->order_by('ws.quantity', 'ASC')->limit(5)->get()->result();
    }

    // 5. MAPS
    public function get_map_markers() {
        return $this->db->select('name, address, latitude, longitude')->from('customers')->where('latitude !=', '')->get()->result();
    }


    // 6. FITUR BARU: TREN HARIAN
    public function get_daily_trend($month, $year) {
        // Hitung jumlah hari dalam bulan tsb
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $daily_data = [];

        // Siapkan array kosong [1=>0, 2=>0, ... 31=>0]
        for ($d = 1; $d <= $days_in_month; $d++) {
            $daily_data[$d] = 0;
        }

        // Query Group By Tanggal
        $this->db->select('DAY(order_date) as day, SUM(total_amount) as total');
        $this->db->where([
            'MONTH(order_date)' => $month, 
            'YEAR(order_date)'  => $year, 
            'status !='         => 'canceled'
        ]);
        $this->db->group_by('DAY(order_date)');
        $query = $this->db->get('sales_orders')->result();

        // Isi data ke array
        foreach ($query as $row) {
            $daily_data[(int)$row->day] = (int) $row->total;
        }

        // Return hanya values-nya saja agar jadi JSON array [0, 50000, 0...]
        return array_values($daily_data);
    }
}