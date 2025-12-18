<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        $this->load->model('Inventory_model');
        $this->load->library('template');
    }

    public function index() {
        $data['title'] = 'Manajemen Stok & Gudang';
        
        // 1. Ambil Data
        $data['stocks']      = $this->Inventory_model->get_all_stock();
        $data['latest_logs'] = $this->Inventory_model->get_logs(8); // 8 Log terakhir

        // 2. Hitung Summary Widget
        $total_fisik = 0;
        $total_aset  = 0;
        $low_stock   = 0;

        foreach($data['stocks'] as $s) {
            $qty = $s->quantity;
            $hpp = $s->base_cost; // Harga Modal

            $total_fisik += $qty;
            $total_aset  += ($qty * $hpp); // Valuasi = Stok x HPP

            if($qty < 100) { // Ambang batas low stock
                $low_stock++;
            }
        }

        $data['summary'] = [
            'total_items' => $total_fisik,
            'asset_value' => $total_aset,
            'low_stock'   => $low_stock
        ];

        $this->template->load('inventory/stock/index', $data);
    }

    // 3. HALAMAN FULL HISTORY (LOGS)
    public function history() {
        $data['title'] = 'Riwayat Aktivitas Stok';
        
        // Ambil 500 log terakhir (atau bisa dibuat pagination nanti)
        $data['logs'] = $this->Inventory_model->get_logs(500); 
        
        $this->template->load('inventory/stock/history', $data);
    }

    // Proses Adjustment (Sama seperti sebelumnya)
    public function adjust() {
        $product_id = $this->input->post('product_id');
        $real_qty   = $this->input->post('real_qty');
        $note       = $this->input->post('note');
        $user_id    = $this->session->userdata('user_id');

        if ($real_qty === '') {
            $this->session->set_flashdata('error', 'Jumlah tidak valid.');
            redirect('inventory/stock');
        }

        if ($this->Inventory_model->process_opname($product_id, $real_qty, $note, $user_id)) {
            $this->session->set_flashdata('message', 'Stock Opname Berhasil Disimpan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal update stok.');
        }
        redirect('inventory/stock');
    }
}