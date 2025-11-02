<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Finish extends CI_Controller
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
        $this->db->where('status_progress', 'PO');
        $data_trx = $this->db->get('analisys_po');
        // End

        $data = [
            'title' => 'Analisys PO',
            'product' => $product->result(),
            'data_trx' => $data_trx->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('finish/v_finish');
    }

    public function preorder()
    {
        $idanalisys_po = $this->input->post('id');
        $priceList = $this->input->post('editPrice');

        if (empty($idanalisys_po) || empty($priceList)) {
            $this->session->set_flashdata('error', 'Data tidak lengkap. Pastikan semua QTY sudah diisi.');
            redirect('pre');
            return;
        }

        // Ambil semua detail berdasarkan id analisys_po
        $this->db->where('idanalisys_po', $idanalisys_po);
        $detailData = $this->db->get('detail_analisys_po')->result();

        if (count($detailData) !== count($priceList)) {
            $this->session->set_flashdata('error', 'Jumlah data QTY tidak sesuai dengan jumlah produk.');
            redirect('pre');
            return;
        }

        // Update masing-masing QTY ke tabel detail_analisys_po
        foreach ($detailData as $index => $detail) {
            $this->db->where('iddetail_analisys_po', $detail->iddetail_analisys_po);
            $this->db->update('detail_analisys_po', [
                'price' => $priceList[$index]
            ]);
        }

        // Ubah status_progress di analisys_po jadi "Waiting Approval"
        $this->db->where('idanalisys_po', $idanalisys_po);
        $this->db->update('analisys_po', [
            'status_progress' => 'PO',
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'PO berhasil disimpan dan dipublikasikan. Silakan cek detail dan buat dokumen anda di finish.');
        redirect('pre');
    }

    public function get_detail_analisys_po($idanalisys_po)
    {
        $this->db->select('d.idanalisys_po, p.nama_produk, p.sku, d.type_sgs, d.type_unit, d.latest_incoming_stock, 
                       d.sale_last_mouth, d.sale_week_one, d.sale_week_two, d.sale_week_three, 
                       d.sale_week_four, d.balance_per_today, d.qty_order, d.price');
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
                    <th>Avg Sales vs Stock (Bulan)</th>
                    <th>Qty Order</th>
                    <th>Price per Unit</th>
                </tr>
            </thead>
            <tbody>';

            foreach ($query->result() as $row) {
                // Hitung rata-rata penjualan per minggu
                $total_sales = floatval($row->sale_week_one) + floatval($row->sale_week_two) + floatval($row->sale_week_three) + floatval($row->sale_week_four);
                $avg_sales = $total_sales / 4;

                // Hindari pembagian nol
                if ($avg_sales > 0) {
                    $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                    $avg_vs_stock = number_format($avg_vs_stock, 2); // tampilkan 2 angka desimal
                } else {
                    $avg_vs_stock = '<span class="text-muted">N/A</span>';
                }

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
                <td>' . $avg_vs_stock . '</td>
                <td>' . htmlspecialchars($row->qty_order) . '</td>
                <td>' . htmlspecialchars($row->price) . '</td>
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
        redirect('Pre');
    }

    public function exportPdf()
    {
        $id = $this->input->get('id');

        $this->db->where('idanalisys_po', $id);
        $data_po = $this->db->get('analisys_po');

        $this->db->select('d.idanalisys_po, p.nama_produk, p.sku, d.type_sgs, d.type_unit, d.latest_incoming_stock, 
                       d.sale_last_mouth, d.sale_week_one, d.sale_week_two, d.sale_week_three, 
                       d.sale_week_four, d.balance_per_today, d.qty_order, d.price');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $id);
        $data_detail_po = $this->db->get();

        $data = [
            'title' => 'Export Pre-Order',
            'po' => $data_po->row(),
            'detail_po' => $data_detail_po->result()
        ];

        $this->load->view('finish/v_pdf', $data);
    }
}
