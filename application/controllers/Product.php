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

        // Get stock movement summary (INCLUDE PACKING LIST)
        $query = "
    SELECT
        sku,
        idgudang,
        SUM(instock) AS total_in,
        SUM(outstock) AS total_out,
        SUM(packing_list) AS total_packing_list,
        SUM(instock) + SUM(packing_list) - SUM(outstock) AS total_stok
    FROM (
        -- Instock data
        SELECT
            detail_instock.sku,
            instock.idgudang,
            detail_instock.jumlah AS instock,
            0 AS outstock,
            0 AS packing_list
        FROM detail_instock
        LEFT JOIN instock ON instock.instock_code = detail_instock.instock_code
        WHERE instock.status_verification = 1

        UNION ALL

        -- Outstock data
        SELECT
            detail_outstock.sku,
            outstock.idgudang,
            0 AS instock,
            detail_outstock.jumlah AS outstock,
            0 AS packing_list
        FROM detail_outstock
        LEFT JOIN outstock ON outstock.outstock_code = detail_outstock.outstock_code
        WHERE outstock.status_verification = 1

        UNION ALL

        -- Packing List data
        SELECT
            p.sku,
            ap.idgudang,
            0 AS instock,
            0 AS outstock,
            IFNULL(dap.qty_receive, dap.qty_order) AS packing_list
        FROM detail_analisys_po dap
        LEFT JOIN product p ON p.idproduct = dap.idproduct
        LEFT JOIN analisys_po ap ON ap.idanalisys_po = dap.idanalisys_po
        WHERE ap.status_verification = 1
        AND (dap.qty_receive > 0 OR dap.qty_order > 0)
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
        $this->db->where('sku', $sku);
        $this->db->where('status', 1);
        $cek = $this->db->get('product')->row();

        if ($cek) {
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
        $idproduct = $this->input->post('inputIdProduct');
        $sku = $this->input->post('inputSku');
        $namaProduk = $this->input->post('inputNamaProduk');
        $barcode = $this->input->post('inputBarcode');

        $produkLama = $this->db->get_where('product', ['idproduct' => $idproduct])->row();

        if (!$produkLama) {
            $this->session->set_flashdata('error', 'Produk tidak ditemukan.');
            redirect('product');
            return;
        }

        $cekBarcode = $this->db->where('barcode', $barcode)
            ->where('idproduct !=', $idproduct)
            ->get('product')->row();
        if ($cekBarcode) {
            $this->session->set_flashdata('error', 'Barcode sudah digunakan oleh produk lain.');
            redirect('product');
            return;
        }

        $gambar = $produkLama->gambar;
        if (!empty($_FILES['inputGambar']['name'])) {
            $config['upload_path'] = './assets/image/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'gambar_' . time();

            $this->upload->initialize($config);
            if ($this->upload->do_upload('inputGambar')) {
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

        $sni = $produkLama->sni;
        if (!empty($_FILES['inputSni']['name'])) {
            $config['upload_path'] = './assets/image/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'sni_' . time();

            $this->upload->initialize($config);
            if ($this->upload->do_upload('inputSni')) {
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

        $data = [
            'sku' => $sku,
            'nama_produk' => $namaProduk,
            'barcode' => $barcode,
            'gambar' => $gambar,
            'sni' => $sni,
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date("Y-m-d H:i:s")
        ];

        $this->db->where('idproduct', $idproduct);
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

    public function stockCard()
    {
        $title = 'Kartu Stok';
        $sku = $this->input->get('sku');
        $idgudang = $this->input->get('idgudang');

        $product = $this->db->where('sku', $sku)->get('product')->row();

        if (!$product) {
            show_error('Produk tidak ditemukan.');
        }

        $idproduct = $product->idproduct;

        // Query terpisah untuk menghindari masalah collation
        $instock_data = $this->db
            ->select("di.instock_code AS stock_code, i.datetime, i.distribution_date, i.kategori, 
                 'INSTOCK' AS tipe, di.jumlah AS instock, 0 AS outstock, 0 AS packing_list, 
                 i.user, di.keterangan, i.distribution_date AS order_date")
            ->from('detail_instock di')
            ->join('instock i', 'i.instock_code = di.instock_code')
            ->where('di.sku', $sku)
            ->where('i.status_verification', 1);

        if ($idgudang) {
            $instock_data->where('i.idgudang', $idgudang);
        }
        $instock_data = $instock_data->get()->result();

        $outstock_data = $this->db
            ->select("do.outstock_code AS stock_code, o.datetime, o.distribution_date, o.kategori, 
                 'OUTSTOCK' AS tipe, 0 AS instock, do.jumlah AS outstock, 0 AS packing_list, 
                 o.user, do.keterangan, o.distribution_date AS order_date")
            ->from('detail_outstock do')
            ->join('outstock o', 'o.outstock_code = do.outstock_code')
            ->where('do.sku', $sku)
            ->where('o.status_verification', 1);

        if ($idgudang) {
            $outstock_data->where('o.idgudang', $idgudang);
        }
        $outstock_data = $outstock_data->get()->result();

        $packing_list_data = $this->db
            ->select("ap.number_po AS stock_code, 
                 ap.created_date AS datetime, 
                 ap.distribution_date, 'PACKING LIST' AS kategori, 'PACKING LIST' AS tipe, 
                 0 AS instock, 0 AS outstock, 
                 COALESCE(dap.qty_receive, dap.qty_order, 0) AS packing_list, 
                 ap.user, 
                 CONCAT('Qty Order: ', COALESCE(dap.qty_order, 0), ', Qty Receive: ', COALESCE(dap.qty_receive, 0)) AS keterangan,
                 ap.order_date")
            ->from('detail_analisys_po dap')
            ->join('analisys_po ap', 'ap.idanalisys_po = dap.idanalisys_po')
            ->where('dap.idproduct', $idproduct)
            ->where('ap.status_verification', 1)
            ->where('(dap.qty_receive > 0 OR dap.qty_order > 0)', null, false);

        if ($idgudang) {
            $packing_list_data->where('ap.idgudang', $idgudang);
        }
        $packing_list_data = $packing_list_data->get()->result();

        // Gabungkan semua data
        $all_transactions = array_merge($instock_data, $outstock_data, $packing_list_data);

        // Urutkan berdasarkan tanggal distribusi dan datetime
        usort($all_transactions, function ($a, $b) {
            $dateCompare = strcmp($a->distribution_date, $b->distribution_date);
            if ($dateCompare === 0) {
                return strcmp($a->datetime, $b->datetime);
            }
            return $dateCompare;
        });

        // Hitung saldo berjalan
        $saldo = 0;
        foreach ($all_transactions as $transaction) {
            $saldo += ($transaction->instock + $transaction->packing_list - $transaction->outstock);
            $transaction->sisa = $saldo;
        }

        $gudang_list = $this->db->get('gudang')->result();

        $data = [
            'title' => $title,
            'product' => $product,
            'transaction_stock' => $all_transactions,
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
            tipe,
            instock,
            outstock,
            packing_list,
            @saldo := @saldo + IFNULL(instock,0) + IFNULL(packing_list,0) - IFNULL(outstock,0) AS sisa,
            user,
            keterangan
        FROM (
            -- Instock data
            SELECT 
                detail_instock.instock_code AS stock_code,
                instock.datetime,
                instock.distribution_date,
                instock.kategori,
                'INSTOCK' AS tipe,
                detail_instock.jumlah AS instock,
                NULL AS outstock,
                NULL AS packing_list,
                instock.user,
                detail_instock.keterangan AS keterangan
            FROM detail_instock
            LEFT JOIN instock ON instock.instock_code = detail_instock.instock_code
            WHERE detail_instock.sku = ? AND instock.idgudang = ? AND instock.status_verification = 1

            UNION ALL

            -- Outstock data
            SELECT 
                detail_outstock.outstock_code AS stock_code,
                outstock.datetime,
                outstock.distribution_date,
                outstock.kategori,
                'OUTSTOCK' AS tipe,
                NULL AS instock,
                detail_outstock.jumlah AS outstock,
                NULL AS packing_list,
                outstock.user,
                detail_outstock.keterangan AS keterangan
            FROM detail_outstock
            LEFT JOIN outstock ON outstock.outstock_code = detail_outstock.outstock_code
            WHERE detail_outstock.sku = ? AND outstock.idgudang = ? AND outstock.status_verification = 1

            UNION ALL

            -- Packing List data
            SELECT 
                ap.number_po AS stock_code,
                CONCAT(ap.order_date, ' ', ap.order_time) AS datetime,
                ap.distribution_date,
                ap.kategori,
                'PACKING LIST' AS tipe,
                NULL AS instock,
                NULL AS outstock,
                IFNULL(dap.qty_receive, dap.qty_order) AS packing_list,
                ap.user,
                CONCAT('Qty Order: ', dap.qty_order, ', Qty Receive: ', IFNULL(dap.qty_receive, 0)) AS keterangan
            FROM detail_analisys_po dap
            LEFT JOIN product p ON p.idproduct = dap.idproduct
            LEFT JOIN analisys_po ap ON ap.idanalisys_po = dap.idanalisys_po
            WHERE p.sku = ? AND ap.idgudang = ? AND ap.status_verification = 1
            AND (dap.qty_receive > 0 OR dap.qty_order > 0)
        ) AS stock_transaction
        JOIN (SELECT @saldo := 0) AS vars
        ORDER BY distribution_date ASC, datetime ASC
    ";

        $transaction_stock = $this->db->query($query, [
            $sku, $idgudang,
            $sku, $idgudang,
            $sku, $idgudang
        ])->result();

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
            tipe,
            instock,
            outstock,
            packing_list,
            @saldo := @saldo + IFNULL(instock,0) + IFNULL(packing_list,0) - IFNULL(outstock,0) AS sisa,
            user,
            keterangan
        FROM (
            -- Instock data
            SELECT 
                detail_instock.instock_code AS stock_code,
                instock.datetime,
                instock.distribution_date,
                instock.kategori,
                'INSTOCK' AS tipe,
                detail_instock.jumlah AS instock,
                NULL AS outstock,
                NULL AS packing_list,
                instock.user,
                detail_instock.keterangan AS keterangan
            FROM detail_instock
            LEFT JOIN instock ON instock.instock_code = detail_instock.instock_code
            WHERE detail_instock.sku = ? AND instock.idgudang = ? AND instock.status_verification = 1

            UNION ALL

            -- Outstock data
            SELECT 
                detail_outstock.outstock_code AS stock_code,
                outstock.datetime,
                outstock.distribution_date,
                outstock.kategori,
                'OUTSTOCK' AS tipe,
                NULL AS instock,
                detail_outstock.jumlah AS outstock,
                NULL AS packing_list,
                outstock.user,
                detail_outstock.keterangan AS keterangan
            FROM detail_outstock
            LEFT JOIN outstock ON outstock.outstock_code = detail_outstock.outstock_code
            WHERE detail_outstock.sku = ? AND outstock.idgudang = ? AND outstock.status_verification = 1

            UNION ALL

            -- Packing List data
            SELECT 
                ap.number_po AS stock_code,
                CONCAT(ap.order_date, ' ', ap.order_time) AS datetime,
                ap.distribution_date,
                ap.kategori,
                'PACKING LIST' AS tipe,
                NULL AS instock,
                NULL AS outstock,
                IFNULL(dap.qty_receive, dap.qty_order) AS packing_list,
                ap.user,
                CONCAT('Qty Order: ', dap.qty_order, ', Qty Receive: ', IFNULL(dap.qty_receive, 0)) AS keterangan
            FROM detail_analisys_po dap
            LEFT JOIN product p ON p.idproduct = dap.idproduct
            LEFT JOIN analisys_po ap ON ap.idanalisys_po = dap.idanalisys_po
            WHERE p.sku = ? AND ap.idgudang = ? AND ap.status_verification = 1
            AND (dap.qty_receive > 0 OR dap.qty_order > 0)
        ) AS stock_transaction
        JOIN (SELECT @saldo := 0) AS vars
        ORDER BY distribution_date ASC, datetime ASC
    ";

        $transaction_stock = $this->db->query($query, [
            $sku, $idgudang,
            $sku, $idgudang,
            $sku, $idgudang
        ])->result();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'Kartu Stok - Asta Homeware');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', "SKU: {$sku}");
        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', "Nama Produk: {$product->nama_produk}");

        // Table headers
        $headers = ['No', 'Kode Transaksi', 'Tipe', 'Tanggal Input', 'Tanggal Distribusi', 'Kategori', 'Stock In', 'Stock Out', 'Packing List', 'Sisa', 'User', 'Keterangan'];
        $sheet->fromArray($headers, null, 'A5');
        $sheet->getStyle('A5:L5')->getFont()->setBold(true);
        $sheet->getStyle('A5:L5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:L5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Fill data
        $rowNum = 6;
        $no = 1;
        foreach ($transaction_stock as $row) {
            $sheet->setCellValue("A{$rowNum}", $no);
            $sheet->setCellValue("B{$rowNum}", $row->stock_code);
            $sheet->setCellValue("C{$rowNum}", $row->tipe);
            $sheet->setCellValue("D{$rowNum}", $row->datetime);
            $sheet->setCellValue("E{$rowNum}", $row->distribution_date);
            $sheet->setCellValue("F{$rowNum}", $row->kategori);
            $sheet->setCellValue("G{$rowNum}", $row->instock !== null ? $row->instock : '-');
            $sheet->setCellValue("H{$rowNum}", $row->outstock !== null ? $row->outstock : '-');
            $sheet->setCellValue("I{$rowNum}", $row->packing_list !== null ? $row->packing_list : '-');
            $sheet->setCellValue("J{$rowNum}", $row->sisa);
            $sheet->setCellValue("K{$rowNum}", $row->user);
            $sheet->setCellValue("L{$rowNum}", $row->keterangan);

            $sheet->getStyle("A{$rowNum}:L{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $rowNum++;
            $no++;
        }

        // Auto size columns
        foreach (range('A', 'L') as $col) {
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
