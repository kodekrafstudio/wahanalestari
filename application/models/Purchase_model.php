<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_model extends CI_Model {

    // 1. GENERATE NOMOR PO (PO/2025/12/001)
    public function generate_po_number() {
        $prefix = "PO/" . date('Y/m') . "/";
        $this->db->select('purchase_no');
        $this->db->like('purchase_no', $prefix, 'after');
        $this->db->order_by('purchase_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('purchases');

        if ($query->num_rows() > 0) {
            $last = $query->row()->purchase_no;
            $number = (int) substr($last, -3); // Ambil 3 digit terakhir
            $new = $number + 1;
        } else {
            $new = 1;
        }
        return $prefix . str_pad($new, 3, "0", STR_PAD_LEFT);
    }

    // 2. SIMPAN PO BARU
    public function create_purchase($header, $items) {
        $this->db->trans_start();

        // A. Simpan Header PO
        $this->db->insert('purchases', $header);
        
        // PENTING: Tangkap ID segera setelah insert header
        $purchase_id = $this->db->insert_id(); 

        // B. Simpan Detail Barang
        $data_items = [];
        foreach($items as $item) {
            $data_items[] = [
                'purchase_id' => $purchase_id, // Gunakan ID yang ditangkap tadi
                'product_id'  => $item['product_id'],
                'qty'         => $item['qty'],
                'cost'        => $item['cost'],
                'subtotal'    => $item['subtotal']
            ];
        }
        
        if(!empty($data_items)) {
            $this->db->insert_batch('purchase_items', $data_items);
        }

        $this->db->trans_complete();

        // LOGIKA RETURN:
        // Jika transaksi sukses, kembalikan ID-nya (Angka).
        // Jika gagal, kembalikan FALSE.
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return $purchase_id; 
        }
    }

    // 3. AMBIL DETAIL PO LENGKAP
    public function get_detail($id) {
        $this->db->select('p.*, s.supplier_name, s.address, s.phone, s.contact_person, u.full_name as creator_name');
        $this->db->from('purchases p');
        $this->db->join('suppliers s', 's.supplier_id = p.supplier_id');
        $this->db->join('users u', 'u.user_id = p.created_by', 'left');
        $this->db->where('p.purchase_id', $id);
        return $this->db->get()->row();
    }

    // 4. AMBIL ITEM BARANG (Join ke Master Produk untuk ambil Satuan/Nama)
    public function get_items($purchase_id) {
        $this->db->select('pi.*, pr.name as product_name, pr.unit');
        $this->db->from('purchase_items pi');
        $this->db->join('salt_products pr', 'pr.product_id = pi.product_id');
        $this->db->where('pi.purchase_id', $purchase_id);
        return $this->db->get()->result();
    }

    // 5. UPDATE PEMBAYARAN & STATUS LUNAS
    public function add_payment($data) {
        $this->db->trans_start();
        
        // A. Insert ke tabel payment
        $this->db->insert('purchase_payments', $data);

        // B. Hitung Total yang sudah dibayar
        $this->db->select_sum('amount');
        $this->db->where('purchase_id', $data['purchase_id']);
        $paid = $this->db->get('purchase_payments')->row()->amount;
        
        // C. Ambil Total Tagihan
        $po = $this->db->get_where('purchases', ['purchase_id' => $data['purchase_id']])->row();
        
        // D. Tentukan Status
        $status = 'unpaid';
        if ($paid >= $po->total_cost) {
            $status = 'paid';
        } elseif ($paid > 0) {
            $status = 'partial'; // Pastikan kolom ENUM sudah diupdate
        }

        // E. Update Tabel Utama (Total Paid & Status)
        $this->db->where('purchase_id', $data['purchase_id']);
        $this->db->update('purchases', [
            'total_paid' => $paid,
            'payment_status' => $status
        ]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}