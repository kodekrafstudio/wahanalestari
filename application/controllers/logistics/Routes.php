<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Routes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        $this->load->model('Delivery_model');
        $this->load->library('template');
    }

    public function index() {
        $data['title'] = 'Manajemen Rute Pengiriman';
        $data['routes'] = $this->Delivery_model->get_all_routes();
        $this->template->load('logistics/routes/index', $data);
    }

    public function create() {
        if ($this->input->post()) {
            $data = [
                'driver_id'  => $this->input->post('driver_id'),
                'vehicle'    => $this->input->post('vehicle'),
                'route_date' => $this->input->post('route_date'),
                'status'     => 'planned'
            ];
            $id = $this->Delivery_model->insert_route($data);
            redirect('logistics/routes/view/'.$id);
        }
        
        // Ambil data driver (User dengan role driver)
        $data['drivers'] = $this->db->get_where('users', ['role' => 'driver'])->result();
        $this->template->load('logistics/routes/create', $data);
    }

    public function view($id) {
        $data['route'] = $this->Delivery_model->get_route_detail($id);
        if(!$data['route']) show_404();

        $data['title'] = 'Detail Rute: ' . $data['route']->driver_name;
        
        // UPDATE: Ambil Pending Orders (Invoice) bukannya Customers biasa
        $data['pending_orders'] = $this->Delivery_model->get_pending_orders();

        $this->template->load('logistics/routes/view', $data);
    }

    // LOGIC UTAMA: Tambah Titik & Update Status Order
    public function add_point($route_id) {
        $sales_order_id = $this->input->post('sales_order_id');
        
        if($sales_order_id) {
            // Ambil info order untuk dapat customer_id
            $order = $this->db->get_where('sales_orders', ['id' => $sales_order_id])->row();
            
            if($order) {
                $data = [
                    'route_id'       => $route_id,
                    'sales_order_id' => $sales_order_id, // Simpan ID Invoice
                    'customer_id'    => $order->customer_id,
                    'sequence_number'=> $this->input->post('sequence_number'),
                    'status'         => 'pending'
                ];
                
                $this->Delivery_model->insert_point($data);

                // OTOMATIS: Update Status Order jadi 'delivering'
                // Ini akan memicu pengurangan stok jika logika Sales Anda sudah jalan
                $this->db->where('id', $sales_order_id);
                $this->db->update('sales_orders', ['status' => 'delivering']);
                
                $this->session->set_flashdata('message', 'Order berhasil ditambahkan ke rute.');
            }
        }
        redirect('logistics/routes/view/'.$route_id);
    }

    // UPDATE: Hapus Titik & Kembalikan Status Order
    public function delete_point($point_id, $route_id) {
        // Cek dulu ID Ordernya sebelum dihapus
        $point = $this->db->get_where('delivery_route_points', ['point_id' => $point_id])->row();
        
        if($point && $point->sales_order_id) {
            // Kembalikan status jadi 'preparing' (Siap Kirim ulang)
            $this->db->where('id', $point->sales_order_id);
            $this->db->update('sales_orders', ['status' => 'preparing']);
        }

        $this->Delivery_model->delete_point($point_id);
        $this->session->set_flashdata('message', 'Titik dihapus dari rute.');
        redirect('logistics/routes/view/'.$route_id);
    }

    public function print_surat_jalan($route_id) {
        $data['route'] = $this->Delivery_model->get_route_detail($route_id);
        $this->load->view('logistics/routes/print', $data);
    }
}