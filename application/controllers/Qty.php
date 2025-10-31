<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qty extends CI_Controller
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

        // Start Product
        $this->db->where('status', 1);
        $product = $this->db->get('product');
        // End

        // Start Data Transaksi
        $data_trx = $this->db->get('analisys_po');
        // End

        $data = [
            'title' => 'Analisys PO',
            'product' => $product->result(),
            'data_trx' => $data_trx->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Po/v_po');
    }

    public function insert()
    {
        $createNumberPo = $this->input->post('createNumberPo');
        $createOrderDate = $this->input->post('createOrderDate');
        $createIdProduct = $this->input->post('createIdProduct');
        $createTypeSgs = $this->input->post('createTypeSgs');
        $createTypeUnit = $this->input->post('createTypeUnit');
        $createLatestIncomingStock = $this->input->post('createLatestIncomingStock');
        $createSaleLastMouth = $this->input->post('createSaleLastMouth');
        $createSaleWeekOne = $this->input->post('createSaleWeekOne');
        $createSaleWeekTwo = $this->input->post('createSaleWeekTwo');
        $createSaleWeekThree = $this->input->post('createSaleWeekThree');
        $createSaleWeekFour = $this->input->post('createSaleWeekFour');
        $createBalancePerToday = $this->input->post('createBalancePerToday');

        $data = [
            'number_po' => $createNumberPo,
            'order_date' => $createOrderDate,
            'created_by' => $this->session->userdata('username'),
            'created_date' => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date("Y-m-d H:i:s"),
            'status' => 1
        ];

        $this->db->insert('analisys_po', $data);
        $insert_id = $this->db->insert_id();

        foreach ($createIdProduct as $key => $id) {
            $data_detail = [
                'idproduct' => $id,
                'idanalisys_po' => $insert_id,
                'type_sgs' => $createTypeSgs[$key],
                'type_unit' => $createTypeUnit[$key],
                'latest_incoming_stock' => $createLatestIncomingStock[$key],
                'sale_last_mouth' => $createSaleLastMouth[$key],
                'sale_week_one' => $createSaleWeekOne[$key],
                'sale_week_two' => $createSaleWeekTwo[$key],
                'sale_week_three' => $createSaleWeekThree[$key],
                'sale_week_four' => $createSaleWeekFour[$key],
                'balance_per_today' => $createBalancePerToday[$key],
            ];

            $this->db->insert('detail_analisys_po', $data_detail);
        }

        $this->session->set_flashdata('success', 'Data PO berhasil disimpan.');
        redirect('po');
    }
}
