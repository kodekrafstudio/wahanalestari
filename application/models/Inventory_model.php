<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model {

    // 1. FUNGSI SAKTI: UPDATE STOK
    public function adjust_stock($product_id, $delta, $description, $user_id) {
        $this->db->trans_start();

        // Cek barang
        $cek = $this->db->get_where('warehouse_stock', ['product_id' => $product_id])->num_rows();
        
        if ($cek > 0) {
            $this->db->set('quantity', "quantity + ($delta)", FALSE);
            $this->db->set('updated_at', date('Y-m-d H:i:s'));
            $this->db->where('product_id', $product_id);
            $this->db->update('warehouse_stock');
        } else {
            $this->db->insert('warehouse_stock', [
                'product_id' => $product_id,
                'quantity'   => $delta,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Catat Log (Selalu simpan positif di change_qty)
        $type = ($delta > 0) ? 'in' : 'out'; 
        $this->db->insert('stock_logs', [
            'product_id'  => $product_id,
            'user_id'     => $user_id,
            'change_qty'  => abs($delta),
            'type'        => $type,
            'description' => $description,
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // 2. KHUSUS STOCK OPNAME
    public function process_opname($product_id, $real_qty, $note, $user_id) {
        $curr = $this->db->get_where('warehouse_stock', ['product_id'=>$product_id])->row();
        $sys_qty = $curr ? $curr->quantity : 0;
        $diff = $real_qty - $sys_qty; 

        if ($diff == 0) return true;

        $desc = "Opname: $note (Sys: $sys_qty -> Real: $real_qty)";
        return $this->adjust_stock($product_id, $diff, $desc, $user_id);
    }

    // 3. AMBIL DATA STOK + HARGA MODAL (Untuk Valuasi)
    public function get_all_stock() {
        $this->db->select('p.product_id, p.name, p.unit, p.type, p.grade, p.base_cost, 
                           COALESCE(ws.quantity, 0) as quantity, ws.updated_at');
        $this->db->from('salt_products p');
        $this->db->join('warehouse_stock ws', 'ws.product_id = p.product_id', 'left');
        $this->db->order_by('p.name', 'ASC');
        return $this->db->get()->result();
    }

    // 4. AMBIL LOG AKTIVITAS
    public function get_logs($limit = 10) {
        $this->db->select('l.*, p.name as product_name, u.full_name');
        $this->db->from('stock_logs l');
        $this->db->join('salt_products p', 'p.product_id = l.product_id');
        $this->db->join('users u', 'u.user_id = l.user_id', 'left');
        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
}