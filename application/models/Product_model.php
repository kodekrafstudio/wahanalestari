<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

    private $table = 'salt_products';

    public function get_all() {
        return $this->db->get($this->table)->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['product_id' => $id])->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        $this->db->where('product_id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id) {
        $this->db->where('product_id', $id);
        return $this->db->delete($this->table);
    }
}