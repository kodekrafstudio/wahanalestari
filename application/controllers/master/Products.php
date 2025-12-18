<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }

        $role = $this->session->userdata('role');
        // Hanya Admin dan Owner yang boleh masuk
        if ($role != 'admin' && $role != 'owner') {
            show_error('Anda tidak memiliki hak akses untuk halaman ini.', 403, 'Forbidden');
        }
        
        $this->load->model('Product_model');
        $this->load->library('template');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Master Produk Garam';
        $data['products'] = $this->Product_model->get_all();
        $this->template->load('master/products/index', $data);
    }

    public function add() {
        $this->_process_form();
    }

    public function edit($id) {
        $data['row'] = $this->Product_model->get_by_id($id);
        if(!$data['row']) show_404();
        $this->_process_form($data['row']);
    }

    public function delete($id) {
        $this->Product_model->delete($id);
        $this->session->set_flashdata('message', 'Produk berhasil dihapus');
        redirect('master/products');
    }

    private function _process_form($row = null) {
        $data['title'] = $row ? 'Edit Produk' : 'Tambah Produk';
        $data['row']   = $row;
        $id = $row ? $row->product_id : null;

        $this->form_validation->set_rules('name', 'Nama Produk', 'required');
        $this->form_validation->set_rules('type', 'Tipe', 'required');
        $this->form_validation->set_rules('base_cost', 'HPP', 'required|numeric');
        $this->form_validation->set_rules('sell_price', 'Harga Jual', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->template->load('master/products/form', $data);
        } else {
            $post_data = [
                'name'       => $this->input->post('name'),
                'type'       => $this->input->post('type'),
                'grade'      => $this->input->post('grade'),
                'unit'       => $this->input->post('unit'),
                'base_cost'  => $this->input->post('base_cost'),
                'sell_price' => $this->input->post('sell_price')
            ];

            if ($id) {
                $this->Product_model->update($id, $post_data);
            } else {
                $this->Product_model->insert($post_data);
            }
            $this->session->set_flashdata('message', 'Data produk berhasil disimpan');
            redirect('master/products');
        }
    }
}