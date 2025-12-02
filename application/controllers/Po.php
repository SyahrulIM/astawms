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
            'title' => 'Analisa PO',
            'product' => $product->result(),
            'data_trx' => $data_trx->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Po/v_po');
    }

    private function translateToEnglish($text)
    {
        try {
            $tr = new \Stichoza\GoogleTranslate\GoogleTranslate('en'); // target English
            return $tr->translate($text);
        } catch (\Exception $e) {
            // fallback ke text asli
            return $text;
        }
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
                    'product_name_en' => $product->nama_produk,
                    'last_mouth_sales' => $sale_last_month,
                    'current_month_sales' => $current_month_sales,
                    'type_unit' => 'pcs',
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

        // Baris ke-5 (index 4) berisi nama bulan
        $header_row_index = 4;
        $header = $rows3[$header_row_index];

        // Kolom terakhir adalah "Total Bulan"
        $total_column_index = count($header) - 1;

        // Loop data mulai dari baris ke-6 (index 5)
        for ($i = $header_row_index + 1; $i < count($rows3); $i++) {
            $sku = trim($rows3[$i][2]); // kolom C = "Kode #"
            if ($sku == '') continue;

            $latest_value = 0;
            $latest_month = null;

            // Loop dari kolom terakhir ke kiri, skip kolom "Total Bulan"
            for ($j = $total_column_index - 1; $j >= 3; $j--) {
                $month_name = trim($header[$j]);
                if (stripos($month_name, 'total') !== false) continue; // skip "Total Bulan"

                $cellValue = trim($rows3[$i][$j]);
                if ($cellValue === '' || $cellValue === '-' || $cellValue === '0') continue;

                $val = floatval(str_replace([',', ' '], '', $cellValue));
                if ($val > 0) {
                    $latest_value = $val;
                    $latest_month = $month_name;
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
                        'latest_incoming_stock' =>
                        '<span class="text-primary"><i class="fa-solid fa-calendar"></i> ' . $latest_month . '</span>' .
                            '<br><span class="text-success"><i class="fa-solid fa-box"></i> ' . $latest_value . ' pcs</span>'
                    ]);
                }
            }
        }

        // === Bersihkan file upload ===
        unlink($file_sale_mouth);
        unlink($file_balance_for_today);
        unlink($file_latest_incoming_stock);

        $this->session->set_flashdata('success', 'Data PO berhasil disimpan dari tiga file Excel!');
        redirect('qty');
    }

    public function get_detail_analisys_po($idanalisys_po)
    {
        $this->db->select('p.nama_produk, p.sku, d.type_sgs, d.type_unit, d.latest_incoming_stock, 
                       d.last_mouth_sales, d.current_month_sales, d.balance_per_today, d.qty_order, d.price, 
                       d.type_sgs, d.type_unit');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $idanalisys_po);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo '<table class="table table-bordered table-striped table-xl">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Product</th>
                    <th>Product Code</th>
                    <th>Last Coming</th>
                    <th>Last Sales</th>
                    <th>Current Sales</th>
                    <th>Balance</th>
                    <th>Avg Ratio</th>
                </tr>
            </thead>
            <tbody>';

            $found = false; // untuk cek apakah ada data yang lolos filter
            $no = 1;
            foreach ($query->result() as $row) {
                $total_sales = $row->current_month_sales;
                $avg_sales = $total_sales / 4;

                if ($avg_sales > 0) {
                    $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                    // filter hanya yg < 1.00
                    if ($avg_vs_stock >= 1) continue;
                    $found = true;
                    $avg_vs_stock_display = number_format($avg_vs_stock, 2);
                } else {
                    continue; // skip kalau avg_sales = 0
                }

                echo '<tr>
                <td>' . $no++ . '</td>
                <td>' . htmlspecialchars($row->nama_produk) . '</td>
                <td>' . htmlspecialchars($row->sku) . '</td>
                <td>' . $row->latest_incoming_stock . '</td>
                <td>' . htmlspecialchars($row->last_mouth_sales) . '</td>
                <td>' . htmlspecialchars($row->current_month_sales) . '</td>
                <td>' . htmlspecialchars($row->balance_per_today) . '</td>
                <td>' . $avg_vs_stock_display . '</td>
            </tr>';
            }

            if (!$found) {
                echo '<tr><td colspan="12" class="text-center text-muted">Tidak ada produk dengan Avg Sales vs Stock di bawah 1.00.</td></tr>';
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
