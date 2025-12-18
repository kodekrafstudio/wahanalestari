<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    public function login()
    {
        // Jika sudah login, tendang ke dashboard
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        // Validasi input
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/login');
        } else {
            $this->_login_process();
        }
    }

    private function _login_process()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->User_model->get_user_by_email($email);

        if ($user) {
            // --- PENGECEKAN STATUS AKTIF (BARU) ---
            if ($user['is_active'] == 0) {
                $this->session->set_flashdata('message', 'Akun Anda dinonaktifkan. Hubungi Administrator.');
                redirect('auth/login');
                return; // Stop proses
            }

            // Opsi 1: Cek Hash (Standard Security)
            if (password_verify($password, $user['password_hash'])) {
                $is_password_valid = true;
            } 
            // Opsi 2: Cek Plain Text (HANYA UNTUK DATA DUMMY 'hash123')
            else if ($password === $user['password_hash']) {
                 $is_password_valid = true;
            }

            if ($is_password_valid) {
                // Siapkan data session
                $data = [
                    'user_id' => $user['user_id'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                $this->session->set_userdata($data);
                
                // Log activity (Opsional, jika ingin mencatat login)
                // $this->Activity_model->add_log($user['user_id'], 'Login', 'users', $user['user_id']);

                redirect('dashboard');
            } else {
                $this->session->set_flashdata('message', 'Password salah!');
                redirect('auth/login');
            }
        } else {
            $this->session->set_flashdata('message', 'Email tidak terdaftar!');
            redirect('auth/login');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('full_name');
        $this->session->unset_userdata('email');

        $this->session->set_flashdata('message', 'Anda telah logout.');
        redirect('auth/login');
    }
}