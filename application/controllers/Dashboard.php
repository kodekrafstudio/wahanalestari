<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        
        $this->load->library('template');
        $this->load->model('Dashboard_model');
        $this->load->model('Delivery_model'); // Untuk Driver
        $this->load->model('Report_model');   // Untuk Admin
    }

    public function index()
    {
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $data['title'] = 'Dashboard';

        // =========================================================
        // A. DASHBOARD DRIVER (Tampilan Mobile Friendly)
        // =========================================================
        if ($role == 'driver') {
             $today = date('Y-m-d');
             
             // 1. Cek Rute Hari Ini
             $route = $this->db->get_where('delivery_routes', [
                 'driver_id' => $user_id, 
                 'route_date'=> $today
             ])->row();
             
             $data['my_route'] = $route ? $this->Delivery_model->get_route_detail($route->route_id) : null;
             
             // 2. Hitung Progress Pengiriman
             $data['total_drop'] = 0;
             $data['done_drop']  = 0;
             
             if($data['my_route'] && isset($data['my_route']->points)) {
                 $data['total_drop'] = count($data['my_route']->points);
                 foreach($data['my_route']->points as $p) {
                     if($p->status == 'delivered' || $p->status == 'done') $data['done_drop']++;
                 }
             }

             $this->template->load('dashboard/dashboard_driver', $data);
        } 
        
        // =========================================================
        // B. DASHBOARD SALES (Fokus ke Target & Komisi)
        // =========================================================
        elseif ($role == 'sales') {
            $month = date('m'); 
            $year  = date('Y');

            // 1. Hitung Omzet Bulan Ini
            $this->db->select_sum('total_amount');
            $this->db->where(['created_by'=>$user_id, 'MONTH(order_date)'=>$month, 'YEAR(order_date)'=>$year, 'status !='=>'canceled']);
            $omzet = $this->db->get('sales_orders')->row()->total_amount ?? 0;

            // 2. Hitung Jumlah Transaksi
            $this->db->where(['created_by'=>$user_id, 'MONTH(order_date)'=>$month, 'YEAR(order_date)'=>$year]);
            $count = $this->db->count_all_results('sales_orders');

            // 3. Order Terbaru (History)
            $this->db->limit(5)->order_by('id','DESC');
            $data['recent_orders'] = $this->db->get_where('sales_orders', ['created_by'=>$user_id])->result();

            // 4. Statistik Sales
            $target = 50000000; // Contoh Target 50 Juta (Bisa diset di database user)
            $data['sales_stats'] = [
                'omzet' => $omzet,
                'count' => $count,
                'komisi'=> $omzet * 0.025, // Komisi 2.5%
                'target'=> $target,
                'persen'=> ($target > 0) ? ($omzet / $target) * 100 : 0
            ];

            $this->template->load('dashboard/dashboard_sales', $data);
        }

        // =========================================================
        // C. DASHBOARD ADMIN / OWNER (Lengkap dengan Filter)
        // =========================================================
        else {
            // 1. Tangkap Filter (Default: Bulan Ini)
            $filter_month = $this->input->get('month') ? $this->input->get('month') : date('m');
            $filter_year  = $this->input->get('year')  ? $this->input->get('year')  : date('Y');
            
            $data['f_month'] = $filter_month;
            $data['f_year']  = $filter_year;

            // 2. Ambil Data Keuangan (KPI)
            $data['kpi'] = $this->Dashboard_model->get_financial_kpi($filter_month, $filter_year);
            
            // 3. Grafik & Peta
            $trend = $this->Dashboard_model->get_monthly_trend($filter_year);
            $data['chart_omzet']  = json_encode($trend['omzet']);
            $data['chart_profit'] = json_encode($trend['profit']);
            $data['map_data']     = json_encode($this->Dashboard_model->get_map_markers());

            // 4. Widget Pendukung
            $data['top_customers'] = $this->Dashboard_model->get_top_customers($filter_month, $filter_year);
            $data['top_products']  = $this->Dashboard_model->get_top_products($filter_month, $filter_year);
            $data['follow_up']     = $this->Dashboard_model->get_smart_followup();
            $data['low_stock']     = $this->Dashboard_model->get_low_stock_items();

            // 5. Pie Chart Data
            $p_names = []; $p_qty = [];
            if(!empty($data['top_products'])){
                foreach($data['top_products'] as $tp) { $p_names[] = $tp->name; $p_qty[] = (int) $tp->total_sold; }
            }
            $data['pie_labels'] = json_encode($p_names);
            $data['pie_data']   = json_encode($p_qty);

            // ... (Kode chart_omzet & chart_profit tetap ada) ...
            
            // --- TAMBAHAN: CHART HARIAN ---
            $daily_sales = $this->Dashboard_model->get_daily_trend($filter_month, $filter_year);
            $data['chart_daily'] = json_encode($daily_sales);
            
            // Buat label tanggal 1 s/d 30/31
            $days_count = cal_days_in_month(CAL_GREGORIAN, $filter_month, $filter_year);
            $data['daily_labels'] = json_encode(range(1, $days_count));
            
            // Nama bulan untuk judul grafik
            $data['month_name'] = date('F Y', mktime(0, 0, 0, $filter_month, 1, $filter_year));
            
            // ... (lanjut ke map_data) ...

            $this->template->load('dashboard/index', $data);
        }
    }
}