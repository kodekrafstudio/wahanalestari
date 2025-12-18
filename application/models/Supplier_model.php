<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends CI_Model {
    public function get_all() {
        return $this->db->get('suppliers')->result();
    }
}