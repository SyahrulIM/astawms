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
        $idanalisys_po = $this->input->post('id');
        $qtyList = $this->input->post('editQty');

        if (empty($idanalisys_po) || empty($qtyList)) {
            $this->session->set_flashdata('error', 'Data tidak lengkap. Pastikan semua QTY sudah diisi.');
            redirect('qty');
            return;
        }

        // Ambil semua detail berdasarkan id analisys_po
        $this->db->where('idanalisys_po', $idanalisys_po);
        $detailData = $this->db->get('detail_analisys_po')->result();

        if (count($detailData) !== count($qtyList)) {
            $this->session->set_flashdata('error', 'Jumlah data QTY tidak sesuai dengan jumlah produk.');
            redirect('qty');
            return;
        }

        // Update masing-masing QTY ke tabel detail_analisys_po
        foreach ($detailData as $index => $detail) {
            $this->db->where('iddetail_analisys_po', $detail->iddetail_analisys_po);
            $this->db->update('detail_analisys_po', [
                'qty_order' => $qtyList[$index]
            ]);
        }

        // Ubah status_progress di analisys_po jadi "Waiting Approval"
        $this->db->where('idanalisys_po', $idanalisys_po);
        $this->db->update('analisys_po', [
            'status_progress' => 'Qty',
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'QTY berhasil disimpan dan silakan lanjut ke tahap Pre-Order.');
        redirect('qty');
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
                <th>Avg Sales vs Stock (Bulan)</th>
                <th>QTY Order</th>
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
            <td><input type="number" class="form-control form-control-sm" name="editQty[]" required></td>
            <input type="hidden" class="form-control form-control-sm" name="id" value="' . htmlspecialchars($row->idanalisys_po) . '">
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
