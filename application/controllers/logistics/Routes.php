<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Routes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }

        $role = $this->session->userdata('role');
        // Hanya Admin dan Owner yang boleh masuk
        if ($role != 'admin' && $role != 'owner' && $role != 'driver') {
            show_error('Anda tidak memiliki hak akses untuk halaman ini.', 403, 'Forbidden');
        }
        
        
        $this->load->model('Delivery_model');
        $this->load->model('User_model'); // Untuk list driver
        $this->load->model('Customer_model'); // Untuk list toko
        $this->load->library('template');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Jadwal Pengiriman';
        $data['routes'] = $this->Delivery_model->get_all_routes();
        $this->template->load('logistics/routes/index', $data);
    }

    // Langkah 1: Buat Header Rute (Siapa Driver & Mobilnya)
    public function create() {
        $this->form_validation->set_rules('driver_id', 'Driver', 'required');
        $this->form_validation->set_rules('vehicle', 'Kendaraan', 'required');
        $this->form_validation->set_rules('route_date', 'Tanggal', 'required');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Buat Rute Baru';
            // Ambil user yang role-nya driver (Asumsi di DB ada role 'driver')
            $data['drivers'] = $this->db->get_where('users', ['role' => 'driver'])->result();
            $this->template->load('logistics/routes/create', $data);
        } else {
            $data = [
                'driver_id' => $this->input->post('driver_id'),
                'vehicle'   => $this->input->post('vehicle'),
                'route_date'=> $this->input->post('route_date'),
                'status'    => 'planned'
            ];
            $route_id = $this->Delivery_model->create_route($data);
            redirect('logistics/routes/view/' . $route_id);
        }
    }

    // Saat membuka halaman Detail Rute (view)
    public function view($id) {
        // ... kode lama ...
        
        // GANTI INI: Jangan ambil semua customer
        // $data['customers'] = $this->Customer_model->get_all(); 
        
        // JADI INI: Ambil hanya Sales Order yang statusnya 'delivering' tapi belum punya Rute
        // (Asumsi Anda menambah kolom 'route_id' di tabel sales_orders atau mapping manual)
        $data['pending_orders'] = $this->db->query("
            SELECT so.id, so.invoice_no, c.name, c.address 
            FROM sales_orders so
            JOIN customers c ON c.customer_id = so.customer_id
            WHERE so.status = 'request' OR so.status = 'preparing'
        ")->result();
        
        $this->template->load('logistics/routes/view', $data);
    }

    // Ubah fungsi add_point untuk menyimpan sales_order_id
    public function add_point($route_id) {
        $sales_order_id = $this->input->post('sales_order_id'); // Tangkap ID Order
        
        if($sales_order_id) {
            // Ambil data customer dari order tersebut
            $order = $this->db->get_where('sales_orders', ['id'=>$sales_order_id])->row();
            
            $data = [
                'route_id' => $route_id,
                'sales_order_id' => $sales_order_id, // Tambah kolom ini di tabel delivery_route_points
                'customer_id' => $order->customer_id,
                'status' => 'pending'
            ];
            
            // Update status Order jadi 'delivering' otomatis saat masuk rute
            $this->db->where('id', $sales_order_id)->update('sales_orders', ['status' => 'delivering']);
            
            // ... panggil model untuk simpan ...
        }
    }

    // Action: Tambah Customer ke Rute
    public function add_point($route_id) {
        $customer_id = $this->input->post('customer_id');
        if($customer_id) {
            // Hitung urutan terakhir
            $last_point = $this->db->order_by('sequence_number', 'DESC')->get_where('delivery_route_points', ['route_id'=>$route_id])->row();
            $seq = $last_point ? $last_point->sequence_number + 1 : 1;

            $data = [
                'route_id' => $route_id,
                'customer_id' => $customer_id,
                'sequence_number' => $seq,
                'status' => 'pending'
            ];
            $this->Delivery_model->add_point($data);
            $this->session->set_flashdata('message', 'Tujuan berhasil ditambahkan');
        }
        redirect('logistics/routes/view/'.$route_id);
    }

    public function delete_point($route_id, $point_id) {
        $this->Delivery_model->delete_point($point_id);
        redirect('logistics/routes/view/'.$route_id);
    }

    // Fitur Cetak Surat Jalan (Sederhana)
    public function print_surat_jalan($id) {
        $data['route'] = $this->Delivery_model->get_route_detail($id);
        $this->load->view('logistics/routes/print', $data);
    }
}