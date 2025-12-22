<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Verification extends CI_Controller
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
        // Query terpisah untuk menghindari masalah collation
        $instock = $this->db
            ->select("'INSTOCK' AS tipe, i.instock_code AS kode_transaksi, i.no_manual, i.tgl_terima AS tanggal, 
                    i.jam_terima AS jam, i.distribution_date, i.kategori, i.user, g.nama_gudang, i.status_verification")
            ->from('instock i')
            ->join('gudang g', 'g.idgudang = i.idgudang', 'left')
            ->get()
            ->result();

        $outstock = $this->db
            ->select("'OUTSTOCK' AS tipe, o.outstock_code AS kode_transaksi, o.no_manual, o.tgl_keluar AS tanggal, 
                    o.jam_keluar AS jam, o.distribution_date, o.kategori, o.user, g.nama_gudang, o.status_verification")
            ->from('outstock o')
            ->join('gudang g', 'g.idgudang = o.idgudang', 'left')
            ->get()
            ->result();

        $packing_list = $this->db
            ->select("'PACKING LIST' AS tipe, a.number_po AS kode_transaksi, a.no_manual, a.created_date AS tanggal, 
                    a.order_time AS jam, a.distribution_date, a.kategori, a.created_by as user, g.nama_gudang, a.status_verification")
            ->from('analisys_po a')
            ->join('gudang g', 'g.idgudang = a.idgudang', 'left')
            ->get()
            ->result();

        // Gabungkan semua hasil
        $all_transactions = array_merge($instock, $outstock, $packing_list);

        // Urutkan berdasarkan tanggal dan jam (terbaru ke terlama)
        usort($all_transactions, function ($a, $b) {
            $dateA = strtotime($a->tanggal . ' ' . $a->jam);
            $dateB = strtotime($b->tanggal . ' ' . $b->jam);
            return $dateB - $dateA;
        });

        $data['transactions'] = $all_transactions;
        $data['warehouse'] = $this->db->get('gudang')->result();
        $data['title'] = 'Verification';

        $this->load->view('theme/v_head', $data);
        $this->load->view('Verification/v_verification', $data);
    }

public function confirm_stock($type, $code)
{
    // Decode URL parameter
    $type = urldecode($type);
    $original_type = $type;
    $type = strtolower($type);

    // Normalisasi tipe
    if ($type == 'packing list') {
        $type = 'packing_list';
    }

    error_log("Confirm Stock - Original Type: $original_type, Normalized: $type, Code: $code");

    // Validasi tipe
    if (!in_array($type, ['instock', 'outstock', 'packing_list'])) {
        $this->session->set_flashdata('error', 'Tipe transaksi tidak valid: ' . $original_type);
        redirect('verification');
        return;
    }

    if ($type == 'instock') {
        // ... kode untuk instock tetap sama ...
        
    } elseif ($type == 'outstock') {
        // ... kode untuk outstock tetap sama ...
        
    } elseif ($type == 'packing_list') {
        // KODE UNTUK PACKING LIST
        $main_table = 'analisys_po';
        $kode_field = 'number_po';

        $trx = $this->db->where($kode_field, $code)->get($main_table)->row();
        if (!$trx) {
            $this->session->set_flashdata('error', 'Packing List tidak ditemukan.');
            redirect('verification');
            return;
        }

        // Ambil data dari POST - gunakan qty_packing_list
        $nomor_accurate = $this->input->post('nomor_accurate');
        $idgudang = $this->input->post('idgudang');
        $tanggal_diterima = $this->input->post('tanggal_diterima');
        $qty_packing_list_data = $this->input->post('qty_packing_list');
        $additional_products_data = $this->input->post('additional_products');

        // Validasi input dari modal
        if (!$nomor_accurate || !$idgudang || !$tanggal_diterima) {
            $this->session->set_flashdata('error', 'Harap isi semua field: Nomor Accurate, Gudang, dan Tanggal Diterima.');
            redirect('verification');
            return;
        }

        // Validasi minimal ada data produk
        if (empty($qty_packing_list_data) && empty($additional_products_data)) {
            $this->session->set_flashdata('error', 'Tidak ada data produk yang akan diverifikasi.');
            redirect('verification');
            return;
        }

        // Cek transaksi sebelumnya yang belum diverifikasi
        $tanggal = $trx->created_date;
        $jam = date('H:i:s', strtotime($trx->created_date)); // Ambil jam dari created_date

        // PERBAIKAN QUERY: Gunakan TIME(created_date) untuk analisys_po
        $query_unverified = "
        SELECT * FROM (
            SELECT tgl_terima AS tanggal, jam_terima AS jam FROM instock WHERE status_verification = 0
            UNION ALL
            SELECT tgl_keluar AS tanggal, jam_keluar AS jam FROM outstock WHERE status_verification = 0
            UNION ALL
            SELECT created_date AS tanggal, TIME(created_date) AS jam FROM analisys_po WHERE status_verification = 0
        ) AS all_unverified
        WHERE (tanggal < '$tanggal') OR (tanggal = '$tanggal' AND jam < '$jam')
        LIMIT 1
        ";

        $older_unverified = $this->db->query($query_unverified)->row();
        if ($older_unverified) {
            $this->session->set_flashdata('error', 'Terdapat transaksi sebelumnya yang belum diverifikasi. Harap verifikasi berdasarkan urutan waktu.');
            redirect('verification');
            return;
        }

        // Cek status verifikasi
        if ($trx->status_verification != 0) {
            $this->session->set_flashdata('error', 'Packing List sudah diverifikasi sebelumnya.');
            redirect('verification');
            return;
        }

        // Mulai transaction
        $this->db->trans_start();

        try {
            // Update analisys_po dengan data baru
            $update_data = [
                'status_verification' => 1,
                'no_manual' => $nomor_accurate,
                'idgudang' => $idgudang,
                'distribution_date' => $tanggal_diterima,
                'updated_by' => $this->session->userdata('username'),
                'updated_date' => date('Y-m-d H:i:s')
            ];

            $this->db->where($kode_field, $code)->update($main_table, $update_data);

            // Buat data instock baru
            $instock_code = 'PL-' . $trx->number_po; // Prefix PL untuk packing list

            // Cek apakah instock dengan kode ini sudah ada
            $existing_instock = $this->db->where('instock_code', $instock_code)->get('instock')->row();
            if ($existing_instock) {
                throw new Exception('Data instock untuk packing list ini sudah ada sebelumnya.');
            }

            // INSERT KE TABEL INSTOCK (HEADER) - DIUBAH: status_verification menjadi 0
            $instock_data = [
                'idgudang' => $idgudang,
                'instock_code' => $instock_code,
                'tgl_terima' => date('Y-m-d'),
                'jam_terima' => date('H:i:s'),
                'datetime' => date('Y-m-d H:i:s'),
                'user' => $this->session->userdata('username'),
                'kategori' => 'PACKING LIST',
                'no_manual' => $nomor_accurate,
                'distribution_date' => $tanggal_diterima,
                'created_by' => $this->session->userdata('username'),
                'created_date' => date('Y-m-d H:i:s'),
                'status_verification' => 0 // DIUBAH: Menjadi 0 (belum diverifikasi)
            ];

            $this->db->insert('instock', $instock_data);
            $idinstock = $this->db->insert_id();

            // Array untuk menyimpan semua product yang akan diproses
            $all_products_to_process = [];
            $processed_products = [];

            // 1. Tambahkan produk yang sudah ada di detail_analisys_po (menggunakan qty_packing_list)
            if (!empty($qty_packing_list_data) && is_array($qty_packing_list_data)) {
                foreach ($qty_packing_list_data as $idproduct => $qty_packing_list) {
                    $idproduct = (int) $idproduct;
                    $qty_packing_list = (int) $qty_packing_list;

                    if ($qty_packing_list >= 0 && !isset($processed_products[$idproduct])) {
                        $all_products_to_process[] = [
                            'idproduct' => $idproduct,
                            'qty_packing_list' => $qty_packing_list,
                            'is_additional' => false
                        ];
                        $processed_products[$idproduct] = true;
                    }
                }
            }

            // 2. Tambahkan produk tambahan
            if (!empty($additional_products_data) && is_array($additional_products_data)) {
                foreach ($additional_products_data as $additional_product) {
                    if (!empty($additional_product['idproduct']) && isset($additional_product['qty_packing_list'])) {
                        $idproduct = (int) $additional_product['idproduct'];
                        $qty_packing_list = (int) $additional_product['qty_packing_list'];

                        if ($qty_packing_list >= 0 && !isset($processed_products[$idproduct])) {
                            $all_products_to_process[] = [
                                'idproduct' => $idproduct,
                                'qty_packing_list' => $qty_packing_list,
                                'is_additional' => true
                            ];
                            $processed_products[$idproduct] = true;
                        }
                    }
                }
            }

            // Proses semua produk
            foreach ($all_products_to_process as $product_data) {
                $idproduct = $product_data['idproduct'];
                $qty_packing_list = $product_data['qty_packing_list'];
                $is_additional = $product_data['is_additional'];

                // Get product info
                $product = $this->db->select('sku, nama_produk')
                    ->where('idproduct', $idproduct)
                    ->get('product')
                    ->row();

                if (!$product) {
                    continue;
                }

                if ($is_additional) {
                    // INSERT KE DETAIL_ANALISYS_PO UNTUK PRODUK TAMBAHAN
                    $new_detail_data = [
                        'idanalisys_po' => $trx->idanalisys_po,
                        'idproduct' => $idproduct,
                        'product_name_en' => $product->nama_produk,
                        'qty_order' => 0,
                        'qty_packing_list' => $qty_packing_list,
                        'price' => 0
                    ];
                    $this->db->insert('detail_analisys_po', $new_detail_data);
                } else {
                    // UPDATE DETAIL_ANALISYS_PO - update qty_packing_list
                    $this->db->set('qty_packing_list', $qty_packing_list)
                        ->where('idanalisys_po', $trx->idanalisys_po)
                        ->where('idproduct', $idproduct)
                        ->update('detail_analisys_po');
                }

                // INSERT KE DETAIL_INSTOCK
                $detail_instock_data = [
                    'instock_code' => $instock_code,
                    'sku' => $product->sku,
                    'nama_produk' => $product->nama_produk,
                    'jumlah' => $qty_packing_list,
                    'sisa' => 0,
                    'keterangan' => 'Dari Packing List: ' . $trx->number_po .
                        ' (Qty Packing List: ' . $qty_packing_list . ')' . ($is_additional ? ' [Produk Tambahan]' : '')
                ];

                $this->db->insert('detail_instock', $detail_instock_data);

                // DIHAPUS: Update stok di sini karena instock belum diverifikasi
                // Stok akan diupdate nanti saat instock diverifikasi secara terpisah
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal memperbarui database.');
            }

            // Hitung total produk
            $total_products = count($all_products_to_process);
            $additional_products_count = count(array_filter($all_products_to_process, function ($item) {
                return $item['is_additional'];
            }));

            $message = 'Packing List berhasil diverifikasi dan data telah masuk ke sistem Instock.';
            $message .= ' Instock dengan kode ' . $instock_code . ' telah dibuat dan menunggu verifikasi.';
            if ($additional_products_count > 0) {
                $message .= " Terdapat $additional_products_count produk tambahan.";
            }

            $this->session->set_flashdata('success', $message);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    redirect('verification');
}

public function get_details($type, $kode)
{
    $type = strtolower($type);

    if ($type == 'packing list') {
        $type = 'packing_list';
    }

    $valid_types = ['instock', 'outstock', 'packing_list'];
    if (!in_array($type, $valid_types)) {
        echo json_encode(['success' => false, 'error' => 'Tipe tidak valid: ' . $type]);
        return;
    }

    try {
        if ($type == 'instock') {
            // ===================================================
            // CASE 1: INSTOCK
            // ===================================================
            $main_data = $this->db->where('instock_code', $kode)->get('instock')->row();
            if (!$main_data) {
                echo json_encode(['success' => false, 'error' => 'Instock tidak ditemukan']);
                return;
            }

            // Cek apakah ini instock dari packing list (lihat prefix PL-)
            $is_from_packing_list = false;
            $packing_list_number = null;
            
            if (strpos($main_data->instock_code, 'PL-') === 0) {
                $is_from_packing_list = true;
                $packing_list_number = substr($main_data->instock_code, 3); // Hilangkan prefix "PL-"
            }

            // Ambil detail instock
            $details = $this->db
                ->select('di.*, p.idproduct, p.nama_produk')
                ->from('detail_instock di')
                ->join('product p', 'di.sku = p.sku', 'left')
                ->where('di.instock_code', $kode)
                ->get()
                ->result();

            $formatted_details = [];
            
            if ($is_from_packing_list && $packing_list_number) {
                // Ambil data packing list
                $packing_list_data = $this->db->where('number_po', $packing_list_number)->get('analisys_po')->row();
                
                if ($packing_list_data) {
                    // PERUBAHAN PENTING: Ambil semua produk dari detail_analisys_po untuk packing list ini
                    // TANPA FILTER qty_order > 0, hanya filter qty_packing_list > 0
                    $packing_list_details = $this->db
                        ->select('dap.*, p.sku, p.nama_produk, p.idproduct')
                        ->from('detail_analisys_po dap')
                        ->join('product p', 'dap.idproduct = p.idproduct', 'left')
                        ->where('dap.idanalisys_po', $packing_list_data->idanalisys_po)
                        ->where('dap.qty_packing_list >', 0) // Hanya yang memiliki qty_packing_list > 0
                        ->get()
                        ->result();
                    
                    // Jika ada data di detail_analisys_po dengan qty_packing_list > 0
                    if (!empty($packing_list_details)) {
                        foreach ($packing_list_details as $pl_detail) {
                            // Cari detail instock yang sesuai berdasarkan SKU
                            $instock_detail = null;
                            foreach ($details as $detail) {
                                if ($detail->sku == $pl_detail->sku) {
                                    $instock_detail = $detail;
                                    break;
                                }
                            }
                            
                            $qty_order = (int) $pl_detail->qty_order;
                            $qty_packing_list = (int) $pl_detail->qty_packing_list;
                            $qty_instock = $instock_detail ? (int) $instock_detail->jumlah : $qty_packing_list;
                            
                            // Tampilkan SEMUA produk yang memiliki qty_packing_list > 0
                            // TIDAK ADA FILTER qty_order > 0 di sini
                            
                            $formatted_details[] = [
                                'sku' => $pl_detail->sku ?: 'N/A',
                                'nama_produk' => $pl_detail->nama_produk ?: ($pl_detail->product_name_en ?: 'N/A'),
                                'idproduct' => $pl_detail->idproduct,
                                'qty_order' => $qty_order,
                                'qty_packing_list' => $qty_packing_list,
                                'qty_instock' => $qty_instock,
                                'is_additional' => ($qty_order == 0 && $qty_packing_list > 0)
                            ];
                        }
                    } else {
                        // Jika tidak ada data di detail_analisys_po, ambil dari detail_instock
                        foreach ($details as $detail) {
                            $qty_instock = (int) $detail->jumlah;
                            
                            if ($qty_instock <= 0) {
                                continue;
                            }
                            
                            $formatted_details[] = [
                                'sku' => $detail->sku ?: 'N/A',
                                'nama_produk' => $detail->nama_produk ?: 'N/A',
                                'idproduct' => $detail->idproduct,
                                'qty_order' => 0,
                                'qty_packing_list' => $qty_instock,
                                'qty_instock' => $qty_instock,
                                'is_additional' => false
                            ];
                        }
                    }
                } else {
                    // Jika packing list tidak ditemukan, ambil dari detail_instock
                    foreach ($details as $detail) {
                        $qty_instock = (int) $detail->jumlah;
                        
                        if ($qty_instock <= 0) {
                            continue;
                        }
                        
                        $formatted_details[] = [
                            'sku' => $detail->sku ?: 'N/A',
                            'nama_produk' => $detail->nama_produk ?: 'N/A',
                            'idproduct' => $detail->idproduct,
                            'qty_instock' => $qty_instock
                        ];
                    }
                }
            } else {
                // Jika bukan dari packing list, tampilkan data instock biasa
                foreach ($details as $detail) {
                    $qty_instock = (int) $detail->jumlah;
                    
                    if ($qty_instock <= 0) {
                        continue;
                    }
                    
                    $formatted_details[] = [
                        'sku' => $detail->sku ?: 'N/A',
                        'nama_produk' => $detail->nama_produk ?: 'N/A',
                        'idproduct' => $detail->idproduct,
                        'qty_instock' => $qty_instock
                    ];
                }
            }

            echo json_encode([
                'success' => true,
                'details' => $formatted_details,
                'is_from_packing_list' => $is_from_packing_list,
                'main_data' => [
                    'kode_transaksi' => $main_data->instock_code,
                    'tipe' => 'INSTOCK',
                    'kategori' => $main_data->kategori,
                    'no_manual' => $main_data->no_manual,
                    'distribution_date' => $main_data->distribution_date,
                    'user' => $main_data->user,
                    'status_verification' => $main_data->status_verification,
                    'idgudang' => $main_data->idgudang
                ]
            ]);
            
        } elseif ($type == 'outstock') {
            // ... kode untuk outstock tetap sama ...
            
        } elseif ($type == 'packing_list') {
            // ===================================================
            // CASE 3: PACKING LIST (ANALISYS_PO)
            // ===================================================
            $main_data = $this->db->where('number_po', $kode)->get('analisys_po')->row();
            if (!$main_data) {
                echo json_encode(['success' => false, 'error' => 'Packing List tidak ditemukan']);
                return;
            }

            // Ambil semua produk dari detail_analisys_po
            // Untuk packing list, tampilkan produk dengan qty_order > 0 ATAU qty_packing_list > 0
            $details = $this->db
                ->select('dap.*, p.sku, p.nama_produk, p.idproduct')
                ->from('detail_analisys_po dap')
                ->join('product p', 'dap.idproduct = p.idproduct', 'left')
                ->where('dap.idanalisys_po', $main_data->idanalisys_po)
                ->where('(dap.qty_order > 0 OR dap.qty_packing_list > 0)', null, false)
                ->get()
                ->result();

            // Ambil semua produk untuk dropdown (produk tambahan)
            $products = $this->db
                ->select('idproduct, sku, nama_produk')
                ->from('product')
                ->order_by('nama_produk', 'asc')
                ->get()
                ->result();

            $formatted_details = [];
            foreach ($details as $detail) {
                $qty_packing_list = (int) $detail->qty_packing_list;
                
                // Untuk packing list, hanya tampilkan produk dengan qty_order > 0
                // (ini sesuai dengan logika JavaScript yang ada)
                if ($detail->qty_order <= 0) {
                    continue;
                }

                $formatted_details[] = [
                    'sku' => $detail->sku ?: 'N/A',
                    'nama_produk' => $detail->nama_produk ?: ($detail->product_name_en ?: 'N/A'),
                    'idproduct' => $detail->idproduct,
                    'qty_order' => (int) $detail->qty_order,
                    'qty_packing_list' => $qty_packing_list,
                    'price' => (int) $detail->price,
                    'is_additional' => ($detail->qty_order == 0 && $qty_packing_list > 0)
                ];
            }

            // Format produk untuk dropdown
            $formatted_products = [];
            foreach ($products as $product) {
                $formatted_products[] = [
                    'id' => $product->idproduct,
                    'sku' => $product->sku,
                    'nama' => $product->nama_produk,
                    'text' => $product->sku . ' - ' . $product->nama_produk
                ];
            }

            echo json_encode([
                'success' => true,
                'details' => $formatted_details,
                'products' => $formatted_products,
                'main_data' => [
                    'kode_transaksi' => $main_data->number_po,
                    'tipe' => 'PACKING LIST',
                    'kategori' => $main_data->kategori,
                    'no_manual' => $main_data->no_manual,
                    'distribution_date' => $main_data->distribution_date,
                    'created_by' => $main_data->created_by,
                    'status_verification' => $main_data->status_verification,
                    'idgudang' => $main_data->idgudang
                ],
                'columns' => [
                    'packing_list' => [
                        'sku' => 'SKU',
                        'nama_produk' => 'Nama Produk',
                        'qty_order' => 'Qty Order',
                        'qty_packing_list' => 'Qty Packing List'
                    ]
                ]
            ]);
        }
    } catch (Exception $e) {
        error_log("Error in get_details: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

    public function reject($type, $code)
    {
        // Decode URL parameter
        $type = urldecode($type);
        $original_type = $type;
        $type = strtolower($type);

        // Normalisasi tipe
        if ($type == 'packing list') {
            $type = 'packing_list';
        }

        error_log("Reject - Original Type: $original_type, Normalized: $type, Code: $code");

        // Validasi tipe
        if (!in_array($type, ['instock', 'outstock', 'packing_list'])) {
            $this->session->set_flashdata('error', 'Tipe transaksi tidak valid: ' . $original_type);
            redirect('verification');
            return;
        }

        if ($type == 'instock' || $type == 'outstock') {
            // KODE ASLI UNTUK INSTOCK DAN OUTSTOCK
            $main_table = $type;
            $kode_field = $type . '_code';

            $trx = $this->db->where($kode_field, $code)->get($main_table)->row();
            if (!$trx) {
                $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
                redirect('verification');
                return;
            }

            // Ambil tanggal dan jam transaksi
            $tanggal_field = $type === 'instock' ? 'tgl_terima' : 'tgl_keluar';
            $jam_field = $type === 'instock' ? 'jam_terima' : 'jam_keluar';

            $tanggal = $trx->$tanggal_field;
            $jam = $trx->$jam_field;

            // Cek apakah ada transaksi sebelumnya yang belum diverifikasi
            $query_unverified = "
                SELECT * FROM (
                    SELECT tgl_terima AS tanggal, jam_terima AS jam FROM instock WHERE status_verification = 0
                    UNION ALL
                    SELECT tgl_keluar AS tanggal, jam_keluar AS jam FROM outstock WHERE status_verification = 0
                ) AS all_unverified
                WHERE (tanggal < '$tanggal') OR (tanggal = '$tanggal' AND jam < '$jam')
                LIMIT 1
            ";

            $older_unverified = $this->db->query($query_unverified)->row();
            if ($older_unverified) {
                $this->session->set_flashdata('error', 'Terdapat transaksi sebelumnya yang belum diverifikasi atau ditolak. Harap proses berdasarkan urutan waktu.');
                redirect('verification');
                return;
            }

            // Cegah reject ulang
            if ($trx->status_verification != 0) {
                $this->session->set_flashdata('error', 'Transaksi sudah diproses sebelumnya.');
                redirect('verification');
                return;
            }

            $this->db->set('status_verification', 2)
                ->where($kode_field, $code)
                ->update($main_table);

            $this->session->set_flashdata('error', 'Transaksi berhasil ditolak.');
        } elseif ($type == 'packing_list') {
            // KODE UNTUK PACKING LIST
            $main_table = 'analisys_po';
            $kode_field = 'number_po';

            $trx = $this->db->where($kode_field, $code)->get($main_table)->row();
            if (!$trx) {
                $this->session->set_flashdata('error', 'Packing List tidak ditemukan.');
                redirect('verification');
                return;
            }

            // Cek status verifikasi
            if ($trx->status_verification != 0) {
                $this->session->set_flashdata('error', 'Packing List sudah diproses sebelumnya.');
                redirect('verification');
                return;
            }

            $this->db->set('status_verification', 2)
                ->set('updated_by', $this->session->userdata('username'))
                ->set('updated_date', date('Y-m-d H:i:s'))
                ->where($kode_field, $code)
                ->update($main_table);

            $this->session->set_flashdata('error', 'Packing List berhasil ditolak.');
        }

        redirect('verification');
    }

    public function exportExcel()
    {
        // Kode exportExcel tetap sama seperti aslinya
        $start_date = $this->input->post('filterInputStart');
        $end_date = $this->input->post('filterInputEnd');

        // Default filter tanggal jika kosong (7 hari terakhir)
        if (!$start_date && !$end_date) {
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $end_date = date('Y-m-d');
        }

        $whereIn = "";
        $whereOut = "";

        if ($start_date && $end_date) {
            $whereIn = "WHERE i.tgl_terima BETWEEN '$start_date' AND '$end_date'";
            $whereOut = "WHERE o.tgl_keluar BETWEEN '$start_date' AND '$end_date'";
        } elseif ($start_date) {
            $whereIn = "WHERE i.tgl_terima >= '$start_date'";
            $whereOut = "WHERE o.tgl_keluar >= '$start_date'";
        } elseif ($end_date) {
            $whereIn = "WHERE i.tgl_terima <= '$end_date'";
            $whereOut = "WHERE o.tgl_keluar <= '$end_date'";
        }

        $query = "
        SELECT 'INSTOCK' AS tipe, i.instock_code AS kode_transaksi, i.no_manual, i.tgl_terima AS tanggal,
               i.jam_terima AS jam, i.distribution_date, i.kategori, i.user, g.nama_gudang, i.status_verification
        FROM instock i
        LEFT JOIN gudang g ON g.idgudang = i.idgudang
        $whereIn

        UNION ALL

        SELECT 'OUTSTOCK' AS tipe, o.outstock_code AS kode_transaksi, o.no_manual, o.tgl_keluar AS tanggal,
               o.jam_keluar AS jam, o.distribution_date, o.kategori, o.user, g.nama_gudang, o.status_verification
        FROM outstock o
        LEFT JOIN gudang g ON g.idgudang = o.idgudang
        $whereOut

        ORDER BY tanggal DESC, jam DESC
        ";

        $transactions = $this->db->query($query)->result();

        // Ambil semua kode transaksi berdasarkan tipe untuk ambil detail sekaligus
        $instockCodes = [];
        $outstockCodes = [];

        foreach ($transactions as $trx) {
            if ($trx->tipe === 'INSTOCK') {
                $instockCodes[] = $trx->kode_transaksi;
            } else {
                $outstockCodes[] = $trx->kode_transaksi;
            }
        }

        // Ambil detail_instock sekaligus
        $detailInstock = [];
        if (!empty($instockCodes)) {
            $this->db->where_in('instock_code', $instockCodes);
            $detailInstock = $this->db->get('detail_instock')->result();
        }

        // Ambil detail_outstock sekaligus
        $detailOutstock = [];
        if (!empty($outstockCodes)) {
            $this->db->where_in('outstock_code', $outstockCodes);
            $detailOutstock = $this->db->get('detail_outstock')->result();
        }

        // Group detail berdasarkan kode transaksi
        $groupedDetailInstock = [];
        foreach ($detailInstock as $d) {
            $groupedDetailInstock[$d->instock_code][] = $d;
        }

        $groupedDetailOutstock = [];
        foreach ($detailOutstock as $d) {
            $groupedDetailOutstock[$d->outstock_code][] = $d;
        }

        // Pasang detail ke masing-masing transaksi
        foreach ($transactions as &$trx) {
            if ($trx->tipe === 'INSTOCK') {
                $trx->details = $groupedDetailInstock[$trx->kode_transaksi] ?? [];
            } else {
                $trx->details = $groupedDetailOutstock[$trx->kode_transaksi] ?? [];
            }
        }

        // Buat spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Styling
        $styleHeader = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $styleTableHeader = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0f0f0']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $styleDetailHeader = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd9edf7']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $styleBorder = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]];

        $row = 1;

        // Header judul
        $sheet->mergeCells("A{$row}:I{$row}")->setCellValue("A{$row}", "Asta Homeware");
        $sheet->getStyle("A{$row}")->applyFromArray($styleHeader);
        $row++;

        $sheet->mergeCells("A{$row}:I{$row}")->setCellValue("A{$row}", "Data Verifikasi Transaksi");
        $sheet->getStyle("A{$row}")->applyFromArray($styleHeader);
        $row++;

        $sheet->mergeCells("A{$row}:I{$row}")->setCellValue("A{$row}", "Periode: " . ($start_date ?? '-') . " s/d " . ($end_date ?? '-'));
        $row += 2;

        $no = 1;
        foreach ($transactions as $trx) {
            // Header tabel
            $sheet->fromArray([
                'No', 'Tipe', 'Kode Transaksi', 'Nomer', 'Tanggal Input', 'Tanggal Distribusi', 'User', 'Gudang', 'Status'
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($styleTableHeader);
            $row++;

            // Data transaksi
            $sheet->fromArray([
                $no,
                $trx->tipe,
                $trx->kode_transaksi,
                $trx->no_manual,
                $trx->tanggal,
                $trx->distribution_date,
                $trx->user,
                $trx->nama_gudang,
                $trx->status_verification == 1 ? 'Accept' : ($trx->status_verification == 2 ? 'Reject' : 'Pending')
            ], null, "A{$row}");
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($styleBorder);
            $row++;

            // Detail produk
            if (!empty($trx->details)) {
                $sheet->mergeCells("A{$row}:I{$row}")->setCellValue("A{$row}", "Detail Transaksi");
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($styleDetailHeader);
                $row++;

                $sheet->setCellValue("A{$row}", 'SKU');
                $sheet->mergeCells("A{$row}:B{$row}");

                $sheet->setCellValue("C{$row}", 'Nama Produk');
                $sheet->mergeCells("C{$row}:E{$row}");

                $sheet->setCellValue("F{$row}", 'Jumlah');
                $sheet->mergeCells("F{$row}:G{$row}");

                $sheet->setCellValue("H{$row}", 'Keterangan');
                $sheet->mergeCells("H{$row}:I{$row}");

                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($styleDetailHeader);
                $row++;

                foreach ($trx->details as $detail) {
                    $sheet->setCellValue("A{$row}", $detail->sku);
                    $sheet->mergeCells("A{$row}:B{$row}");

                    $sheet->setCellValue("C{$row}", $detail->nama_produk);
                    $sheet->mergeCells("C{$row}:E{$row}");

                    $sheet->setCellValue("F{$row}", $detail->jumlah);
                    $sheet->mergeCells("F{$row}:G{$row}");

                    $sheet->setCellValue("H{$row}", $detail->keterangan);
                    $sheet->mergeCells("H{$row}:I{$row}");

                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($styleBorder);
                    $row++;
                }
            }

            $no++;
        }

        // Set lebar kolom manual (hindari setAutoSize agar lebih cepat)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(15);

        $filename = 'Verifikasi_Transaksi_' . date('Y-m-d_H-i-s') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
