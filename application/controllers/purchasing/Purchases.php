<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchases extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Cek Login
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        
        $this->load->model('Purchase_model');
        $this->load->model('Supplier_model');
        $this->load->model('Product_model');
        $this->load->library('template');
    }

    // ==========================================================
    // 1. HALAMAN DAFTAR PO (INDEX) - PERBAIKAN DISINI
    // ==========================================================
    public function index() {
        $data['title'] = 'Data Pembelian (PO)';
        
        // A. Ambil Filter dari URL
        $start_date = $this->input->get('start_date');
        $end_date   = $this->input->get('end_date');
        $status     = $this->input->get('status');

        // Default Tanggal (Tahun ini) agar data tampil
        if(!$start_date) $start_date = date('Y-01-01'); 
        if(!$end_date)   $end_date   = date('Y-m-d');

        // B. Query Utama (Join Supplier & Hitung Item)
        // Kita butuh 'item_count' agar tidak error di View
        $this->db->select('p.*, s.supplier_name, COUNT(pi.id) as item_count'); 
        $this->db->from('purchases p');
        $this->db->join('suppliers s', 's.supplier_id = p.supplier_id');
        $this->db->join('purchase_items pi', 'pi.purchase_id = p.purchase_id', 'left');
        
        // Terapkan Filter
        $this->db->where('DATE(p.purchase_date) >=', $start_date);
        $this->db->where('DATE(p.purchase_date) <=', $end_date);
        
        if($status && $status != 'all') {
            $this->db->where('p.status', $status);
        }

        $this->db->group_by('p.purchase_id'); // Wajib Group By karena ada COUNT
        $this->db->order_by('p.purchase_id', 'DESC');
        
        $data['purchases'] = $this->db->get()->result();

        // C. Hitung Ringkasan (Summary Widget)
        $total_belanja = 0;
        $total_hutang  = 0;
        $total_lunas   = 0;

        if(!empty($data['purchases'])) {
            foreach($data['purchases'] as $row) {
                // PO Batal tidak dihitung
                if($row->status != 'canceled') {
                    $total_belanja += $row->total_cost;
                    $total_lunas   += $row->total_paid;
                    
                    $sisa = $row->total_cost - $row->total_paid;
                    if($sisa > 0) $total_hutang += $sisa;
                }
            }
        }

        // Kirim Data ke View
        $data['filter']  = ['start_date' => $start_date, 'end_date' => $end_date, 'status' => $status];
        $data['summary'] = ['belanja' => $total_belanja, 'hutang' => $total_hutang, 'lunas' => $total_lunas];
        
        $this->template->load('purchasing/purchases/index', $data);
    }

    // ==========================================================
    // 2. BUAT PO BARU (CREATE)
    // ==========================================================
    public function create() {
        if ($this->input->post()) {
            $post = $this->input->post();

            // Validasi Barang
            if (empty($post['product_id'])) {
                $this->session->set_flashdata('error', 'Pilih minimal satu barang!');
                redirect('purchasing/purchases/create');
            }

            // A. Header PO
            $header = [
                'purchase_no'   => $this->Purchase_model->generate_po_number(),
                'supplier_id'   => $post['supplier_id'],
                'purchase_date' => $post['purchase_date'],
                'total_cost'    => str_replace(['Rp', '.', ' '], '', $post['grand_total']),
                'total_paid'    => 0,
                'status'        => 'ordered',
                'payment_status'=> 'unpaid',
                'created_by'    => $this->session->userdata('user_id'),
                'created_at'    => date('Y-m-d H:i:s')
            ];

            // B. Items PO
            $items = [];
            $count = count($post['product_id']);
            for ($i = 0; $i < $count; $i++) {
                if(!empty($post['product_id'][$i])) {
                    $items[] = [
                        'product_id' => $post['product_id'][$i],
                        'qty'        => $post['qty'][$i],
                        'cost'       => str_replace(['Rp', '.', ' '], '', $post['price'][$i]), // Harga Beli
                        'subtotal'   => str_replace(['Rp', '.', ' '], '', $post['subtotal'][$i])
                    ];
                }
            }

            // Simpan via Model dan Tangkap ID Baru
            $new_id = $this->Purchase_model->create_purchase($header, $items);

            if ($new_id) {
                $this->session->set_flashdata('message', 'PO berhasil dibuat.');
                redirect('purchasing/purchases/detail/' . $new_id);
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan data.');
                redirect('purchasing/purchases/create');
            }
        }

        $data['suppliers'] = $this->db->get('suppliers')->result();
        $data['products']  = $this->db->get('salt_products')->result();
        $data['title']     = 'Buat PO Baru';
        $this->template->load('purchasing/purchases/create', $data);
    }

    // ==========================================================
    // 3. DETAIL PO
    // ==========================================================
    public function detail($id) {
        $data['po'] = $this->Purchase_model->get_detail($id);
        if(!$data['po']) show_404();

        // Ambil items dan payments untuk ditampilkan
        $data['items']    = $this->Purchase_model->get_items($id);
        $data['payments'] = $this->db->get_where('purchase_payments', ['purchase_id' => $id])->result();
        $data['title']    = 'Detail PO: ' . $data['po']->purchase_no;
        
        $this->template->load('purchasing/purchases/detail', $data);
    }

    // ==========================================================
    // 4. TERIMA BARANG (UPDATE STOK & HPP)
    // ==========================================================
    public function receive($id) {
        $po = $this->db->get_where('purchases', ['purchase_id' => $id])->row();
        
        // Validasi: Hanya status 'ordered' yang bisa diterima
        if (!$po || $po->status != 'ordered') {
            $this->session->set_flashdata('error', 'Gagal: PO tidak valid atau sudah diproses.');
            redirect('purchasing/purchases/detail/' . $id);
        }

        $items = $this->Purchase_model->get_items($id);
        $this->db->trans_start();

        foreach ($items as $item) {
            // A. Hitung HPP Average
            $prod = $this->db->select('p.base_cost, ws.quantity')
                             ->from('salt_products p')
                             ->join('warehouse_stock ws', 'ws.product_id = p.product_id', 'left')
                             ->where('p.product_id', $item->product_id)
                             ->get()->row();

            $old_qty  = $prod->quantity ? $prod->quantity : 0;
            $old_cost = $prod->base_cost ? $prod->base_cost : 0;
            
            $new_qty  = $item->qty;
            $new_cost = $item->cost;

            $total_val_old = $old_qty * $old_cost;
            $total_val_new = $new_qty * $new_cost;
            $total_qty     = $old_qty + $new_qty;

            $final_hpp = ($total_qty > 0) ? ($total_val_old + $total_val_new) / $total_qty : $new_cost;

            // B. Update Master Produk
            $this->db->where('product_id', $item->product_id);
            $this->db->update('salt_products', [
                'base_cost' => ceil($final_hpp),
                'last_purchase_price' => $new_cost
            ]);

            // C. Update Stok Gudang
            $cek = $this->db->get_where('warehouse_stock', ['product_id' => $item->product_id])->num_rows();
            if ($cek > 0) {
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

            // D. Log Stok
            $this->db->insert('stock_logs', [
                'product_id'  => $item->product_id,
                'user_id'     => $this->session->userdata('user_id'),
                'change_qty'  => $new_qty,
                'type'        => 'in',
                'description' => 'PO: ' . $po->purchase_no,
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }

        // E. Update Status PO
        $this->db->where('purchase_id', $id);
        $this->db->update('purchases', [
            'status' => 'received',
            'received_date' => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();
        
        if ($this->db->trans_status()) {
            $this->session->set_flashdata('message', 'Stok berhasil masuk gudang & HPP terupdate.');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan database.');
        }
        redirect('purchasing/purchases/detail/' . $id);
    }

    // ==========================================================
    // 5. INPUT PEMBAYARAN (Dengan Proteksi Cancel)
    // ==========================================================
    public function submit_payment() {
        $post = $this->input->post();
        $purchase_id = $post['purchase_id'];

        // Proteksi: Cek apakah PO canceled?
        $po = $this->db->get_where('purchases', ['purchase_id' => $purchase_id])->row();
        if ($po->status == 'canceled') {
            $this->session->set_flashdata('error', 'GAGAL: PO Batal tidak bisa dibayar.');
            redirect('purchasing/purchases/detail/' . $purchase_id);
            return;
        }

        $amount = str_replace(['Rp', '.', ' '], '', $post['amount']);
        $data = [
            'purchase_id'    => $purchase_id,
            'payment_date'   => $post['payment_date'],
            'amount'         => $amount,
            'payment_method' => $post['payment_method'],
            'note'           => $post['note'],
            'created_by'     => $this->session->userdata('user_id')
        ];

        if($this->Purchase_model->add_payment($data)) {
            $this->session->set_flashdata('message', 'Pembayaran berhasil disimpan.');
        }
        redirect('purchasing/purchases/detail/' . $purchase_id);
    }

    // ==========================================================
    // 6. BATALKAN PO
    // ==========================================================
    public function cancel($id) {
        $po = $this->db->get_where('purchases', ['purchase_id'=>$id])->row();
        
        if ($po->status == 'ordered') {
            $this->db->where('purchase_id', $id)->update('purchases', ['status'=>'canceled']);
            $this->session->set_flashdata('message', 'PO berhasil dibatalkan.');
        } else {
            $this->session->set_flashdata('error', 'Hanya PO status Ordered yang bisa dibatalkan.');
        }
        redirect('purchasing/purchases/detail/' . $id);
    }

    // ==========================================================
    // 7. HAPUS PO
    // ==========================================================
    public function delete($id) {
        $po = $this->db->get_where('purchases', ['purchase_id' => $id])->row();
        
        // Hanya boleh hapus jika BELUM diterima
        if ($po->status == 'received') {
            $this->session->set_flashdata('error', 'Bahaya! PO yang sudah diterima stoknya tidak boleh dihapus.');
        } else {
            $this->db->trans_start();
            $this->db->delete('purchase_items', ['purchase_id' => $id]);
            $this->db->delete('purchase_payments', ['purchase_id' => $id]);
            $this->db->delete('purchases', ['purchase_id' => $id]);
            $this->db->trans_complete();
            $this->session->set_flashdata('message', 'Data PO berhasil dihapus permanen.');
        }
        redirect('purchasing/purchases');
    }

    // ==========================================================
    // 8. CETAK PO (PRINT VIEW)
    // ==========================================================
    public function print_po($id) {
        $data['po']    = $this->Purchase_model->get_detail($id);
        
        // Pastikan pakai 'get_items' sesuai Model terakhir kita
        $data['items'] = $this->Purchase_model->get_items($id); 
        
        if(!$data['po']) show_404();

        $this->load->view('purchasing/purchases/print_po', $data);
    }
}