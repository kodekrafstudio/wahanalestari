<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // 1. Cek Login
        if (!$this->session->userdata('user_id')) { redirect('auth/login'); }
        
        // 2. Cek Role (Hanya Admin & Owner yang boleh masuk sini)
        $role = $this->session->userdata('role');
        if ($role != 'admin' && $role != 'owner') {
            show_error('Akses Ditolak. Anda tidak memiliki izin mengakses halaman ini.', 403, 'Forbidden');
        }

        $this->load->model('User_model');
        $this->load->library('template');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Manajemen Pengguna';
        $data['users'] = $this->User_model->get_all(); // Ambil semua user
        $this->template->load('master/users/index', $data);
    }

    public function add() {
        $this->_process_form();
    }

    public function edit($id) {
        // Ambil data user lama untuk validasi email unik nanti
        $data['row'] = $this->User_model->get_by_id($id);
        if(!$data['row']) show_404();
        
        $this->_process_form($data['row']);
    }

    // FUNGSI GANTI STATUS (AKTIF / NON-AKTIF)
    public function toggle_status($id, $new_status) {
        // 1. Proteksi: Tidak boleh menonaktifkan diri sendiri
        if($id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Tidak bisa menonaktifkan akun sendiri!');
            redirect('master/users');
        }

        // 2. Update Status
        // $new_status: 1 (Aktifkan) atau 0 (Non-aktifkan)
        if ($this->User_model->update_status($id, $new_status)) {
            $status_text = ($new_status == 1) ? 'Diaktifkan' : 'Dinonaktifkan';
            $this->session->set_flashdata('message', "User berhasil $status_text.");
        } else {
            $this->session->set_flashdata('error', 'Gagal mengubah status.');
        }
        
        redirect('master/users');
    }

    // FUNGSI PROSES FORM (ADD & EDIT JADI SATU)
    private function _process_form($row = null) {
        $data['title'] = $row ? 'Edit User' : 'Tambah User Baru';
        $data['row']   = $row;
        $id = $row ? $row->user_id : null;

        // A. Validasi Nama & Role
        $this->form_validation->set_rules('full_name', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('phone', 'No HP', 'trim|numeric');
        $this->form_validation->set_rules('role', 'Role', 'required');

        // B. Validasi Email Unik (Logic Cerdas)
        if ($id) {
            // Jika Edit: Cek unik TAPI kecualikan email dia sendiri (biar gak error kalau gak ganti email)
            $original_email = $row->email;
            if($this->input->post('email') != $original_email) {
                $is_unique =  '|is_unique[users.email]';
            } else {
                $is_unique =  '';
            }
        } else {
            // Jika Baru: Wajib Unik
            $is_unique =  '|is_unique[users.email]';
        }
        
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim' . $is_unique, [
            'is_unique' => 'Email ini sudah terdaftar. Gunakan email lain.'
        ]);

        // C. Validasi Password
        if ($id) {
            // Edit: Boleh kosong. TAPI jika diisi, minimal 5 karakter
            $this->form_validation->set_rules('password', 'Password', 'trim');
            if(!empty($this->input->post('password'))) {
                $this->form_validation->set_rules('password', 'Password', 'min_length[5]');
            }
        } else {
            // Baru: Wajib isi, minimal 5 karakter
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
        }

        // EKSEKUSI
        if ($this->form_validation->run() == FALSE) {
            $this->template->load('master/users/form', $data);
        } else {
            $post = [
                'full_name' => $this->input->post('full_name'),
                'email'     => $this->input->post('email'),
                'phone'     => $this->input->post('phone'),
                'role'      => $this->input->post('role'),
                // Password ditangani di Model (di-hash jika tidak kosong)
                'password'  => $this->input->post('password')
            ];

            if ($id) {
                $this->User_model->update($id, $post);
                $this->session->set_flashdata('message', 'Data user berhasil diperbarui.');
            } else {
                $this->User_model->insert($post);
                $this->session->set_flashdata('message', 'User baru berhasil ditambahkan.');
            }
            redirect('master/users');
        }
    }
}