<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    private $table = 'users';

    // 1. Untuk Login (Auth)
    public function get_user_by_email($email) {
        return $this->db->get_where($this->table, ['email' => $email])->row_array();
    }

    // 2. Ambil Semua User (Kecuali user yang sedang login agar tidak hapus diri sendiri)
    public function get_all($exclude_id = null) {
        if($exclude_id) {
            $this->db->where('user_id !=', $exclude_id);
        }
        $this->db->order_by('full_name', 'ASC');
        return $this->db->get($this->table)->result();
    }

    // 3. Ambil 1 User by ID
    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['user_id' => $id])->row();
    }

    // 4. Tambah User Baru
    public function insert($data) {
        // Hash password sebelum simpan
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']); // Hapus plain text password dari array
        
        return $this->db->insert($this->table, $data);
    }

    // 5. Update User
    public function update($id, $data) {
        // Cek apakah ada request ganti password?
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        unset($data['password']); // Hapus plain text

        $this->db->where('user_id', $id);
        return $this->db->update($this->table, $data);
    }

    // GANTI fungsi delete() yang lama dengan ini, atau tambahkan saja
    public function update_status($id, $status) {
        $this->db->where('user_id', $id);
        return $this->db->update($this->table, ['is_active' => $status]);
    }
}