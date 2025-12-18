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

    // Langkah 2: Kelola Titik Tujuan (Tambah Toko ke Rute ini)
    public function view($id) {
        $data['route'] = $this->Delivery_model->get_route_detail($id);
        if(!$data['route']) show_404();

        $data['title'] = 'Detail Rute Pengiriman';
        $data['customers'] = $this->Customer_model->get_all(); // Untuk dropdown tambah titik
        
        $this->template->load('logistics/routes/view', $data);
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