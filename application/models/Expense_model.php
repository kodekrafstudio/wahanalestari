<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model {

    // Ambil semua pengeluaran dengan filter tanggal
    public function get_all($start_date, $end_date) {
        $this->db->select('e.*, u.full_name as user_name');
        $this->db->from('operational_expenses e');
        $this->db->join('users u', 'u.user_id = e.created_by', 'left');
        
        $this->db->where('e.expense_date >=', $start_date);
        $this->db->where('e.expense_date <=', $end_date);
        
        $this->db->order_by('e.expense_date', 'DESC');
        return $this->db->get()->result();
    }

    // Simpan data
    public function insert($data) {
        return $this->db->insert('operational_expenses', $data);
    }

    // Hapus data
    public function delete($id) {
        $this->db->where('expense_id', $id);
        return $this->db->delete('operational_expenses');
    }

    // Hitung Total (Untuk Widget Dashboard nanti)
    public function get_total_expenses($start_date, $end_date) {
        $this->db->select_sum('amount');
        $this->db->where('expense_date >=', $start_date);
        $this->db->where('expense_date <=', $end_date);
        $query = $this->db->get('operational_expenses')->row();
        return $query->amount ?? 0;
    }
}