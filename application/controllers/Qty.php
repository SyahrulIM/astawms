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
        $this->db->where('status_progress', 'Listing');
        $data_trx = $this->db->get('analisys_po');
        // End

        $data = [
            'title' => 'Analisys PO',
            'product' => $product->result(),
            'data_trx' => $data_trx->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Qty/v_qty');
    }

    public function process()
    {
        $id = $this->input->post('id');
        $qty = $this->input->post('editQty');
        echo '<pre>';
        print_r($qty);
        die;
    }

    public function get_detail_analisys_po($idanalisys_po)
    {
        $this->db->select('d.idanalisys_po, p.nama_produk, p.sku, d.type_sgs, d.type_unit, d.latest_incoming_stock, 
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
                <th>QTY</th>
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
            <td><input type="hidden" class="form-control form-control-sm" name="id" value="' . htmlspecialchars($row->idanalisys_po) . '"></td>
            <td><input type="number" class="form-control form-control-sm" name="editQty[]" placeholder="0" max="10000"></td>
          </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="text-center text-muted py-3">Tidak ada produk dalam analisis PO ini.</div>';
        }
    }

    public function cancel($idanalisys_po)
    {
        $this->db->set('status_progress', 'Cancel');
        $this->db->where('idanalisys_po', $idanalisys_po);
        $this->db->update('analisys_po');

        $this->session->set_flashdata('success', 'Pemesanan berhasil dibatalkan.');
        redirect('qty');
    }
}
