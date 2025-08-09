            <!-- Page content-->
            <div class="container-fluid">
                <div class="row mt-4">
                    <div class="col">
                        <h1>Realisasi Pengiriman</h1>
                    </div>
                </div>
                <?php if ($this->session->userdata('idrole') != 4) { ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeliver">
                        <i class="fa-solid fa-plus"></i> Tambah Realisasi Pengiriman
                    </button>
                <?php } ?>

                <!-- Flash messages -->
                <?php if ($this->session->flashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <?= $this->session->flashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <!-- End -->

                <!-- Modal Tambah Delivery -->
                <div class="modal fade" id="addDeliver" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addDeliverLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="post" enctype="multipart/form-data" action="<?php echo base_url('Delivery_note/createDelivery') ?>">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="addDeliverLabel">Tambah Realisasi Pengiriman</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="inputNo" class="form-label">No Surat Jalan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control input-no" id="inputNo" name="inputNo" required>
                                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#scanBarcodeModal">
                                                <i class="fa-solid fa-barcode"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputFoto">Foto Surat Jalan</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" id="inputFoto" name="inputFoto" accept="image/*" capture="environment" required>
                                            <button type="button" class="btn btn-secondary" onclick="openPhotoModal()">
                                                <i class="fa-solid fa-camera"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End -->
                <!-- Modal Konfirmasi Verifikasi -->
                <div class="modal fade" id="confirmVerifikasiModal" tabindex="-1" aria-labelledby="confirmVerifikasiLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Verifikasi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin memverifikasi pengiriman ini?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <a id="confirmVerifikasiBtn" href="#" class="btn btn-primary">Ya, Verifikasi</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End -->
                <!-- Modal Konfirmasi Validasi -->
                <div class="modal fade" id="confirmValidasiModal" tabindex="-1" aria-labelledby="confirmValidasiLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Validasi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin memvalidasi pengiriman ini?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <a id="confirmValidasiBtn" href="#" class="btn btn-primary">Ya, Validasi</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End -->
                <!-- Modal Konfirmasi Final -->
                <div class="modal fade" id="confirmFinalModal" tabindex="-1" aria-labelledby="confirmFinalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Final DIR</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin memfinal pengiriman ini?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <a id="confirmFinalBtn" href="#" class="btn btn-primary">Ya, Final</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End -->
                <!-- Modal Scan Barcode -->
                <div class="modal fade" id="scanBarcodeModal" tabindex="-1" aria-labelledby="scanBarcodeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Scan Barcode</h5>
                                <button type="button" class="btn-close" onclick="closeScanModal()" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div id="qr-reader" style="width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End -->
                <!-- Modal Ambil Foto -->
                <div class="modal fade" id="photoCaptureModal" tabindex="-1" aria-labelledby="photoCaptureLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ambil Foto Surat Jalan</h5>
                                <button type="button" class="btn-close" onclick="closePhotoModal()" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <video id="photo-video" width="100%" autoplay></video>
                                <button class="btn btn-success mt-3" onclick="takePhoto()">Ambil Foto</button>
                                <canvas id="photo-canvas" width="640" height="480" style="display:none;"></canvas>
                                <img id="photo-preview" src="#" style="display:none; width:100%; margin-top:1rem;" />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closePhotoModal()">Tutup</button>
                                <button type="button" class="btn btn-primary" onclick="submitPhoto()">Gunakan Foto</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End -->
                <!-- Modal Preview Surat Jalan -->
                <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-body p-0">
                                <img id="modalImage" src="#" alt="Preview Surat Jalan" style="width:100%; height:auto;" oncontextmenu="return false;">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End -->
                <!-- Modal Revisi Surat Jalan -->
                <div class="modal fade" id="revisionDeliver" tabindex="-1" aria-labelledby="revisionDeliverLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="revisionDeliverLabel">Revisi Realisasi Pengiriman</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" enctype="multipart/form-data" action="<?= base_url('Delivery_note/revisionDelivery') ?>">
                                <input type="hidden" name="id" id="revisionId">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Current Data Section -->
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">Data Saat Ini</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">No Surat Jalan</label>
                                                        <div class="form-control bg-light" id="currentNoManual"></div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Foto Surat Jalan</label>
                                                        <div class="text-center border p-2 rounded bg-light">
                                                            <img id="currentFotoPreview" src="" class="img-fluid" style="max-height: 200px;" oncontextmenu="return false;">
                                                            <div class="mt-2 text-muted small">Dokumen saat ini</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <!-- Revision Form Section -->
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">Perubahan</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label for="revisionNo" class="form-label">No Surat Jalan Baru</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="revisionNo" name="no_manual" placeholder="Isi jika ingin mengubah">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#scanBarcodeModal">
                                                                <i class="fas fa-barcode"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="revisionFoto" class="form-label">Foto Surat Jalan Baru</label>
                                                        <div class="file-upload-wrapper">
                                                            <div class="input-group">
                                                                <input type="file" class="form-control" id="revisionFoto" name="foto" accept="image/*">
                                                                <button type="button" class="btn btn-outline-secondary" onclick="openPhotoModal()">
                                                                    <i class="fas fa-camera"></i>
                                                                </button>
                                                            </div>
                                                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto</small>
                                                        </div>
                                                        <div id="newFotoPreview" class="mt-3 text-center" style="display: none;">
                                                            <div class="border p-2 rounded">
                                                                <img id="newFotoImg" src="#" class="img-fluid" style="max-height: 150px;" oncontextmenu="return false;">
                                                                <div class="mt-2 text-muted small">Pratinjau dokumen baru</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End -->
                <!-- Modal konfirmasi delete-->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                Apakah kamu yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Hapus</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End -->
                <div class="row">
                    <div class="col">
                        <table id="tableproduct" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomer Surat Jalan</th>
                                    <th>Tanggal Kirim</th>
                                    <th>Penginput</th>
                                    <th>Tanggal Input</th>
                                    <th>Surat Jalan</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($delivery as $dkey => $dvalue) { ?>
                                    <tr>
                                        <td><?php echo $dkey + 1; ?></td>
                                        <td><?php echo $dvalue->no_manual; ?></td>
                                        <td><?php echo $dvalue->send_date; ?></td>
                                        <td><?php echo $dvalue->user_input; ?></td>
                                        <td><?php echo $dvalue->created_date; ?></td>
                                        <td>
                                            <img src="<?php echo base_url('assets/image/surat_jalan/' . $dvalue->foto); ?>" alt="<?php echo $dvalue->foto; ?>" width="100px" height="100px" style="cursor:pointer;" onclick="showImageModal('<?php echo base_url('assets/image/surat_jalan/' . $dvalue->foto); ?>')" oncontextmenu="return false;">
                                        </td>
                                        <td>
                                            <?php if ($dvalue->progress == 1) { ?>
                                                <span class="badge rounded-pill text-bg-secondary">Dikirim</span>
                                            <?php } else if ($dvalue->progress == 2) { ?>
                                                <span class="badge rounded-pill text-bg-primary">Terverifikasi(Diterima)</span>
                                            <?php } else if ($dvalue->progress == 3) { ?>
                                                <span class="badge rounded-pill text-bg-info">Tervalidasi(Terdata)</span>
                                            <?php } else { ?>
                                                <span class="badge rounded-pill text-bg-success">Final Direksi</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($dvalue->progress == 1) { ?>
                                                <a href="#" class="btn btn-sm btn-primary mb-1" onclick="showConfirmModal(<?= $dvalue->iddelivery_note ?>)">
                                                    <i class="fas fa-check"></i> Verifikasi
                                                </a><br>
                                            <?php } else if ($dvalue->progress == 2) { ?>
                                                <a href="#" class="btn btn-sm btn-info mb-1" onclick="showValidasiModal(<?= $dvalue->iddelivery_note ?>)">
                                                    <i class="fas fa-check-double"></i> Validasi
                                                </a><br>
                                            <?php } else if ($dvalue->progress == 3) { ?>
                                                <a href="#" class="btn btn-sm btn-success mb-1" onclick="showFinalModal(<?= $dvalue->iddelivery_note ?>)">
                                                    <i class="fas fa-check-double"></i> Final DIR
                                                </a><br>
                                            <?php } ?>
                                            <?php if ($this->session->userdata('idrole') == 1) { ?>
                                                <a href="<?= base_url('assets/image/surat_jalan/' . $dvalue->foto) ?>" download class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-download small"></i> Download
                                                </a>
                                                <button type="button" class="btn btn-warning" data-id="<?= $dvalue->iddelivery_note ?>" data-no_manual="<?= $dvalue->no_manual ?>" data-foto="<?= $dvalue->foto ?>" data-bs-toggle="modal" data-bs-target="#revisionDeliver">
                                                    <i class="fas fa-edit"></i> Revisi
                                                </button>
                                                <button type="button" class="btn btn-danger btn-delete" data-id="<?= $dvalue->iddelivery_note ?>" data-toggle="modal" data-target="#deleteModal">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
            <!-- 2. DataTables JS -->
            <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
            <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
            <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
            <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
            <script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js"></script>
            <script src="https://cdn.datatables.net/responsive/3.0.4/js/responsive.dataTables.js"></script>
            <!-- 3. Bootstrap bundle -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
            <!-- 4. Core theme JS -->
            <script src="<?php echo base_url(); ?>js/scripts.js"></script>

            <!-- Initialize DataTables AFTER all scripts are loaded -->
            <script>
                $(document).ready(function() {
                    // Initialize DataTable
                    new DataTable('#tableproduct', {
                        responsive: true,
                        // order: [
                        //     [2, 'desc']
                        // ],
                        layout: {
                            bottomEnd: {
                                paging: {
                                    firstLast: false
                                }
                            }
                        }
                    });

                    // Toast container for notifications
                    $('body').append('<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 1100;"></div>');

                    // Update table row function
                    function updateTableRow(id) {
                        $.ajax({
                            url: '<?= base_url("Delivery_note/getDeliveryRow/") ?>' + id,
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                const row = $(`tr:has(td:contains(${response.no_manual}))`).first();

                                // Update status column
                                row.find('td:nth-child(7)').html(getStatusBadge(response.progress));

                                // Update action column
                                row.find('td:nth-child(8)').html(getActionButtons(response));
                            },
                            error: function(xhr, status, error) {
                                showToast('error', 'Gagal memperbarui data: ' + error);
                            }
                        });
                    }

                    // Helper functions
                    function getStatusBadge(progress) {
                        if (progress == 1) return '<span class="badge rounded-pill text-bg-secondary">Dikirim</span>';
                        if (progress == 2) return '<span class="badge rounded-pill text-bg-primary">Terverifikasi(Diterima)</span>';
                        if (progress == 3) return '<span class="badge rounded-pill text-bg-info">Tervalidasi(Terdata)</span>';
                        return '<span class="badge rounded-pill text-bg-success">Final Direksi</span>';
                    }

                    function getActionButtons(data) {
                        let buttons = '';

                        if (data.progress == 1) {
                            buttons += `<a href="#" class="btn btn-sm btn-primary mb-1" onclick="showConfirmModal(${data.iddelivery_note})">
                <i class="fas fa-check"></i> Verifikasi
            </a><br>`;
                        } else if (data.progress == 2) {
                            buttons += `<a href="#" class="btn btn-sm btn-info mb-1" onclick="showValidasiModal(${data.iddelivery_note})">
                <i class="fas fa-check-double"></i> Validasi
            </a><br>`;
                        } else if (data.progress == 3) {
                            buttons += `<a href="#" class="btn btn-sm btn-success mb-1" onclick="showFinalModal(${data.iddelivery_note})">
                <i class="fas fa-check-double"></i> Final DIR
            </a><br>`;
                        }

                        if (<?= $this->session->userdata('idrole') == 1 ? 'true' : 'false' ?>) {
                            buttons += `<a href="<?= base_url('assets/image/surat_jalan/') ?>${data.foto}" download class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-download small"></i> Download
            </a>
            <button type="button" class="btn btn-warning" data-id="${data.iddelivery_note}" data-no_manual="${data.no_manual}" data-foto="${data.foto}" data-bs-toggle="modal" data-bs-target="#revisionDeliver">
                <i class="fas fa-edit"></i> Revisi
            </button>`;
                        }

                        return buttons;
                    }

                    function showToast(type, message) {
                        const toast = `<div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>`;

                        $('#toastContainer').append(toast);
                        $('.toast').toast('show');

                        setTimeout(() => {
                            $('.toast').toast('hide').remove();
                        }, 5000);
                    }

                    // Modal functions
                    window.showConfirmModal = function(id) {
                        $('#confirmVerifikasiBtn').off('click').on('click', function() {
                            $.get('<?= site_url('delivery_note/updateDelivery?id=') ?>' + id, function(response) {
                                const result = typeof response === 'string' ? JSON.parse(response) : response;

                                if (result.status === 'success') {
                                    $('#confirmVerifikasiModal').modal('hide');
                                    updateTableRow(id);
                                    showToast('success', result.message);
                                } else {
                                    showToast(result.status, result.message);
                                }
                            });
                        });
                        $('#confirmVerifikasiModal').modal('show');
                    };

                    window.showValidasiModal = function(id) {
                        $('#confirmValidasiBtn').off('click').on('click', function() {
                            $.get('<?= site_url('delivery_note/validasiDelivery?id=') ?>' + id, function(response) {
                                const result = typeof response === 'string' ? JSON.parse(response) : response;

                                if (result.status === 'success') {
                                    $('#confirmValidasiModal').modal('hide');
                                    updateTableRow(id);
                                    showToast('success', result.message);
                                } else {
                                    showToast(result.status, result.message);
                                }
                            });
                        });
                        $('#confirmValidasiModal').modal('show');
                    };

                    window.showFinalModal = function(id) {
                        $('#confirmFinalBtn').off('click').on('click', function() {
                            $.get('<?= site_url('delivery_note/finalDelivery?id=') ?>' + id, function(response) {
                                const result = typeof response === 'string' ? JSON.parse(response) : response;

                                if (result.status === 'success') {
                                    $('#confirmFinalModal').modal('hide');
                                    updateTableRow(id);
                                    showToast('success', result.message);
                                } else {
                                    showToast(result.status, result.message);
                                }
                            });
                        });
                        $('#confirmFinalModal').modal('show');
                    };

                    // Revision form handling
                    $('#revisionDeliver form').submit(function(e) {
                        e.preventDefault();

                        const formData = new FormData(this);
                        const id = $('#revisionId').val();

                        $.ajax({
                            url: $(this).attr('action'),
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('#revisionDeliver').modal('hide');
                                updateTableRow(id);
                                showToast('success', 'Revisi pengiriman berhasil disimpan.');
                            },
                            error: function(xhr, status, error) {
                                showToast('error', 'Error: ' + error);
                            }
                        });
                    });

                    // Initialize revision modal
                    $('#revisionDeliver').on('show.bs.modal', function(event) {
                        const button = $(event.relatedTarget);
                        const id = button.data('id');
                        const noManual = button.data('no_manual');
                        const foto = button.data('foto');

                        $('#revisionId').val(id);
                        $('#currentNoManual').text(noManual);
                        $('#currentFotoPreview').attr('src', '<?= base_url('assets/image/surat_jalan/') ?>' + foto);
                        $('#revisionNo').val('');
                        $('#revisionFoto').val('');
                        $('#newFotoPreview').hide();
                    });

                    // Preview new photo in revision
                    $('#revisionFoto').change(function() {
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                $('#newFotoImg').attr('src', e.target.result);
                                $('#newFotoPreview').show();
                            }
                            reader.readAsDataURL(file);
                        } else {
                            $('#newFotoPreview').hide();
                        }
                    });
                });

                let html5QrcodeScanner;

                $('#scanBarcodeModal').on('shown.bs.modal', function() {
                    html5QrcodeScanner = new Html5Qrcode("qr-reader");
                    Html5Qrcode.getCameras().then(devices => {
                        if (devices && devices.length) {
                            html5QrcodeScanner.start({
                                    facingMode: "environment"
                                }, {
                                    fps: 10,
                                    qrbox: 250
                                },
                                (decodedText, decodedResult) => {
                                    $('#inputNo').val(decodedText); // Isi input
                                    stopScanner(); // Hentikan kamera
                                    $('#scanBarcodeModal').modal('hide'); // Tutup hanya barcode modal
                                    $('#inputNo').focus(); // Optional: fokus ke inputNo
                                },
                                errorMessage => {
                                    // Bisa diabaikan atau ditampilkan
                                }
                            );
                        }
                    }).catch(err => {
                        alert("Tidak bisa mengakses kamera.");
                    });
                    $('#addDeliver').modal('show');
                });

                function stopScanner() {
                    if (html5QrcodeScanner) {
                        html5QrcodeScanner.stop().then(() => {
                            html5QrcodeScanner.clear();
                        }).catch(err => {
                            console.error("Gagal stop scanner:", err);
                        });
                    }
                }

                function closeScanModal() {
                    stopScanner();
                    $('#scanBarcodeModal').modal('hide');
                }

                let photoStream;

                function openPhotoModal() {
                    navigator.mediaDevices.getUserMedia({
                            video: true
                        })
                        .then(function(stream) {
                            photoStream = stream;
                            const video = document.getElementById('photo-video');
                            video.srcObject = stream;
                            $('#photoCaptureModal').modal('show');
                        })
                        .catch(function(err) {
                            alert("Tidak dapat mengakses kamera: " + err.message);
                        });
                }

                function takePhoto() {
                    const canvas = document.getElementById('photo-canvas');
                    const video = document.getElementById('photo-video');
                    const context = canvas.getContext('2d');

                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageData = canvas.toDataURL('image/png');
                    document.getElementById('photo-preview').src = imageData;
                    document.getElementById('photo-preview').style.display = 'block';
                }

                function submitPhoto() {
                    const canvas = document.getElementById('photo-canvas');
                    canvas.toBlob(function(blob) {
                        const file = new File([blob], "foto_suratjalan.png", {
                            type: 'image/png'
                        });

                        // Simulasi input file
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        document.getElementById('inputFoto').files = dataTransfer.files;

                        closePhotoModal();
                    }, 'image/png');
                }

                function closePhotoModal() {
                    if (photoStream) {
                        const tracks = photoStream.getTracks();
                        tracks.forEach(track => track.stop());
                    }
                    $('#photoCaptureModal').modal('hide');
                }

                function showImageModal(src) {
                    document.getElementById('modalImage').src = src;
                    const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
                    modal.show();
                }

                // Start Modal Preview Surat Jalan
                // Handle Revisi button click
                $(document).on('click', '.btn-warning[data-no_manual]', function() {
                    const id = $(this).data('id');
                    const noManual = $(this).data('no_manual');
                    const foto = $(this).data('foto');

                    // Set values to modal fields
                    $('#revisionId').val(id);
                    $('#currentNoManual').text(noManual);
                    $('#currentFotoPreview').attr('src', '<?= base_url('assets/image/surat_jalan/') ?>' + foto);
                    $('#revisionNo').val('');

                    // Clear any previous new photo preview
                    $('#newFotoPreview').hide();
                    $('#revisionFoto').val('');

                    // Show the revision modal
                    $('#revisionDeliver').modal('show');
                });

                // Preview new file when selected
                $('#revisionFoto').change(function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            $('#newFotoImg').attr('src', e.target.result);
                            $('#newFotoPreview').show();
                        }

                        reader.readAsDataURL(file);
                    } else {
                        $('#newFotoPreview').hide();
                    }
                });

                // Clear previews when modal is closed
                $('#revisionDeliver').on('hidden.bs.modal', function() {
                    $('#existingFotoPreview, #newFotoPreview').remove();
                });
                // End
                // Function to show the image preview modal
                function showImageModal(src) {
                    // Set the image source
                    document.getElementById('modalImage').src = src;

                    // Initialize and show the modal
                    const previewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
                    previewModal.show();

                    // Prevent right-click download
                    document.getElementById('modalImage').oncontextmenu = function(e) {
                        e.preventDefault();
                        return false;
                    };
                }

                // Close modal when clicking outside the image
                document.getElementById('imagePreviewModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        const modal = bootstrap.Modal.getInstance(this);
                        modal.hide();
                    }
                });

                // Keyboard controls for the preview modal
                document.addEventListener('keydown', function(e) {
                    const previewModal = document.getElementById('imagePreviewModal');
                    if (previewModal.classList.contains('show')) {
                        // Close on ESC key
                        if (e.key === 'Escape') {
                            const modal = bootstrap.Modal.getInstance(previewModal);
                            modal.hide();
                        }
                    }
                });

                // Zoom functionality for the image
                document.getElementById('modalImage').addEventListener('click', function(e) {
                    if (this.style.transform === 'scale(1.5)') {
                        this.style.transform = 'scale(1)';
                        this.style.cursor = 'zoom-in';
                    } else {
                        this.style.transform = 'scale(1.5)';
                        this.style.cursor = 'zoom-out';
                    }
                });

                // Reset zoom when modal is hidden
                document.getElementById('imagePreviewModal').addEventListener('hidden.bs.modal', function() {
                    document.getElementById('modalImage').style.transform = 'scale(1)';
                    document.getElementById('modalImage').style.cursor = 'zoom-in';
                });
                // End
                // Modal konfrim delete
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.btn-delete')) {
                        let btn = e.target.closest('.btn-delete');
                        let id = btn.getAttribute('data-id');
                        let deleteUrl = "<?= base_url('delivery_note/deleteDelivery?id=') ?>" + id;
                        document.getElementById('confirmDeleteBtn').setAttribute('href', deleteUrl);

                        // Tampilkan modal secara manual
                        let modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                        modal.show();
                    }
                });


                $(document).on('click', '.btn-delete', function() {
                    let id = $(this).data('id');
                    let deleteUrl = "<?= base_url('delivery_note/deleteDelivery?id=') ?>" + id;
                    $('#confirmDeleteBtn').attr('href', deleteUrl);
                });
                // End
            </script>
            </body>

            </html>