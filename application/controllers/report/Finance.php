<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        
        $this->load->model('Report_model');
        $this->load->library('template');
    }

    // HALAMAN UTAMA LAPORAN
    public function index() {
        $data['title'] = 'Laporan Keuangan Profesional';
        
        $month = $this->input->get('month') ? $this->input->get('month') : date('m');
        $year  = $this->input->get('year')  ? $this->input->get('year')  : date('Y');

        // Laba Rugi (Kinerja Periode Ini)
        $data['pl'] = $this->Report_model->get_profit_loss($month, $year);
        
        // Neraca (Posisi Keuangan SAAT INI / Akumulasi)
        $data['bs'] = $this->Report_model->get_balance_sheet(); 
        
        // Ratio Keuangan untuk Investor (PENTING!)
        // Net Profit Margin = (Laba Bersih / Omzet) * 100
        $omzet = $data['pl']['revenue'];
        $data['net_margin'] = ($omzet > 0) ? ($data['pl']['net_profit'] / $omzet) * 100 : 0;

        $data['filter'] = ['month' => $month, 'year' => $year];

        $this->template->load('report/finance/index', $data);
    }
    
    // CETAK LAPORAN (Print View)
    public function print_report() {
        $month = $this->input->get('month');
        $year  = $this->input->get('year');
        
        $data['pl']           = $this->Report_model->get_profit_loss($month, $year);
        $data['top_products'] = $this->Report_model->get_top_products($month, $year);
        $data['expenses']     = $this->Report_model->get_expenses_detail($month, $year);
        $data['period']       = date("F Y", mktime(0, 0, 0, $month, 10));

        $this->load->view('report/finance/print', $data);
    }
}