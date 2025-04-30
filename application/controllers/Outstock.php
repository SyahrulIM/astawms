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
        if (empty($inputDatetime)) {
            $inputDatetime = date("Y-m-d H:i:s");
        }
        $inputUser = $this->session->userdata('username');
        $inputKategori = $this->input->post('inputKategori');

        $data_outstock = [
            'idgudang' => $inputGudang,
            'outstock_code' => $inputOutstockCode,
            'tgl_keluar' => $inputTglTerima,
            'jam_keluar' => $inputJamTerima,
            'datetime' => $inputDatetime,
            'user' => $inputUser,
            'kategori' => $inputKategori,
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
                // Kalau belum ada, insert dulu stok awal dengan 0
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

            // Update stok di tabel product_stock
            $this->db->set('stok', "stok - {$jumlah}", false);
            $this->db->where('idproduct', $idproduct);
            $this->db->where('idgudang', $inputGudang);
            $this->db->update('product_stock');
        }

        // Simpan ke tabel outstock utama
        $this->db->insert('outstock', $data_outstock);

        redirect('outstock');
    }

    public function detail_outstock()
    {
        $outstock_code = $this->input->get('outstock_code');

        $data_detail_outstock = $this->db
        ->select('detail_outstock.*, outstock.tgl_keluar, outstock.jam_keluar, outstock.user, outstock.kategori, gudang.nama_gudang as nama_gudang, product.nama_produk as nama_produk')
        ->from('detail_outstock')
        ->join('outstock', 'detail_outstock.outstock_code = outstock.outstock_code')
        ->join('gudang', 'outstock.idgudang = gudang.idgudang')
        ->join('product', 'detail_outstock.sku = product.sku') // JOIN tambahan ke product
        ->where('detail_outstock.outstock_code', $outstock_code)
        ->get()
        ->result();    

        $title = $outstock_code;
        $data = [
            'title' => $title,
            'outstock_code' => $outstock_code,
            'detailoutstock' => $data_detail_outstock
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Outstock/v_detail_outstock');
    }
}
