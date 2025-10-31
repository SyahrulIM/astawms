<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Po extends CI_Controller
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
            'status_progress' => 'Listing',
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

    public function get_detail_analisys_po($idanalisys_po)
    {
        $this->db->select('p.nama_produk, p.sku, d.type_sgs, d.type_unit, d.latest_incoming_stock, 
                       d.sale_last_mouth, d.sale_week_one, d.sale_week_two, d.sale_week_three, 
                       d.sale_week_four, d.balance_per_today');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $idanalisys_po);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo '<table class="table table-bordered table-striped table-xl align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Produk</th>
                        <th>SKU</th>
                        <th>SGS/Non-SGS</th>
                        <th>Tipe Satuan</th>
                        <th>Stock Masuk Terakhir</th>
                        <th>Penjualan Bulan Lalu</th>
                        <th>Minggu 1</th>
                        <th>Minggu 2</th>
                        <th>Minggu 3</th>
                        <th>Minggu 4</th>
                        <th>Saldo Hari Ini</th>
                    </tr>
                </thead>
                <tbody>';
            foreach ($query->result() as $row) {
                echo '<tr>
                    <td>' . htmlspecialchars($row->nama_produk) . '</td>
                    <td>' . htmlspecialchars($row->sku) . '</td>
                    <td>' . htmlspecialchars($row->type_sgs) . '</td>
                    <td>' . htmlspecialchars($row->type_unit) . '</td>
                    <td>' . htmlspecialchars($row->latest_incoming_stock) . '</td>
                    <td>' . htmlspecialchars($row->sale_last_mouth) . '</td>
                    <td>' . htmlspecialchars($row->sale_week_one) . '</td>
                    <td>' . htmlspecialchars($row->sale_week_two) . '</td>
                    <td>' . htmlspecialchars($row->sale_week_three) . '</td>
                    <td>' . htmlspecialchars($row->sale_week_four) . '</td>
                    <td>' . htmlspecialchars($row->balance_per_today) . '</td>
                  </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="text-center text-muted py-3">Tidak ada produk dalam analisis PO ini.</div>';
        }
    }
}
