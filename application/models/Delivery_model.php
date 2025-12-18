<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_model extends CI_Model {

    // Ambil semua rute (Header)
    public function get_all_routes() {
        $this->db->select('dr.*, u.full_name as driver_name');
        $this->db->from('delivery_routes dr');
        $this->db->join('users u', 'u.user_id = dr.driver_id', 'left');
        $this->db->order_by('dr.route_date', 'DESC');
        return $this->db->get()->result();
    }

    // Ambil detail satu rute beserta titik-titik tujuannya
    public function get_route_detail($route_id) {
        // Ambil Header
        $this->db->select('dr.*, u.full_name as driver_name');
        $this->db->from('delivery_routes dr');
        $this->db->join('users u', 'u.user_id = dr.driver_id', 'left');
        $this->db->where('dr.route_id', $route_id);
        $route = $this->db->get()->row();

        if ($route) {
            // Ambil Detail Points (Toko yg dikunjungi)
            $this->db->select('drp.*, c.name as customer_name, c.address, c.city, c.phone');
            $this->db->from('delivery_route_points drp');
            $this->db->join('customers c', 'c.customer_id = drp.customer_id');
            $this->db->where('drp.route_id', $route_id);
            $this->db->order_by('drp.sequence_number', 'ASC');
            $route->points = $this->db->get()->result();
        }

        return $route;
    }

    public function create_route($data) {
        $this->db->insert('delivery_routes', $data);
        return $this->db->insert_id();
    }

    public function add_point($data) {
        return $this->db->insert('delivery_route_points', $data);
    }

    public function delete_point($point_id) {
        $this->db->where('point_id', $point_id);
        return $this->db->delete('delivery_route_points');
    }
    
    // Update status rute (Planned -> Ongoing -> Completed)
    public function update_route_status($route_id, $status) {
        $this->db->where('route_id', $route_id);
        return $this->db->update('delivery_routes', ['status' => $status]);
    }
}