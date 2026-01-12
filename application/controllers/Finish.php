<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Finish extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Eeettss gak boleh nakal, Login dulu ya kak hehe.');
            redirect('auth');
        }

        // Load translation helper
        $this->load->helper('translation');
    }

    public function index()
    {
        // Start Product
        $this->db->where('status', 1);
        $product = $this->db->get('product');
        // End

        // Start Data Transaksi
        $this->db->where('status_progress', 'Finish');
        $data_trx = $this->db->get('analisys_po');
        // End

        $data = [
            'title' => 'Analisa PO',
            'product' => $product->result(),
            'data_trx' => $data_trx->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('finish/v_finish');
    }

    public function get_detail_analisys_po($idanalisys_po)
    {
        // Ambil data header PO
        $this->db->where('idanalisys_po', $idanalisys_po);
        $header_data = $this->db->get('analisys_po')->row();

        // Tentukan simbol mata uang
        $currency = '';

        // Normalisasi ke uppercase biar aman
        $money = strtoupper(trim($header_data->money_currency));

        if ($money === 'RMB' || $money === 'CNY') {
            $currency = '¥';
        } elseif ($money === 'IDR' || $money === 'RP') {
            $currency = 'Rp';
        } else {
            $currency = ''; // fallback kalau ada mata uang lain
        }

        // Ambil data detail PO
        $this->db->select('d.idanalisys_po, p.nama_produk, p.sku, d.type_sgs, d.type_unit, , d.latest_incoming_stock_mouth, d.latest_incoming_stock_pcs, 
                   d.last_mouth_sales, d.current_month_sales, d.balance_per_today, d.qty_order, d.price, d.description');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $idanalisys_po);
        $this->db->where('d.qty_order > 0');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            // Tampilkan informasi header PO
            echo '<div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="fa-solid fa-file-invoice me-2"></i>Informasi PO</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <strong>No Purchase Order:</strong><br>
                        ' . ($header_data->number_po ? htmlspecialchars($header_data->number_po) : '<span class="text-muted">-</span>') . '
                    </div>
                    <div class="col-md">
                        <strong>Order Date:</strong><br>
                        ' . ($header_data->order_date ? htmlspecialchars($header_data->order_date) : '<span class="text-muted">-</span>') . '
                    </div>
                    <div class="col-md">
                        <strong>Shipment Number:</strong><br>
                        ' . ($header_data->name_container ? htmlspecialchars($header_data->name_container) : '<span class="text-muted">-</span>') . '
                    </div>
                    <div class="col-md">
                    <strong>Supplier:</strong><br>
                    ' . ($header_data->name_supplier ? strtoupper(htmlspecialchars($header_data->name_supplier)) : '<span class="text-muted">-</span>') . '
                    </div>
                    <div class="col-md">
                        <strong>Money Currency:</strong><br>
                        ' . ($header_data->money_currency ? strtoupper(htmlspecialchars($header_data->money_currency)) : '<span class="text-muted">-</span>') . '
                    </div>
                </div>
            </div>
        </div>';

            echo '<div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="font-size: small;">
            <thead class="table-light">
                <tr>
                        <th class="text-center">No</th>
                        <th class="text-center" width="100">Product</th>
                        <th class="text-center" width="100">Product Code</th>
                        <th class="text-center" width="100">Type SGS</th>
                        <th class="text-center" width="100">Type Unit</th>
                        <th class="text-center">Last Coming</th>
                        <th class="text-center">Last Sales</th>
                        <th class="text-center">Current Sales</th>
                        <th class="text-center">Balance</th>
                        <th class="text-center">Avg Ratio</th>
                        <th class="text-center" width="100">Qty Order</th>
                        <th class="text-center" width="125">Price</th>
                </tr>
            </thead>
            <tbody>';

            $no = 1;
            $total_qty = 0;
            $total_value = 0;
            $found = false;

            foreach ($query->result() as $row) {
                // Hitung rata-rata penjualan per minggu
                $total_sales = floatval($row->current_month_sales);
                $avg_sales = $total_sales / 4;

                // Filter: hanya tampilkan jika Avg Sales vs Stock di bawah 1
                if ($avg_sales > 0) {
                    $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                    if ($avg_vs_stock >= 1) {
                        continue; // Skip produk dengan avg >= 1
                    }
                    $avg_vs_stock_display = number_format($avg_vs_stock, 2);
                    $found = true;
                } else {
                    continue; // Skip produk dengan avg_sales = 0
                }

                // Hitung total
                $item_value = floatval($row->qty_order) * floatval($row->price);
                $total_qty += floatval($row->qty_order);
                $total_value += $item_value;

                echo '<tr>
                <td class="text-center">' . $no++ . '</td>
                <td>' . htmlspecialchars($row->nama_produk) . '</td>
                <td>' . htmlspecialchars($row->sku) . '</td>
                <td class="text-center">' . ($row->type_sgs ? strtoupper(htmlspecialchars($row->type_sgs)) : '-') . '</td>
                <td class="text-center">' . ($row->type_unit ? strtoupper(htmlspecialchars($row->type_unit)) : '-') . '</td>
                <td class="text-center"><span class="text-primary"><i class="fa-solid fa-calendar"></i> ' . $row->latest_incoming_stock_mouth . '</span><br><span class="text-success"><i class="fa-solid fa-box"></i> ' . $row->latest_incoming_stock_pcs . ' Pcs</span></td>
                <td class="text-end">' . ($row->last_mouth_sales ? htmlspecialchars($row->last_mouth_sales) : '-') . '</td>
                <td class="text-end">' . ($row->current_month_sales ? htmlspecialchars($row->current_month_sales) : '-') . '</td>
                <td class="text-end">' . ($row->balance_per_today ? htmlspecialchars($row->balance_per_today) : '-') . '</td>
                <td class="text-end text-danger fw-bold">' . $avg_vs_stock_display . '</td>
                <td class="text-end">' . ($row->qty_order ? number_format($row->qty_order) : '0') . '</td>
                <td class="text-end">' . ($row->price ? number_format($row->price, 2) : '0.00') . '</td>
            </tr>';
            }

            if (!$found) {
                echo '<tr>
                <td colspan="13" class="text-center text-muted py-4">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Tidak ada produk dengan Avg Sales vs Stock di bawah 1.00.
                </td>
            </tr>';
            } else {
                echo '<tr class="table-info fw-bold">
                <td colspan="10" class="text-end"><strong>TOTAL:</strong></td>
                <td class="text-end">' . number_format($total_qty) . '</td>
                <td class="text-end"> ' . $currency . number_format($total_value) . '</td>
            </tr>';
            }

            echo '</tbody></table></div>';

            if ($found) {
                echo '<div class="mt-4 text-center">
                <a href="' . base_url('finish/exportPdf?id=' . $idanalisys_po) . '" class="btn btn-danger" target="_blank">
                    <i class="fa-solid fa-file-pdf me-2"></i>Export to PDF
                </a>
            </div>';
            }
        } else {
            echo '<div class="alert alert-warning text-center">
            <i class="fa-solid fa-exclamation-triangle me-2"></i>
            Tidak ada produk dalam analisis PO ini.
        </div>';
        }
    }

    public function cancel($idanalisys_po)
    {
        $this->db->set('status_progress', 'Cancel');
        $this->db->where('idanalisys_po', $idanalisys_po);
        $this->db->update('analisys_po');

        $this->session->set_flashdata('success', 'Pemesanan berhasil dibatalkan.');
        redirect('Pre');
    }

    public function exportPdf()
    {
        $id = $this->input->get('id');

        // Ambil data header PO
        $this->db->where('idanalisys_po', $id);
        $data_po = $this->db->get('analisys_po');
        $po = $data_po->row();

        // Ambil data detail PO + gambar produk
        $this->db->select('d.idanalisys_po, p.nama_produk, p.sku, p.gambar, d.type_sgs, d.type_unit, 
                       d.latest_incoming_stock_mouth, d.latest_incoming_stock_pcs, d.last_mouth_sales, d.current_month_sales, 
                       d.balance_per_today, d.qty_order, d.price, d.description');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $id);
        $this->db->where('d.qty_order > 0');
        $query = $this->db->get();

        // Filter data (Avg Sales vs Stock < 1) dan translate
        $filtered_detail_po = [];
        $total_qty = 0;
        $total_value = 0;
        $no = 1;

        foreach ($query->result() as $row) {
            $total_sales = floatval($row->current_month_sales);
            $avg_sales = $total_sales / 4;

            if ($avg_sales > 0) {
                $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                if ($avg_vs_stock < 1) {
                    $item_value = floatval($row->qty_order) * floatval($row->price);

                    // Try to translate using API, fallback to simple translation
                    try {
                        $translated_product_name = translate_id_to_en($row->nama_produk);
                        $translated_description = !empty($row->description) ? translate_id_to_en($row->description) : '';
                    } catch (Exception $e) {
                        // Fallback to simple translation
                        $translated_product_name = simple_translate_id_to_en($row->nama_produk);
                        $translated_description = !empty($row->description) ? simple_translate_id_to_en($row->description) : '';
                    }

                    // FIXED: Get correct image path with better checking
                    $image_url = $this->getProductImageUrl($row->gambar);

                    $filtered_detail_po[] = [
                        'row' => $row,
                        'translated_product_name' => $translated_product_name,
                        'translated_description' => $translated_description,
                        'avg_vs_stock' => number_format($avg_vs_stock, 2),
                        'item_value' => $item_value,
                        'no' => $no++,
                        'image_url' => $image_url
                    ];

                    $total_qty += floatval($row->qty_order);
                    $total_value += $item_value;
                }
            }
        }

        $data = [
            'title' => 'Export Purchase Order',
            'po' => $po,
            'detail_po' => $filtered_detail_po,
            'total_qty' => $total_qty,
            'total_value' => $total_value
        ];

        $this->load->view('finish/v_pdf', $data); // Use fixed view
    }

    // Helper function to get product image URL
    private function getProductImageUrl($image_filename)
    {
        if (empty($image_filename)) {
            return base_url('assets/image/no-image.png');
        }

        // Check multiple possible locations
        $possible_paths = [
            'uploads/product/' . $image_filename,
            'assets/image/' . $image_filename,
            'assets/images/' . $image_filename,
            'images/' . $image_filename,
            'uploads/' . $image_filename
        ];

        foreach ($possible_paths as $path) {
            if (file_exists(FCPATH . $path)) {
                return base_url($path);
            }
        }

        // If image not found in any location
        return base_url('assets/image/no-image.png');
    }

    // Optional: Pre-translate function for better performance
    public function preTranslate($idanalisys_po = null)
    {
        if ($idanalisys_po) {
            // Translate single PO
            $this->db->select('d.idanalisys_po, p.nama_produk, d.description');
            $this->db->from('detail_analisys_po d');
            $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
            $this->db->where('d.idanalisys_po', $idanalisys_po);
            $this->db->where('d.qty_order > 0');
            $query = $this->db->get();

            $translation_count = 0;
            foreach ($query->result() as $row) {
                // Pre-translate to cache
                $translated_name = translate_id_to_en($row->nama_produk);
                if (!empty($row->description)) {
                    translate_id_to_en($row->description);
                }
                $translation_count++;
            }

            $this->session->set_flashdata('success', "Pre-translated $translation_count product names for PO #$idanalisys_po");
            redirect('finish');
        } else {
            // Translate all products in database
            $this->db->select('nama_produk');
            $this->db->distinct();
            $products = $this->db->get('product');

            $translation_count = 0;
            foreach ($products->result() as $product) {
                translate_id_to_en($product->nama_produk);
                $translation_count++;
            }

            $this->session->set_flashdata('success', "Pre-translated $translation_count unique product names");
            redirect('finish');
        }
    }
}
