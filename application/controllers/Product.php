<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Product extends CI_Controller
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
        $title = 'Product';
        $products = $this->db->where('status', 1)->get('product')->result();
        $gudangs = $this->db->get('gudang')->result();

        // Map idproduct to sku and sku to idproduct
        $skuToIdProduct = [];
        foreach ($products as $p) {
            $skuToIdProduct[$p->sku] = $p->idproduct;
        }

        // Get stock movement summary
        $query = "
        SELECT
            sku,
            idgudang,
            SUM(instock) AS total_in,
            SUM(outstock) AS total_out,
            SUM(instock) - SUM(outstock) AS total_stok
        FROM (
            SELECT
                detail_instock.sku,
                instock.idgudang,
                detail_instock.jumlah AS instock,
                0 AS outstock
            FROM detail_instock
            LEFT JOIN instock ON instock.instock_code = detail_instock.instock_code
            WHERE instock.status_verification = 1

            UNION ALL

            SELECT
                detail_outstock.sku,
                outstock.idgudang,
                0 AS instock,
                detail_outstock.jumlah AS outstock
            FROM detail_outstock
            LEFT JOIN outstock ON outstock.outstock_code = detail_outstock.outstock_code
            WHERE outstock.status_verification = 1
        ) AS stock_movements
        GROUP BY sku, idgudang
    ";

        $stock_data = $this->db->query($query)->result();

        // Create stokMap using idproduct instead of sku
        $stokMap = [];
        $totalStokAllGudang = []; // New array to store total stock across all warehouses
        foreach ($stock_data as $row) {
            if (isset($skuToIdProduct[$row->sku])) {
                $idproduct = $skuToIdProduct[$row->sku];
                $stokMap[$idproduct][$row->idgudang] = $row->total_stok;

                // Calculate total stock for each product across all warehouses
                if (!isset($totalStokAllGudang[$idproduct])) {
                    $totalStokAllGudang[$idproduct] = 0;
                }
                $totalStokAllGudang[$idproduct] += $row->total_stok;
            }
        }

        $data = [
            'title' => $title,
            'product' => $products,
            'gudang' => $gudangs,
            'stokMap' => $stokMap,
            'totalStokAllGudang' => $totalStokAllGudang // Pass to the view
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Product/v_product');
    }

    public function addProduct()
    {
        $this->load->library('upload');

        $sku = $this->input->post('inputSku');
        $namaProduk = $this->input->post('inputNamaProduk');
        $barcode = $this->input->post('inputBarcode');

        // Cek duplikasi SKU atau Barcode
        $cek = $this->db->get_where('product', [
            'sku' => $sku
        ])->row();

        if ($cek || $this->db->get_where('product', ['barcode' => $barcode])->row()) {
            $this->session->set_flashdata('error', 'Produk dengan SKU atau Barcode tersebut sudah ada.');
            redirect('product');
            return;
        }

        // Upload gambar
        $gambar = '';
        if (!empty($_FILES['inputGambar']['name'])) {
            $config['upload_path'] = './assets/image/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'gambar_' . time();

            $this->upload->initialize($config);
            if ($this->upload->do_upload('inputGambar')) {
                $gambar = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('product');
                return;
            }
        }

        // Upload SNI
        $sni = '';
        if (!empty($_FILES['inputSni']['name'])) {
            $config['upload_path'] = './assets/image/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'sni_' . time();

            $this->upload->initialize($config);
            if ($this->upload->do_upload('inputSni')) {
                $sni = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('product');
                return;
            }
        }

        $data = [
            'sku' => $sku,
            'nama_produk' => $namaProduk,
            'gambar' => $gambar,
            'barcode' => $barcode,
            'sni' => $sni,
            'created_by' => $this->session->userdata('username'),
            'created_date' => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date("Y-m-d H:i:s"),
        ];

        $this->db->insert('product', $data);

        $idproduct = $this->db->insert_id();

        $gudangList = $this->db->get('gudang')->result();
        foreach ($gudangList as $gudang) {
            $this->db->insert('product_stock', [
                'idproduct' => $idproduct,
                'idgudang' => $gudang->idgudang,
                'stok' => 0
            ]);
        }

        $this->session->set_flashdata('success', 'Produk berhasil ditambahkan.');
        redirect('product');
    }

    public function editProduct()
    {
        $this->load->library('upload');

        $sku = $this->input->post('inputSku');
        $namaProduk = $this->input->post('inputNamaProduk');
        $barcode = $this->input->post('inputBarcode');

        // Ambil data produk saat ini
        $produkLama = $this->db->get_where('product', ['sku' => $sku])->row();

        if (!$produkLama) {
            $this->session->set_flashdata('error', 'Produk tidak ditemukan.');
            redirect('product');
            return;
        }

        // Cek duplikasi barcode (selain milik produk ini)
        $cekBarcode = $this->db->where('barcode', $barcode)
            ->where('sku !=', $sku)
            ->get('product')->row();
        if ($cekBarcode) {
            $this->session->set_flashdata('error', 'Barcode sudah digunakan oleh produk lain.');
            redirect('product');
            return;
        }

        // Upload gambar
        $gambar = $produkLama->gambar;
        if (!empty($_FILES['inputGambar']['name'])) {
            $config['upload_path'] = './assets/image/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'gambar_' . time();

            $this->upload->initialize($config);
            if ($this->upload->do_upload('inputGambar')) {
                // Hapus gambar lama kalau ada
                if ($gambar && file_exists('./assets/image/' . $gambar)) {
                    unlink('./assets/image/' . $gambar);
                }
                $gambar = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('product');
                return;
            }
        }

        // Upload SNI
        $sni = $produkLama->sni;
        if (!empty($_FILES['inputSni']['name'])) {
            $config['upload_path'] = './assets/image/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'sni_' . time();

            $this->upload->initialize($config);
            if ($this->upload->do_upload('inputSni')) {
                // Hapus SNI lama kalau ada
                if ($sni && file_exists('./assets/image/' . $sni)) {
                    unlink('./assets/image/' . $sni);
                }
                $sni = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('product');
                return;
            }
        }

        // Siapkan data yang akan diupdate
        $data = [
            'nama_produk' => $namaProduk,
            'barcode' => $barcode,
            'gambar' => $gambar,
            'sni' => $sni,
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date("Y-m-d H:i:s")
        ];

        // Update data berdasarkan SKU
        $this->db->where('sku', $sku);
        $this->db->update('product', $data);

        $this->session->set_flashdata('success', 'Produk berhasil diupdate.');
        redirect('product');
    }

    public function deleteProduct()
    {
        $idproduct = $this->input->get('idproduct');
        if ($idproduct) {
            $this->db->where('idproduct', $idproduct)->update('product', ['status' => 0]);
        }
        redirect('product');
    }

    // public function stockCard()
    // {
    //     $title = 'Kartu Stok';
    //     $sku = $this->input->get('sku');
    //     $idgudang = $this->input->get('idgudang');

    //     $product = $this->db->where('sku', $sku)->get('product')->row();

    //     if (!$product) {
    //         show_error('Produk tidak ditemukan.');
    //     }

    //     $query = "
    //     SELECT 
    //         stock_code,
    //         datetime,
    //         kategori,
    //         instock,
    //         outstock,
    //         @saldo := @saldo + IFNULL(instock,0) - IFNULL(outstock,0) AS sisa,
    //         user,
    //         keterangan
    //     FROM (
    //         SELECT 
    //             detail_instock.instock_code AS stock_code,
    //             instock.datetime,
    //             instock.kategori,
    //             detail_instock.jumlah AS instock,
    //             NULL AS outstock,
    //             instock.user,
    //             detail_instock.keterangan AS keterangan
    //         FROM detail_instock
    //         LEFT JOIN instock ON instock.instock_code = detail_instock.instock_code
    //         WHERE detail_instock.sku = ? AND instock.idgudang = ? AND instock.status_verification = 1

    //         UNION ALL

    //         SELECT 
    //             detail_outstock.outstock_code AS stock_code,
    //             outstock.datetime,
    //             outstock.kategori,
    //             NULL AS instock,
    //             detail_outstock.jumlah AS outstock,
    //             outstock.user,
    //             detail_outstock.keterangan AS keterangan
    //         FROM detail_outstock
    //         LEFT JOIN outstock ON outstock.outstock_code = detail_outstock.outstock_code
    //         WHERE detail_outstock.sku = ? AND outstock.idgudang = ? AND outstock.status_verification = 1
    //     ) AS stock_transaction
    //     JOIN (SELECT @saldo := 0) AS vars
    //     ORDER BY datetime
    //     ";

    //     $transaction_stock = $this->db->query($query, [$sku, $idgudang, $sku, $idgudang])->result();

    //     $gudang_list = $this->db->get('gudang')->result();

    //     $data = [
    //         'title' => $title,
    //         'product' => $product,
    //         'transaction_stock' => $transaction_stock,
    //         'gudang_list' => $gudang_list,
    //         'selected_gudang' => $idgudang
    //     ];

    //     $this->load->view('theme/v_head', $data);
    //     $this->load->view('Product/v_stock_card');
    // }

    public function stockCard()
    {
        $title = 'Kartu Stok';
        $sku = $this->input->get('sku');
        $idgudang = $this->input->get('idgudang');

        $product = $this->db->where('sku', $sku)->get('product')->row();

        if (!$product) {
            show_error('Produk tidak ditemukan.');
        }

        $query = "
            SELECT 
                stock_code,
                datetime,
                distribution_date,
                kategori,
                instock,
                outstock,
                @saldo := @saldo + IFNULL(instock,0) - IFNULL(outstock,0) AS sisa,
                user,
                keterangan
            FROM (
                SELECT 
                    detail_instock.instock_code AS stock_code,
                    instock.datetime,
                    instock.distribution_date,
                    instock.kategori,
                    detail_instock.jumlah AS instock,
                    NULL AS outstock,
                    instock.user,
                    detail_instock.keterangan AS keterangan
                FROM detail_instock
                LEFT JOIN instock ON instock.instock_code = detail_instock.instock_code
                WHERE detail_instock.sku = ? AND instock.idgudang = ? AND instock.status_verification = 1

                UNION ALL

                SELECT 
                    detail_outstock.outstock_code AS stock_code,
                    outstock.datetime,
                    outstock.distribution_date,
                    outstock.kategori,
                    NULL AS instock,
                    detail_outstock.jumlah AS outstock,
                    outstock.user,
                    detail_outstock.keterangan AS keterangan
                FROM detail_outstock
                LEFT JOIN outstock ON outstock.outstock_code = detail_outstock.outstock_code
                WHERE detail_outstock.sku = ? AND outstock.idgudang = ? AND outstock.status_verification = 1
            ) AS stock_transaction
            JOIN (SELECT @saldo := 0) AS vars
            ORDER BY distribution_date ASC
        ";

        $transaction_stock = $this->db->query($query, [$sku, $idgudang, $sku, $idgudang])->result();

        $gudang_list = $this->db->get('gudang')->result();

        $data = [
            'title' => $title,
            'product' => $product,
            'transaction_stock' => $transaction_stock,
            'gudang_list' => $gudang_list,
            'selected_gudang' => $idgudang
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Product/v_stock_card');
    }

    public function exportPdf()
    {
        $title = 'Kartu Stok';
        $sku = $this->input->get('sku');
        $idgudang = $this->input->get('idgudang');  // Get selected warehouse ID

        $product = $this->db->where('sku', $sku)->get('product')->row();

        if (!$product) {
            show_error('Produk tidak ditemukan.');
        }

        $query = "
            SELECT 
                stock_code,
                datetime,
                distribution_date,
                kategori,
                instock,
                outstock,
                @saldo := @saldo + IFNULL(instock,0) - IFNULL(outstock,0) AS sisa,
                user,
                keterangan
            FROM (
                SELECT 
                    detail_instock.instock_code AS stock_code,
                    instock.datetime,
                    instock.distribution_date,
                    instock.kategori,
                    detail_instock.jumlah AS instock,
                    NULL AS outstock,
                    instock.user,
                    detail_instock.keterangan AS keterangan
                FROM detail_instock
                LEFT JOIN instock ON instock.instock_code = detail_instock.instock_code
                WHERE detail_instock.sku = ? AND instock.idgudang = ? AND instock.status_verification = 1

                UNION ALL

                SELECT 
                    detail_outstock.outstock_code AS stock_code,
                    outstock.datetime,
                    outstock.distribution_date,
                    outstock.kategori,
                    NULL AS instock,
                    detail_outstock.jumlah AS outstock,
                    outstock.user,
                    detail_outstock.keterangan AS keterangan
                FROM detail_outstock
                LEFT JOIN outstock ON outstock.outstock_code = detail_outstock.outstock_code
                WHERE detail_outstock.sku = ? AND outstock.idgudang = ? AND outstock.status_verification = 1
            ) AS stock_transaction
            JOIN (SELECT @saldo := 0) AS vars
            ORDER BY distribution_date ASC
        ";

        $transaction_stock = $this->db->query($query, [$sku, $idgudang, $sku, $idgudang])->result();

        $data = [
            'title' => $title,
            'product' => $product,
            'transaction_stock' => $transaction_stock,
        ];

        $this->load->view('Product/v_print_pdf', $data);
    }

    public function exportExcel()
    {
        $sku = $this->input->get('sku');
        $idgudang = $this->input->get('idgudang');

        $product = $this->db->where('sku', $sku)->get('product')->row();

        if (!$product) {
            show_error('Produk tidak ditemukan.');
        }

        $query = "
            SELECT 
                stock_code,
                datetime,
                distribution_date,
                kategori,
                instock,
                outstock,
                @saldo := @saldo + IFNULL(instock,0) - IFNULL(outstock,0) AS sisa,
                user,
                keterangan
            FROM (
                SELECT 
                    detail_instock.instock_code AS stock_code,
                    instock.datetime,
                    instock.distribution_date,
                    instock.kategori,
                    detail_instock.jumlah AS instock,
                    NULL AS outstock,
                    instock.user,
                    detail_instock.keterangan AS keterangan
                FROM detail_instock
                LEFT JOIN instock ON instock.instock_code = detail_instock.instock_code
                WHERE detail_instock.sku = ? AND instock.idgudang = ? AND instock.status_verification = 1

                UNION ALL

                SELECT 
                    detail_outstock.outstock_code AS stock_code,
                    outstock.datetime,
                    outstock.distribution_date,
                    outstock.kategori,
                    NULL AS instock,
                    detail_outstock.jumlah AS outstock,
                    outstock.user,
                    detail_outstock.keterangan AS keterangan
                FROM detail_outstock
                LEFT JOIN outstock ON outstock.outstock_code = detail_outstock.outstock_code
                WHERE detail_outstock.sku = ? AND outstock.idgudang = ? AND outstock.status_verification = 1
            ) AS stock_transaction
            JOIN (SELECT @saldo := 0) AS vars
            ORDER BY distribution_date ASC
        ";

        $transaction_stock = $this->db->query($query, [$sku, $idgudang, $sku, $idgudang])->result();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'Kartu Stok - Asta Homeware');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', "SKU: {$sku}");
        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A3', "Nama Produk: {$product->nama_produk}");

        // Table headers
        $headers = ['No', 'Kode Transaksi', 'Tanggal Input', 'Tanggal Distribusi', 'Kategori', 'Stock In', 'Stock Out', 'Sisa', 'User'];
        $sheet->fromArray($headers, null, 'A5');
        $sheet->getStyle('A5:I5')->getFont()->setBold(true);
        $sheet->getStyle('A5:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:I5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Fill data
        $rowNum = 6;
        $no = 1;
        foreach ($transaction_stock as $row) {
            $sheet->setCellValue("A{$rowNum}", $no);
            $sheet->setCellValue("B{$rowNum}", $row->stock_code);
            $sheet->setCellValue("C{$rowNum}", $row->datetime);
            $sheet->setCellValue("D{$rowNum}", $row->distribution_date);
            $sheet->setCellValue("E{$rowNum}", $row->kategori);
            $sheet->setCellValue("F{$rowNum}", $row->instock !== null ? $row->instock : '-');
            $sheet->setCellValue("G{$rowNum}", $row->outstock !== null ? $row->outstock : '-');
            $sheet->setCellValue("H{$rowNum}", $row->sisa);
            $sheet->setCellValue("I{$rowNum}", $row->user);

            $sheet->getStyle("A{$rowNum}:I{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $rowNum++;
            $no++;
        }

        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "kartu_stok_{$sku}.xlsx";

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
