<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        $this->load->library('template');
        $this->load->model('Dashboard_model'); // Pastikan model ini diload
    }

    public function index()
    {
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $data['title'] = 'Dashboard';

        // --- A. DASHBOARD DRIVER (Mobile Friendly) ---
        if ($role == 'driver') {
             $this->load->model('Delivery_model');
             $today = date('Y-m-d');
             
             // Cari rute hari ini khusus driver ini
             $route = $this->db->get_where('delivery_routes', [
                 'driver_id' => $user_id, 
                 'route_date'=> $today
             ])->row();
             
             $data['my_route'] = $route ? $this->Delivery_model->get_route_detail($route->route_id) : null;
             
             // Statistik Harian Driver
             $data['total_drop'] = $data['my_route'] ? count($data['my_route']->points) : 0;
             $data['done_drop']  = 0;
             if($data['my_route']) {
                 foreach($data['my_route']->points as $p) {
                     if($p->status == 'delivered') $data['done_drop']++;
                 }
             }

             $this->template->load('dashboard/dashboard_driver', $data);
        } 
        
        // --- B. DASHBOARD SALES (Personal Performance) ---
        elseif ($role == 'sales') {
            
            $month = date('m');
            $year = date('Y');

            // 1. Omzet Pribadi Bulan Ini
            $this->db->select_sum('total_amount');
            $this->db->where('created_by', $user_id); // Filter User
            $this->db->where('MONTH(order_date)', $month);
            $this->db->where('YEAR(order_date)', $year);
            $this->db->where('status !=', 'canceled');
            $omzet = $this->db->get('sales_orders')->row()->total_amount ?? 0;

            // 2. Jumlah Transaksi Pribadi
            $this->db->where('created_by', $user_id);
            $this->db->where('MONTH(order_date)', $month);
            $this->db->where('YEAR(order_date)', $year);
            $trx_count = $this->db->count_all_results('sales_orders');

            // 3. Order Terbaru Saya
            $this->db->select('invoice_no, total_amount, status, order_date');
            $this->db->where('created_by', $user_id);
            $this->db->order_by('id', 'DESC');
            $this->db->limit(5);
            $recent_orders = $this->db->get('sales_orders')->result();

            // 4. Estimasi Komisi (Misal 2.5% dari Omzet)
            $komisi = $omzet * 0.025; 

            $data['sales_stats'] = [
                'omzet' => $omzet,
                'count' => $trx_count,
                'komisi'=> $komisi
            ];
            $data['recent_orders'] = $recent_orders;

            $this->template->load('dashboard/dashboard_sales', $data);
        }

        // --- C. DASHBOARD ADMIN / OWNER (Executive) ---
        else {
            // (Kode Dashboard Admin yang sudah kita buat sebelumnya - Biarkan tetap ada)
            // ... Copy paste logika admin sebelumnya di sini ...
            // Agar ringkas, saya asumsikan kode admin di atas sudah Anda miliki.
            // Panggil fungsi model get_financial_kpi dll di sini.
            
            // CONTOH PEMANGGILAN (Pastikan Model diload):
            $data['kpi'] = $this->Dashboard_model->get_financial_kpi();
            $trend = $this->Dashboard_model->get_monthly_trend(date('Y'));
            $data['chart_omzet']  = json_encode($trend['omzet']);
            $data['chart_profit'] = json_encode($trend['profit']);
            $data['follow_up'] = $this->Dashboard_model->get_smart_followup();
            $data['top_customers'] = $this->Dashboard_model->get_top_customers();
            $data['top_products']  = $this->Dashboard_model->get_top_products();
            $data['low_stock']     = $this->Dashboard_model->get_low_stock_items();
            
            // Format Pie Chart
            $p_names = []; $p_qty = [];
            foreach($data['top_products'] as $tp) { $p_names[] = $tp->name; $p_qty[] = $tp->total_sold; }
            $data['pie_labels'] = json_encode($p_names);
            $data['pie_data']   = json_encode($p_qty);

            $this->template->load('dashboard/index', $data);
        }
    }
}