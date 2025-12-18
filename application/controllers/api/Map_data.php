<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Map_data extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Cek login
        if (!$this->session->userdata('user_id')) { exit('No Access'); }
    }

    public function get_all_customers() {
        // Query Agregat:
        // Ambil Data Customer + Total Belanja (Hanya yang status 'done' atau 'delivering')
        // Gunakan COALESCE agar jika null dianggap 0
        
        $this->db->select('
            c.customer_id, 
            c.name, 
            c.address, 
            c.latitude, 
            c.longitude, 
            c.status,
            bc.category_name,
            COALESCE(SUM(so.total_amount), 0) as total_sales
        ');
        $this->db->from('customers c');
        $this->db->join('business_categories bc', 'bc.category_id = c.category_id', 'left');
        
        // Join ke tabel order, tapi filter hanya yang valid (bukan canceled/request)
        // Kita hitung yang statusnya 'done' atau 'delivering' atau 'paid'
        $this->db->join('sales_orders so', 'so.customer_id = c.customer_id AND so.status IN ("done", "delivering")', 'left');
        
        // Filter: Wajib punya koordinat
        $this->db->where('c.latitude !=', '');
        $this->db->where('c.latitude !=', 0);
        
        $this->db->group_by('c.customer_id'); 
        
        $data = $this->db->get()->result();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function get_customer_history($customer_id) {
        $this->db->select('invoice_no, order_date, total_amount, status');
        $this->db->from('sales_orders');
        $this->db->where('customer_id', $customer_id);
        $this->db->order_by('order_date', 'DESC');
        $this->db->limit(3); // Ambil 3 transaksi terakhir saja agar popup tidak kepanjangan
        $history = $this->db->get()->result();

        header('Content-Type: application/json');
        echo json_encode($history);
    }
}