<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller
{

    public function index()
    {
        $title = 'Customer';
        
        $customer = $this->db->get('customer');

        $data = [
            'title' => $title,
            'customer' => $customer
        ];
        $this->load->view('theme/v_head',$data);
        $this->load->view('Customer/v_customer');
    }
}