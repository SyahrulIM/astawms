<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barangmasuk extends CI_Controller
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

        $this->load->view('theme\v_head', $data);
        $this->load->view('barangMasuk\v_barang_masuk');
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
        $inputDatetime = date("Y-m-d H:i:s");
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
        ];

        $this->db->insert('instock', $data_instock);

        foreach ($inputSKU as $key => $sku) {
            $jumlah = (int) $inputJumlah[$key];

            $product = $this->db->where('sku', $sku)->get('product')->row();

            // Ambil stok existing di product_stock
            $stok_exist = $this->db->get_where('product_stock', [
                'idproduct' => $product->idproduct,
                'idgudang' => $inputGudang
            ])->row();

            if ($stok_exist) {
                $stokBaru = $stok_exist->stok + $jumlah;

                // Update stok
                $this->db->set('stok', $stokBaru);
                $this->db->where('idproduct', $product->idproduct);
                $this->db->where('idgudang', $inputGudang);
                $this->db->update('product_stock');
            } else {
                $stokBaru = $jumlah;

                // Insert stok baru
                $this->db->insert('product_stock', [
                    'idproduct' => $product->idproduct,
                    'idgudang' => $inputGudang,
                    'stok' => $stokBaru
                ]);
            }

            // Insert ke detail_instock dengan sisa hasil penjumlahan
            $data_detail_instock = [
                'instock_code' => $inputInstockCode,
                'sku' => $sku,
                'nama_produk' => $inputNamaProduk[$key],
                'jumlah' => $jumlah,
                'sisa' => $stokBaru, // sesuai stok terbaru di gudang tersebut
                'keterangan' => $inputKeterangan[$key],
            ];
            $this->db->insert('detail_instock', $data_detail_instock);
        }

        redirect('barangmasuk');
    }

    public function detail_instock()
    {
        $instock_code = $this->input->get('instock_code');

        $data_detail_instock = $this->db
            ->select('detail_instock.*, instock.tgl_terima, instock.jam_terima, instock.user, instock.kategori, gudang.nama_gudang as nama_gudang')
            ->from('detail_instock')
            ->join('instock', 'instock.instock_code = detail_instock.instock_code')
            ->join('gudang', 'gudang.idgudang = instock.idgudang')
            ->where('detail_instock.instock_code', $instock_code)
            ->get()
            ->result();

        $title = $instock_code;
        $data = [
            'title' => $title,
            'instock_code' => $instock_code,
            'detailInStock' => $data_detail_instock
        ];

        $this->load->view('theme\v_head', $data);
        $this->load->view('barangMasuk\v_detail_instock');
    }
}
