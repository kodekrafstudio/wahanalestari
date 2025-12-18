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

        // Filter Tanggal (Default: Bulan Ini)
        $start = $this->input->get('start') ? $this->input->get('start') : date('Y-m-01');
        $end   = $this->input->get('end') ? $this->input->get('end') : date('Y-m-d');

        $data['expenses'] = $this->Expense_model->get_all($start, $end);
        $data['total']    = $this->Expense_model->get_total_expenses($start, $end);
        
        // Kirim filter balik ke view
        $data['filter'] = ['start' => $start, 'end' => $end];

        $this->template->load('finance/expenses/index', $data);
    }

    public function create() {
        if ($this->input->post()) {
            $data = [
                'expense_date' => $this->input->post('expense_date'),
                'category'     => $this->input->post('category'),
                'description'  => $this->input->post('description'),
                'amount'       => $this->input->post('amount'),
                'created_by'   => $this->session->userdata('user_id')
            ];

            if ($this->Expense_model->insert($data)) {
                $this->session->set_flashdata('message', 'Pengeluaran berhasil dicatat.');
                redirect('finance/expenses');
            }
        }

        $data['title'] = 'Catat Pengeluaran Baru';
        // Daftar Kategori (Bisa ditambah manual disini)
        $data['categories'] = [
            'Gaji & Upah', 
            'Listrik & Air', 
            'Transportasi/Bensin', 
            'Sewa Tempat', 
            'Perawatan Kendaraan', 
            'Konsumsi', 
            'Promosi/Iklan',
            'Lain-lain'
        ];
        
        $this->template->load('finance/expenses/create', $data);
    }

    public function delete($id) {
        $this->Expense_model->delete($id);
        $this->session->set_flashdata('message', 'Data dihapus.');
        redirect('finance/expenses');
    }
}