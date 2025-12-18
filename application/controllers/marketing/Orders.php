<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }

        $role = $this->session->userdata('role');
        // Hanya Admin dan Owner yang boleh masuk
        if ($role != 'admin' && $role != 'owner') {
            show_error('Anda tidak memiliki hak akses untuk halaman ini.', 403, 'Forbidden');
        }
        
        
        $this->load->model('Order_model');
        $this->load->model('Customer_model');
        $this->load->model('Product_model');
        $this->load->model('Inventory_model');
        
        $this->load->library('template');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Daftar Pesanan (Sales Order)';
        $data['orders'] = $this->Order_model->get_all();
        $this->template->load('marketing/orders/index', $data);
    }

    public function create() {
        $this->_process_form();
    }

    public function edit($id) {
        $data['row'] = $this->Order_model->get_by_id($id);
        if(!$data['row']) show_404();
        $this->_process_form($data['row']);
    }
    
    // Fitur Delete
    public function delete($id) {
        $this->Order_model->delete($id);
        $this->session->set_flashdata('message', 'Order berhasil dihapus');
        redirect('marketing/orders');
    }

    private function _process_form($row = null) {
        $data['title'] = $row ? 'Edit Order' : 'Buat Order Baru';
        $data['row']   = $row;
        
        // Ambil data Customer & Produk untuk Dropdown
        $data['customers'] = $this->Customer_model->get_all();
        $data['products']  = $this->Product_model->get_all();

        $id = $row ? $row->order_id : null;

        $this->form_validation->set_rules('customer_id', 'Pelanggan', 'required');
        $this->form_validation->set_rules('product_id', 'Produk', 'required');
        $this->form_validation->set_rules('quantity', 'Qty', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->template->load('marketing/orders/form', $data);
        } else {
            $post_data = [
                'customer_id' => $this->input->post('customer_id'),
                'product_id'  => $this->input->post('product_id'),
                'quantity'    => $this->input->post('quantity'),
                'price'       => $this->input->post('price'),
                'total'       => $this->input->post('total'),
                'status'      => $this->input->post('status'),
            ];

            // LOGIKA AUTOMASI STOK (LENGKAP: POTONG & KEMBALIKAN)
            $stock_reducing_statuses = ['delivering', 'done']; // Status di mana barang dianggap "hilang" dari gudang

            if ($id) {
                // --- CASE EDIT ORDER ---
                
                // 1. Ambil status LAMA & BARU
                $old_order_data = $this->Order_model->get_by_id($id);
                $old_status = $old_order_data->status;
                $new_status = $post_data['status'];

                // 2. Update Data Order di Database
                $this->Order_model->update($id, $post_data);

                // 3. LOGIKA DUA ARAH
                
                // KASUS A: Barang Keluar (Belum Dikirim -> Dikirim/Selesai)
                // Syarat: Dulu 'aman', Sekarang 'keluar'
                if (!in_array($old_status, $stock_reducing_statuses) && in_array($new_status, $stock_reducing_statuses)) {
                    
                    $fresh_order = $this->db->query("SELECT o.*, c.name as customer_name FROM customer_orders o JOIN customers c ON c.customer_id = o.customer_id WHERE o.order_id = ?", array($id))->row();
                    $this->Inventory_model->deduct_stock_from_order($fresh_order);
                    $this->session->set_flashdata('message', 'Order Dikirim: Stok Gudang otomatis BERKURANG.');

                } 
                // KASUS B: Barang Balik / Batal (Dikirim/Selesai -> Batal/Request)
                // Syarat: Dulu 'keluar', Sekarang 'aman/batal'
                else if (in_array($old_status, $stock_reducing_statuses) && !in_array($new_status, $stock_reducing_statuses)) {
                    
                    $fresh_order = $this->db->query("SELECT o.*, c.name as customer_name FROM customer_orders o JOIN customers c ON c.customer_id = o.customer_id WHERE o.order_id = ?", array($id))->row();
                    $this->Inventory_model->restore_stock_from_order($fresh_order);
                    $this->session->set_flashdata('message', 'Order Dibatalkan: Stok Gudang otomatis DIKEMBALIKAN.');

                } 
                else {
                    // Kasus C: Perubahan status biasa (misal Request -> Preparing) -> Tidak pengaruh stok
                    $this->session->set_flashdata('message', 'Status order berhasil diupdate.');
                }

            } else {
                // ... (Logika create order baru tetap sama, hanya menangani potong stok) ...
                
                // (Paste kode Create Order yang sebelumnya di sini)
                $post_data['created_by'] = $this->session->userdata('user_id');
                $post_data['order_date'] = date('Y-m-d H:i:s');
                $this->Order_model->insert($post_data);
                $new_order_id = $this->db->insert_id();

                if (in_array($post_data['status'], $stock_reducing_statuses)) {
                    $fresh_order = $this->db->query("SELECT o.*, c.name as customer_name FROM customer_orders o JOIN customers c ON c.customer_id = o.customer_id WHERE o.order_id = ?", array($new_order_id))->row();
                    $this->Inventory_model->deduct_stock_from_order($fresh_order);
                    $this->session->set_flashdata('message', 'Order dibuat & Stok otomatis dipotong.');
                } else {
                    $this->session->set_flashdata('message', 'Order baru berhasil dibuat');
                }
            }
            redirect('marketing/orders');
        }
    }
}