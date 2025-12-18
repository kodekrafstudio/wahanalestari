<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Suppliers extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        // Kita gunakan Supplier_model yang sudah ada (perlu kita lengkapi sedikit)
        $this->load->model('Supplier_model');
        $this->load->library('template');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Master Supplier';
        $data['suppliers'] = $this->Supplier_model->get_all();
        $this->template->load('master/suppliers/index', $data);
    }

    public function add() {
        $this->_process_form();
    }

    public function edit($id) {
        $data['row'] = $this->db->get_where('suppliers', ['supplier_id' => $id])->row();
        if(!$data['row']) show_404();
        $this->_process_form($data['row']);
    }

    public function delete($id) {
        $this->db->where('supplier_id', $id);
        $this->db->delete('suppliers');
        $this->session->set_flashdata('message', 'Data supplier dihapus');
        redirect('master/suppliers');
    }

    private function _process_form($row = null) {
        $data['title'] = $row ? 'Edit Supplier' : 'Tambah Supplier';
        $data['row']   = $row;
        
        $this->form_validation->set_rules('supplier_name', 'Nama Supplier', 'required');
        $this->form_validation->set_rules('phone', 'No HP', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->template->load('master/suppliers/form', $data);
        } else {
            $post = [
                'supplier_name' => $this->input->post('supplier_name'),
                'pic_name'      => $this->input->post('pic_name'),
                'phone'         => $this->input->post('phone'),
                'address'       => $this->input->post('address'),
            ];

            if ($row) {
                $this->db->where('supplier_id', $row->supplier_id)->update('suppliers', $post);
            } else {
                $this->db->insert('suppliers', $post);
            }
            $this->session->set_flashdata('message', 'Data supplier disimpan');
            redirect('master/suppliers');
        }
    }
}