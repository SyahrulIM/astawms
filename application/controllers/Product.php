<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product extends CI_Controller
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
        $title = 'Product';
        $products = $this->db->where('status', 1)->get('product')->result();
        $gudangs = $this->db->get('gudang')->result();
        $stok = $this->db
            ->select('product_stock.idproduct, product_stock.idgudang, product_stock.stok')
            ->get('product_stock')
            ->result();
        $stokMap = [];
        foreach ($stok as $s) {
            $stokMap[$s->idproduct][$s->idgudang] = $s->stok;
        }

        $data = [
            'title' => $title,
            'product' => $products,
            'gudang' => $gudangs,
            'stokMap' => $stokMap
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
        ORDER BY datetime
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
        ORDER BY datetime
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
        ORDER BY datetime
        ";

        $transaction_stock = $this->db->query($query, [$sku, $idgudang, $sku, $idgudang])->result();

        $this->load->helper('download');

        $filename = 'kartu_stok_' . $sku . '.xls';

        $content = "<table>";
        $content .= "<tr><td colspan='9' style='font-weight:bold; text-align:center;'>Kartu Stok - Asta Homeware</td></tr>";
        $content .= "<tr><td colspan='9'>SKU: {$sku}</td></tr>";
        $content .= "<tr><td colspan='9'>Nama Produk: {$product->nama_produk}</td></tr>";
        $content .= "<tr><td colspan='9'>&nbsp;</td></tr>";
        $content .= "</table>";
        $content .= "<table border='1'>";
        $content .= "<thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Transaksi</th>
                            <th>Datetime</th>
                            <th>Kategori</th>
                            <th>Stock In</th>
                            <th>Stock Out</th>
                            <th>Sisa</th>
                            <th>User</th>
                            <th>Keterangan</th>
                        </tr>
                     </thead><tbody>";

        $no = 1;
        foreach ($transaction_stock as $row) {
            $content .= "<tr>
                            <td>{$no}</td>
                            <td>{$row->stock_code}</td>
                            <td>{$row->datetime}</td>
                            <td>{$row->kategori}</td>
                            <td>" . ($row->instock !== null ? $row->instock : '-') . "</td>
                            <td>" . ($row->outstock !== null ? $row->outstock : '-') . "</td>
                            <td>{$row->sisa}</td>
                            <td>{$row->user}</td>
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
