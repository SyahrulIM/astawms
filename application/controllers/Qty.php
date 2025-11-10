<?php
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
        $this->db->where('status_progress', 'Listing');
        $this->db->order_by('idanalisys_po', 'DESC');
        $data_trx = $this->db->get('analisys_po');

        $data = [
            'title' => 'Analisys PO',
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

        // Debug: Cek data yang diterima
        error_log("ID Analisys PO: " . $idanalisys_po);
        error_log("Mata Uang: " . $money_currency);
        error_log("QTY List: " . print_r($qtyList, true));

        // Update mata uang dan status di analisys_po
        $updateAnalisys = [
            'money_currency' => $money_currency,
            'status_progress' => 'Qty',
            'updated_by' => $this->session->userdata('username'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $this->db->where('idanalisys_po', $idanalisys_po);
        $result = $this->db->update('analisys_po', $updateAnalisys);

        error_log("Update analisys_po result: " . ($result ? 'Success' : 'Failed'));

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
                $detailResult = $this->db->update('detail_analisys_po', $updateData);

                error_log("Update detail {$detail_id}: " . ($detailResult ? 'Success' : 'Failed'));
            }
        }

        $this->session->set_flashdata('success', 'QTY berhasil disimpan dan silakan lanjut ke tahap Pre-Order.');
        redirect('qty');
    }

    public function get_detail_analisys_po($idanalisys_po)
    {
        // Ambil data mata uang yang sudah ada
        $this->db->select('money_currency');
        $this->db->where('idanalisys_po', $idanalisys_po);
        $analisys_data = $this->db->get('analisys_po')->row();

        $current_currency = $analisys_data->money_currency ?? '';

        $this->db->select('p.nama_produk, p.sku, d.iddetail_analisys_po, d.type_sgs, d.type_unit, d.latest_incoming_stock, d.last_mouth_sales, d.current_month_sales, d.balance_per_today, d.qty_order, d.price, d.description');
        $this->db->from('detail_analisys_po d');
        $this->db->join('product p', 'p.idproduct = d.idproduct', 'left');
        $this->db->where('d.idanalisys_po', $idanalisys_po);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo '<input type="hidden" name="idanalisys_po" value="' . $idanalisys_po . '">';

            // Tampilkan form mata uang dengan selected value
            echo '
            <div class="row mb-4">
                <div class="col-md">
                    <div class="mb-3">
                    <label for="number_po" class="form-label">Nomer PO</label>
                    <input type="text" class="form-control" id="number_po" name="number_po">
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md">
                    <div class="mb-3">
                        <label for="money-currency" class="form-label">Mata Uang</label>
                        <select name="money-currency" id="money-currency" class="form-select" required>
                            <option value="">Pilih Mata Uang</option>
                            <option value="rmb" ' . ($current_currency == 'rmb' ? 'selected' : '') . '>RMB</option>
                            <option value="idr" ' . ($current_currency == 'idr' ? 'selected' : '') . '>IDR</option>
                        </select>
                    </div>
                </div>';

            echo '<div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Produk</th>
                            <th>SKU</th>
                            <th>Stock Masuk Terakhir</th>
                            <th>Penjualan Bulan Lalu</th>
                            <th>Penjualan Bulan Ini</th>
                            <th>Saldo Hari Ini</th>
                            <th>Avg Sales vs Stock (Bulan)</th>
                            <th>SGS/Non-SGS</th>
                            <th>Tipe Satuan</th>
                            <th width="120">Qty Order</th>
                            <th width="150">Price per Unit</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>';

            $found = false;
            $no = 1;
            foreach ($query->result() as $row) {
                $total_sales = $row->current_month_sales;
                $avg_sales = $total_sales / 4;

                if ($avg_sales > 0) {
                    $avg_vs_stock = floatval($row->balance_per_today) / $avg_sales;
                    if ($avg_vs_stock >= 1) continue;
                    $found = true;
                    $avg_vs_stock_display = number_format($avg_vs_stock, 2);
                } else {
                    continue;
                }

                // dropdown SGS/Non-SGS
                $select_sgs =
                    '<select class="form-select" name="editTypeSgs[' . $row->iddetail_analisys_po . ']">
                        <option value="">Pilih SGS</option>
                        <option value="sgs" ' . ($row->type_sgs == 'sgs' ? 'selected' : '') . '>SGS</option>
                        <option value="non sgs" ' . ($row->type_sgs == 'non sgs' ? 'selected' : '') . '>Non SGS</option>
                    </select>';

                echo '<tr>
                    <td>' . $no++ . '</td>
                    <td>' . htmlspecialchars($row->nama_produk) . '</td>
                    <td>' . htmlspecialchars($row->sku) . '</td>
                    <td class="text-center">' . htmlspecialchars($row->latest_incoming_stock) . '</td>
                    <td class="text-center">' . htmlspecialchars($row->last_mouth_sales) . '</td>
                    <td class="text-center">' . htmlspecialchars($row->current_month_sales) . '</td>
                    <td class="text-center">' . htmlspecialchars($row->balance_per_today) . '</td>
                    <td class="text-center ' . ($avg_vs_stock < 1 ? 'text-danger fw-bold' : '') . '">' . $avg_vs_stock_display . '</td>
                    <td>' . $select_sgs . '</td>
                    <td><input type="text" class="form-control" name="editTypeUnit[' . $row->iddetail_analisys_po . ']" value="' . htmlspecialchars($row->type_unit ?: '') . '"></td>
                    <td><input type="number" class="form-control text-center" name="editQty[' . $row->iddetail_analisys_po . ']" value="' . htmlspecialchars($row->qty_order ?: '') . '" min="0" required></td>
                    <td><input type="number" class="form-control text-end" name="editPrice[' . $row->iddetail_analisys_po . ']" value="' . htmlspecialchars($row->price ?: '') . '" min="0" step="0.01"></td>
                    <td><textarea class="form-control" name="editDescription[' . $row->iddetail_analisys_po . ']" placeholder="Keterangan" rows="2">' . htmlspecialchars($row->description ?: '') . '</textarea></td>
                </tr>';
            }

            if (!$found) {
                echo '<tr>
                    <td colspan="13" class="text-center text-muted py-4">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Tidak ada produk dengan Avg Sales vs Stock di bawah 1.00.
                    </td>
                </tr>';
            }

            echo '</tbody>
                </table>
            </div>';
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
        redirect('qty');
    }
}
