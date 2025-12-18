<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template {
    
    protected $_ci;

    function __construct() {
        $this->_ci =& get_instance();
    }

    /**
     * @param string $content_view  Nama file view untuk konten utama (misal: 'dashboard/index')
     * @param array  $data          Data yang akan dikirim ke view
     */
    function load($content_view, $data = NULL) {
        // Load konten utama ke dalam variabel 'content_body'
        $data['content_body'] = $this->_ci->load->view($content_view, $data, TRUE);
        
        // Load view master (layout utama) yang membungkus semuanya
        $this->_ci->load->view('_partials/layout', $data);
    }
}