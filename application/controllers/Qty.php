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
        // ==========================
        // 0️⃣ AMBIL DATA HEADER PO
        // ==========================
        $this->db->select('money_currency, number_po, order_date, name_container, name_supplier');
        $this->db->where('idanalisys_po', $idanalisys_po);
        $analisys_data = $this->db->get('analisys_po')->row();

        $current_currency = $analisys_data->money_currency ?? '';
        $current_number_po = $analisys_data->number_po ?? '';
        $current_order_date = $analisys_data->order_date ?? '';
        $current_name_container = $analisys_data->name_container ?? '';
        $current_name_supplier = $analisys_data->name_supplier ?? '';

        // ====================================
        // 1️⃣ CARI ANALISIS PO SEBELUMNYA
        // ====================================
        $this->db->select('idanalisys_po');
        $this->db->where('idanalisys_po <', $idanalisys_po);
        $this->db->order_by('idanalisys_po', 'DESC');
        $this->db->limit(1);
        $prev_po = $this->db->get('analisys_po')->row();

        $previous_qty = []; // key: idproduct → previous qty_order

        if ($prev_po) {
            $prev_id = $prev_po->idanalisys_po;
            $this->db->select('idproduct, qty_order');
            $this->db->where('idanalisys_po', $prev_id);
            $prev_details = $this->db->get('detail_analisys_po')->result();

            foreach ($prev_details as $pd) {
                $previous_qty[$pd->idproduct] = $pd->qty_order;
            }
        }

        // ====================================
        // 2️⃣ AMBIL DETAIL PRODUK SAAT INI
        // ====================================
        $this->db->select('
        p.nama_produk, p.sku, 
        d.iddetail_analisys_po, d.type_sgs, d.type_unit, 
        d.latest_incoming_stock, d.last_mouth_sales, 
        d.current_month_sales, d.balance_per_today,
        d.qty_order, d.price, d.description, d.idproduct
    ');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $idanalisys_po);
        $query = $this->db->get();

        if ($query->num_rows() == 0) {
            echo '<div class="alert alert-warning text-center">Tidak ada produk dalam analisis PO ini.</div>';
            return;
        }

        $products = [];
        $found = false;

        foreach ($query->result() as $row) {

            // hitung avg ratio
            $total_sales = $row->current_month_sales;
            $avg_sales = $total_sales / 4;

            if ($avg_sales > 0) {
                $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                $avg_vs_stock_display = number_format($avg_vs_stock, 2);

                if ($avg_vs_stock < 1) {
                    $found = true;

                    // Inject qty sebelumnya
                    $row->qty_previous = $previous_qty[$row->idproduct] ?? null;

                    $products[] = [
                        'row' => $row,
                        'avg_vs_stock' => $avg_vs_stock,
                        'avg_vs_stock_display' => $avg_vs_stock_display
                    ];
                }
            }
        }

        // sort by avg ratio
        usort($products, function ($a, $b) {
            return $a['avg_vs_stock'] <=> $b['avg_vs_stock'];
        });

        // Hidden input
        echo '<input type="hidden" name="idanalisys_po" value="' . $idanalisys_po . '">';

        // ====================================
        // 3️⃣ SEARCH BAR
        // ====================================
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

        // ====================================
        // 4️⃣ FORM HEADER
        // ====================================
        echo '
    <div class="row mb-4">
        <div class="col-md-4">
            <label class="form-label">No Purchase Order</label>
            <input type="text" class="form-control" id="number_po" name="number_po" value="' . htmlspecialchars($current_number_po) . '">
        </div>
        <div class="col-md-4">
            <label class="form-label">Order Date</label>
            <input type="date" class="form-control" id="order_date" name="order_date" value="' . htmlspecialchars($current_order_date) . '">
        </div>
        <div class="col-md-4">
            <label class="form-label">Shipment Number</label>
            <input type="text" class="form-control" id="name_container" name="name_container" value="' . htmlspecialchars($current_name_container) . '">
        </div>
        <div class="col-md-4 mt-3">
            <label class="form-label">Supplier</label>
            <input type="text" class="form-control" id="name_supplier" name="name_supplier" value="' . htmlspecialchars($current_name_supplier) . '">
        </div>
        <div class="col-md mt-3">
            <label class="form-label">Money Currency</label>
            <select name="money-currency" id="money-currency" class="form-select">
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
                <tbody>';

        if (!$found) {
            echo '
        <tr>
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

            // qty default → ambil previous kalau ada
            $default_qty = $row->qty_previous !== null ? $row->qty_previous : $row->qty_order;

            // tooltips previous qty
            $tooltip = $row->qty_previous !== null
                ? 'title="Last Order Qty: ' . $row->qty_previous . '"'
                : '';

            echo '
        <tr>
            <td class="text-center">' . $no++ . '</td>
            <td class="text-center">' . htmlspecialchars($row->nama_produk) . '</td>
            <td class="text-center">' . htmlspecialchars($row->sku) . '</td>
            <td class="text-center">' . $row->latest_incoming_stock . '</td>
            <td class="text-end">' . number_format($row->last_mouth_sales) . '</td>
            <td class="text-end">' . number_format($row->current_month_sales) . '</td>
            <td class="text-end">' . number_format($row->balance_per_today) . '</td>
            <td class="text-end ' . ($product['avg_vs_stock'] < 1 ? 'text-danger fw-bold' : '') . '">' . $avg_vs_stock_display . '</td>

            <td>
                <select class="form-select" name="editTypeSgs[' . $row->iddetail_analisys_po . ']">
                    <option value="">Pilih SGS</option>
                    <option value="sgs" ' . ($row->type_sgs == 'sgs' ? 'selected' : '') . '>SGS</option>
                    <option value="non sgs" ' . ($row->type_sgs == 'non sgs' ? 'selected' : '') . '>Non SGS</option>
                </select>
            </td>

            <td>
                <input type="text" class="form-control"
                    name="editTypeUnit[' . $row->iddetail_analisys_po . ']"
                    value="' . htmlspecialchars($row->type_unit ?: '') . '">
            </td>

            <td>
                <input type="number" class="form-control text-end"
                    ' . $tooltip . '
                    style="' . (!empty($row->qty_previous) ? 'background:red;color:white;' : '') . '"
                    name="editQty[' . $row->iddetail_analisys_po . ']"
                    value="' . (is_numeric($default_qty) ? $default_qty : 0) . '"
                    min="0" step="1">
            </td>

            <td>
                <input type="number" class="form-control text-end"
                    name="editPrice[' . $row->iddetail_analisys_po . ']"
                    value="' . (is_numeric($row->price) ? $row->price : 0) . '"
                    min="0" step="0.01">
            </td>
        </tr>

        <tr>
            <td colspan="12">
                <div class="mb-2"><b>Description:</b></div>
                <textarea class="form-control"
                    name="editDescription[' . $row->iddetail_analisys_po . ']"
                    rows="2">' . htmlspecialchars($row->description ?: '') . '</textarea>
            </td>
        </tr>';
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
