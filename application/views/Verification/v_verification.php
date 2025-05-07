<!-- Page content -->
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col">
            <table id="tableproduct" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tipe</th>
                        <th>Transaction Code</th>
                        <th>Tanggal</th>
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
                            <td><?= $trx->user ?></td>
                            <td>
                                <h5>
                                    <?php if ($trx->status_verification == 1) : ?>
                                        <span class="badge bg-success">Accept</span>
                                    <?php elseif ($trx->status_verification == 2) : ?>
                                        <span class="badge bg-danger">Cancel</span>
                                    <?php else : ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </h5>
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-verifikasi" data-bs-toggle="modal" data-bs-target="#verifikasiModal" data-id="<?= $trx->kode_transaksi ?>" data-tipe="<?= $trx->tipe ?>">
                                    Verifikasi
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Verifikasi Modal -->
<div class="modal fade" id="verifikasiModal" tabindex="-1" aria-labelledby="verifikasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifikasiModalLabel">Konfirmasi Verifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memverifikasi transaksi ini?</p>

                <!-- Tabel Detail Stok -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Jumlah</th>
                            <th>Stok Tersedia</th>
                        </tr>
                    </thead>
                    <tbody id="detailStockTable">
                        <!-- Data detail stok akan ditambahkan di sini dengan JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
    let selectedTipe = null;

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
            selectedTransactionCode = $(this).data('id'); // Get the transaction code
            selectedTransactionType = 'INSTOCK'; // Set the transaction type (INSTOCK in this case)

            // Make an AJAX request to fetch the transaction details
            $.ajax({
                url: '<?= base_url('verification/get_details/') ?>' + selectedTransactionType + '/' + selectedTransactionCode,
                type: 'GET',
                success: function(response) {
                    console.log(response); // Check the response in the console
                    if (response.details) {
                        // Populate the details table with transaction data
                        $('#detailStockTable').empty(); // Clear the table first
                        response.details.forEach(function(detail) {
                            var row = '<tr>' +
                                '<td>' + detail.sku + '</td>' +
                                '<td>' + detail.nama_produk + '</td>' +
                                '<td>' + detail.jumlah + '</td>' +
                                '<td>' + detail.sisa_stok + '</td>' +
                                '</tr>';
                            $('#detailStockTable').append(row); // Append each row to the table
                        });
                    } else {
                        alert('Failed to fetch transaction details.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error: ", error); // Log error for debugging
                    alert('Failed to fetch transaction details.');
                }
            });
        });


        $('#confirmVerifikasi').on('click', function() {
            if (selectedTransactionCode && selectedTransactionType) {
                window.location.href = "<?= base_url('verification/confirm_stock/') ?>" + selectedTransactionType + "/" + selectedTransactionCode;
            }
        });

    });
</script>