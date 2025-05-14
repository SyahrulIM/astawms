<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_note extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Eeettss gak boleh nakal, Login dulu ya kak hehe.');
            redirect('auth');  // Assuming 'auth' is your login controller
        }
    }

    public function index()
    {
        $title = 'Realisasi Pengiriman';

        $this->db->where_in('user.idrole', [3, 5]);
        $this->db->where('user.status', 1);
        $this->db->join('role', 'role.idrole = user.idrole');
        $admin = $this->db->get('user')->result();

        $this->db->select('delivery_note.no_manual as no_manual, user_received.full_name as user_received, delivery_note.send_date as send_date, user_input.full_name as user_input, delivery_note.created_date as created_date');
        $this->db->join('user as user_received','user_received.iduser = delivery_note.idreceived', 'left');
        $this->db->join('user as user_input','user_input.iduser = delivery_note.iduser', 'left');
        $this->db->where('delivery_note.status', 1);
        $delivery = $this->db->get('delivery_note')->result();        

        $data = [
            'title' => $title,
            'admin' => $admin,
            'delivery' => $delivery,
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Delivery_note/v_delivery_note');
    }

    public function createDelivery()
    {
        $no_manual = $this->input->post('inputNo');
        $idreceived = $this->input->post('inputReceived');
        $send_date = $this->input->post('inputDate');

        $data = [
            'no_manual' => $no_manual,
            'idreceived' => $idreceived,
            'send_date' => $send_date,
            'iduser' => $this->session->userdata('iduser'),
            'created_by' => $this->session->userdata('username'),
            'created_date' => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date("Y-m-d H:i:s"),
            'status' => 1
        ];

        $this->db->insert('delivery_note', $data);

        redirect('delivery_note');
    }
}