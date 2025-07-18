<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Outstock extends CI_Controller
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
        $title = 'Barang Keluar';
        $product = $this->db->get('product');
        $outStock = $this->db->get('outstock');
        $gudang = $this->db->get('gudang');
        $data = [
            'title' => $title,
            'product' => $product->result(),
            'outstock' => $outStock->result(),
            'gudang' => $gudang->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Outstock/v_outstock');
    }

    public function stockOut()
    {
        $inputGudang = $this->input->post('inputGudang');
        $inputSKU = $this->input->post('inputSKU');
        $inputNamaProduk = $this->input->post('inputNamaProduk');
        $inputJumlah = $this->input->post('inputJumlah');
        $inputKeterangan = $this->input->post('inputKeterangan');
        $inputOutstockCode = "TSC" . date("Ymd") . date("His");
        $inputTglTerima = date("Y-m-d");
        $inputJamTerima = date("H:i:s");
        $inputDatetime = $this->input->post('inputDatetime');
        $inputNo = $this->input->post('inputNo');
        $inputType = $this->input->post('inputType');
        $inputDistribution  = $this->input->post('inputDistribution');
        if (empty($inputDatetime)) {
            $inputDatetime = date("Y-m-d H:i:s");
        }
        $inputUser = $this->session->userdata('username');
        $inputKategori = $this->input->post('inputKategori');

        // Menambahkan status_verification = 0 untuk transaksi ini
        $data_outstock = [
            'idgudang' => $inputGudang,
            'outstock_code' => $inputOutstockCode,
            'tgl_keluar' => $inputTglTerima,
            'jam_keluar' => $inputJamTerima,
            'datetime' => $inputDatetime,
            'user' => $inputUser,
            'kategori' => $inputKategori,
            'no_manual' => $inputNo,
            'outstock_type' => $inputType,
            'distribution_date' => $inputDistribution,
            'status_verification' => 0,  // Status verification yang baru
            'created_by' => $this->session->userdata('username'),
            'created_date' => date("Y-m-d H:i:s"),
        ];
        $this->db->insert('outstock', $data_outstock);

        foreach ($inputSKU as $key => $sku) {
            // Simpan detail outstock
            $data_detail_outstock = [
                'outstock_code' => $inputOutstockCode,
                'sku' => $sku,
                'nama_produk' => $inputNamaProduk[$key],
                'jumlah' => (int)$inputJumlah[$key],
                'sisa' => 0,
                'keterangan' => $inputKeterangan[$key],
            ];
            $this->db->insert('detail_outstock', $data_detail_outstock);
        }

        // Start WhatsApp API
        $this->db->select('handphone');
        $this->db->from('user');
        $this->db->where('idrole', 3);
        $this->db->where('is_whatsapp', 1);
        $this->db->where('status', 1);
        $this->db->where('handphone IS NOT NULL');
        $query = $this->db->get();
        $results = $query->result();

        $targets = array_column($results, 'handphone');
        $target = count($targets) > 1 ? implode(',', $targets) : (count($targets) === 1 ? $targets[0] : '');

        if ($target !== '') {
            $token = 'EyuhsmTqzeKaDknoxdxt';
            $message = 'Transaksi barang keluar dengan kode outstock ' . $inputOutstockCode .
                (strlen($inputNo) > 0 ? ' dan nomor ' . $inputNo : '') .
                ' telah dibuat oleh ' . $this->session->userdata('username') .
                '. Admin Stock dimohon untuk segera melakukan pengecekan verifikasi transaksi.';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => array(
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            echo $response;
        }
        // End

        redirect('outstock');
    }

    public function detail_outstock()
    {
        $outstock_code = $this->input->get('outstock_code');

        $data_detail_outstock = $this->db
            ->select('detail_outstock.*, outstock.tgl_keluar, outstock.jam_keluar, outstock.user, outstock.kategori, gudang.nama_gudang as nama_gudang, product.nama_produk as nama_produk, outstock.no_manual as no_manual')
            ->from('detail_outstock')
            ->join('outstock', 'detail_outstock.outstock_code = outstock.outstock_code')
            ->join('gudang', 'outstock.idgudang = gudang.idgudang')
            ->join('product', 'detail_outstock.sku = product.sku') // JOIN tambahan ke product
            ->where('detail_outstock.outstock_code', $outstock_code)
            ->get()
            ->result();

        $no_manual = isset($data_detail_outstock[0]) ? $data_detail_outstock[0]->no_manual : '-';

        $title = $outstock_code;
        $data = [
            'title' => $title,
            'outstock_code' => $outstock_code,
            'detailoutstock' => $data_detail_outstock,
            'no_manual' => $no_manual
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Outstock/v_detail_outstock');
    }

    public function exportExcel()
    {
        $outstock_code = $this->input->get('outstock_code');

        $detail_outstock = $this->db
            ->select('detail_outstock.*, outstock.tgl_keluar, outstock.jam_keluar, outstock.user, outstock.kategori, gudang.nama_gudang as nama_gudang, product.nama_produk as nama_produk, outstock.no_manual as no_manual')
            ->from('detail_outstock')
            ->join('outstock', 'detail_outstock.outstock_code = outstock.outstock_code')
            ->join('gudang', 'outstock.idgudang = gudang.idgudang')
            ->join('product', 'detail_outstock.sku = product.sku')
            ->where('detail_outstock.outstock_code', $outstock_code)
            ->get()
            ->result();

        $no_manual = isset($detail_outstock[0]) ? $detail_outstock[0]->no_manual : '-';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Barang Keluar - Asta Homeware');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', "Kode Barang Keluar: {$outstock_code}");
        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', "Nomer: {$no_manual}");

        // Table header
        $header = ['No', 'SKU', 'Nama Produk', 'Gudang', 'Jumlah', 'Keterangan'];
        $sheet->fromArray($header, null, 'A5');
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        $sheet->getStyle('A5:F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:F5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data rows
        $row = 6;
        $no = 1;
        foreach ($detail_outstock as $item) {
            $sheet->setCellValue("A{$row}", $no);
            $sheet->setCellValue("B{$row}", $item->sku);
            $sheet->setCellValue("C{$row}", $item->nama_produk);
            $sheet->setCellValue("D{$row}", $item->nama_gudang);
            $sheet->setCellValue("E{$row}", $item->jumlah);
            $sheet->setCellValue("F{$row}", $item->keterangan);

            $sheet->getStyle("A{$row}:F{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $row++;
            $no++;
        }

        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Barang_Keluar_{$outstock_code}.xlsx";

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
