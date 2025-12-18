<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_model extends CI_Model {

    // Ambil semua rute (untuk halaman index)
    public function get_all_routes() {
        $this->db->select('dr.*, u.full_name as driver_name, COUNT(drp.point_id) as total_points');
        $this->db->from('delivery_routes dr');
        $this->db->join('users u', 'u.user_id = dr.driver_id', 'left');
        $this->db->join('delivery_route_points drp', 'drp.route_id = dr.route_id', 'left');
        $this->db->group_by('dr.route_id');
        $this->db->order_by('dr.route_date', 'DESC');
        return $this->db->get()->result();
    }

    // Ambil detail rute beserta titik tujuannya
    public function get_route_detail($route_id) {
        // Data Header Rute
        $route = $this->db->select('dr.*, u.full_name as driver_name')
                          ->from('delivery_routes dr')
                          ->join('users u', 'u.user_id = dr.driver_id', 'left')
                          ->where('dr.route_id', $route_id)
                          ->get()->row();

        if (!$route) return null;

        // Data Titik Tujuan (Points) - UPDATE JOIN SALES_ORDERS
        $this->db->select('drp.*, c.name as customer_name, c.address, c.latitude, c.longitude, so.invoice_no, so.grand_total');
        $this->db->from('delivery_route_points drp');
        $this->db->join('customers c', 'c.customer_id = drp.customer_id', 'left');
        
        // JOIN BARU: Hubungkan dengan Sales Order agar muncul No Invoice
        $this->db->join('sales_orders so', 'so.id = drp.sales_order_id', 'left');
        
        $this->db->where('drp.route_id', $route_id);
        $this->db->order_by('drp.sequence_number', 'ASC');
        $route->points = $this->db->get()->result();

        return $route;
    }

    // FUNGSI BARU: Ambil Order yang siap dikirim (Belum masuk rute manapun)
    public function get_pending_orders() {
        $this->db->select('so.*, c.name as customer_name, c.address');
        $this->db->from('sales_orders so');
        $this->db->join('customers c', 'c.customer_id = so.customer_id');
        
        // Hanya ambil status Request/Preparing
        // DAN pastikan order ini belum ada di tabel delivery_route_points (biar gak dobel kirim)
        $this->db->where_in('so.status', ['request', 'preparing']);
        $this->db->where('so.id NOT IN (SELECT sales_order_id FROM delivery_route_points WHERE sales_order_id IS NOT NULL)', NULL, FALSE);
        
        $this->db->order_by('so.order_date', 'ASC');
        return $this->db->get()->result();
    }

    // Fungsi dasar CRUD
    public function insert_route($data) {
        $this->db->insert('delivery_routes', $data);
        return $this->db->insert_id();
    }

    public function insert_point($data) {
        return $this->db->insert('delivery_route_points', $data);
    }
    
    public function delete_point($point_id) {
        $this->db->where('point_id', $point_id);
        return $this->db->delete('delivery_route_points');
    }
}