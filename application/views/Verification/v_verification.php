<!-- Page content -->
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col">
            <h1>Verifikasi Transaksi</h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <?php if ($this->session->flashdata('success')) : ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
            <?php elseif ($this->session->flashdata('error')) : ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
            <?php endif; ?>
            <form action="<?= base_url('verification/exportExcel') ?>" method="post">
                <div class="card mt-3">
                    <div class="card-header">
                        <strong>Filter</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-1">
                            <label for="filterInputStart" class="form-label">Tanggal Input (Start)</label>
                            <input type="date" id="filterInputStart" class="form-control" name="filterInputStart">
                        </div>
                        <div class="mb-1">
                            <label for="filterInputEnd" class="form-label">Tanggal Input (End)</label>
                            <input type="date" id="filterInputEnd" class="form-control" name="filterInputEnd">
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-success"><i class="fa-solid fa-print"></i> Print Excel</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row mb-3">
                <table id="tableproduct" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tipe</th>
                            <th>Transaction Code</th>
                            <th>Tanggal Input</th>
                            <th>Tanggal Distribusi</th>
                            <th>User Penginput</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $i => $trx) : ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= ucfirst($trx->tipe) ?></td>
                            <td><?= $trx->kode_transaksi ?></td>
                            <td><?= $trx->tanggal . ' ' . $trx->jam ?></td>
                            <td><?= $trx->distribution_date ?></td>
                            <td><?= $trx->user ?></td>
                            <td>
                                <h5>
                                    <?php if ($trx->status_verification == 1) : ?>
                                    <span class="badge bg-success">Accept</span>
                                    <?php elseif ($trx->status_verification == 2) : ?>
                                    <span class="badge bg-danger">Reject</span>
                                    <?php else : ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </h5>
                            </td>
                            <td>
                                <?php if ($trx->status_verification == 0) : ?>
                                <?php if (in_array($this->session->userdata('idrole'), [1, 3, 5])) : ?>
                                <button type="button" class="btn btn-info btn-verifikasi" data-bs-toggle="modal" data-bs-target="#verifikasiModal" data-id="<?= $trx->kode_transaksi ?>" data-tipe="<?= $trx->tipe ?>">
                                    Verifikasi
                                </button>
                                <?php else : ?>
                                <span class="text-warning">Menunggu admin stock verifikasi</span>
                                <?php endif; ?>
                                <?php else : ?>
                                <span class="text-muted">Sudah diverifikasi</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div class="modal fade" id="verifikasiModal" tabindex="-1" aria-labelledby="verifikasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="verifikasiModalLabel">Konfirmasi Verifikasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="verifikasiForm" method="post" action="">
                    <div class="modal-body">
                        <!-- Transaction Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Transaksi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <small class="text-muted">Tipe Transaksi:</small>
                                            <div id="modalTipeTransaksi" class="fw-bold"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <small class="text-muted">Kode Transaksi:</small>
                                            <div id="modalKodeTransaksi" class="fw-bold text-primary"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <small class="text-muted">Tanggal Distribusi:</small>
                                            <div id="modalTanggalDistribusi" class="fw-bold"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <small class="text-muted">User Penginput:</small>
                                            <div id="modalUserPenginput" class="fw-bold"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Fields for Packing List -->
                        <div id="poInputGroup" style="display:none;">
                            <div class="card mb-3">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0"><i class="fas fa-edit"></i> Data Tambahan untuk Packing List</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="inputNomorPo" class="form-label">Nomor Accurate <span class="text-danger">*</span></label>
                                                <input type="text" id="inputNomorPo" name="nomor_accurate" class="form-control" placeholder="Masukkan Nomor Accurate">
                                                <div class="form-text">Contoh: ACC-2025-001</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="inputWarehouse" class="form-label">Gudang <span class="text-danger">*</span></label>
                                                <select class="form-select" name="idgudang" id="inputWarehouse" required>
                                                    <option value="">Pilih Gudang</option>
                                                    <?php foreach ($warehouse as $values) : ?>
                                                    <option value="<?= $values->idgudang ?>"><?= $values->nama_gudang ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="inputDateReceiving" class="form-label">Tanggal Diterima <span class="text-danger">*</span></label>
                                                <input type="datetime-local" id="inputDateReceiving" name="tanggal_diterima" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Items (Produk yang ada di order) -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-list"></i> Detail Barang (Dalam Order)</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr id="detailHeaderInstockOutstock">
                                                <th width="20%">SKU</th>
                                                <th width="60%">Nama Produk</th>
                                                <th width="20%" class="text-end">QTY</th>
                                            </tr>
                                            <tr id="detailHeaderPackingList" style="display:none;">
                                                <th width="15%">SKU</th>
                                                <th width="40%">Nama Produk</th>
                                                <th width="15%" class="text-end">Qty Order</th>
                                                <th width="20%" class="text-end">Qty Packing List</th>
                                                <th width="10%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detailStockTable">
                                            <!-- Data akan diisi oleh JavaScript -->
                                        </tbody>
                                        <tfoot id="detailStockTableFooter" style="display:none;">
                                            <tr class="table-info">
                                                <td colspan="3" class="text-end"><strong>Total Qty Receive:</strong></td>
                                                <td class="text-end"><strong id="totalQtyReceive">0</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Penambahan Produk di Luar Order -->
                        <div id="additionalProductsContainer" style="display:none;">
                            <div class="card">
                                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Produk di Luar Order</h6>
                                    <button type="button" class="btn btn-sm btn-light" id="btnTambahProdukLuarOrder">
                                        <i class="fas fa-plus me-1"></i> Tambah Produk
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- Form Search untuk Produk -->
                                    <div class="row mb-3" id="searchProductForm" style="display:none;">
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                <input type="text" class="form-control" id="searchProduct" placeholder="Cari produk berdasarkan SKU atau nama...">
                                                <button class="btn btn-outline-secondary" type="button" id="btnClearSearch">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">Ketik untuk mencari produk</small>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-end">
                                                <button type="button" class="btn btn-secondary me-2" id="btnBatalCariProduk">
                                                    <i class="fas fa-times"></i> Batal
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Daftar Produk Hasil Pencarian -->
                                    <div id="productSearchResults" class="mb-3" style="display:none; max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="20%">SKU</th>
                                                    <th width="60%">Nama Produk</th>
                                                    <th width="20%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productList">
                                                <!-- Daftar produk akan diisi di sini -->
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Tabel Produk yang sudah ditambahkan -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead class="table-warning">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="25%">SKU</th>
                                                    <th width="40%">Nama Produk</th>
                                                    <th width="15%">Qty</th>
                                                    <th width="15%" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="additionalProductsTable">
                                                <!-- Produk yang ditambahkan akan muncul di sini -->
                                            </tbody>
                                            <tfoot id="additionalProductsFooter" style="display:none;">
                                                <tr class="table-success">
                                                    <td colspan="3" class="text-end"><strong>Total Produk Tambahan:</strong></td>
                                                    <td class="text-end"><strong id="totalAdditionalQty">0</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="button" class="btn btn-danger" id="rejectVerifikasi">
                            <i class="fas fa-times-circle"></i> Tolak
                        </button>
                        <button type="submit" class="btn btn-primary" id="confirmVerifikasi">
                            <i class="fas fa-check-circle"></i> Ya, Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>js/scripts.js"></script>
