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
        $this->load->library('upload');
        $this->load->helper('file');
        require_once FCPATH . 'vendor/autoload.php';

        $upload_path = './assets/excel/';
        if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => '*',
            'max_size' => 2048,
            'encrypt_name' => TRUE
        ];
        $this->upload->initialize($config);

        // === Upload sale_mouth ===
        if (!$this->upload->do_upload('sale_mouth')) {
            $this->session->set_flashdata('error', 'Gagal upload file penjualan: ' . $this->upload->display_errors());
            redirect('po');
        }
        $file_sale_mouth = $this->upload->data('full_path');

        // === Upload balance_for_today ===
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('balance_for_today')) {
            $this->session->set_flashdata('error', 'Gagal upload file saldo: ' . $this->upload->display_errors());
            redirect('po');
        }
        $file_balance_for_today = $this->upload->data('full_path');

        // === Upload latest_incoming_stock ===
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('latest_incoming_stock')) {
            $this->session->set_flashdata('error', 'Gagal upload file pembelian: ' . $this->upload->display_errors());
            redirect('po');
        }
        $file_latest_incoming_stock = $this->upload->data('full_path');

        // === Simpan data utama analisys_po ===
        $data = [
            'status_progress' => 'Listing',
            'created_by' => $this->session->userdata('username'),
            'created_date' => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date("Y-m-d H:i:s"),
            'status' => 1
        ];
        $this->db->insert('analisys_po', $data);
        $idanalisys_po = $this->db->insert_id();

        // === File 1: sale_mouth ===
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_sale_mouth);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        for ($i = 1; $i < count($rows); $i++) {
            $sku = trim($rows[$i][2]);
            $sale_last_month = floatval($rows[$i][3]);
            $current_month_sales = floatval($rows[$i][4]);

            $product = $this->db->get_where('product', ['sku' => $sku])->row();
            if ($product) {
                $data_detail = [
                    'idanalisys_po' => $idanalisys_po,
                    'idproduct' => $product->idproduct,
                    'last_mouth_sales' => $sale_last_month,
                    'current_month_sales' => $current_month_sales,
                    'sale_week_one' => 0,
                    'sale_week_two' => 0,
                    'sale_week_three' => 0,
                    'sale_week_four' => 0,
                    'balance_per_today' => 0,
                    'latest_incoming_stock' => 0
                ];
                $this->db->insert('detail_analisys_po', $data_detail);
            }
        }

        // === File 2: balance_for_today ===
        $spreadsheet2 = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_balance_for_today);
        $rows2 = $spreadsheet2->getActiveSheet()->toArray();

        for ($i = 1; $i < count($rows2); $i++) {
            $sku = trim($rows2[$i][2]); // kolom C
            $balance_today = floatval($rows2[$i][8]); // kolom I

            $product = $this->db->get_where('product', ['sku' => $sku])->row();
            if ($product) {
                $this->db->where([
                    'idanalisys_po' => $idanalisys_po,
                    'idproduct' => $product->idproduct
                ])->update('detail_analisys_po', [
                    'balance_per_today' => $balance_today
                ]);
            }
        }

        // === File 3: latest_incoming_stock ===
        $spreadsheet3 = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_latest_incoming_stock);
        $sheet3 = $spreadsheet3->getActiveSheet();
        $rows3 = $sheet3->toArray();

        $header = $rows3[0];
        $total_column_index = count($header) - 1; // Kolom "Total Bulan"

        for ($i = 1; $i < count($rows3); $i++) {
            $sku = trim($rows3[$i][1]); // kolom B (Kode #)
            if ($sku == '') continue;

            $latest_value = 0;
            $latest_month = null;

            // Cari kolom bulan terakhir dengan nilai > 0 (lewati kolom total)
            for ($j = $total_column_index - 1; $j >= 2; $j--) {
                $val = floatval(str_replace(',', '', $rows3[$i][$j]));
                if ($val > 0) {
                    $latest_value = $val;
                    $latest_month = $header[$j]; // ambil nama bulan dari header
                    break;
                }
            }

            if ($latest_value > 0 && $latest_month) {
                $product = $this->db->get_where('product', ['sku' => $sku])->row();
                if ($product) {
                    $this->db->where([
                        'idanalisys_po' => $idanalisys_po,
                        'idproduct' => $product->idproduct
                    ])->update('detail_analisys_po', [
                        'latest_incoming_stock' => $latest_month . ' - ' . $latest_value,
                    ]);
                }
            }
        }

        // === Bersihkan file upload ===
        unlink($file_sale_mouth);
        unlink($file_balance_for_today);
        unlink($file_latest_incoming_stock);

        $this->session->set_flashdata('success', 'Data PO berhasil disimpan dari tiga file Excel!');
        redirect('po');
    }

    public function get_detail_analisys_po($idanalisys_po)
    {
        $this->db->select('p.nama_produk, p.sku, d.type_sgs, d.type_unit, d.latest_incoming_stock, 
                       d.last_mouth_sales, d.sale_week_one, d.sale_week_two, d.sale_week_three, 
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
                    <td>' . htmlspecialchars($row->last_mouth_sales) . '</td>
                    <td>' . htmlspecialchars($row->sale_week_one) . '</td>
                    <td>' . htmlspecialchars($row->sale_week_two) . '</td>
                    <td>' . htmlspecialchars($row->sale_week_three) . '</td>
                    <td>' . htmlspecialchars($row->sale_week_four) . '</td>
                    <td>' . htmlspecialchars($row->balance_per_today) . '</td>
                    <td>' . $avg_vs_stock . '</td>
                    <td>' . ($row->qty_order > 0 ? htmlspecialchars($row->qty_order) : '<span class="text-muted">Qty Order belum diproses</span>') . '</td>
                    <td>' . ($row->price > 0 ? htmlspecialchars($row->price) : '<span class="text-muted">Pre-Order belum diproses</span>') . '</td>
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
        redirect('po');
    }
}
