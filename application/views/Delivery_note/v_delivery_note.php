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
                                            <img src="<?php echo base_url('assets/image/surat_jalan/' . $dvalue->foto); ?>" alt="<?php echo $dvalue->foto; ?>" width="100px" height="100px" oncontextmenu="return false;">
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
                                                <a href="#" class="btn btn-sm btn-primary mb-1" onclick="showConfirmModal('<?php echo site_url('delivery_note/updateDelivery?id=' . $dvalue->iddelivery_note); ?>')">
                                                    <i class="fas fa-check"></i> Verifikasi
                                                </a><br>
                                            <?php } else if ($dvalue->progress == 2) { ?>
                                                <a href="#" class="btn btn-sm btn-info mb-1" onclick="showValidasiModal('<?php echo site_url('delivery_note/validasiDelivery?id=' . $dvalue->iddelivery_note); ?>')">
                                                    <i class="fas fa-check-double"></i> Validasi
                                                </a><br>
                                            <?php } else if ($dvalue->progress == 3) { ?>
                                                <a href="#" class="btn btn-sm btn-success mb-1" onclick="showValidasiModal('<?php echo site_url('delivery_note/finalDelivery?id=' . $dvalue->iddelivery_note); ?>')">
                                                    <i class="fas fa-check-double"></i> Final DIR
                                                </a><br>
                                            <?php } ?>
                                            <?php if ($this->session->userdata('idrole') == 1) { ?>
                                                <a href="<?php echo base_url('assets/image/surat_jalan/' . $dvalue->foto); ?>" download class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-download small"></i> Download Surat Jalan
                                                </a>
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
                });

                function showConfirmModal(link) {
                    $('#confirmVerifikasiBtn').attr('href', link);
                    $('#confirmVerifikasiModal').modal('show');
                }

                function showValidasiModal(link) {
                    $('#confirmValidasiBtn').attr('href', link);
                    $('#confirmValidasiModal').modal('show');
                }

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
            </script>
            </body>

            </html>