<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        
        $this->load->model('Report_model');
        $this->load->model('Product_model'); // Untuk filter produk di stok
        $this->load->library('template');
    }

    // --- HALAMAN 1: LAPORAN PENJUALAN ---
    public function sales() {
        $data['title'] = 'Laporan Penjualan';
        
        // Ambil Filter (Default: Bulan Ini)
        $start = $this->input->get('start') ? $this->input->get('start') : date('Y-m-01');
        $end   = $this->input->get('end') ? $this->input->get('end') : date('Y-m-d');
        $status= $this->input->get('status') ? $this->input->get('status') : 'all';

        // Ambil Data
        $data['report'] = $this->Report_model->get_sales_report($start, $end, $status);
        
        // Kirim Parameter Filter balik ke View (agar input date tidak reset)
        $data['filter'] = ['start' => $start, 'end' => $end, 'status' => $status];

        // Cek apakah user minta Export Excel?
        if($this->input->get('export') == 'excel') {
            $this->_export_sales_excel($data['report'], $start, $end);
        } else {
            $this->template->load('reports/sales_view', $data);
        }
    }

    // --- HALAMAN 2: LAPORAN STOK ---
    public function stock() {
        $data['title'] = 'Laporan Mutasi Stok';
        
        $start = $this->input->get('start') ? $this->input->get('start') : date('Y-m-01');
        $end   = $this->input->get('end') ? $this->input->get('end') : date('Y-m-d');
        $pid   = $this->input->get('product_id') ? $this->input->get('product_id') : 'all';

        $data['report'] = $this->Report_model->get_stock_report($start, $end, $pid);
        $data['products'] = $this->Product_model->get_all(); // Untuk dropdown filter
        
        $data['filter'] = ['start' => $start, 'end' => $end, 'product_id' => $pid];

        if($this->input->get('export') == 'excel') {
            $this->_export_stock_excel($data['report'], $start, $end);
        } else {
            $this->template->load('reports/stock_view', $data);
        }
    }

    // --- FUNGSI INTERNAL: EXPORT EXCEL ---
    // Kita gunakan teknik header HTML table agar tidak perlu install library berat
    private function _export_sales_excel($data, $start, $end) {
        $filename = "Laporan_Penjualan_" . $start . "_sd_" . $end . ".xls";
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        
        // Load view khusus yang isinya hanya tabel HTML polos
        $view_data['report'] = $data;
        $view_data['period'] = "$start s/d $end";
        $this->load->view('reports/excel/sales_excel', $view_data);
    }

    private function _export_stock_excel($data, $start, $end) {
        $filename = "Laporan_Stok_" . $start . "_sd_" . $end . ".xls";
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        
        $view_data['report'] = $data;
        $view_data['period'] = "$start s/d $end";
        $this->load->view('reports/excel/stock_excel', $view_data);
    }

    // ... (method sales & stock sebelumnya) ...

    // --- HALAMAN 3: LABA RUGI ---
    public function profit_loss() {
        $data['title'] = 'Laporan Laba Rugi';
        
        // Filter Bulan & Tahun (Default: Hari ini)
        $month = $this->input->get('month') ? $this->input->get('month') : date('m');
        $year  = $this->input->get('year') ? $this->input->get('year') : date('Y');

        // Ambil Data dari Model
        $financial = $this->Report_model->get_profit_loss($month, $year);

        // Kalkulasi Akhir
        $gross_profit = $financial['revenue'] - $financial['hpp'];
        $net_profit   = $gross_profit - $financial['expenses'];

        // Packing Data
        $data['data'] = $financial;
        $data['calc'] = [
            'gross_profit' => $gross_profit,
            'net_profit'   => $net_profit
        ];
        $data['filter'] = ['month' => $month, 'year' => $year];

        $this->template->load('reports/profit_loss_view', $data);
    }

    // HALAMAN UTAMA LAPORAN
    public function neraca() {
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

        $this->template->load('reports/finance/index', $data);
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