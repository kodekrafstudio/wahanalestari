<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Security Check: Sudah Bagus!
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        
        $this->load->library('template');
        $this->load->model('Dashboard_model');
    }

    public function index()
    {
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $data['title'] = 'Dashboard';

        // --- A. DASHBOARD DRIVER ---
        if ($role == 'driver') {
             $this->load->model('Delivery_model');
             $today = date('Y-m-d');
             
             // Ambil rute hari ini
             $route = $this->db->get_where('delivery_routes', [
                 'driver_id' => $user_id, 
                 'route_date'=> $today
             ])->row();
             
             // Cegah error jika $route null (belum ada rute)
             $data['my_route'] = $route ? $this->Delivery_model->get_route_detail($route->route_id) : null;
             
             // Hitung Statistik
             $data['total_drop'] = ($data['my_route'] && isset($data['my_route']->points)) ? count($data['my_route']->points) : 0;
             $data['done_drop']  = 0;
             
             if($data['my_route'] && isset($data['my_route']->points)) {
                 foreach($data['my_route']->points as $p) {
                     // Asumsi status 'delivered' atau 'done'
                     if($p->status == 'delivered' || $p->status == 'done') $data['done_drop']++;
                 }
             }

             $this->template->load('dashboard/dashboard_driver', $data);
        } 
        
        // --- B. DASHBOARD SALES ---
        elseif ($role == 'sales') {
            
            $month = date('m');
            $year = date('Y');

            // 1. Omzet (Tambahkan IFNULL di SQL agar return 0 jika null)
            $this->db->select_sum('total_amount');
            $this->db->where('created_by', $user_id);
            $this->db->where('MONTH(order_date)', $month);
            $this->db->where('YEAR(order_date)', $year);
            $this->db->where('status !=', 'canceled');
            $query_omzet = $this->db->get('sales_orders')->row();
            $omzet = $query_omzet->total_amount ?? 0; // Null Coalescing Operator

            // 2. Jumlah Transaksi
            $this->db->where('created_by', $user_id);
            $this->db->where('MONTH(order_date)', $month);
            $this->db->where('YEAR(order_date)', $year);
            $trx_count = $this->db->count_all_results('sales_orders');

            // 3. Recent Orders
            $this->db->select('invoice_no, total_amount, status, order_date');
            $this->db->where('created_by', $user_id);
            $this->db->order_by('id', 'DESC');
            $this->db->limit(5);
            $recent_orders = $this->db->get('sales_orders')->result();

            // 4. Hitung Komisi
            $komisi = $omzet * 0.025; 

            $data['sales_stats'] = [
                'omzet' => $omzet,
                'count' => $trx_count,
                'komisi'=> $komisi
            ];
            $data['recent_orders'] = $recent_orders;

            $this->template->load('dashboard/dashboard_sales', $data);
        }

        // --- C. DASHBOARD ADMIN / OWNER ---
        else {
            // PERBAIKAN 3: Melengkapi Logika Admin & Mencegah Error Data Kosong
            
            // 1. KPI Cards
            // Pastikan method get_financial_kpi() ada di Dashboard_model
            $data['kpi'] = $this->Dashboard_model->get_financial_kpi();

            // 2. Chart Trend
            $trend = $this->Dashboard_model->get_monthly_trend(date('Y'));
            
            // Safety check: Pastikan array key tersedia sebelum json_encode
            $data['chart_omzet']  = isset($trend['omzet']) ? json_encode($trend['omzet']) : json_encode([]);
            $data['chart_profit'] = isset($trend['profit']) ? json_encode($trend['profit']) : json_encode([]);
            
            // 3. Data Pendukung Lain
            $data['follow_up']     = $this->Dashboard_model->get_smart_followup();
            $data['top_customers'] = $this->Dashboard_model->get_top_customers();
            $data['top_products']  = $this->Dashboard_model->get_top_products();
            $data['low_stock']     = $this->Dashboard_model->get_low_stock_items();
            
            // 4. Format Pie Chart Top Products
            $p_names = []; 
            $p_qty = [];
            
            if(!empty($data['top_products'])){
                foreach($data['top_products'] as $tp) { 
                    $p_names[] = $tp->name; 
                    $p_qty[] = (int) $tp->total_sold; 
                }
            }
            
            $data['pie_labels'] = json_encode($p_names);
            $data['pie_data']   = json_encode($p_qty);

            $this->template->load('dashboard/index', $data);
        }
    }
}