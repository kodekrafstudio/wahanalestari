<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Cek Login
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }

        $role = $this->session->userdata('role');
        // Hanya Admin dan Owner yang boleh masuk
        if ($role != 'admin' && $role != 'owner') {
            show_error('Anda tidak memiliki hak akses untuk halaman ini.', 403, 'Forbidden');
        }
        
        
        $this->load->model('Category_model');
        $this->load->library('template');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Master Kategori Bisnis';
        $data['categories'] = $this->Category_model->get_all();
        $this->template->load('master/categories/index', $data);
    }

    public function add() {
        $this->_process_form();
    }

    public function edit($id) {
        $data['row'] = $this->Category_model->get_by_id($id);
        if(!$data['row']) show_404();
        
        $this->_process_form($data['row']);
    }

    public function delete($id) {
        $this->Category_model->delete($id);
        $this->session->set_flashdata('message', 'Data berhasil dihapus');
        redirect('master/categories');
    }

    // Fungsi private untuk menangani Add dan Edit sekaligus
    private function _process_form($row = null) {
        $data['title'] = $row ? 'Edit Kategori' : 'Tambah Kategori';
        $data['row']   = $row;
        $id = $row ? $row->category_id : null;

        $this->form_validation->set_rules('category_name', 'Nama Kategori', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->template->load('master/categories/form', $data);
        } else {
            $post_data = [
                'category_name' => $this->input->post('category_name'),
                'description'   => $this->input->post('description')
            ];

            if ($id) {
                $this->Category_model->update($id, $post_data);
                $this->session->set_flashdata('message', 'Data berhasil diupdate');
            } else {
                $this->Category_model->insert($post_data);
                $this->session->set_flashdata('message', 'Data berhasil disimpan');
            }
            redirect('master/categories');
        }
    }
}