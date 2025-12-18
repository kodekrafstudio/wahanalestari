<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // Load di sini sekali saja agar bisa dipakai semua fungsi
        $this->load->model('Inventory_model'); 
    }
    
    // 1. GENERATE NOMOR FAKTUR
    public function generate_invoice_no() {
        $month = date('m');
        $year  = date('Y');
        $prefix = "INV/$year/$month/";
        
        $this->db->select('invoice_no');
        $this->db->like('invoice_no', $prefix, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('sales_orders');

        if ($query->num_rows() > 0) {
            $last = $query->row()->invoice_no;
            $last_no = (int)substr($last, -3);
            $new_no = $last_no + 1;
        } else {
            $new_no = 1;
        }
        return $prefix . sprintf("%03d", $new_no);
    }

    // 2. SIMPAN TRANSAKSI
    public function create_order($header, $items) {
        $this->db->trans_start();

        // A. Simpan Header
        $this->db->insert('sales_orders', $header);
        $insert_id = $this->db->insert_id();

        // B. Simpan Detail Items
        $final_items = [];
        foreach ($items as $item) {
            $final_items[] = [
                'sales_order_id' => $insert_id,
                'product_id'     => $item['product_id'],
                'qty'            => $item['qty'],
                'cost'           => $item['cost'],     // HPP
                'price'          => $item['price'],    // Harga Jual
                'discount'       => $item['discount'],
                'subtotal'       => $item['subtotal']
            ];
        }

        if(!empty($final_items)){
            $this->db->insert_batch('sales_order_items', $final_items);
        }

        $this->db->trans_complete();
        return $this->db->trans_status() === FALSE ? FALSE : $insert_id;
    }

    // 3. AMBIL DETAIL HEADER + ITEM (Versi Nested)
    public function get_order_detail($id) {
        $this->db->select('so.*, c.name as customer_name, c.address, c.city, c.phone, 
                           u.full_name as creator_name, 
                           s.full_name as salesman_name');
        $this->db->from('sales_orders so');
        $this->db->join('customers c', 'c.customer_id = so.customer_id');
        $this->db->join('users u', 'u.user_id = so.created_by', 'left');
        $this->db->join('users s', 's.user_id = so.salesman_id', 'left');
        $this->db->where('so.id', $id);
        $header = $this->db->get()->row();

        if ($header) {
            // Panggil fungsi get_items yang baru kita buat agar konsisten
            $header->items = $this->get_items($id);
        }
        return $header;
    }

    // Load Model Inventory di Constructor agar tidak lupa
    // (Tambahkan __construct di paling atas Sales_model)
    /*
    public function __construct() {
        parent::__construct();
        $this->load->model('Inventory_model');
    }
    */

    // 4. POTONG STOK (Via Inventory Model)
    public function deduct_stock_multi($order_id) {
        // Pastikan Inventory Model sudah di-load
        $this->load->model('Inventory_model');
        
        $items = $this->get_items($order_id);
        $order = $this->db->get_where('sales_orders', ['id'=>$order_id])->row();
        
        if(!empty($items)) {
            foreach($items as $item) {
                $desc = "Penjualan: " . $order->invoice_no;
                // Kirim nilai NEGATIF untuk mengurangi
                $qty_change = -1 * abs($item->qty); 
                
                $this->Inventory_model->adjust_stock(
                    $item->product_id, 
                    $qty_change, 
                    $desc, 
                    $this->session->userdata('user_id')
                );
            }
        }
    }

    // 5. KEMBALIKAN STOK (Via Inventory Model)
    public function restore_stock_multi($order_id) {
        $this->load->model('Inventory_model');
        
        $items = $this->get_items($order_id);
        $order = $this->db->get_where('sales_orders', ['id'=>$order_id])->row();
        
        if(!empty($items)) {
            foreach($items as $item) {
                $desc = "Batal Penjualan: " . $order->invoice_no;
                // Kirim nilai POSITIF untuk menambah kembali
                $qty_change = abs($item->qty); 
                
                $this->Inventory_model->adjust_stock(
                    $item->product_id, 
                    $qty_change, 
                    $desc, 
                    $this->session->userdata('user_id')
                );
            }
        }
    }

    // 6. HISTORY PEMBAYARAN
    public function get_payments($order_id) {
        return $this->db->get_where('sales_payments', ['sales_order_id' => $order_id])->result();
    }

    // 7. INPUT PEMBAYARAN
    public function add_payment($data) {
        $this->db->trans_start();

        // Simpan
        $this->db->insert('sales_payments', $data);

        // Update Total Bayar
        $order_id = $data['sales_order_id'];
        $amount   = $data['amount'];
        $this->db->set('total_paid', "total_paid + $amount", FALSE);
        $this->db->where('id', $order_id);
        $this->db->update('sales_orders');

        // Cek Lunas
        $order = $this->db->get_where('sales_orders', ['id' => $order_id])->row();
        $tagihan = ($order->grand_total > 0) ? $order->grand_total : $order->total_amount;

        $status_bayar = 'unpaid';
        if ($order->total_paid >= ($tagihan - 100)) { 
            $status_bayar = 'paid';
        } elseif ($order->total_paid > 0) {
            $status_bayar = 'partial';
        }

        $this->db->where('id', $order_id);
        $this->db->update('sales_orders', ['payment_status' => $status_bayar]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // 8. AMBIL ITEM ORDER (INI YANG TADI KURANG)
    public function get_items($order_id) {
        $this->db->select('soi.*, p.name as product_name, p.unit');
        $this->db->from('sales_order_items soi');
        $this->db->join('salt_products p', 'p.product_id = soi.product_id', 'left');
        $this->db->where('soi.sales_order_id', $order_id);
        return $this->db->get()->result();
    }
}