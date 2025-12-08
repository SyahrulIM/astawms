<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Packinglist extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Eeettss gak boleh nakal, Login dulu ya kak hehe.');
            redirect('auth');
        }
    }


    public function index()
    {
        $data = [
            'title' => 'Packing List',
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Packinglist/v_packinglist');
    }
}
