<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }

        $role = $this->session->userdata('role');
        // Hanya Admin dan Owner yang boleh masuk
        if ($role != 'admin' && $role != 'owner') {
            show_error('Anda tidak memiliki hak akses untuk halaman ini.', 403, 'Forbidden');
        }
        
        
        $this->load->model('Customer_model');
        $this->load->model('Category_model'); // Load ini untuk dropdown
        $this->load->library('template');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Database Pelanggan';
        
        // QUERY ADVANCED:
        // Mengambil data pelanggan + Total Belanja + Tanggal Order Terakhir
        $this->db->select('
            c.*, 
            bc.category_name,
            COALESCE(SUM(so.total_amount), 0) as total_spent,
            MAX(so.order_date) as last_order
        ');
        $this->db->from('customers c');
        $this->db->join('business_categories bc', 'bc.category_id = c.category_id', 'left');
        // Join ke sales_orders (hanya yg statusnya tidak batal)
        $this->db->join('sales_orders so', 'so.customer_id = c.customer_id AND so.status != "canceled"', 'left');
        
        $this->db->group_by('c.customer_id');
        $this->db->order_by('total_spent', 'DESC'); // Urutkan dari yang paling SULTAN
        
        $data['customers'] = $this->db->get()->result();
        
        $this->template->load('marketing/customers/index', $data);
    }

    public function add() {
        // 1. Validasi Input
        $this->form_validation->set_rules('name', 'Nama Pelanggan', 'required|trim');
        $this->form_validation->set_rules('phone', 'No HP', 'required|numeric');
        $this->form_validation->set_rules('category_id', 'Kategori', 'required');
        
        // Validasi Alamat (Opsional tapi disarankan)
        $this->form_validation->set_rules('address', 'Alamat', 'trim');
        $this->form_validation->set_rules('district', 'Kecamatan', 'trim'); // Baru
        $this->form_validation->set_rules('city', 'Kota', 'trim');         // Baru

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal / baru buka halaman
            $data['title'] = 'Tambah Pelanggan Baru';
            $data['categories'] = $this->db->get('business_categories')->result();
            
            $this->template->load('marketing/customers/add', $data);
        } else {
            // 2. Siapkan Data Simpan
            $data = [
                'name'           => $this->input->post('name'),
                'contact_person' => $this->input->post('contact_person'),
                'phone'          => $this->input->post('phone'),
                'category_id'    => $this->input->post('category_id'),
                
                // Data Lokasi Lengkap
                'address'        => $this->input->post('address'),
                'district'       => $this->input->post('district'), // Tangkap Kecamatan
                'city'           => $this->input->post('city'),     // Tangkap Kota
                
                'latitude'       => $this->input->post('latitude'),
                'longitude'      => $this->input->post('longitude'),
                
                'status'         => 'prospect', // Default status: Prospek
                'created_at'     => date('Y-m-d H:i:s'),
                'created_by'     => $this->session->userdata('user_id')
            ];

            // 3. Eksekusi Simpan
            $this->db->insert('customers', $data);
            
            $this->session->set_flashdata('message', 'Pelanggan baru berhasil ditambahkan.');
            redirect('marketing/customers');
        }
    }

    public function edit($id) {
        // 1. Ambil Data Lama
        $row = $this->db->get_where('customers', ['customer_id' => $id])->row();
        if(!$row) show_404();

        // 2. Validasi (Sama dengan Add)
        $this->form_validation->set_rules('name', 'Nama Pelanggan', 'required|trim');
        $this->form_validation->set_rules('phone', 'No HP', 'required|numeric');
        $this->form_validation->set_rules('category_id', 'Kategori', 'required');

        if ($this->form_validation->run() == FALSE) {
            // LOAD VIEW 'ADD' TAPI BAWA DATA ($row)
            $data['title']      = 'Edit Pelanggan: ' . $row->name;
            $data['categories'] = $this->db->get('business_categories')->result();
            $data['row']        = $row; // Kirim data pelanggan ke view
            
            // Perhatikan ini: Kita load 'add', bukan 'form'
            $this->template->load('marketing/customers/add', $data); 
        } else {
            // 3. Proses Update
            $data = [
                'name'           => $this->input->post('name'),
                'contact_person' => $this->input->post('contact_person'),
                'phone'          => $this->input->post('phone'),
                'category_id'    => $this->input->post('category_id'),
                'address'        => $this->input->post('address'),
                'district'       => $this->input->post('district'),
                'city'           => $this->input->post('city'),
                'latitude'       => $this->input->post('latitude'),
                'longitude'      => $this->input->post('longitude'),
                // Status & Created_by tidak perlu diupdate
            ];

            $this->db->where('customer_id', $id);
            $this->db->update('customers', $data);
            
            $this->session->set_flashdata('message', 'Data pelanggan berhasil diperbarui.');
            redirect('marketing/customers');
        }
    }

    public function delete($id) {
        $this->Customer_model->delete($id);
        $this->session->set_flashdata('message', 'Pelanggan berhasil dihapus');
        redirect('marketing/customers');
    }

    public function map() {
        $data['title'] = 'Peta Sebaran Pelanggan';
        $this->template->load('marketing/customers/map', $data);
    }

    private function _process_form($row = null) {
        $data['title'] = $row ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru';
        $data['row']   = $row;
        // Kita butuh data kategori untuk dropdown
        $data['categories'] = $this->Category_model->get_all(); 

        $id = $row ? $row->customer_id : null;

        // Validasi Form
        $this->form_validation->set_rules('name', 'Nama Pelanggan', 'required');
        $this->form_validation->set_rules('category_id', 'Kategori Bisnis', 'required');
        $this->form_validation->set_rules('city', 'Kota', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->template->load('marketing/customers/form', $data);
        } else {
            $post_data = [
                'name'           => $this->input->post('name'),
                'category_id'    => $this->input->post('category_id'),
                'address'        => $this->input->post('address'),
                'city'           => $this->input->post('city'),
                'district'       => $this->input->post('district'),
                'contact_person' => $this->input->post('contact_person'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'status'         => $this->input->post('status'),
                // Ini penting untuk WebGIS nanti:
                'latitude'       => $this->input->post('latitude'), 
                'longitude'      => $this->input->post('longitude'),
            ];

            if ($id) {
                $this->Customer_model->update($id, $post_data);
                $this->session->set_flashdata('message', 'Data pelanggan diupdate');
            } else {
                $this->Customer_model->insert($post_data);
                $this->session->set_flashdata('message', 'Pelanggan baru ditambahkan');
            }
            redirect('marketing/customers');
        }
    }

    public function detail($id) {
        // 1. Ambil Data Pelanggan
        $this->db->select('c.*, bc.category_name');
        $this->db->from('customers c');
        $this->db->join('business_categories bc', 'bc.category_id = c.category_id', 'left');
        $this->db->where('c.customer_id', $id);
        $customer = $this->db->get()->row();

        if (!$customer) show_404();

        // 2. Ambil Statistik Belanja (FIX ERROR DISINI)
        $this->db->select_sum('total_amount');
        $this->db->select_max('order_date', 'last_order');
        
        // PERBAIKAN: Gunakan select manual untuk count
        $this->db->select('COUNT(id) as total_trx'); 
        
        $this->db->where('customer_id', $id);
        $this->db->where('status !=', 'canceled');
        $stats = $this->db->get('sales_orders')->row();

        // 3. Ambil Riwayat Transaksi (10 Terakhir)
        $this->db->select('*');
        $this->db->from('sales_orders');
        $this->db->where('customer_id', $id);
        $this->db->order_by('order_date', 'DESC');
        $this->db->limit(10);
        $history = $this->db->get()->result();

        $data = [
            'title'    => 'Profil Pelanggan: ' . $customer->name,
            'row'      => $customer,
            'stats'    => $stats,
            'history'  => $history
        ];

        $this->template->load('marketing/customers/detail', $data);
    }
}