<script>
    let selectedTransactionCode = null;
    let selectedTransactionType = null;
    let productDetails = [];
    let additionalProducts = [];
    let allProducts = [];
    let filteredProducts = [];
    let additionalProductCounter = 0;

    $(document).ready(function() {
        new DataTable('#tableproduct', {
            responsive: true,
            layout: {
                bottomEnd: {
                    paging: {
                        firstLast: false
                    }
                }
            }
        });

        $(document).on('click', '.btn-verifikasi', function() {
            selectedTransactionCode = $(this).data('id');
            selectedTransactionType = $(this).data('tipe');

            let row = $(this).closest('tr');
            let distributionDate = row.find('td:eq(4)').text();
            let userInput = row.find('td:eq(5)').text();

            resetForm();

            $('#modalTipeTransaksi').text(selectedTransactionType);
            $('#modalKodeTransaksi').text(selectedTransactionCode);
            $('#modalTanggalDistribusi').text(distributionDate || '-');
            $('#modalUserPenginput').text(userInput || '-');

            let normalizedType = selectedTransactionType.toLowerCase();
            if (normalizedType === 'packing list') {
                $('#poInputGroup').show();
                $('#additionalProductsContainer').show();
                let now = new Date();
                let formattedDate = now.toISOString().slice(0, 16);
                $('#inputDateReceiving').val(formattedDate);
                $('#inputNomorPo').prop('required', true);
                $('#inputWarehouse').prop('required', true);
                $('#inputDateReceiving').prop('required', true);
                $('#detailHeaderInstockOutstock').hide();
                $('#detailHeaderPackingList').show();
            } else {
                $('#poInputGroup').hide();
                $('#additionalProductsContainer').hide();
                $('#inputNomorPo').prop('required', false);
                $('#inputWarehouse').prop('required', false);
                $('#inputDateReceiving').prop('required', false);
                $('#detailHeaderInstockOutstock').hide();
                $('#detailHeaderPackingList').hide();
            }

            let ajaxType = selectedTransactionType.toLowerCase();
            if (ajaxType === 'packing list') {
                ajaxType = 'packing_list';
            }

            let colspan = normalizedType === 'packing list' ? 5 : 3;
            $('#detailStockTable').html('<tr><td colspan="' + colspan + '" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');

            $.ajax({
                url: '<?= base_url('verification/get_details/') ?>' + ajaxType + '/' + selectedTransactionCode,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#detailStockTable').empty();
                    productDetails = [];
                    additionalProducts = [];
                    additionalProductCounter = 0;
                    $('#additionalProductsTable').empty();
                    $('#additionalProductsFooter').hide();

                    if (response.success) {
                        if (response.products) {
                            allProducts = response.products;
                            filteredProducts = [...allProducts];
                        }

                        let normalizedType = selectedTransactionType.toLowerCase();

                        if (normalizedType === 'packing list') {
                            let totalQtyOrder = 0;
                            let totalQtyPackingList = 0;
                            let displayedProductsCount = 0;

                            $('#detailHeaderInstockOutstock').hide();
                            $('#detailHeaderPackingList').show();

                            if (response.details && response.details.length > 0) {
                                response.details.forEach(function(detail, index) {
                                    let qtyOrder = detail.qty_order || 0;
                                    let qtyPackingList = detail.qty_packing_list || 0;

                                    // FILTER: Hanya tampilkan produk dengan qty_order > 0
                                    if (qtyOrder <= 0) {
                                        return; // Skip produk ini
                                    }

                                    let isAdditional = detail.is_additional || false;

                                    productDetails[index] = {
                                        sku: detail.sku,
                                        idproduct: detail.idproduct,
                                        qty_order: qtyOrder,
                                        qty_packing_list: qtyPackingList,
                                        price: detail.price || 0,
                                        is_existing: true,
                                        is_additional: isAdditional
                                    };

                                    displayedProductsCount++;

                                    var row = '<tr data-product-id="' + detail.idproduct + '" data-is-existing="true">' +
                                        '<td>' + (detail.sku || 'N/A') + '</td>' +
                                        '<td>' + (detail.nama_produk || '') + '</td>' +
                                        '<td class="text-end">' + qtyOrder.toLocaleString() + '</td>' +
                                        '<td>' +
                                        '<input type="number" class="form-control form-control-sm qty-packing-list-input" ' +
                                        'name="qty_packing_list[' + detail.idproduct + ']" ' +
                                        'value="' + qtyPackingList + '" ' +
                                        'min="0" ' +
                                        'data-index="' + index + '" ' +
                                        'data-sku="' + (detail.sku || '') + '" ' +
                                        'data-idproduct="' + detail.idproduct + '" ' +
                                        'data-is-existing="true" ' +
                                        'data-is-additional="' + (isAdditional ? 'true' : 'false') + '" ' +
                                        'required>' +
                                        '</td>' +
                                        '<td class="text-center">' +
                                        '<button type="button" class="btn btn-sm btn-danger btn-hapus-produk" data-index="' + index + '" data-is-existing="true" title="Hapus">' +
                                        '<i class="fas fa-trash"></i>' +
                                        '</button>' +
                                        '</td>' +
                                        '</tr>';
                                    $('#detailStockTable').append(row);

                                    totalQtyOrder += qtyOrder;
                                    totalQtyPackingList += qtyPackingList;
                                });
                            }

                            // Tampilkan pesan jika tidak ada produk dengan qty_order > 0
                            if (displayedProductsCount === 0) {
                                $('#detailStockTable').html('<tr><td colspan="5" class="text-center text-warning">Tidak ada produk dengan Qty Order lebih dari 0</td></tr>');
                                $('#detailStockTableFooter').hide();
                            } else {
                                $('#detailStockTableFooter').show();
                                $('#detailStockTable').append(
                                    '<tr class="table-info">' +
                                    '<td colspan="2" class="text-end"><strong>Total Qty Order:</strong></td>' +
                                    '<td class="text-end"><strong>' + totalQtyOrder.toLocaleString() + '</strong></td>' +
                                    '<td class="text-end"><strong id="totalQtyPackingList">' + totalQtyPackingList.toLocaleString() + '</strong></td>' +
                                    '<td></td>' +
                                    '</tr>'
                                );
                            }
                            $('#totalQtyReceive').text(totalQtyPackingList.toLocaleString());

                        } else if (normalizedType === 'instock') {
                            let isInstockFromPackingList = response.is_from_packing_list || false;

                            if (isInstockFromPackingList) {
                                $('#detailHeaderInstockOutstock').hide();
                                $('#detailHeaderPackingList').hide();

                                let customHeader = '<tr id="detailHeaderInstockPackingList">' +
                                    '<th width="20%">SKU</th>' +
                                    '<th width="40%">Nama Produk</th>' +
                                    '<th width="20%" class="text-end">Qty Packing List</th>' +
                                    '<th width="20%" class="text-end">Qty Instock</th>' +
                                    '</tr>';

                                $('thead tr').not('.table-light').remove();
                                $('thead').append(customHeader);

                                let totalQtyPackingList = 0;
                                let totalQtyInstock = 0;
                                let displayedProductsCount = 0;

                                if (response.details && response.details.length > 0) {
                                    response.details.forEach(function(detail) {
                                        let qtyPackingList = detail.qty_packing_list || 0;
                                        let qtyInstock = detail.qty_instock || 0;
                                        let isAdditional = detail.is_additional || false;

                                        // PERUBAHAN PENTING: HAPUS FILTER qty_order > 0
                                        // Tampilkan semua produk yang memiliki qty_packing_list > 0
                                        // Backend sudah memfilter dengan qty_packing_list > 0
                                        if (qtyPackingList <= 0) {
                                            return; // Skip jika qty_packing_list = 0
                                        }

                                        displayedProductsCount++;

                                        // Tambahkan label untuk produk tambahan
                                        let productName = detail.nama_produk || '';
                                        if (isAdditional) {
                                            productName += ' <span class="badge bg-success ms-2">Produk Tambahan</span>';
                                        }

                                        var row = '<tr>' +
                                            '<td>' + (detail.sku || 'N/A') + '</td>' +
                                            '<td>' + productName + '</td>' +
                                            '<td class="text-end">' + qtyPackingList.toLocaleString() + '</td>' +
                                            '<td class="text-end">' + qtyInstock.toLocaleString() + '</td>' +
                                            '</tr>';
                                        $('#detailStockTable').append(row);

                                        totalQtyPackingList += qtyPackingList;
                                        totalQtyInstock += qtyInstock;
                                    });
                                }

                                // Tampilkan pesan jika tidak ada produk
                                if (displayedProductsCount === 0) {
                                    $('#detailStockTable').html('<tr><td colspan="4" class="text-center text-warning">Tidak ada produk dengan Qty Packing List lebih dari 0</td></tr>');
                                } else {
                                    $('#detailStockTable').append(
                                        '<tr class="table-info">' +
                                        '<td colspan="2" class="text-end"><strong>Total:</strong></td>' +
                                        '<td class="text-end"><strong>' + totalQtyPackingList.toLocaleString() + '</strong></td>' +
                                        '<td class="text-end"><strong>' + totalQtyInstock.toLocaleString() + '</strong></td>' +
                                        '</tr>'
                                    );
                                }
                            } else {
                                $('#detailHeaderInstockOutstock').show();
                                $('#detailHeaderPackingList').hide();

                                $('#detailHeaderInstockOutstock').html(
                                    '<th width="20%">SKU</th>' +
                                    '<th width="60%">Nama Produk</th>' +
                                    '<th width="20%" class="text-end">Qty Instock</th>'
                                );

                                let totalQtyInstock = 0;
                                let displayedProductsCount = 0;

                                if (response.details && response.details.length > 0) {
                                    response.details.forEach(function(detail) {
                                        let qtyInstock = parseInt(detail.qty_instock || 0);

                                        // Skip jika qty instock 0
                                        if (qtyInstock <= 0) {
                                            return;
                                        }

                                        displayedProductsCount++;

                                        var row = '<tr>' +
                                            '<td>' + (detail.sku || 'N/A') + '</td>' +
                                            '<td>' + (detail.nama_produk || '') + '</td>' +
                                            '<td class="text-end">' + qtyInstock + '</td>' +
                                            '</tr>';
                                        $('#detailStockTable').append(row);
                                        totalQtyInstock += qtyInstock;
                                    });
                                }

                                // Tampilkan pesan jika tidak ada produk
                                if (displayedProductsCount === 0) {
                                    $('#detailStockTable').html('<tr><td colspan="3" class="text-center text-warning">Tidak ada produk dengan Qty Instock lebih dari 0</td></tr>');
                                } else if (totalQtyInstock > 0) {
                                    var summaryRow = '<tr class="table-info">' +
                                        '<td colspan="2"><strong>Total Qty Instock:</strong></td>' +
                                        '<td class="text-end"><strong>' + totalQtyInstock.toLocaleString() + '</strong></td>' +
                                        '</tr>';
                                    $('#detailStockTable').append(summaryRow);
                                }
                            }

                        } else if (normalizedType === 'outstock') {
                            $('#detailHeaderInstockOutstock').show();
                            $('#detailHeaderPackingList').hide();

                            $('#detailHeaderInstockOutstock').html(
                                '<th width="20%">SKU</th>' +
                                '<th width="60%">Nama Produk</th>' +
                                '<th width="20%" class="text-end">Qty Outstock</th>'
                            );

                            let totalQtyOutstock = 0;
                            let displayedProductsCount = 0;

                            if (response.details && response.details.length > 0) {
                                response.details.forEach(function(detail) {
                                    let qtyOutstock = parseInt(detail.qty_outstock || 0);

                                    // Skip jika qty outstock 0
                                    if (qtyOutstock <= 0) {
                                        return;
                                    }

                                    displayedProductsCount++;

                                    var row = '<tr>' +
                                        '<td>' + (detail.sku || 'N/A') + '</td>' +
                                        '<td>' + (detail.nama_produk || '') + '</td>' +
                                        '<td class="text-end">' + qtyOutstock + '</td>' +
                                        '</tr>';
                                    $('#detailStockTable').append(row);
                                    totalQtyOutstock += qtyOutstock;
                                });
                            }

                            // Tampilkan pesan jika tidak ada produk
                            if (displayedProductsCount === 0) {
                                $('#detailStockTable').html('<tr><td colspan="3" class="text-center text-warning">Tidak ada produk dengan Qty Outstock lebih dari 0</td></tr>');
                            } else if (totalQtyOutstock > 0) {
                                var summaryRow = '<tr class="table-info">' +
                                    '<td colspan="2"><strong>Total Qty Outstock:</strong></td>' +
                                    '<td class="text-end"><strong>' + totalQtyOutstock.toLocaleString() + '</strong></td>' +
                                    '</tr>';
                                $('#detailStockTable').append(summaryRow);
                            }
                        }
                    } else {
                        let errorMsg = response.error || 'Tidak ada data detail yang ditemukan';
                        let colspan = normalizedType === 'packing list' ? 5 : 3;
                        $('#detailStockTable').html('<tr><td colspan="' + colspan + '" class="text-center text-warning">' + errorMsg + '</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    let colspan = normalizedType === 'packing list' ? 5 : 3;
                    $('#detailStockTable').html('<tr><td colspan="' + colspan + '" class="text-center text-danger">Gagal memuat data detail transaksi.</td></tr>');
                }
            });
        });

        function resetForm() {
            $('#verifikasiForm')[0].reset();
            $('#detailStockTable').empty();
            $('#detailStockTableFooter').hide();
            $('#searchProductForm').hide();
            $('#productSearchResults').hide();
            $('#productList').empty();
            $('#searchProduct').val('');
            $('#additionalProductsTable').empty();
            $('#additionalProductsFooter').hide();
            $('#verifikasiForm').attr('action', '');
            productDetails = [];
            additionalProducts = [];
            allProducts = [];
            filteredProducts = [];
            additionalProductCounter = 0;
        }

        $('#btnTambahProdukLuarOrder').on('click', function() {
            $('#searchProductForm').show();
            $('#productSearchResults').show();
            populateProductList();
            $(this).prop('disabled', true);
        });

        function populateProductList() {
            $('#productList').empty();

            if (filteredProducts.length === 0) {
                $('#productList').html('<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada produk tersedia</td></tr>');
                return;
            }

            let existingProductIds = [];
            $('tr[data-product-id]').each(function() {
                existingProductIds.push($(this).data('product-id').toString());
            });
            additionalProducts.forEach(function(product) {
                existingProductIds.push(product.idproduct.toString());
            });

            filteredProducts.forEach(function(product) {
                if (existingProductIds.includes(product.id.toString())) {
                    return;
                }

                let row = '<tr class="product-item" data-product-id="' + product.id + '" data-sku="' + product.sku + '" data-nama="' + product.nama + '">' +
                    '<td><strong>' + product.sku + '</strong></td>' +
                    '<td>' + product.nama + '</td>' +
                    '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-primary btn-pilih-produk-luar-order" title="Tambahkan produk ini">' +
                    '<i class="fas fa-plus me-1"></i> Tambah' +
                    '</button>' +
                    '</td>' +
                    '</tr>';
                $('#productList').append(row);
            });
        }

        $('#searchProduct').on('input', function() {
            let searchTerm = $(this).val().toLowerCase();

            if (!searchTerm) {
                filteredProducts = [...allProducts];
            } else {
                filteredProducts = allProducts.filter(function(product) {
                    return product.sku.toLowerCase().includes(searchTerm) ||
                        product.nama.toLowerCase().includes(searchTerm) ||
                        product.text.toLowerCase().includes(searchTerm);
                });
            }

            populateProductList();
        });

        $('#btnClearSearch').on('click', function() {
            $('#searchProduct').val('');
            filteredProducts = [...allProducts];
            populateProductList();
        });

        $('#btnBatalCariProduk').on('click', function() {
            $('#searchProductForm').hide();
            $('#productSearchResults').hide();
            $('#searchProduct').val('');
            $('#btnTambahProdukLuarOrder').prop('disabled', false);
            filteredProducts = [...allProducts];
        });

        $(document).on('click', '.btn-pilih-produk-luar-order', function() {
            let row = $(this).closest('tr');
            let productId = row.data('product-id');
            let sku = row.data('sku');
            let nama = row.data('nama');

            let isDuplicate = additionalProducts.some(function(product) {
                return product.idproduct.toString() === productId.toString();
            });

            if (isDuplicate) {
                alert('Produk ini sudah ditambahkan ke dalam daftar produk tambahan.');
                return;
            }

            additionalProductCounter++;
            let newIndex = additionalProducts.length;

            additionalProducts.push({
                id: additionalProductCounter,
                idproduct: productId,
                sku: sku,
                nama_produk: nama,
                qty_packing_list: 1
            });

            let newRow = '<tr data-product-id="' + productId + '" data-additional-id="' + additionalProductCounter + '">' +
                '<td>' + additionalProductCounter + '</td>' +
                '<td>' + sku + '</td>' +
                '<td>' + nama + '</td>' +
                '<td>' +
                '<input type="number" class="form-control form-control-sm additional-qty-packing-list-input" ' +
                'name="additional_products[' + newIndex + '][qty_packing_list]" ' +
                'value="1" ' +
                'min="1" ' +
                'data-index="' + newIndex + '" ' +
                'data-sku="' + sku + '" ' +
                'data-idproduct="' + productId + '" ' +
                'required>' +
                '<input type="hidden" name="additional_products[' + newIndex + '][idproduct]" value="' + productId + '">' +
                '</td>' +
                '<td class="text-center">' +
                '<button type="button" class="btn btn-sm btn-danger btn-hapus-produk-tambahan" data-index="' + newIndex + '" data-additional-id="' + additionalProductCounter + '" title="Hapus">' +
                '<i class="fas fa-trash"></i>' +
                '</button>' +
                '</td>' +
                '</tr>';

            $('#additionalProductsTable').append(newRow);

            if (additionalProducts.length > 0) {
                $('#additionalProductsFooter').show();
            }

            updateTotalAdditionalQty();
            row.remove();
            populateProductList();
        });

        $(document).on('click', '.btn-hapus-produk-tambahan', function() {
            let index = $(this).data('index');
            let additionalId = $(this).data('additional-id');

            if (!confirm('Apakah Anda yakin ingin menghapus produk tambahan ini?')) {
                return;
            }

            additionalProducts = additionalProducts.filter(function(product) {
                return product.id !== additionalId;
            });

            $(this).closest('tr').remove();

            $('#additionalProductsTable tr').each(function(i) {
                let row = $(this);
                if (row.find('.additional-qty-packing-list-input').length > 0) {
                    let newIndex = i - 1;
                    row.find('.additional-qty-packing-list-input').attr('name', 'additional_products[' + newIndex + '][qty_packing_list]')
                        .data('index', newIndex);
                    row.find('input[type="hidden"]').attr('name', 'additional_products[' + newIndex + '][idproduct]');
                    row.find('.btn-hapus-produk-tambahan').data('index', newIndex);
                    row.find('td:first').text(newIndex + 1);
                }
            });

            if (additionalProducts.length === 0) {
                $('#additionalProductsFooter').hide();
            }

            updateTotalAdditionalQty();
            populateProductList();
        });

        $(document).on('click', '.btn-hapus-produk', function() {
            let index = $(this).data('index');
            let isExisting = $(this).data('is-existing') || false;

            if (!confirm('Apakah Anda yakin ingin menghapus produk ini dari daftar?')) {
                return;
            }

            if (isExisting && productDetails[index]) {
                // Set qty_packing_list menjadi 0 untuk produk yang ada di database
                productDetails[index].qty_packing_list = 0;

                // Update input field menjadi 0
                $('.qty-packing-list-input[data-index="' + index + '"]').val(0);

                // Update total
                updateTotalQtyPackingList();

                alert('Produk telah dihapus dari daftar verifikasi (Qty Packing List diubah menjadi 0).');
            }
        });

        $(document).on('change input', '.additional-qty-packing-list-input', function() {
            let index = $(this).data('index');
            let newQtyPackingList = parseInt($(this).val()) || 1;
            let sku = $(this).data('sku');

            if (newQtyPackingList < 1) {
                alert('QTY Packing List untuk produk tambahan minimal 1.');
                $(this).val(1);
                newQtyPackingList = 1;
            }

            if (additionalProducts[index]) {
                additionalProducts[index].qty_packing_list = newQtyPackingList;
            }

            updateTotalAdditionalQty();
        });

        $(document).on('change input', '.qty-packing-list-input', function() {
            let index = $(this).data('index');
            let newQtyPackingList = parseInt($(this).val()) || 0;
            let sku = $(this).data('sku');
            let isExisting = $(this).data('is-existing') || false;

            if (newQtyPackingList < 0) {
                alert('QTY Packing List untuk SKU ' + sku + ' tidak boleh negatif');
                $(this).val(0);
                newQtyPackingList = 0;
            }

            if (isExisting && productDetails[index]) {
                productDetails[index].qty_packing_list = newQtyPackingList;
            }

            updateTotalQtyPackingList();
        });

        function updateTotalQtyPackingList() {
            let total = 0;
            productDetails.forEach(function(detail) {
                total += detail.qty_packing_list || 0;
            });
            $('#totalQtyPackingList').text(total.toLocaleString());
            $('#totalQtyReceive').text(total.toLocaleString());
        }

        function updateTotalAdditionalQty() {
            let total = 0;
            additionalProducts.forEach(function(product) {
                total += product.qty_packing_list || 0;
            });
            $('#totalAdditionalQty').text(total.toLocaleString());
        }

        $('#verifikasiForm').on('submit', function(e) {
            e.preventDefault();

            if (!selectedTransactionCode || !selectedTransactionType) {
                alert('Data transaksi tidak valid.');
                return false;
            }

            let normalizedType = selectedTransactionType.toLowerCase();

            if (normalizedType === 'packing list') {
                if (!$('#inputNomorPo').val().trim()) {
                    alert('Harap masukkan Nomor Accurate.');
                    $('#inputNomorPo').focus();
                    return false;
                }
                if (!$('#inputWarehouse').val()) {
                    alert('Harap pilih Gudang.');
                    $('#inputWarehouse').focus();
                    return false;
                }
                if (!$('#inputDateReceiving').val()) {
                    alert('Harap pilih Tanggal Diterima.');
                    $('#inputDateReceiving').focus();
                    return false;
                }

                // Validasi: minimal ada 1 produk dengan qty_packing_list > 0
                let hasValidProduct = false;
                let totalQtyPackingList = 0;

                // Cek produk yang ada di order
                $('.qty-packing-list-input').each(function() {
                    let qtyPackingList = parseInt($(this).val()) || 0;
                    totalQtyPackingList += qtyPackingList;
                    if (qtyPackingList > 0) {
                        hasValidProduct = true;
                    }
                });

                // Cek produk tambahan
                $('.additional-qty-packing-list-input').each(function() {
                    let qtyPackingList = parseInt($(this).val()) || 0;
                    totalQtyPackingList += qtyPackingList;
                    if (qtyPackingList > 0) {
                        hasValidProduct = true;
                    }
                });

                if (!hasValidProduct) {
                    alert('Harap setidaknya ada 1 produk dengan Qty Packing List lebih dari 0.');
                    return false;
                }

                if (totalQtyPackingList <= 0) {
                    alert('Total Qty Packing List harus lebih dari 0.');
                    return false;
                }
            }

            let hasInvalidQty = false;

            $('.qty-packing-list-input').each(function() {
                let qtyPackingList = parseInt($(this).val()) || 0;
                let sku = $(this).data('sku');

                if (qtyPackingList < 0) {
                    alert('QTY Packing List untuk SKU ' + sku + ' tidak boleh negatif.');
                    $(this).focus();
                    hasInvalidQty = true;
                    return false;
                }
            });

            $('.additional-qty-packing-list-input').each(function() {
                let qtyPackingList = parseInt($(this).val()) || 1;
                let sku = $(this).data('sku');

                if (qtyPackingList < 1) {
                    alert('QTY Packing List untuk produk tambahan ' + sku + ' minimal 1.');
                    $(this).focus();
                    hasInvalidQty = true;
                    return false;
                }
            });

            if (hasInvalidQty) return false;

            let actionUrl = "<?= base_url('verification/confirm_stock/') ?>" +
                encodeURIComponent(selectedTransactionType) + "/" +
                encodeURIComponent(selectedTransactionCode);
            $(this).attr('action', actionUrl);

            this.submit();
        });

        $('#rejectVerifikasi').on('click', function() {
            if (!selectedTransactionCode || !selectedTransactionType) {
                alert('Data transaksi tidak valid.');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin menolak transaksi ini?')) {
                return;
            }

            let rejectUrl = "<?= base_url('verification/reject/') ?>" +
                encodeURIComponent(selectedTransactionType) + "/" +
                encodeURIComponent(selectedTransactionCode);
            window.location.href = rejectUrl;
        });

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let start = $('#filterInputStart').val();
                let end = $('#filterInputEnd').val();
                let tanggalInput = data[3].split(' ')[0];

                if (!start && !end) return true;

                if (start && tanggalInput < start) return false;
                if (end && tanggalInput > end) return false;

                return true;
            }
        );

        $('#filterInputStart, #filterInputEnd').on('change', function() {
            $('#tableproduct').DataTable().draw();
        });
    });
</script>