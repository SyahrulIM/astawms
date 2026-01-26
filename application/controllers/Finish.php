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
        $this->load->view('Finish/v_finish');
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

        // Ambil data detail PO (termasuk produk tambahan)
        $this->db->select('d.idanalisys_po, p.nama_produk, p.sku, d.type_sgs, d.type_unit, 
                   d.latest_incoming_stock_mouth, d.latest_incoming_stock_pcs, 
                   d.last_mouth_sales, d.current_month_sales, d.balance_per_today, 
                   d.qty_order, d.price, d.description, d.idproduct');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $idanalisys_po);
        $this->db->where('d.qty_order > 0');
        $this->db->order_by('d.iddetail_analisys_po', 'ASC'); // Urutkan berdasarkan ID detail
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
                    <div class="col-md">
                        <strong>Type PO:</strong><br>
                        ' . ($header_data->type_po ? strtoupper(htmlspecialchars($header_data->type_po)) : '<span class="text-muted">-</span>') . '
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
                        <th class="text-center" width="150">Keterangan</th>
                </tr>
            </thead>
            <tbody>';

            $no = 1;
            $total_qty = 0;
            $total_value = 0;
            $found = false;

            foreach ($query->result() as $row) {
                // Untuk produk tambahan yang mungkin tidak memiliki data sales, set default values
                $last_sales = $row->last_mouth_sales ? $row->last_mouth_sales : 0;
                $current_sales = $row->current_month_sales ? $row->current_month_sales : 0;
                $balance = $row->balance_per_today ? $row->balance_per_today : 0;
                $avg_vs_stock_display = '-';

                // Hitung avg_vs_stock hanya jika data tersedia dan valid
                if ($current_sales > 0 && $balance > 0) {
                    $total_sales = floatval($current_sales);
                    $avg_sales = $total_sales / 4;

                    if ($avg_sales > 0) {
                        $avg_vs_stock = floatval($balance) / $avg_sales;
                        $avg_vs_stock_display = number_format($avg_vs_stock, 2);

                        // Filter: hanya tampilkan jika Avg Sales vs Stock di bawah 1 (untuk produk original)
                        // Tapi untuk produk tambahan, tampilkan semua meskipun avg >= 1
                        // Kita cek apakah ini produk tambahan (mungkin memiliki description khusus)
                        $is_additional_product = (strpos($row->description ?? '', 'Produk tambahan') !== false) || (empty($row->last_mouth_sales) && empty($row->current_month_sales));

                        if (!$is_additional_product && $avg_vs_stock >= 1) {
                            continue; // Skip produk original dengan avg >= 1
                        }

                        $found = true;
                    } else {
                        $found = true; // Produk tambahan dengan avg_sales = 0 tetap ditampilkan
                    }
                } else {
                    // Produk tanpa data sales (kemungkinan produk tambahan)
                    $found = true;
                }

                // Hitung total
                $item_value = floatval($row->qty_order) * floatval($row->price);
                $total_qty += floatval($row->qty_order);
                $total_value += $item_value;

                // Format price dengan currency
                $formatted_price = '';
                if (!empty($row->price)) {
                    if ($currency == '¥') {
                        $formatted_price = $currency . number_format($row->price, 2);
                    } else {
                        $formatted_price = $currency . ' ' . number_format($row->price, 2);
                    }
                }

                echo '<tr>
                <td class="text-center">' . $no++ . '</td>
                <td>' . htmlspecialchars($row->nama_produk) . '</td>
                <td>' . htmlspecialchars($row->sku) . '</td>
                <td class="text-center">' . ($row->type_sgs ? strtoupper(htmlspecialchars($row->type_sgs)) : '-') . '</td>
                <td class="text-center">' . ($row->type_unit ? strtoupper(htmlspecialchars($row->type_unit)) : '-') . '</td>
                <td class="text-center">';

                if (!empty($row->latest_incoming_stock_mouth)) {
                    echo '<span class="text-primary"><i class="fa-solid fa-calendar"></i> ' . $row->latest_incoming_stock_mouth . '</span><br>';
                }
                if (!empty($row->latest_incoming_stock_pcs)) {
                    echo '<span class="text-success"><i class="fa-solid fa-box"></i> ' . $row->latest_incoming_stock_pcs . ' Pcs</span>';
                } else {
                    echo '-';
                }

                echo '</td>
                <td class="text-end">' . ($last_sales ? number_format($last_sales) : '-') . '</td>
                <td class="text-end">' . ($current_sales ? number_format($current_sales) : '-') . '</td>
                <td class="text-end">' . ($balance ? number_format($balance) : '-') . '</td>
                <td class="text-end">' . $avg_vs_stock_display . '</td>
                <td class="text-end">' . ($row->qty_order ? number_format($row->qty_order) : '0') . '</td>
                <td class="text-end">' . ($row->price ? $formatted_price : '0.00') . '</td>
                <td class="text-center">' . ($row->description ? htmlspecialchars($row->description) : '-') . '</td>
            </tr>';
            }

            if (!$found) {
                echo '<tr>
                <td colspan="13" class="text-center text-muted py-4">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Tidak ada produk dalam PO ini.
                </td>
            </tr>';
            } else {
                // Format total value dengan currency
                $formatted_total_value = '';
                if ($currency == '¥') {
                    $formatted_total_value = $currency . number_format($total_value, 2);
                } else {
                    $formatted_total_value = $currency . ' ' . number_format($total_value, 2);
                }

                echo '<tr class="table-info fw-bold">
                <td colspan="10" class="text-end"><strong>TOTAL:</strong></td>
                <td class="text-end">' . number_format($total_qty) . '</td>
                <td class="text-end" colspan="2">' . $formatted_total_value . '</td>
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

        // Ambil data detail PO + gambar produk (semua produk termasuk tambahan)
        $this->db->select('d.idanalisys_po, p.nama_produk, p.sku, p.gambar, d.type_sgs, d.type_unit, 
                       d.latest_incoming_stock_mouth, d.latest_incoming_stock_pcs, d.last_mouth_sales, d.current_month_sales, 
                       d.balance_per_today, d.qty_order, d.price, d.description');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $id);
        $this->db->where('d.qty_order > 0');
        $this->db->order_by('d.iddetail_analisys_po', 'ASC');
        $query = $this->db->get();

        // Filter data dan translate
        $filtered_detail_po = [];
        $total_qty = 0;
        $total_value = 0;
        $no = 1;

        foreach ($query->result() as $row) {
            // Untuk produk tambahan, tampilkan semua tanpa filter avg_vs_stock
            $is_additional_product = (strpos($row->description ?? '', 'Produk tambahan') !== false) || (empty($row->last_mouth_sales) && empty($row->current_month_sales));

            // Jika bukan produk tambahan, cek avg_vs_stock
            if (!$is_additional_product) {
                $total_sales = floatval($row->current_month_sales);
                $avg_sales = $total_sales / 4;

                if ($avg_sales > 0) {
                    $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                    if ($avg_vs_stock >= 1) {
                        continue; // Skip produk original dengan avg >= 1
                    }
                } else {
                    continue; // Skip produk original dengan avg_sales = 0
                }
            }

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
                'avg_vs_stock' => $is_additional_product ? '-' : number_format($avg_vs_stock ?? 0, 2),
                'item_value' => $item_value,
                'no' => $no++,
                'image_url' => $image_url,
                'is_additional' => $is_additional_product
            ];

            $total_qty += floatval($row->qty_order);
            $total_value += $item_value;
        }

        $data = [
            'title' => 'Export Purchase Order',
            'po' => $po,
            'detail_po' => $filtered_detail_po,
            'total_qty' => $total_qty,
            'total_value' => $total_value
        ];

        $this->load->view('finish/v_pdf', $data);
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
}
