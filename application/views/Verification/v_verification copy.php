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
                    <h5 class="modal-title" id="verifikasiModalLabel">Verifikasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="verifikasiForm" method="post" action="">
                    <div class="modal-body">
                        <!-- Transaction Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Transaksi</h6>
                            </div>
                            <div class="card-body" style="text-align: center;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <small class="text-muted">Tipe Transaksi:</small>
                                            <div id="modalTipeTransaksi" class="fw-bold"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <small class="text-muted">Nomor PO:</small>
                                            <div id="modalKodeTransaksi" class="fw-bold text-primary"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <small class="text-muted">User Penginput:</small>
                                            <div id="modalUserPenginput" class="fw-bold"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Items (Produk yang ada di order) -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-list"></i> Detail Barang</h6>
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
            $('#modalUserPenginput').text(userInput || '-');

            let normalizedType = selectedTransactionType.toLowerCase();
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
                    console.log('Response from server:', response);

                    $('#detailStockTable').empty();
                    productDetails = [];

                    if (response.success) {
                        let normalizedType = selectedTransactionType.toLowerCase();

                        // Di dalam bagian yang menampilkan produk packing list, ubah kode berikut:
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
                                    let isAdditional = detail.is_additional || false;

                                    // FILTER: Hanya tampilkan produk dengan qty_order > 0
                                    if (qtyOrder <= 0) {
                                        return; // Skip produk ini
                                    }

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
                                    '<th width="15%">SKU</th>' +
                                    '<th width="35%">Nama Produk</th>' +
                                    '<th width="15%" class="text-end">Qty Packing List</th>' +
                                    '<th width="20%" class="text-end">Qty Instock</th>' +
                                    '<th width="15%" class="text-center">Aksi</th>' +
                                    '</tr>';

                                $('thead tr').not('.table-light').remove();
                                $('thead').append(customHeader);

                                let totalQtyPackingList = 0;
                                let totalQtyInstock = 0;
                                let displayedProductsCount = 0;

                                if (response.details && response.details.length > 0) {
                                    response.details.forEach(function(detail, index) {
                                        let qtyPackingList = detail.qty_packing_list || 0;
                                        let qtyInstock = detail.qty_instock || 0;
                                        let isAdditional = detail.is_additional || false;

                                        // Tampilkan semua produk yang memiliki qty_packing_list > 0
                                        if (qtyPackingList <= 0) {
                                            return; // Skip jika qty_packing_list = 0
                                        }

                                        displayedProductsCount++;

                                        // Tambahkan label untuk produk tambahan
                                        let productName = detail.nama_produk || '';
                                        if (isAdditional) {
                                            productName += ' <span class="badge bg-success ms-2">Produk Tambahan</span>';
                                        }

                                        var row = '<tr data-product-id="' + detail.idproduct + '" data-is-existing="true">' +
                                            '<td>' + (detail.sku || 'N/A') + '</td>' +
                                            '<td>' + productName + '</td>' +
                                            '<td class="text-end">' + qtyPackingList.toLocaleString() + '</td>' +
                                            '<td>' +
                                            '<input type="number" class="form-control form-control-sm qty-instock-input" ' +
                                            'name="qty_instock[' + detail.idproduct + ']" ' +
                                            'value="' + qtyInstock + '" ' +
                                            'min="0" ' +
                                            'data-index="' + index + '" ' +
                                            'data-sku="' + (detail.sku || '') + '" ' +
                                            'data-idproduct="' + detail.idproduct + '" ' +
                                            'data-is-existing="true" ' +
                                            'required>' +
                                            '</td>' +
                                            '<td class="text-center">' +
                                            '<button type="button" class="btn btn-sm btn-danger btn-hapus-produk" data-index="' + index + '" data-is-existing="true" title="Hapus">' +
                                            '<i class="fas fa-trash"></i>' +
                                            '</button>' +
                                            '</td>' +
                                            '</tr>';
                                        $('#detailStockTable').append(row);

                                        totalQtyPackingList += qtyPackingList;
                                        totalQtyInstock += qtyInstock;
                                    });
                                }

                                // Tampilkan pesan jika tidak ada produk
                                if (displayedProductsCount === 0) {
                                    $('#detailStockTable').html('<tr><td colspan="5" class="text-center text-warning">Tidak ada produk dengan Qty Packing List lebih dari 0</td></tr>');
                                } else {
                                    $('#detailStockTable').append(
                                        '<tr class="table-info">' +
                                        '<td colspan="2" class="text-end"><strong>Total Qty Packing List:</strong></td>' +
                                        '<td class="text-end"><strong>' + totalQtyPackingList.toLocaleString() + '</strong></td>' +
                                        '<td class="text-end"><strong id="totalQtyInstock">' + totalQtyInstock.toLocaleString() + '</strong></td>' +
                                        '<td></td>' +
                                        '</tr>'
                                    );
                                }
                            } else {
                                // Untuk instock biasa (bukan dari packing list)
                                $('#detailHeaderInstockOutstock').hide();
                                $('#detailHeaderPackingList').hide();

                                let customHeader = '<tr id="detailHeaderInstockRegular">' +
                                    '<th width="15%">SKU</th>' +
                                    '<th width="50%">Nama Produk</th>' +
                                    '<th width="20%" class="text-end">Qty Instock</th>' +
                                    '<th width="15%" class="text-center">Aksi</th>' +
                                    '</tr>';

                                $('thead tr').not('.table-light').remove();
                                $('thead').append(customHeader);

                                let totalQtyInstock = 0;
                                let displayedProductsCount = 0;

                                if (response.details && response.details.length > 0) {
                                    response.details.forEach(function(detail, index) {
                                        let qtyInstock = parseInt(detail.qty_instock || 0);

                                        // Skip jika qty instock 0
                                        if (qtyInstock <= 0) {
                                            return;
                                        }

                                        displayedProductsCount++;

                                        var row = '<tr data-product-id="' + detail.idproduct + '" data-is-existing="true">' +
                                            '<td>' + (detail.sku || 'N/A') + '</td>' +
                                            '<td>' + (detail.nama_produk || '') + '</td>' +
                                            '<td>' +
                                            '<input type="number" class="form-control form-control-sm qty-instock-input" ' +
                                            'name="qty_instock[' + detail.idproduct + ']" ' +
                                            'value="' + qtyInstock + '" ' +
                                            'min="0" ' +
                                            'data-index="' + index + '" ' +
                                            'data-sku="' + (detail.sku || '') + '" ' +
                                            'data-idproduct="' + detail.idproduct + '" ' +
                                            'data-is-existing="true" ' +
                                            'required>' +
                                            '</td>' +
                                            '<td class="text-center">' +
                                            '<button type="button" class="btn btn-sm btn-danger btn-hapus-produk" data-index="' + index + '" data-is-existing="true" title="Hapus">' +
                                            '<i class="fas fa-trash"></i>' +
                                            '</button>' +
                                            '</td>' +
                                            '</tr>';
                                        $('#detailStockTable').append(row);
                                        totalQtyInstock += qtyInstock;
                                    });
                                }

                                // Tampilkan pesan jika tidak ada produk
                                if (displayedProductsCount === 0) {
                                    $('#detailStockTable').html('<tr><td colspan="4" class="text-center text-warning">Tidak ada produk dengan Qty Instock lebih dari 0</td></tr>');
                                } else {
                                    $('#detailStockTable').append(
                                        '<tr class="table-info">' +
                                        '<td colspan="2" class="text-end"><strong>Total Qty Instock:</strong></td>' +
                                        '<td class="text-end"><strong id="totalQtyInstock">' + totalQtyInstock.toLocaleString() + '</strong></td>' +
                                        '<td></td>' +
                                        '</tr>'
                                    );
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
            $('#verifikasiForm').attr('action', '');
            productDetails = [];
        }

        $(document).on('click', '.btn-hapus-produk', function() {
            let index = $(this).data('index');
            let isExisting = $(this).data('is-existing') || false;

            if (!confirm('Apakah Anda yakin ingin menghapus produk ini dari daftar?')) {
                return;
            }

            if (isExisting) {
                // Set qty menjadi 0 untuk produk yang ada di database
                let inputField = $(this).closest('tr').find('.qty-instock-input, .qty-packing-list-input');
                if (inputField.length > 0) {
                    inputField.val(0);

                    // Update total
                    if (inputField.hasClass('qty-instock-input')) {
                        updateTotalQtyInstock();
                    } else if (inputField.hasClass('qty-packing-list-input')) {
                        updateTotalQtyPackingList();
                    }

                    alert('Produk telah dihapus dari daftar verifikasi (Qty diubah menjadi 0).');
                }
            }
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

        $(document).on('change input', '.qty-instock-input', function() {
            let index = $(this).data('index');
            let newQtyInstock = parseInt($(this).val()) || 0;
            let sku = $(this).data('sku');

            if (newQtyInstock < 0) {
                alert('QTY Instock untuk SKU ' + sku + ' tidak boleh negatif');
                $(this).val(0);
                newQtyInstock = 0;
            }

            // Update total qty instock
            updateTotalQtyInstock();
        });

        function updateTotalQtyPackingList() {
            let total = 0;
            $('.qty-packing-list-input').each(function() {
                total += parseInt($(this).val()) || 0;
            });
            $('#totalQtyPackingList').text(total.toLocaleString());
            $('#totalQtyReceive').text(total.toLocaleString());
        }

        function updateTotalQtyInstock() {
            let total = 0;
            $('.qty-instock-input').each(function() {
                total += parseInt($(this).val()) || 0;
            });
            $('#totalQtyInstock').text(total.toLocaleString());
        }

        $('#verifikasiForm').on('submit', function(e) {
            e.preventDefault();

            if (!selectedTransactionCode || !selectedTransactionType) {
                alert('Data transaksi tidak valid.');
                return false;
            }

            let normalizedType = selectedTransactionType.toLowerCase();
            let hasInvalidQty = false;

            // Validasi umum untuk semua tipe
            $('.qty-packing-list-input, .qty-instock-input').each(function() {
                let qty = parseInt($(this).val()) || 0;
                let sku = $(this).data('sku');

                if (qty < 0) {
                    alert('QTY untuk SKU ' + sku + ' tidak boleh negatif.');
                    $(this).focus();
                    hasInvalidQty = true;
                    return false;
                }
            });

            if (hasInvalidQty) return false;

            // Validasi khusus untuk instock
            if (normalizedType === 'instock') {
                let hasValidProduct = false;
                let totalQtyInstock = 0;

                $('.qty-instock-input').each(function() {
                    let qtyInstock = parseInt($(this).val()) || 0;
                    totalQtyInstock += qtyInstock;
                    if (qtyInstock > 0) {
                        hasValidProduct = true;
                    }
                });

                if (!hasValidProduct) {
                    alert('Harap setidaknya ada 1 produk dengan Qty Instock lebih dari 0.');
                    return false;
                }

                if (totalQtyInstock <= 0) {
                    alert('Total Qty Instock harus lebih dari 0.');
                    return false;
                }
            }

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