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
                                        <?php if (in_array($this->session->userdata('idrole'), [1, 3])) : ?>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifikasiModalLabel">Konfirmasi Verifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <!-- Transaction Information -->
                    <div>
                        <h6>Informasi Transaksi</h6>
                        <div class="row mb-2">
                            <div class="col">
                                <div>Tipe Transaksi:</div>
                                <div id="modalTipeTransaksi"></div>
                            </div>
                            <div class="col">
                                <div>Kode Transaksi:</div>
                                <div id="modalKodeTransaksi"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <div>Tanggal Distribusi:</div>
                                <div id="modalTanggalDistribusi"></div>
                            </div>
                            <div class="col">
                                <div>User Penginput:</div>
                                <div id="modalUserPenginput"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Detail Stok -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody id="detailStockTable">
                                <!-- Data detail stok akan ditambahkan di sini dengan JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="rejectVerifikasi">Reject</button>
                    <button type="button" class="btn btn-primary" id="confirmVerifikasi">Ya, Verifikasi</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url(); ?>js/scripts.js"></script>
    <script>
        let selectedTransactionCode = null;
        let selectedTransactionType = null;

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

                // Get the row data
                let row = $(this).closest('tr');
                let distributionDate = row.find('td:eq(4)').text();
                let userInput = row.find('td:eq(5)').text();

                // Set the modal information
                $('#modalTipeTransaksi').text(selectedTransactionType.charAt(0).toUpperCase() + selectedTransactionType.slice(1));
                $('#modalKodeTransaksi').text(selectedTransactionCode);
                $('#modalTanggalDistribusi').text(distributionDate);
                $('#modalUserPenginput').text(userInput);

                $.ajax({
                    url: '<?= base_url('verification/get_details/') ?>' + selectedTransactionType + '/' + selectedTransactionCode,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#detailStockTable').empty();
                        if (response.details) {
                            response.details.forEach(function(detail) {
                                var row = '<tr>' +
                                    '<td>' + (detail.sku || '') + '</td>' +
                                    '<td>' + (detail.nama_produk || '') + '</td>' +
                                    '<td>' + (detail.jumlah || '') + '</td>' +
                                    '</tr>';
                                $('#detailStockTable').append(row);
                            });
                        } else {
                            $('#detailStockTable').html('<tr><td colspan="3">Tidak ada data</td></tr>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", error);
                        $('#detailStockTable').html('<tr><td colspan="3">Gagal memuat data</td></tr>');
                    }
                });
            });

            $('#confirmVerifikasi').on('click', function() {
                if (selectedTransactionCode && selectedTransactionType) {
                    console.log('Confirming:', selectedTransactionCode, selectedTransactionType);
                    window.location.href = "<?= base_url('verification/confirm_stock/') ?>" + selectedTransactionType + "/" + selectedTransactionCode;
                } else {
                    console.error('Cannot confirm - missing transaction data');
                }
            });

            $('#rejectVerifikasi').on('click', function() {
                if (selectedTransactionCode && selectedTransactionType) {
                    console.log('Rejecting:', selectedTransactionCode, selectedTransactionType);
                    window.location.href = "<?= base_url('verification/reject/') ?>" + selectedTransactionType + "/" + selectedTransactionCode;
                } else {
                    console.error('Cannot reject - missing transaction data');
                }
            });

            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    let start = $('#filterInputStart').val();
                    let end = $('#filterInputEnd').val();
                    let tanggalInput = data[3].split(' ')[0]; // Kolom ke-4: "Tanggal Input" (format yyyy-mm-dd)

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