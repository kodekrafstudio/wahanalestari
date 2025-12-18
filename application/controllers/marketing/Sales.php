<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        
        $this->load->model('Sales_model');
        $this->load->model('Customer_model');
        $this->load->model('Product_model');
        $this->load->library('template');
    }

    // ... (Index, Create, Detail, Print sama seperti sebelumnya - AMAN) ...

    public function index() {
        $data['title'] = 'Daftar Penjualan';
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : date('Y-m-01');
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : date('Y-m-d');
        $status     = $this->input->get('status');

        $this->db->select('so.*, c.name as customer_name, u.full_name as sales_name');
        $this->db->from('sales_orders so');
        $this->db->join('customers c', 'c.customer_id = so.customer_id');
        $this->db->join('users u', 'u.user_id = so.salesman_id', 'left');
        $this->db->where('DATE(so.order_date) >=', $start_date);
        $this->db->where('DATE(so.order_date) <=', $end_date);
        
        if($status && $status != 'all') { 
            $this->db->where('so.status', $status); 
        }
        
        $this->db->order_by('so.id', 'DESC');
        $data['orders'] = $this->db->get()->result();

        $total_omzet = 0; $total_piutang = 0; $total_lunas = 0;
        foreach($data['orders'] as $o) {
            if($o->status != 'canceled') {
                $grand = ($o->grand_total > 0) ? $o->grand_total : $o->total_amount;
                $total_omzet += $grand;
                $total_lunas += $o->total_paid;
                $sisa = $grand - $o->total_paid;
                if($sisa > 0) $total_piutang += $sisa;
            }
        }
        
        $data['filter']  = ['start_date'=>$start_date, 'end_date'=>$end_date, 'status'=>$status];
        $data['summary'] = ['omzet'=>$total_omzet, 'piutang'=>$total_piutang, 'lunas'=>$total_lunas];
        $this->template->load('marketing/sales/index', $data);
    }

    public function create() {
        if ($this->input->post()) {
            $post = $this->input->post();
            if (empty($post['product_id'])) {
                $this->session->set_flashdata('error', 'Keranjang belanja kosong!');
                redirect('marketing/sales/create');
            }

            $items_to_save = [];
            $count = count($post['product_id']);
            for ($i = 0; $i < $count; $i++) {
                if(!empty($post['product_id'][$i])) {
                    $pid = $post['product_id'][$i];
                    $qty = $post['qty'][$i];
                    $prod = $this->db->select('base_cost')->get_where('salt_products', ['product_id'=>$pid])->row();

                    $items_to_save[] = [
                        'product_id' => $pid,
                        'qty'        => $qty,
                        'cost'       => $prod ? $prod->base_cost : 0,
                        'price'      => $post['price'][$i],
                        'discount'   => isset($post['discount'][$i]) ? $post['discount'][$i] : 0,
                        'subtotal'   => $post['subtotal'][$i]
                    ];
                }
            }

            $grand_total = str_replace(['Rp','.',' '], '', $post['grand_total']);
            $raw_total   = str_replace(['Rp','.',' '], '', $post['total_amount_raw']);
            $shipping    = $post['shipping_cost'] ? str_replace(['Rp','.',' '], '', $post['shipping_cost']) : 0;
            $other_disc  = $post['other_discount'] ? str_replace(['Rp','.',' '], '', $post['other_discount']) : 0;

            $header = [
                'invoice_no'    => $this->Sales_model->generate_invoice_no(),
                'customer_id'   => $post['customer_id'],
                'salesman_id'   => $post['salesman_id'],
                'order_date'    => $post['order_date'] . ' ' . date('H:i:s'),
                'total_amount'  => $raw_total,
                'shipping_cost' => $shipping,
                'other_discount'=> $other_disc,
                'grand_total'   => $grand_total,
                'status'        => 'request',
                'payment_status'=> 'unpaid',
                'created_by'    => $this->session->userdata('user_id'),
                'created_at'    => date('Y-m-d H:i:s')
            ];

            $new_id = $this->Sales_model->create_order($header, $items_to_save);
            if ($new_id) {
                $this->session->set_flashdata('message', 'Order Berhasil Dibuat (Status: Request).');
                redirect('marketing/sales/detail/' . $new_id);
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan transaksi.');
                redirect('marketing/sales/create');
            }
        }
        $data['title']     = 'Buat Penjualan Baru';
        $data['customers'] = $this->Customer_model->get_all();
        $data['products']  = $this->db->select('p.*, ws.quantity as stock')
                                      ->from('salt_products p')
                                      ->join('warehouse_stock ws', 'ws.product_id = p.product_id', 'left')
                                      ->get()->result();
        $data['salesmen']  = $this->db->get_where('users', ['role' => 'sales'])->result();
        $this->template->load('marketing/sales/create', $data);
    }

    public function detail($id) {
        $data['order']    = $this->Sales_model->get_order_detail($id);
        if(!$data['order']) show_404();
        $data['payments'] = $this->Sales_model->get_payments($id);
        $data['title']    = 'Faktur: ' . $data['order']->invoice_no;
        $this->template->load('marketing/sales/detail', $data);
    }

    public function print_invoice($id) {
        $data['order'] = $this->Sales_model->get_order_detail($id);
        $this->load->view('marketing/sales/print_invoice', $data);
    }

    // --- UPDATE STATUS (DIPERBAIKI) ---
    public function update_status() {
        $order_id   = $this->input->post('order_id');
        $new_status = $this->input->post('status'); 
        
        $order      = $this->Sales_model->get_order_detail($order_id);
        $old_status = $order->status;

        if ($new_status == $old_status) {
            redirect('marketing/sales/detail/' . $order_id);
        }

        // 1. SKENARIO KIRIM BARANG (Potong Stok)
        if ($new_status == 'delivering' && ($old_status == 'request' || $old_status == 'preparing')) {
            // Cek Stok
            $items = $this->Sales_model->get_items($order_id);
            foreach ($items as $item) {
                $stok_gudang = $this->db->get_where('warehouse_stock', ['product_id' => $item->product_id])->row();
                $sisa = $stok_gudang ? $stok_gudang->quantity : 0;
                if ($sisa < $item->qty) {
                    $this->session->set_flashdata('error', "GAGAL KIRIM! Stok {$item->product_name} tidak cukup. Sisa: {$sisa}");
                    redirect('marketing/sales/detail/' . $order_id);
                    return; 
                }
            }
            $this->Sales_model->deduct_stock_multi($order_id);
            $this->db->where('id', $order_id)->update('sales_orders', ['status' => $new_status]);
            $this->session->set_flashdata('message', 'Status: Delivering. Stok Gudang Berhasil DIKURANGI.');
        }

        // 2. SKENARIO BATAL (Cancel/Retur)
        else if ($new_status == 'canceled') {
            
            $pesan_stok = "Stok aman (belum terpotong).";
            
            // Logika Stok: Balikin jika sudah keluar
            if ($old_status == 'delivering' || $old_status == 'done') {
                $this->Sales_model->restore_stock_multi($order_id);
                $pesan_stok = "Barang sudah keluar, stok DIKEMBALIKAN ke gudang.";
            } 

            // Logika Keuangan & Catatan
            $data_update = ['status' => 'canceled'];
            $pesan_uang  = "";

            // Ambil input note dari modal
            $input_note = $this->input->post('note'); 
            
            if ($order->total_paid > 0) {
                $data_update['payment_status'] = 'refunded'; 
                $pesan_uang = " Status pembayaran diubah menjadi REFUNDED.";
                
                // Tambah info refund ke note
                $note_final = $input_note . " [SYSTEM: Refund Rp " . number_format($order->total_paid,0,',','.') . "]";
            } else {
                $note_final = $input_note;
            }
            
            $data_update['note'] = $note_final;

            $this->db->where('id', $order_id)->update('sales_orders', $data_update);
            $this->session->set_flashdata('message', 'Order Dibatalkan. ' . $pesan_stok . $pesan_uang);
        }

        // 3. SKENARIO AKTIFKAN KEMBALI
        else if ($old_status == 'canceled' && $new_status == 'request') {
             $this->db->where('id', $order_id)->update('sales_orders', ['status' => $new_status]);
             $this->session->set_flashdata('message', 'Order Diaktifkan kembali.');
        }

        // 4. STATUS LAIN
        else {
            $this->db->where('id', $order_id)->update('sales_orders', ['status' => $new_status]);
            $this->session->set_flashdata('message', 'Status berhasil diupdate.');
        }

        redirect('marketing/sales/detail/' . $order_id);
    }

    // ... (submit_payment sama seperti sebelumnya - AMAN) ...
    public function submit_payment() {
        $post = $this->input->post();
        $amount = str_replace(['Rp','.',' '], '', $post['amount']);
        $order_id = $post['sales_order_id'];
        
        $order = $this->db->get_where('sales_orders', ['id'=>$order_id])->row();
        
        if($order->status == 'canceled') {
             $this->session->set_flashdata('error', 'Gagal. Faktur Batal tidak bisa dibayar.');
             redirect('marketing/sales/detail/' . $order_id);
             return;
        }

        if ($amount > 0) {
            $this->Sales_model->add_payment([
                'sales_order_id' => $order_id,
                'payment_date'   => $post['payment_date'],
                'amount'         => $amount,
                'payment_method' => $post['payment_method'],
                'note'           => $post['note']
            ]);
            $this->session->set_flashdata('message', 'Pembayaran diterima.');
        }
        redirect('marketing/sales/detail/' . $order_id);
    }
}