<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        
        $this->load->model('Expense_model');
        $this->load->library('template');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Biaya Operasional';
        $start = $this->input->get('start') ? $this->input->get('start') : date('Y-m-01');
        $end   = $this->input->get('end') ? $this->input->get('end') : date('Y-m-d');

        $data['expenses'] = $this->Expense_model->get_all($start, $end);
        $data['total']    = $this->Expense_model->get_total_expenses($start, $end);
        $data['filter']   = ['start' => $start, 'end' => $end];

        $this->template->load('finance/expenses/index', $data);
    }

    // UPDATE FUNGSI INI
    public function create() {
        if ($this->input->post()) {
            $data = [
                'expense_date' => $this->input->post('expense_date'),
                
                // Simpan ID Kategori & Nama Kategori (untuk backup)
                'category_id'  => $this->input->post('category_id'),
                'category'     => $this->input->post('category_name'), 
                
                'description'  => $this->input->post('description'),
                'amount'       => str_replace(['Rp','.',' '], '', $this->input->post('amount')),
                'created_by'   => $this->session->userdata('user_id')
            ];

            if ($this->Expense_model->insert($data)) {
                $this->session->set_flashdata('message', 'Pengeluaran berhasil dicatat.');
                redirect('finance/expenses');
            }
        }

        $data['title'] = 'Catat Pengeluaran Baru';
        
        // Ambil data kategori dari database
        // Pastikan tabel expense_categories sudah dibuat di SQL sebelumnya
        $data['categories'] = $this->db->get('expense_categories')->result();
        
        $this->template->load('finance/expenses/create', $data);
    }

    public function delete($id) {
        $this->Expense_model->delete($id);
        $this->session->set_flashdata('message', 'Data dihapus.');
        redirect('finance/expenses');
    }
}