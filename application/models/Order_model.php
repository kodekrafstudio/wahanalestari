<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model {

    private $table = 'customer_orders';

    public function get_all() {
        $this->db->select('o.*, c.name as customer_name, p.name as product_name, p.unit, u.full_name as sales_name');
        $this->db->from('customer_orders o');
        $this->db->join('customers c', 'c.customer_id = o.customer_id');
        $this->db->join('salt_products p', 'p.product_id = o.product_id');
        $this->db->join('users u', 'u.user_id = o.created_by');
        $this->db->order_by('o.order_date', 'DESC');
        return $this->db->get()->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['order_id' => $id])->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        $this->db->where('order_id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id) {
        $this->db->where('order_id', $id);
        return $this->db->delete($this->table);
    }
}