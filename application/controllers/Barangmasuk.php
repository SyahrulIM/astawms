<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
class Barangmasuk extends CI_Controller
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
        $title = 'Barang Masuk';
        $product = $this->db->get('product');
        $inStock = $this->db->get('instock');
        $gudang = $this->db->get('gudang');
        $data = [
            'title' => $title,
            'product' => $product->result(),
            'instock' => $inStock->result(),
            'gudang' => $gudang->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('barangMasuk/v_barang_masuk');
    }

    public function stockIn()
    {
        $inputGudang = $this->input->post('inputGudang');
        $inputSKU = $this->input->post('inputSKU');
        $inputNamaProduk = $this->input->post('inputNamaProduk');
        $inputJumlah = $this->input->post('inputJumlah');
        $inputKeterangan = $this->input->post('inputKeterangan');
        $inputInstockCode = "TSC" . date("Ymd") . date("His");
        $inputTglTerima = date("Y-m-d");
        $inputJamTerima = date("H:i:s");
        $inputDatetime = $this->input->post('inputDatetime');
        $inputNo = $this->input->post('inputNo');
        $inputDistribution  = $this->input->post('inputDistribution');
        if (empty($inputDatetime)) {
            $inputDatetime = date("Y-m-d H:i:s");
        }
        $inputUser = $this->session->userdata('username');
        $inputKategori = $this->input->post('inputKategori');

        $data_instock = [
            'idgudang' => $inputGudang,
            'instock_code' => $inputInstockCode,
            'tgl_terima' => $inputTglTerima,
            'jam_terima' => $inputJamTerima,
            'datetime' => $inputDatetime,
            'user' => $inputUser,
            'kategori' => $inputKategori,
            'no_manual' => $inputNo,
            'distribution_date' => $inputDistribution,
            'created_by' => $this->session->userdata('username'),
            'created_date' => date("Y-m-d H:i:s"),
            'status_verification' => 0, // belum diverifikasi
        ];
        $this->db->insert('instock', $data_instock);

        foreach ($inputSKU as $key => $sku) {
            $data_detail_instock = [
                'instock_code' => $inputInstockCode,
                'sku' => $sku,
                'nama_produk' => $inputNamaProduk[$key],
                'jumlah' => (int)$inputJumlah[$key],
                'sisa' => 0,
                'keterangan' => $inputKeterangan[$key],
            ];
            $this->db->insert('detail_instock', $data_detail_instock);
        }

        // WhatsApp API
        $this->db->select('user.handphone');
        $this->db->where('user.idrole', 1);
        $this->db->where('user.handphone IS NOT NULL');
        $target = $this->db->get('user')->row()->handphone;

        $token = 'EyuhsmTqzeKaDknoxdxt';
        $message = 'Transaksi barang masuk dengan kode instock ' . $inputInstockCode .
            (strlen($inputNo) > 0 ? ' dan nomor ' . $inputNo : '') .
            ' telah dibuat oleh ' . $this->session->userdata('username') .
            '. Super Admin dimohon untuk segera melakukan pengecekan verifikasi transaksi.';
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
        // End

        redirect('barangmasuk');
    }

    public function detail_instock()
    {
        $instock_code = $this->input->get('instock_code');

        $data_detail_instock = $this->db
            ->select('detail_instock.*, instock.tgl_terima, instock.jam_terima, instock.user, instock.kategori, gudang.nama_gudang as nama_gudang, product.nama_produk as nama_produk, instock.no_manual as no_manual')
            ->from('detail_instock')
            ->join('instock', 'instock.instock_code = detail_instock.instock_code')
            ->join('gudang', 'gudang.idgudang = instock.idgudang')
            ->join('product', 'detail_instock.sku = product.sku')
            ->where('detail_instock.instock_code', $instock_code)
            ->get()
            ->result();

        $no_manual = isset($data_detail_instock[0]) ? $data_detail_instock[0]->no_manual : '-';

        $title = $instock_code;
        $data = [
            'title' => $title,
            'instock_code' => $instock_code,
            'detailInStock' => $data_detail_instock,
            'no_manual' => $no_manual
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('barangMasuk/v_detail_instock');
    }

    public function exportExcel()
    {
        $instock_code = $this->input->get('instock_code');

        $detail_instock = $this->db
            ->select('detail_instock.*, instock.tgl_terima, instock.jam_terima, instock.user, instock.kategori, gudang.nama_gudang as nama_gudang, product.nama_produk as nama_produk, instock.no_manual as no_manual')
            ->from('detail_instock')
            ->join('instock', 'instock.instock_code = detail_instock.instock_code')
            ->join('gudang', 'gudang.idgudang = instock.idgudang')
            ->join('product', 'detail_instock.sku = product.sku')
            ->where('detail_instock.instock_code', $instock_code)
            ->get()
            ->result();

        $no_manual = isset($detail_instock[0]) ? $detail_instock[0]->no_manual : '-';

        // Load PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Title Header merged and center
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Barang Masuk - Asta Homeware');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Kode Barang Masuk & Nomer di bawah
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', "Kode Barang Masuk: {$instock_code}");
        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', "Nomer: {$no_manual}");

        // Header tabel
        $header = ['No', 'SKU', 'Nama Produk', 'Gudang', 'Jumlah', 'Keterangan'];
        $sheet->fromArray($header, null, 'A5');

        // Styling header
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        $sheet->getStyle('A5:F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:F5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Isi data
        $row = 6;
        $no = 1;
        foreach ($detail_instock as $item) {
            $sheet->setCellValue("A{$row}", $no);
            $sheet->setCellValue("B{$row}", $item->sku);
            $sheet->setCellValue("C{$row}", $item->nama_produk);
            $sheet->setCellValue("D{$row}", $item->nama_gudang);
            $sheet->setCellValue("E{$row}", $item->jumlah);
            $sheet->setCellValue("F{$row}", $item->keterangan);

            // Borders for each row
            $sheet->getStyle("A{$row}:F{$row}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row++;
            $no++;
        }

        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output to browser
        $filename = "Barang_Masuk_{$instock_code}.xlsx";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
