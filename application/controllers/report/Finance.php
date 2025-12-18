<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        
        $this->load->model('Report_model');
        $this->load->library('template');
    }

    public function index() {
        $data['title'] = 'Laporan Keuangan Profesional';
        
        $month = $this->input->get('month') ? $this->input->get('month') : date('m');
        $year  = $this->input->get('year')  ? $this->input->get('year')  : date('Y');

        // Menggunakan method baru dari Report_model yang sudah diupdate
        $data['pl'] = $this->Report_model->get_profit_loss($month, $year);
        $data['bs'] = $this->Report_model->get_balance_sheet();
        
        $data['filter'] = ['month' => $month, 'year' => $year];

        // Pastikan path view sesuai dengan nama folder Anda (report atau reports)
        $this->template->load('reports/finance/index', $data);
    }
    
    // FUNGSI BARU: CETAK FORMAT BANK
    public function print_bank_standard() {
        $month = $this->input->get('month') ? $this->input->get('month') : date('m');
        $year  = $this->input->get('year')  ? $this->input->get('year')  : date('Y');
        
        $data['pl'] = $this->Report_model->get_profit_loss($month, $year);
        $data['bs'] = $this->Report_model->get_balance_sheet();
        $data['filter'] = ['month' => $month, 'year' => $year];

        // Load view khusus print (tanpa template dashboard)
        $this->load->view('reports/finance/print_bank_standard', $data);
    }
}