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

        error_log("idanalisys_po: " . $idanalisys_po);
        error_log("money-currency: " . $money_currency);
        error_log("Qty List received: " . print_r($qtyList, true));
        error_log("Price List received: " . print_r($priceList, true));

        // Validate required fields
        if (empty($idanalisys_po) || empty($money_currency) || empty($number_po) || empty($order_date) || empty($name_supplier)) {
            $this->session->set_flashdata('error', 'Data tidak lengkap. Pastikan semua field wajib diisi.');
            redirect('qty');
            return;
        }

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

        // Process detail records
        if (!empty($qtyList) && is_array($qtyList)) {
            error_log("Processing " . count($qtyList) . " qty items");

            foreach ($qtyList as $detail_id => $qty) {
                // Validate detail_id is numeric
                if (!is_numeric($detail_id)) {
                    error_log("Skipping invalid detail_id: " . $detail_id);
                    continue;
                }

                // Convert quantity
                $clean_qty = intval($qty);

                // Get price
                $price = isset($priceList[$detail_id]) ? $priceList[$detail_id] : 0;
                $clean_price = 0;

                if (!empty($price) && $price !== '' && $price !== '0') {
                    // Handle decimal separator
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

                error_log("Updating detail ID {$detail_id}: " . print_r($updateData, true));

                // Update the detail record
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

        error_log("=== END PROCESS METHOD ===");

        $this->session->set_flashdata('success', 'Data PO berhasil disimpan dan silakan lanjut ke tahap Pre-Order.');
        redirect('qty');
    }

    public function get_detail_analisys_po($idanalisys_po)
    {
        $this->db->select('money_currency, number_po, order_date, name_container, name_supplier');
        $this->db->where('idanalisys_po', $idanalisys_po);
        $analisys_data = $this->db->get('analisys_po')->row();

        $current_currency = $analisys_data->money_currency ?? '';
        $current_number_po = $analisys_data->number_po ?? '';
        $current_order_date = $analisys_data->order_date ?? '';
        $current_name_container = $analisys_data->name_container ?? '';
        $current_name_supplier = $analisys_data->name_supplier ?? '';

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

        echo '
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label">No Purchase Order <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="number_po" name="number_po" value="' . htmlspecialchars($current_number_po) . '" placeholder="Nomer Purchase Order" required>
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
                            <th class="text-center">Last Sales</th>
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
            $default_qty = $row->qty_order ?? $row->latest_incoming_stock_pcs;

            $tooltip = $row->latest_incoming_stock_pcs !== null
                ? 'title="Last Order Qty: ' . $row->latest_incoming_stock_pcs . '"'
                : '';

            $display_price = is_numeric($row->price) ? $row->price : '0';
            $display_qty = is_numeric($default_qty) ? $default_qty : '0';

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
                        value="' . htmlspecialchars($row->type_unit ?: '') . '">
                </td>

                <td>
                    <input type="number" class="form-control text-end qty-input"
                        ' . $tooltip . '
                        style="' . (empty($row->qty_order) && $row->status_progress == 'Listing' ? 'background:#ffcccc;color:#333;' : '') . '"
                        name="editQty[' . $row->iddetail_analisys_po . ']"
                        value="' . $display_qty . '"
                        oninput="validateNumberInput(this)">
                </td>

                <td>
                    <input type="number" class="form-control text-end price-input"
                        name="editPrice[' . $row->iddetail_analisys_po . ']"
                        value="' . $display_price . '"
                        oninput="validateNumberInput(this)">
                </td>
            </tr>

            <tr data-search="' . htmlspecialchars($searchData) . '" data-product-id="' . $row->iddetail_analisys_po . '">
                <td colspan="12">
                    <div class="mb-2"><b>Description:</b></div>
                    <textarea class="form-control description-textarea"
                        name="editDescription[' . $row->iddetail_analisys_po . ']"
                        rows="2">' . htmlspecialchars($row->description ?: '') . '</textarea>
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
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <small><strong>Informasi:</strong> Sistem otomatis mengisi Qty Order berdasarkan analisis PO sebelumnya.</small>
                </div>
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
