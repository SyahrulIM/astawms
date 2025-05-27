<?php
defined('BASEPATH') or exit('No direct script access allowed');

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

        $filename = 'Barang_Masuk_' . $instock_code . '.xls';

        $content = "<table>";
        $content .= "<tr><td colspan='9' style='font-weight:bold; text-align:center;'>Barang Masuk - Asta Homeware</td></tr>";
        $content .= "<tr><td colspan='9'>Kode Barang Mauk: {$instock_code}</td></tr>";
        $content .= "<tr><td colspan='9'>Nomer: {$no_manual}</td></tr>";
        $content .= "<tr><td colspan='9'>&nbsp;</td></tr>";
        $content .= "</table>";
        $content .= "<table border='1'>";
        $content .= "<thead>
                        <tr>
                            <th>No</th>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Gudang</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                     </thead><tbody>";

        $no = 1;
        foreach ($detail_instock as $row) {
            $content .= "<tr>
                            <td>{$no}</td>
                            <td>{$row->sku}</td>
                            <td>{$row->nama_produk}</td>
                            <td>{$row->nama_gudang}</td>
                            <td>{$row->jumlah}</td>
                            <td>{$row->keterangan}</td>
                         </tr>";
            $no++;
        }
        $content .= "</tbody></table>";

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $content;
        exit;
    }
}
