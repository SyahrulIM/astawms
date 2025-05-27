<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Outstock extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Check if the user is logged in
        if (!$this->session->userdata('logged_in')) {
            // Redirect to login with a message
            $this->session->set_flashdata('error', 'Eeettss gak boleh nakal, Login dulu ya kak hehe.');
            redirect('auth');  // Assuming 'auth' is your login controller
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
            'status_verification' => 0,  // Status verification yang baru
            'created_by' => $this->session->userdata('username'),
            'created_date' => date("Y-m-d H:i:s"),
        ];

        foreach ($inputSKU as $key => $sku) {
            $jumlah = (int) $inputJumlah[$key];

            // Ambil produk berdasarkan SKU
            $product = $this->db->where('sku', $sku)->get('product')->row();
            $idproduct = $product->idproduct;

            // Cek stok awal di gudang tersebut
            $product_stock = $this->db
                ->where('idproduct', $idproduct)
                ->where('idgudang', $inputGudang)
                ->get('product_stock')
                ->row();

            if (!$product_stock) {
                // Kalau belum ada, insert stok awal dengan 0
                $this->db->insert('product_stock', [
                    'idproduct' => $idproduct,
                    'idgudang' => $inputGudang,
                    'stok' => 0
                ]);
                $sisa_stok = 0 - $jumlah;
            } else {
                $sisa_stok = $product_stock->stok - $jumlah;
            }

            // Simpan detail outstock
            $data_detail_outstock = [
                'outstock_code' => $inputOutstockCode,
                'sku' => $sku,
                'nama_produk' => $inputNamaProduk[$key],
                'jumlah' => $jumlah,
                'sisa' => $sisa_stok,
                'keterangan' => $inputKeterangan[$key],
            ];
            $this->db->insert('detail_outstock', $data_detail_outstock);

            // Hapus update stok product_stock
            // Tidak perlu update 'product_stock' lagi di sini
        }

        // Simpan ke tabel outstock utama dengan status_verification = 0
        $this->db->insert('outstock', $data_outstock);

        // WhatsApp API
        $this->db->select('user.handphone');
        $this->db->where('user.idrole', 3);
        $this->db->where('user.handphone IS NOT NULL');
        $target = $this->db->get('user')->row()->handphone;

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

        $this->load->helper('download');

        $no_manual = isset($detail_outstock[0]) ? $detail_outstock[0]->no_manual : '-';

        $filename = 'Barang_Keluar_' . $outstock_code . '.xls';

        $content = "<table>";
        $content .= "<tr><td colspan='9' style='font-weight:bold; text-align:center;'>Barang Keluar - Asta Homeware</td></tr>";
        $content .= "<tr><td colspan='9'>Kode Barang Keluar: {$outstock_code}</td></tr>";
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
        foreach ($detail_outstock as $row) {
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
