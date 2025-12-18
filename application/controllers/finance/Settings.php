<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        $this->load->library('template');
    }

    public function index() {
        $data['title'] = 'Pengaturan Keuangan';
        
        // Ambil Data Modal
        $data['capitals'] = $this->db->order_by('date', 'DESC')->get('company_capital')->result();
        
        // Ambil Data Kategori Biaya
        $data['categories'] = $this->db->get('expense_categories')->result();

        $this->template->load('finance/settings/index', $data);
    }

    // --- 1. KELOLA MODAL (CAPITAL) ---
    public function add_capital() {
        $data = [
            'date'        => $this->input->post('date'),
            'description' => $this->input->post('description'),
            'amount'      => str_replace(['Rp','.',' '], '', $this->input->post('amount')),
            'type'        => $this->input->post('type') // capital_in atau prive
        ];
        
        // Jika Prive (Tarik Modal), nilai harus minus di laporan nanti, 
        // tapi di database simpan positif saja, nanti logic report yang kurangi.
        // Atau simpan minus langsung:
        if($data['type'] == 'prive') {
            $data['amount'] = -1 * abs($data['amount']);
        }

        $this->db->insert('company_capital', $data);
        $this->session->set_flashdata('message', 'Transaksi modal berhasil disimpan.');
        redirect('finance/settings');
    }

    public function delete_capital($id) {
        $this->db->where('id', $id)->delete('company_capital');
        redirect('finance/settings');
    }

    // --- 2. KELOLA KATEGORI BIAYA ---
    public function add_category() {
        $data = [
            'name' => $this->input->post('name'),
            'type' => $this->input->post('type')
        ];
        $this->db->insert('expense_categories', $data);
        $this->session->set_flashdata('message', 'Kategori baru berhasil ditambah.');
        redirect('finance/settings');
    }

    public function delete_category($id) {
        $this->db->where('category_id', $id)->delete('expense_categories');
        redirect('finance/settings');
    }
}