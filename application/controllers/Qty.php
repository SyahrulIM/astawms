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

        // Start Data Transaksi
        $this->db->where('status_progress', 'Listing');
        $this->db->order_by('idanalisys_po', 'DESC');
        $data_trx = $this->db->get('analisys_po');

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
        $this->db->select('p.nama_produk, p.sku, d.type_sgs, d.type_unit, d.latest_incoming_stock, d.last_mouth_sales, d.current_month_sales, d.balance_per_today, d.qty_order, d.price, d.description');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $idanalisys_po);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo '<table class="table table-bordered table-striped table-xl align-middle">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>SKU</th>
                <th>Stock Masuk Terakhir</th>
                <th>Penjualan Bulan Lalu</th>
                <th>Penjualan Bulan Ini</th>
                <th>Saldo Hari Ini</th>
                <th>Avg Sales vs Stock (Bulan)</th>
                <th>SGS/Non-SGS</th>
                <th>Tipe Satuan</th>
                <th>Qty Order</th>
                <th>Price per Unit</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>';

            $found = false;
            $no = 1;
            foreach ($query->result() as $row) {
                $total_sales = $row->current_month_sales;
                $avg_sales = $total_sales / 4;

                if ($avg_sales > 0) {
                    $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                    if ($avg_vs_stock >= 1) continue;
                    $found = true;
                    $avg_vs_stock_display = number_format($avg_vs_stock, 2);
                } else {
                    continue;
                }

                // dropdown SGS/Non-SGS
                $select_sgs =
                    '<select class="form-select" name="editTypeSgs[]">
                        <option value="">Pilih SGS</option>
                        <option value="sgs" ' . ($row->type_sgs == 'sgs' ? 'selected' : '') . '>SGS</option>
                        <option value="non sgs" ' . ($row->type_sgs == 'non sgs' ? 'selected' : '') . '>Non SGS</option>
                    </select>';

                echo '<tr>
                <td>' . $no++ . '</td>
                <td>' . htmlspecialchars($row->nama_produk) . '</td>
                <td>' . htmlspecialchars($row->sku) . '</td>
                <td>' . htmlspecialchars($row->latest_incoming_stock) . '</td>
                <td>' . htmlspecialchars($row->last_mouth_sales) . '</td>
                <td>' . htmlspecialchars($row->current_month_sales) . '</td>
                <td>' . htmlspecialchars($row->balance_per_today) . '</td>
                <td>' . $avg_vs_stock_display . '</td>
                <td>' . $select_sgs . '</td>
                <td><input type="text" class="form-control" name="editTypeUnit[]" value="' . htmlspecialchars($row->type_unit) . '"></td>
                <td><input type="number" class="form-control" name="editQty[]" value="' . htmlspecialchars($row->qty_order ?: '') . '"></td>
                <td><input type="number" class="form-control" name="editPrice[]" value="' . htmlspecialchars($row->price ?: '') . '"></td>
                <td><input type="textarea" class="form-control" name="editDescription[]" placeholder="Keterangan" value="' . htmlspecialchars($row->description ?: '') . '"></td>
            </tr>';
            }

            if (!$found) {
                echo '<tr><td colspan="13" class="text-center text-muted">Tidak ada produk dengan Avg Sales vs Stock di bawah 1.00.</td></tr>';
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
