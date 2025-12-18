<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Map_data extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // PERBAIKAN 1: Respon JSON jika tidak login (Standard API)
        if (!$this->session->userdata('user_id')) { 
            header('Content-Type: application/json');
            http_response_code(401); // Kode Unauthorized
            echo json_encode(['status' => false, 'message' => 'Session Expired / No Access']);
            exit; 
        }
    }

    public function get_all_customers() {
        // Query ini sudah bagus (menggunakan COALESCE untuk handle null)
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
        
        // Filter Sales Order yang valid (Paid/Done/Delivering)
        // Pastikan ejaan status di database sesuai (huruf kecil/besar)
        $this->db->join('sales_orders so', 'so.customer_id = c.customer_id AND so.status IN ("done", "delivering", "paid")', 'left');
        
        // Hanya ambil customer yang punya koordinat valid
        $this->db->where('c.latitude !=', '');
        $this->db->where('c.latitude !=', '0');
        $this->db->where('c.latitude IS NOT NULL'); // Tambahan safety
        
        $this->db->group_by('c.customer_id'); 
        
        $data = $this->db->get()->result();

        // Tambahkan header JSON
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function get_customer_history($customer_id) {
        // PERBAIKAN 2: Casting ke Integer untuk keamanan (Security)
        $customer_id = (int) $customer_id;

        $this->db->select('invoice_no, order_date, total_amount, status');
        $this->db->from('sales_orders');
        $this->db->where('customer_id', $customer_id);
        $this->db->order_by('order_date', 'DESC');
        $this->db->limit(5); // Naikkan sedikit jadi 5 agar lebih informatif
        $history = $this->db->get()->result();

        header('Content-Type: application/json');
        echo json_encode($history);
    }
}