<?php

use PhpParser\Node\Expr\AssignOp\Div;

defined('BASEPATH') or exit('No direct script access allowed');

class Qty extends CI_Controller
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
        $this->db->where('status', 1);
        $product = $this->db->get('product');

        $this->db->where_in('status_progress', ['Listing', 'Finish']);
        $this->db->order_by('idanalisys_po', 'DESC');
        $data_trx = $this->db->get('analisys_po');

        $data = [
            'title' => 'Analisa PO',
            'product' => $product->result(),
            'data_trx' => $data_trx->result()
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Qty/v_qty');
    }

    public function process()
    {
        // Debug: Log semua POST data
        error_log("=== START PROCESS METHOD ===");
        error_log("POST Data: " . print_r($_POST, true));

        $idanalisys_po = $this->input->post('idanalisys_po');
        $money_currency = $this->input->post('money-currency');
        $number_po = $this->input->post('number_po');
        $order_date = $this->input->post('order_date');
        $name_container = $this->input->post('name_container');
        $name_supplier = $this->input->post('name_supplier');
        $qtyList = $this->input->post('editQty');
        $priceList = $this->input->post('editPrice');
        $typeSgsList = $this->input->post('editTypeSgs');
        $typeUnitList = $this->input->post('editTypeUnit');
        $descriptionList = $this->input->post('editDescription');

        // Data produk tambahan
        $additional_products_data = $this->input->post('additional_products');

        error_log("idanalisys_po: " . $idanalisys_po);
        error_log("money-currency: " . $money_currency);
        error_log("Qty List received: " . print_r($qtyList, true));
        error_log("Price List received: " . print_r($priceList, true));
        error_log("Additional Products received: " . print_r($additional_products_data, true));

        // Validate required fields
        if (empty($idanalisys_po) || empty($money_currency) || empty($number_po) || empty($order_date) || empty($name_supplier)) {
            $this->session->set_flashdata('error', 'Data tidak lengkap. Pastikan semua field wajib diisi.');
            redirect('qty');
            return;
        }

        // Ambil data analisys_po untuk cek type_po
        $analisys_po = $this->db->where('idanalisys_po', $idanalisys_po)->get('analisys_po')->row();
        if (!$analisys_po) {
            $this->session->set_flashdata('error', 'Data analisys PO tidak ditemukan.');
            redirect('qty');
            return;
        }

        $is_local = ($analisys_po->type_po == 'local');

        // Start transaction
        $this->db->trans_start();

        try {
            // Update main analisys_po record
            $updateAnalisys = [
                'money_currency' => $money_currency,
                'number_po' => $number_po,
                'order_date' => $order_date,
                'name_container' => $name_container,
                'name_supplier' => $name_supplier,
                'status_progress' => 'Finish',
                'updated_by' => $this->session->userdata('username'),
                'updated_date' => date('Y-m-d H:i:s')
            ];

            $this->db->where('idanalisys_po', $idanalisys_po);
            $update_result = $this->db->update('analisys_po', $updateAnalisys);

            error_log("Update analisys_po result: " . ($update_result ? 'Success' : 'Failed'));

            // 1. Update produk yang sudah ada di detail analisys po
            if (!empty($qtyList) && is_array($qtyList)) {
                error_log("Processing " . count($qtyList) . " qty items");

                foreach ($qtyList as $detail_id => $qty) {
                    if (!is_numeric($detail_id)) {
                        error_log("Skipping invalid detail_id: " . $detail_id);
                        continue;
                    }

                    $clean_qty = intval($qty);

                    $price = isset($priceList[$detail_id]) ? $priceList[$detail_id] : 0;
                    $clean_price = 0;

                    if (!empty($price) && $price !== '' && $price !== '0') {
                        $price = str_replace(',', '.', $price);
                        $clean_price = floatval($price);
                    }

                    $updateData = [
                        'qty_order' => $clean_qty,
                        'type_sgs' => !empty($typeSgsList[$detail_id]) ? $typeSgsList[$detail_id] : null,
                        'type_unit' => !empty($typeUnitList[$detail_id]) ? $typeUnitList[$detail_id] : null,
                        'price' => $clean_price,
                        'description' => !empty($descriptionList[$detail_id]) ? $descriptionList[$detail_id] : null
                    ];

                    // Jika type_po adalah local, set qty_packing_list sama dengan qty_order
                    if ($is_local && $clean_qty > 0) {
                        $updateData['qty_packing_list'] = $clean_qty;
                    }

                    error_log("Updating detail ID {$detail_id}: " . print_r($updateData, true));

                    $this->db->where('iddetail_analisys_po', $detail_id);
                    $this->db->where('idanalisys_po', $idanalisys_po);
                    $detail_update_result = $this->db->update('detail_analisys_po', $updateData);

                    if ($detail_update_result) {
                        error_log("Successfully updated detail ID {$detail_id}");
                    } else {
                        $error = $this->db->error();
                        error_log("Failed to update detail ID {$detail_id}: " . print_r($error, true));
                    }
                }
            } else {
                error_log("QtyList is empty or not an array");
            }

            // 2. Handle additional products
            if (!empty($additional_products_data) && is_array($additional_products_data)) {
                error_log("Processing " . count($additional_products_data) . " additional products");

                foreach ($additional_products_data as $index => $additional_product) {
                    if (!empty($additional_product['idproduct']) && isset($additional_product['qty_order'])) {
                        $idproduct = (int) $additional_product['idproduct'];
                        $qty_order = (int) $additional_product['qty_order'];
                        $type_sgs = !empty($additional_product['type_sgs']) ? $additional_product['type_sgs'] : null;
                        $type_unit = !empty($additional_product['type_unit']) ? $additional_product['type_unit'] : null; // PERBAIKAN: ambil type_unit
                        $qty_packing_list = !empty($additional_product['qty_packing_list']) ? (int) $additional_product['qty_packing_list'] : 0;
                        $price = !empty($additional_product['price']) ? floatval($additional_product['price']) : 0;
                        $description = !empty($additional_product['description']) ? $additional_product['description'] : null; // PERBAIKAN: gunakan string langsung

                        error_log("Processing additional product #{$index}: ID={$idproduct}, Qty={$qty_order}, Desc='{$description}'");

                        if ($qty_order <= 0) {
                            error_log("Skipping additional product ID {$idproduct} - qty_order is 0 or negative");
                            continue;
                        }

                        // Get product info
                        $product = $this->db->where('idproduct', $idproduct)->get('product')->row();
                        if (!$product) {
                            error_log("Product with ID {$idproduct} not found, skipping...");
                            continue;
                        }

                        // Get sales data for this product (last month sales, current month sales, balance)
                        $last_month_sales = 0;
                        $current_month_sales = 0;
                        $balance_per_today = 0;

                        // You might want to fetch actual sales data here or leave as 0
                        // This is just placeholder - adjust based on your business logic

                        // Insert new detail for additional product
                        $new_detail_data = [
                            'idanalisys_po' => $idanalisys_po,
                            'idproduct' => $idproduct,
                            'product_name_en' => $product->nama_produk,
                            'qty_order' => $qty_order,
                            'qty_packing_list' => $is_local ? $qty_order : $qty_packing_list,
                            'price' => $price,
                            'type_sgs' => $type_sgs,
                            'type_unit' => $type_unit, // PERBAIKAN: masukkan type_unit
                            'latest_incoming_stock_mouth' => date('M Y'),
                            'latest_incoming_stock_pcs' => 0,
                            'last_mouth_sales' => $last_month_sales,
                            'current_month_sales' => $current_month_sales,
                            'balance_per_today' => $balance_per_today,
                            'description' => $description // PERBAIKAN: langsung gunakan string
                        ];

                        error_log("Inserting additional product data: " . print_r($new_detail_data, true));

                        $insert_result = $this->db->insert('detail_analisys_po', $new_detail_data);

                        if ($insert_result) {
                            error_log("Successfully added additional product ID {$idproduct} with qty {$qty_order}");
                        } else {
                            $error = $this->db->error();
                            error_log("Failed to add additional product ID {$idproduct}: " . print_r($error, true));
                        }
                    } else {
                        error_log("Invalid additional product data at index {$index}: " . print_r($additional_product, true));
                    }
                }
            } else {
                error_log("No additional products data received");
            }

            // ===================================================
            // JIKA TYPE_PO = LOCAL, BUAT INSTOCK OTOMATIS
            // ===================================================
            if ($is_local) {
                error_log("Processing LOCAL PO, creating instock automatically...");

                // Cek apakah instock sudah ada untuk PO ini
                $instock_code = $number_po;
                $existing_instock = $this->db->where('instock_code', $instock_code)->get('instock')->row();

                if (!$existing_instock) {
                    // Get default warehouse ID
                    $default_warehouse = $this->db->where('is_default', 1)->get('gudang')->row();
                    $idgudang = $default_warehouse ? $default_warehouse->idgudang : 1;

                    // Buat data instock baru
                    $instock_data = [
                        'instock_code' => $instock_code,
                        'idgudang' => $idgudang,
                        'tgl_terima' => date('Y-m-d'),
                        'jam_terima' => date('H:i:s'),
                        'datetime' => date('Y-m-d H:i:s'),
                        'user' => $this->session->userdata('username'),
                        'distribution_date' => $order_date,
                        'kategori' => 'PEMBELIAN',
                        'no_manual' => $number_po,
                        'created_by' => $this->session->userdata('username'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'status_verification' => 1 // Langsung diverifikasi untuk local PO
                    ];

                    $this->db->insert('instock', $instock_data);
                    $idinstock = $this->db->insert_id();
                    error_log("Created instock with ID: " . $idinstock);

                    // Ambil semua detail analisys_po untuk PO ini (termasuk produk tambahan)
                    $details = $this->db
                        ->select('dap.*, p.sku, p.nama_produk')
                        ->from('detail_analisys_po dap')
                        ->join('product p', 'dap.idproduct = p.idproduct', 'left')
                        ->where('dap.idanalisys_po', $idanalisys_po)
                        ->where('dap.qty_order >', 0)
                        ->get()
                        ->result();

                    $total_qty_instock = 0;

                    // Insert ke detail_instock
                    foreach ($details as $detail) {
                        $detail_instock_data = [
                            'instock_code' => $instock_code,
                            'sku' => $detail->sku,
                            'nama_produk' => $detail->nama_produk,
                            'jumlah' => $detail->qty_order, // Jumlah sama dengan qty_order
                            'sisa' => 0,
                            'keterangan' => 'Auto-instock dari Local PO: ' . $number_po . ($detail->description ? ' - ' . $detail->description : '')
                        ];

                        $this->db->insert('detail_instock', $detail_instock_data);

                        // Update stock di product_stock
                        $stock = $this->db->where('idproduct', $detail->idproduct)
                            ->where('idgudang', $idgudang)
                            ->get('product_stock')
                            ->row();

                        if (!$stock) {
                            $this->db->insert('product_stock', [
                                'idproduct' => $detail->idproduct,
                                'idgudang' => $idgudang,
                                'stok' => $detail->qty_order
                            ]);
                        } else {
                            $this->db->set('stok', "stok + {$detail->qty_order}", false)
                                ->where('idproduct', $detail->idproduct)
                                ->where('idgudang', $idgudang)
                                ->update('product_stock');
                        }

                        $total_qty_instock += $detail->qty_order;
                        error_log("Added to instock: SKU {$detail->sku}, Qty: {$detail->qty_order}");
                    }

                    // Update analisys_po untuk menandakan sudah dibuat instock
                    $this->db->where('idanalisys_po', $idanalisys_po)
                        ->update('analisys_po', [
                            'status_verification' => 1,
                            'no_manual' => $number_po,
                            'idgudang' => $idgudang
                        ]);

                    error_log("Auto-instock created successfully for local PO. Total products: " . count($details) . ", Total Qty: " . $total_qty_instock);
                } else {
                    error_log("Instock already exists for PO: " . $number_po);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal memperbarui database.');
            }

            // Hitung total produk yang diproses
            $total_products = 0;
            $additional_products_count = 0;

            if (!empty($qtyList)) {
                $total_products += count($qtyList);
            }

            if (!empty($additional_products_data)) {
                $additional_products_count = count($additional_products_data);
                $total_products += $additional_products_count;
            }

            // Pesan sukses berdasarkan type_po
            $success_message = 'Data PO berhasil disimpan. ';
            $success_message .= 'Total produk yang diproses: ' . $total_products . '.';

            if ($additional_products_count > 0) {
                $success_message .= ' Termasuk ' . $additional_products_count . ' produk tambahan.';
            }

            if ($is_local) {
                $success_message .= ' Instock telah dibuat otomatis untuk local PO.';
            } else {
                $success_message .= ' Silakan lanjut ke tahap Pre-Order.';
            }

            $this->session->set_flashdata('success', $success_message);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            error_log("Transaction failed: " . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            redirect('qty');
            return;
        }

        error_log("=== END PROCESS METHOD ===");
        redirect('qty');
    }

    public function get_detail_analisys_po($idanalisys_po)
    {
        $this->db->select('money_currency, number_po, order_date, name_container, name_supplier, type_po');
        $this->db->where('idanalisys_po', $idanalisys_po);
        $analisys_data = $this->db->get('analisys_po')->row();

        $current_currency = $analisys_data->money_currency ?? '';
        $current_number_po = $analisys_data->number_po ?? '';
        $current_order_date = $analisys_data->order_date ?? '';
        $current_name_container = $analisys_data->name_container ?? '';
        $current_name_supplier = $analisys_data->name_supplier ?? '';
        $current_type_po = $analisys_data->type_po ?? '';

        // Ambil semua number_po yang sudah ada (kecuali yang sedang diedit)
        $this->db->select('number_po');
        $this->db->where('number_po IS NOT NULL');
        $this->db->where('number_po !=', '');
        $this->db->where('idanalisys_po !=', $idanalisys_po);
        $existing_pos = $this->db->get('analisys_po')->result_array();

        $existing_numbers = array_column($existing_pos, 'number_po');

        // Konversi ke JSON untuk JavaScript
        $existing_numbers_json = json_encode($existing_numbers);
        $existing_numbers_js = htmlspecialchars($existing_numbers_json, ENT_QUOTES, 'UTF-8');

        $this->db->select('idanalisys_po');
        $this->db->where('idanalisys_po <', $idanalisys_po);
        $this->db->order_by('idanalisys_po', 'DESC');
        $this->db->limit(1);
        $prev_po = $this->db->get('analisys_po')->row();

        $previous_qty = [];

        if ($prev_po) {
            $prev_id = $prev_po->idanalisys_po;
            $this->db->select('idproduct, qty_order');
            $this->db->where('idanalisys_po', $prev_id);
            $prev_details = $this->db->get('detail_analisys_po')->result();

            foreach ($prev_details as $pd) {
                $previous_qty[$pd->idproduct] = $pd->qty_order;
            }
        }

        $this->db->select('
        p.nama_produk, p.sku, 
        d.iddetail_analisys_po, d.type_sgs, d.type_unit, 
        d.latest_incoming_stock_mouth, d.latest_incoming_stock_pcs, d.last_mouth_sales, 
        d.current_month_sales, d.balance_per_today,
        d.qty_order, d.price, d.description, d.idproduct, a.status_progress
    ');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->join('analisys_po a', 'a.idanalisys_po = d.idanalisys_po', 'left');
        $this->db->where('d.idanalisys_po', $idanalisys_po);
        $query = $this->db->get();

        // Ambil semua produk aktif untuk dropdown tambah produk
        $all_products = $this->db->select('idproduct, sku, nama_produk')
            ->where('status', 1)
            ->order_by('nama_produk', 'asc')
            ->get('product')
            ->result();

        $all_products_json = json_encode(array_map(function ($product) {
            return [
                'id' => $product->idproduct,
                'sku' => $product->sku,
                'nama' => $product->nama_produk,
                'text' => $product->sku . ' - ' . $product->nama_produk
            ];
        }, $all_products));

        if ($query->num_rows() == 0) {
            echo '<div class="alert alert-warning text-center">Tidak ada produk dalam analisis PO ini.</div>';
            return;
        }

        $products = [];
        $found = false;

        foreach ($query->result() as $row) {
            $total_sales = $row->current_month_sales;
            $avg_sales = $total_sales / 4;

            if ($avg_sales > 0) {
                $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                $avg_vs_stock_display = number_format($avg_vs_stock, 2);

                if ($avg_vs_stock < 1) {
                    $found = true;

                    $row->qty_previous = $previous_qty[$row->idproduct] ?? null;

                    $products[] = [
                        'row' => $row,
                        'avg_vs_stock' => $avg_vs_stock,
                        'avg_vs_stock_display' => $avg_vs_stock_display
                    ];
                }
            }
        }

        usort($products, function ($a, $b) {
            return $a['avg_vs_stock'] <=> $b['avg_vs_stock'];
        });

        echo '<input type="hidden" name="idanalisys_po" value="' . $idanalisys_po . '">';
        echo '<input type="hidden" id="all_products_data" value=\'' . htmlspecialchars($all_products_json, ENT_QUOTES, 'UTF-8') . '\'>';
        echo '<input type="hidden" id="current_type_po" value="' . $current_type_po . '">';

        echo '
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                <input type="text" id="searchDetailTable" class="form-control" placeholder="Cari produk, kode, atau lainnya...">
                <button type="button" class="btn btn-outline-secondary" id="clearSearch">Clear</button>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-text" id="searchResultInfo">Menampilkan semua data</div>
        </div>
    </div>';

        // TOMBOL TAMBAH PRODUK - TAMPIL UNTUK SEMUA TIPE PO
        echo '
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-success btn-sm" id="btnTambahProdukPO">
                    <i class="fa-solid fa-plus"></i> Tambah Produk
                </button>
            </div>
        </div>
    </div>';

        echo '
    <div class="row mb-4">
        <div class="col-md-4">
            <label class="form-label">No Purchase Order <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control" 
                   id="number_po" 
                   name="number_po" 
                   value="' . htmlspecialchars($current_number_po) . '" 
                   placeholder="Nomer Purchase Order" 
                   required
                   data-existing-po=\'' . $existing_numbers_js . '\'
                   data-current-po=\'' . htmlspecialchars($current_number_po) . '\'>
            <div class="invalid-feedback" id="po-error">
                Nomor PO sudah digunakan sebelumnya.
            </div>
            <div class="valid-feedback">
                Nomor PO tersedia.
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Order Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="order_date" name="order_date" value="' . htmlspecialchars($current_order_date) . '" placeholder="Order Date" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Shipment Number</label>
            <input type="text" class="form-control" id="name_container" name="name_container" value="' . htmlspecialchars($current_name_container) . '" placeholder="Name Container">
        </div>
        <div class="col-md-4 mt-3">
            <label class="form-label">Supplier <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name_supplier" name="name_supplier" value="' . htmlspecialchars($current_name_supplier) . '" placeholder="Name Supplier" required>
        </div>
        <div class="col-md mt-3">
            <label class="form-label">Money Currency <span class="text-danger">*</span></label>
            <select name="money-currency" id="money-currency" class="form-select" required>
                <option value="">Select Money Currency</option>
                <option value="rmb" ' . ($current_currency == 'rmb' ? 'selected' : '') . '>RMB</option>
                <option value="idr" ' . ($current_currency == 'idr' ? 'selected' : '') . '>IDR</option>
            </select>
        </div>
    </div>';

        // FORM PENCARIAN PRODUK (Sembunyikan Awalnya)
        echo '
    <div class="card mb-3" id="productSearchForm" style="display: none;">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fa-solid fa-search"></i> Cari Produk</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" id="searchProductPO" class="form-control" placeholder="Ketik SKU atau nama produk...">
                        <button type="button" class="btn btn-outline-secondary" id="clearProductSearch">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-secondary" id="btnBatalCariProdukPO">
                        <i class="fa-solid fa-times"></i> Batal
                    </button>
                </div>
            </div>
            <div class="mt-3">
                <div id="productSearchResultsPO" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; display: none;">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="20%">SKU</th>
                                <th width="60%">Nama Produk</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="productListPO">
                            <!-- Daftar produk akan diisi di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>';

        // TABEL PRODUK TAMBAHAN
        echo '
    <div class="card mb-3" id="additionalProductsTableContainer" style="display: none;">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="fa-solid fa-list"></i> Produk Tambahan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="additionalProductsTablePO">
                    <thead class="table-success">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">SKU</th>
                            <th width="40%">Nama Produk</th>
                            <th width="15%">Type SGS</th>
                            <th width="15%">Qty Order</th>
                            <th width="10%">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="additionalProductsBody">
                        <!-- Produk tambahan akan muncul di sini -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>';

        // TABEL PRODUK UTAMA
        echo '
    <div class="table-responsive">
        <div class="table-scroll">
            <table class="table table-bordered table-striped table-hover" style="font-size: small;" id="detailTable">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center" width="100">Product</th>
                        <th class="text-center" width="100">Product Code</th>
                        <th class="text-center">Last Coming</th>
                        <th class="text-center">L Month Sales</th>
                        <th class="text-center">Current Sales</th>
                        <th class="text-center">Balance</th>
                        <th class="text-center">Avg Ratio</th>
                        <th class="text-center" width="100">Type SGS</th>
                        <th class="text-center" width="100">Type Unit</th>
                        <th class="text-center" width="100">Qty Order</th>
                        <th class="text-center" width="125">Price</th>
                    </tr>
                </thead>
                <tbody id="detailTableBody">';

        if (!$found) {
            echo '
        <tr id="noDataRow">
            <td colspan="12" class="text-center text-muted py-4">
                <i class="fa-solid fa-info-circle me-2"></i>
                Tidak ada produk dengan Avg < 1.00.
            </td>
        </tr>';
            echo '</tbody></table></div></div>';
            return;
        }

        $no = 1;
        foreach ($products as $product) {
            $row = $product['row'];
            $avg_vs_stock_display = $product['avg_vs_stock_display'];

            // Tooltip untuk informasi qty sebelumnya
            $tooltip = '';
            if ($row->latest_incoming_stock_pcs !== null) {
                $tooltip = 'title="Last Order Qty: ' . $row->latest_incoming_stock_pcs . '"';
            }

            // Jika ada qty_previous, tambahkan informasi
            if (!empty($row->qty_previous)) {
                $tooltip = 'title="Previous PO Qty: ' . $row->qty_previous . '"';
            }

            // Hanya ambil nilai dari database jika sudah ada, jika tidak kosongkan
            $display_price = (!empty($row->price) && is_numeric($row->price)) ? $row->price : '';
            $display_qty = (!empty($row->qty_order) && is_numeric($row->qty_order)) ? $row->qty_order : '';

            $searchData = strtolower(
                htmlspecialchars($row->nama_produk) . ' ' .
                    htmlspecialchars($row->sku) . ' ' .
                    htmlspecialchars($row->type_sgs ?: '') . ' ' .
                    htmlspecialchars($row->type_unit ?: '') . ' ' .
                    htmlspecialchars($row->description ?: '')
            );

            echo '
        <tr data-search="' . htmlspecialchars($searchData) . '" data-product-id="' . $row->iddetail_analisys_po . '">
            <td class="text-center">' . $no . '</td>
            <td class="text-center">' . htmlspecialchars($row->nama_produk) . '</td>
            <td class="text-center">' . htmlspecialchars($row->sku) . '</td>
            <td class="text-center"><span class="text-primary"><i class="fa-solid fa-calendar"></i> ' . $row->latest_incoming_stock_mouth . '</span><br><span class="text-success"><i class="fa-solid fa-box"></i> ' . $row->latest_incoming_stock_pcs . ' Pcs</span></td>
            <td class="text-end">' . number_format($row->last_mouth_sales) . '</td>
            <td class="text-end">' . number_format($row->current_month_sales) . '</td>
            <td class="text-end">' . number_format($row->balance_per_today) . '</td>
            <td class="text-end ' . ($product['avg_vs_stock'] < 1 ? 'text-danger fw-bold' : '') . '">' . $avg_vs_stock_display . '</td>

            <td>
                <select class="form-select type-sgs-select" name="editTypeSgs[' . $row->iddetail_analisys_po . ']" data-id="' . $row->iddetail_analisys_po . '">
                    <option value="">Pilih SGS</option>
                    <option value="sgs" ' . ($row->type_sgs == 'sgs' ? 'selected' : '') . '>SGS</option>
                    <option value="non sgs" ' . ($row->type_sgs == 'non sgs' ? 'selected' : '') . '>Non SGS</option>
                </select>
            </td>

            <td>
                <input type="text" class="form-control type-unit-input"
                    name="editTypeUnit[' . $row->iddetail_analisys_po . ']"
                    value="' . htmlspecialchars($row->type_unit ?: '') . '"
                    placeholder="Unit">
            </td>

            <td>
                <input type="number" class="form-control text-end qty-input"
                    ' . $tooltip . '
                    style="' . (empty($row->qty_order) && $row->status_progress == 'Listing' ? 'background:#ffcccc;color:#333;' : '') . '"
                    name="editQty[' . $row->iddetail_analisys_po . ']"
                    value="' . $display_qty . '"
                    placeholder="Isi manual"
                    min="0"
                    oninput="validateNumberInput(this)">
            </td>

            <td>
                <input type="number" class="form-control text-end price-input"
                    name="editPrice[' . $row->iddetail_analisys_po . ']"
                    value="' . $display_price . '"
                    placeholder="Price"
                    min="0"
                    step="0.01"
                    oninput="validateNumberInput(this)">
            </td>
        </tr>

        <tr data-search="' . htmlspecialchars($searchData) . '" data-product-id="' . $row->iddetail_analisys_po . '">
            <td colspan="12">
                <div class="mb-2"><b>Description:</b></div>
                <textarea class="form-control description-textarea"
                    name="editDescription[' . $row->iddetail_analisys_po . ']"
                    rows="2"
                    placeholder="Deskripsi produk...">' . htmlspecialchars($row->description ?: '') . '</textarea>
            </td>
        </tr>';

            $no++;
        }

        echo '
            </tbody>
        </table>
    </div>
</div>';

        echo '
    <script>
        function validateNumberInput(input) {
            // Ensure value is not negative
            if (parseFloat(input.value) < 0) {
                input.value = 0;
            }
        }
    </script>';
    }

    public function cancel($idanalisys_po)
    {
        $this->db->set('status_progress', 'Cancel');
        $this->db->where('idanalisys_po', $idanalisys_po);
        $this->db->update('analisys_po');

        $this->session->set_flashdata('success', 'Pemesanan berhasil dibatalkan.');
        redirect('qty');
    }
}
