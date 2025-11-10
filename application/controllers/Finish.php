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
        $this->db->where('status_progress', 'Finish');
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
        // Ambil data header PO
        $this->db->where('idanalisys_po', $idanalisys_po);
        $header_data = $this->db->get('analisys_po')->row();

        // Ambil data detail PO
        $this->db->select('d.idanalisys_po, p.nama_produk, p.sku, d.type_sgs, d.type_unit, d.latest_incoming_stock, 
                   d.last_mouth_sales, d.current_month_sales, d.balance_per_today, d.qty_order, d.price, d.description');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $idanalisys_po);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            // Tampilkan informasi header PO
            echo '<div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="fa-solid fa-file-invoice me-2"></i>Informasi PO</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Nomor PO:</strong><br>
                        ' . ($header_data->number_po ? htmlspecialchars($header_data->number_po) : '<span class="text-muted">-</span>') . '
                    </div>
                    <div class="col-md-3">
                        <strong>Tanggal Pesan:</strong><br>
                        ' . ($header_data->order_date ? htmlspecialchars($header_data->order_date) : '<span class="text-muted">-</span>') . '
                    </div>
                    <div class="col-md-3">
                        <strong>Container:</strong><br>
                        ' . ($header_data->name_container ? htmlspecialchars($header_data->name_container) : '<span class="text-muted">-</span>') . '
                    </div>
                    <div class="col-md-3">
                        <strong>Mata Uang:</strong><br>
                        ' . ($header_data->money_currency ? strtoupper(htmlspecialchars($header_data->money_currency)) : '<span class="text-muted">-</span>') . '
                    </div>
                </div>
            </div>
        </div>';

            echo '<div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th>SGS/Non-SGS</th>
                    <th>Tipe Satuan</th>
                    <th>Stock Masuk Terakhir</th>
                    <th>Penjualan Bulan Lalu</th>
                    <th>Penjualan Bulan Ini</th>
                    <th>Saldo Hari Ini</th>
                    <th>Avg Sales vs Stock (Bulan)</th>
                    <th>Qty Order</th>
                    <th>Price per Unit</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>';

            $no = 1;
            $total_qty = 0;
            $total_value = 0;
            $found = false;

            foreach ($query->result() as $row) {
                // Hitung rata-rata penjualan per minggu
                $total_sales = floatval($row->current_month_sales);
                $avg_sales = $total_sales / 4;

                // Filter: hanya tampilkan jika Avg Sales vs Stock di bawah 1
                if ($avg_sales > 0) {
                    $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                    if ($avg_vs_stock >= 1) {
                        continue; // Skip produk dengan avg >= 1
                    }
                    $avg_vs_stock_display = number_format($avg_vs_stock, 2);
                    $found = true;
                } else {
                    continue; // Skip produk dengan avg_sales = 0
                }

                // Hitung total
                $item_value = floatval($row->qty_order) * floatval($row->price);
                $total_qty += floatval($row->qty_order);
                $total_value += $item_value;

                echo '<tr>
                <td class="text-center">' . $no++ . '</td>
                <td>' . htmlspecialchars($row->nama_produk) . '</td>
                <td>' . htmlspecialchars($row->sku) . '</td>
                <td class="text-center">' . ($row->type_sgs ? htmlspecialchars($row->type_sgs) : '-') . '</td>
                <td class="text-center">' . ($row->type_unit ? htmlspecialchars($row->type_unit) : '-') . '</td>
                <td class="text-center">' . ($row->latest_incoming_stock ? htmlspecialchars($row->latest_incoming_stock) : '-') . '</td>
                <td class="text-center">' . ($row->last_mouth_sales ? htmlspecialchars($row->last_mouth_sales) : '-') . '</td>
                <td class="text-center">' . ($row->current_month_sales ? htmlspecialchars($row->current_month_sales) : '-') . '</td>
                <td class="text-center">' . ($row->balance_per_today ? htmlspecialchars($row->balance_per_today) : '-') . '</td>
                <td class="text-center text-danger fw-bold">' . $avg_vs_stock_display . '</td>
                <td class="text-center">' . ($row->qty_order ? number_format($row->qty_order) : '0') . '</td>
                <td class="text-end">' . ($row->price ? number_format($row->price, 2) : '0.00') . '</td>
                <td>' . ($row->description ? htmlspecialchars($row->description) : '-') . '</td>
            </tr>';
            }

            if (!$found) {
                echo '<tr>
                <td colspan="13" class="text-center text-muted py-4">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Tidak ada produk dengan Avg Sales vs Stock di bawah 1.00.
                </td>
            </tr>';
            } else {
                // Tampilkan total hanya jika ada produk yang ditampilkan
                echo '<tr class="table-info fw-bold">
                <td colspan="10" class="text-end"><strong>TOTAL:</strong></td>
                <td class="text-center">' . number_format($total_qty) . '</td>
                <td class="text-end">' . number_format($total_value, 2) . '</td>
                <td></td>
            </tr>';
            }

            echo '</tbody></table></div>';

            // Tombol export PDF hanya jika ada produk yang ditampilkan
            if ($found) {
                echo '<div class="mt-4 text-center">
                <a href="' . base_url('finish/exportPdf?id=' . $idanalisys_po) . '" class="btn btn-danger" target="_blank">
                    <i class="fa-solid fa-file-pdf me-2"></i>Export to PDF
                </a>
            </div>';
            }
        } else {
            echo '<div class="alert alert-warning text-center">
            <i class="fa-solid fa-exclamation-triangle me-2"></i>
            Tidak ada produk dalam analisis PO ini.
        </div>';
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

        // Ambil data header PO
        $this->db->where('idanalisys_po', $id);
        $data_po = $this->db->get('analisys_po');

        // Ambil data detail PO dengan filter Avg Sales vs Stock < 1
        $this->db->select('d.idanalisys_po, p.nama_produk, p.sku, d.type_sgs, d.type_unit, d.latest_incoming_stock, 
                   d.last_mouth_sales, d.current_month_sales, d.balance_per_today, d.qty_order, d.price, d.description');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $id);
        $query = $this->db->get();

        // Filter data yang akan ditampilkan (Avg Sales vs Stock < 1)
        $filtered_detail_po = [];
        $total_qty = 0;
        $total_value = 0;

        foreach ($query->result() as $row) {
            // Hitung rata-rata penjualan per minggu
            $total_sales = floatval($row->current_month_sales);
            $avg_sales = $total_sales / 4;

            // Filter: hanya ambil jika Avg Sales vs Stock di bawah 1
            if ($avg_sales > 0) {
                $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                if ($avg_vs_stock < 1) {
                    // Hitung nilai item
                    $item_value = floatval($row->qty_order) * floatval($row->price);

                    // Tambahkan data yang difilter
                    $filtered_detail_po[] = [
                        'row' => $row,
                        'avg_vs_stock' => number_format($avg_vs_stock, 2),
                        'item_value' => $item_value
                    ];

                    // Akumulasi total
                    $total_qty += floatval($row->qty_order);
                    $total_value += $item_value;
                }
            }
        }

        $data = [
            'title' => 'Export Pre-Order',
            'po' => $data_po->row(),
            'detail_po' => $filtered_detail_po,
            'total_qty' => $total_qty,
            'total_value' => $total_value
        ];

        $this->load->view('finish/v_pdf', $data);
    }
}
