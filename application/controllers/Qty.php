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
        // Start Product
        $this->db->where('status', 1);
        $product = $this->db->get('product');

        // Start Data Transaksi
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

        if (empty($idanalisys_po) || empty($money_currency)) {
            $this->session->set_flashdata('error', 'Data tidak lengkap. Pastikan mata uang sudah dipilih.');
            redirect('qty');
            return;
        }

        // Update data di analisys_po
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
        $result = $this->db->update('analisys_po', $updateAnalisys);

        // Update masing-masing detail ke tabel detail_analisys_po
        if (!empty($qtyList) && is_array($qtyList)) {
            foreach ($qtyList as $detail_id => $qty) {
                $updateData = [
                    'qty_order' => $qty ?: 0,
                    'type_sgs' => !empty($typeSgsList[$detail_id]) ? $typeSgsList[$detail_id] : null,
                    'type_unit' => !empty($typeUnitList[$detail_id]) ? $typeUnitList[$detail_id] : null,
                    'price' => !empty($priceList[$detail_id]) ? $priceList[$detail_id] : null,
                    'description' => !empty($descriptionList[$detail_id]) ? $descriptionList[$detail_id] : null
                ];

                $this->db->where('iddetail_analisys_po', $detail_id);
                $this->db->where('idanalisys_po', $idanalisys_po);
                $this->db->update('detail_analisys_po', $updateData);
            }
        }

        $this->session->set_flashdata('success', 'Data PO berhasil disimpan dan silakan lanjut ke tahap Pre-Order.');
        redirect('qty');
    }

    public function get_detail_analisys_po($idanalisys_po)
    {
        // Ambil data yang sudah ada
        $this->db->select('money_currency, number_po, order_date, name_container, name_supplier');
        $this->db->where('idanalisys_po', $idanalisys_po);
        $analisys_data = $this->db->get('analisys_po')->row();

        $current_currency = $analisys_data->money_currency ?? '';
        $current_number_po = $analisys_data->number_po ?? '';
        $current_order_date = $analisys_data->order_date ?? '';
        $current_name_container = $analisys_data->name_container ?? '';
        $current_name_supplier = $analisys_data->name_supplier ?? '';

        $this->db->select('p.nama_produk, p.sku, d.iddetail_analisys_po, d.type_sgs, d.type_unit, d.latest_incoming_stock, d.last_mouth_sales, d.current_month_sales, d.balance_per_today, d.qty_order, d.price, d.description');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $idanalisys_po);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
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

            // Search section
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

            // Form section
            echo '
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="number_po" class="form-label">No Purchase Order</label>
                    <input type="text" class="form-control" id="number_po" name="number_po" value="' . htmlspecialchars($current_number_po) . '" placeholder="Masukkan nomer PO">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="order_date" class="form-label">Order Date</label>
                    <input type="date" class="form-control" id="order_date" name="order_date" value="' . htmlspecialchars($current_order_date) . '">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="name_container" class="form-label">Shipment Number</label>
                    <input type="text" class="form-control" id="name_container" name="name_container" value="' . htmlspecialchars($current_name_container) . '" placeholder="Masukkan nama container">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="name_supplier" class="form-label">Supplier</label>
                    <input type="text" class="form-control" id="name_supplier" name="name_supplier" value="' . htmlspecialchars($current_name_supplier) . '" placeholder="Masukkan nama supplier">
                </div>
            </div>
            <div class="col-md">
                <div class="mb-3">
                    <label for="money-currency" class="form-label">Money Currency</label>
                    <select name="money-currency" id="money-currency" class="form-select" required>
                        <option value="">Select Money Currency</option>
                        <option value="rmb" ' . ($current_currency == 'rmb' ? 'selected' : '') . '>RMB</option>
                        <option value="idr" ' . ($current_currency == 'idr' ? 'selected' : '') . '>IDR</option>
                    </select>
                </div>
            </div>
        </div>';

            // Table section
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

            if ($found) {
                $no = 1;
                foreach ($products as $product) {
                    $row = $product['row'];
                    $avg_vs_stock_display = $product['avg_vs_stock_display'];
                    $avg_vs_stock = $product['avg_vs_stock'];

                    // Prepare search data for filtering
                    $searchData = htmlspecialchars(strtolower($row->nama_produk . ' ' . $row->sku . ' ' . $row->type_sgs . ' ' . $row->type_unit));

                    // Dropdown for SGS/Non-SGS
                    $select_sgs = '
                    <select class="form-select" name="editTypeSgs[' . $row->iddetail_analisys_po . ']">
                        <option value="">Pilih SGS</option>
                        <option value="sgs" ' . ($row->type_sgs == 'sgs' ? 'selected' : '') . '>SGS</option>
                        <option value="non sgs" ' . ($row->type_sgs == 'non sgs' ? 'selected' : '') . '>Non SGS</option>
                    </select>';

                    // Product row
                    echo '<tr data-search="' . $searchData . '">
                    <td class="text-center">' . $no++ . '</td>
                    <td class="text-center">' . htmlspecialchars($row->nama_produk) . '</td>
                    <td class="text-center">' . htmlspecialchars($row->sku) . '</td>
                    <td class="text-center">' . $row->latest_incoming_stock . '</td>
                    <td class="text-end">' . number_format($row->last_mouth_sales) . '</td>
                    <td class="text-end">' . number_format($row->current_month_sales) . '</td>
                    <td class="text-end">' . number_format($row->balance_per_today) . '</td>
                    <td class="text-end ' . ($avg_vs_stock < 1 ? 'text-danger fw-bold' : '') . '">' . $avg_vs_stock_display . '</td>
                    <td>' . $select_sgs . '</td>
                    <td><input type="text" class="form-control" name="editTypeUnit[' . $row->iddetail_analisys_po . ']" value="' . htmlspecialchars($row->type_unit ?: '') . '" placeholder="Type unit"></td>
                    <td><input type="number" class="form-control text-end" name="editQty[' . $row->iddetail_analisys_po . ']" value="' . htmlspecialchars($row->qty_order ?: '') . '" min="0" step="1"></td>
                    <td><input type="number" class="form-control text-end" name="editPrice[' . $row->iddetail_analisys_po . ']" value="' . htmlspecialchars($row->price ?: '') . '" min="0" step="0.01"></td>
                </tr>';

                    // Description row
                    echo '<tr data-search="' . $searchData . '">
                    <td colspan="12">
                        <div class="mb-2">
                            <span class="fw-bold">Description:</span>
                        </div>
                        <textarea class="form-control" name="editDescription[' . $row->iddetail_analisys_po . ']" placeholder="Keterangan tambahan..." rows="2">' . htmlspecialchars($row->description ?: '') . '</textarea>
                    </td>
                </tr>';
                }
            } else {
                echo '<tr>
                <td colspan="13" class="text-center text-muted py-4">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Tidak ada produk dengan Avg Sales vs Stock di bawah 1.00.
                </td>
            </tr>';
            }

            echo '</tbody>
            </table>
        </div> <!-- end table-scroll -->
    </div> <!-- end table-responsive -->';

            // Summary section (optional)
            echo '
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <small>
                        <i class="fa-solid fa-circle-info me-2"></i>
                        <strong>Informasi:</strong> Sistem hanya menampilkan produk dengan rasio Avg Sales vs Stock di bawah 1.00. 
                        Pastikan semua data quantity dan price telah diisi sebelum memproses PO.
                    </small>
                </div>
            </div>
        </div>';
        } else {
            echo '<div class="alert alert-warning text-center">
            <i class="fa-solid fa-exclamation-triangle me-2"></i>
            Tidak ada produk dalam analisis PO ini atau data belum tersedia.
        </div>';
        }
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
