<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_model extends CI_Model {

    private $table = 'customers';

    // Ambil semua customer, JOIN dengan kategori agar nama kategori muncul
    public function get_all() {
        $this->db->select('customers.*, business_categories.category_name');
        $this->db->from($this->table);
        $this->db->join('business_categories', 'business_categories.category_id = customers.category_id', 'left');
        $this->db->order_by('customers.name', 'ASC');
        return $this->db->get()->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['customer_id' => $id])->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    // Opsi: Jika ingin pakai Model (Best Practice)
    public function create_customer($data) {
        return $this->db->insert('customers', $data);
    }

    public function update($id, $data) {
        $this->db->where('customer_id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id) {
        $this->db->where('customer_id', $id);
        return $this->db->delete($this->table);
    }
}