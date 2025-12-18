<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_model extends CI_Model {

    // 1. GENERATE NOMOR PO (Format: PO/YYYY/MM/001)
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

    // 2. SIMPAN HEADER PO (Untuk Controller Baru)
    public function create_purchase_header($data) {
        $this->db->insert('purchases', $data);
        return $this->db->insert_id();
    }

    // 3. LOGIKA TERIMA BARANG (HPP AVERAGE) - CRITICAL UPDATE
    public function receive_items($po_id) {
        $po = $this->db->get_where('purchases', ['purchase_id' => $po_id])->row();
        
        if (!$po || $po->status != 'ordered') {
            return ['status' => false, 'msg' => 'Gagal: PO tidak valid atau sudah diterima.'];
        }

        $items = $this->db->get_where('purchase_items', ['purchase_id' => $po_id])->result();
        
        $this->db->trans_start();

        foreach ($items as $item) {
            // A. Ambil Data Master Produk (HPP Lama)
            $master_prod = $this->db->get_where('salt_products', ['product_id' => $item->product_id])->row();
            
            // B. Ambil Stok Fisik Gudang
            $stok_query = $this->db->select_sum('quantity')->get_where('warehouse_stock', ['product_id' => $item->product_id])->row();
            $old_qty    = ($stok_query && $stok_query->quantity) ? $stok_query->quantity : 0;
            
            // Pastikan qty tidak minus (safety check)
            if ($old_qty < 0) $old_qty = 0;

            // HPP Lama
            $old_cost = ($master_prod && $master_prod->base_cost) ? $master_prod->base_cost : 0;
            
            // Data Baru (Dari PO)
            $new_qty  = $item->qty;
            $new_cost = $item->cost;

            // C. RUMUS WEIGHTED AVERAGE (Rata-rata Tertimbang)
            $total_val_old = $old_qty * $old_cost;
            $total_val_new = $new_qty * $new_cost;
            $total_qty_all = $old_qty + $new_qty;

            // Jika total qty > 0, hitung rata-rata. Jika 0, pakai harga baru.
            if ($total_qty_all > 0) {
                $final_hpp = ($total_val_old + $total_val_new) / $total_qty_all;
            } else {
                $final_hpp = $new_cost;
            }

            // D. Update Master Produk
            $this->db->where('product_id', $item->product_id);
            $this->db->update('salt_products', [
                'base_cost'           => ceil($final_hpp), // Bulatkan ke atas
                // 'last_purchase_price' => $new_cost,
                'last_purchase_price' => (float) $new_cost,
                'stock'               => $total_qty_all // Opsional: Sinkronkan stok master
            ]);

            // E. Update Stok Gudang
            $cek_stok = $this->db->get_where('warehouse_stock', ['product_id' => $item->product_id])->num_rows();
            if ($cek_stok > 0) {
                $this->db->set('quantity', 'quantity + ' . $new_qty, FALSE);
                $this->db->set('updated_at', date('Y-m-d H:i:s'));
                $this->db->where('product_id', $item->product_id);
                $this->db->update('warehouse_stock');
            } else {
                $this->db->insert('warehouse_stock', [
                    'product_id' => $item->product_id,
                    'quantity'   => $new_qty,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            // F. Log Mutasi
            $this->db->insert('stock_logs', [
                'product_id'  => $item->product_id,
                'user_id'     => $this->session->userdata('user_id'),
                'change_qty'  => $new_qty,
                'type'        => 'in',
                'description' => 'PO Received: ' . $po->purchase_no,
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }

        // G. Update Status PO
        $this->db->where('purchase_id', $po_id);
        $this->db->update('purchases', [
            'status'        => 'received', 
            'received_date' => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['status' => false, 'msg' => 'Database Error saat Receive.'];
        }
        return ['status' => true, 'msg' => 'Stok diterima dan HPP diperbarui.'];
    }

    // 4. AMBIL DETAIL PO LENGKAP
    public function get_detail($id) {
        $this->db->select('p.*, s.supplier_name, s.address, s.phone, s.contact_person, u.full_name as creator_name');
        $this->db->from('purchases p');
        $this->db->join('suppliers s', 's.supplier_id = p.supplier_id');
        $this->db->join('users u', 'u.user_id = p.created_by', 'left');
        $this->db->where('p.purchase_id', $id);
        return $this->db->get()->row();
    }

    // 5. AMBIL ITEM BARANG (Join Master Produk)
    public function get_items($purchase_id) {
        $this->db->select('pi.*, pr.name as product_name, pr.unit');
        $this->db->from('purchase_items pi');
        $this->db->join('salt_products pr', 'pr.product_id = pi.product_id');
        $this->db->where('pi.purchase_id', $purchase_id);
        return $this->db->get()->result();
    }

    // 6. UPDATE PEMBAYARAN
    public function add_payment($data) {
        $this->db->trans_start();
        
        // A. Insert Payment
        $this->db->insert('purchase_payments', $data);

        // B. Hitung Total Paid
        $this->db->select_sum('amount');
        $this->db->where('purchase_id', $data['purchase_id']);
        $paid = $this->db->get('purchase_payments')->row()->amount;
        
        // C. Cek Total Tagihan
        $po = $this->db->get_where('purchases', ['purchase_id' => $data['purchase_id']])->row();
        
        // D. Tentukan Status
        $status = 'unpaid';
        if ($paid >= $po->total_cost) {
            $status = 'paid';
        } elseif ($paid > 0) {
            $status = 'partial';
        }

        // E. Update PO Header
        $this->db->where('purchase_id', $data['purchase_id']);
        $this->db->update('purchases', [
            'total_paid'     => $paid,
            'payment_status' => $status
        ]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}