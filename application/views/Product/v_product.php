            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Database Product</h1>
                    </div>
                </div>
                <!-- Button trigger modal Tambah Produk -->
                <?php if ($this->session->userdata('idrole') != 4 && $this->session->userdata('idrole') != 2) { ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProduct">
                    <i class="fa-solid fa-plus"></i> Tambah Produk
                </button>
                <?php } ?>

                <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
                <?php endif; ?>

                <!-- Modal Tambah Produk -->
                <div class="modal fade" id="addProduct" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
                    <form method="post" action="<?php echo base_url('product/addProduct') ?>" enctype="multipart/form-data">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="addProductLabel">Tambah Produk</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="inputSku" class="form-label">SKU</label>
                                        <input type="text" class="form-control" id="inputSku" name="inputSku" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputNamaProduk" class="form-label">Nama Produk</label>
                                        <input type="text" class="form-control" id="inputNamaProduk" name="inputNamaProduk" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputGambar">Gambar</label>
                                        <input type="file" class="form-control" id="inputGambar" name="inputGambar" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputBarcode" class="form-label">Barcode</label>
                                        <input type="text" class="form-control" id="inputBarcode" name="inputBarcode" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inputSni">SNI</label>
                                        <input type="file" class="form-control" id="inputSni" name="inputSni" accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- End -->
                <!-- Modal Edit Produk -->
                <div class="modal fade" id="editProduct" tabindex="-1" aria-labelledby="editProductLabel" aria-hidden="true">
                    <form method="post" action="<?php echo base_url('product/editProduct') ?>" enctype="multipart/form-data">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="editProductLabel">Edit Produk</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Hidden input for idproduct -->
                                    <input type="hidden" id="editIdProduct" name="inputIdProduct">

                                    <div class="mb-3">
                                        <label for="editSku" class="form-label">SKU</label>
                                        <input type="text" class="form-control" id="editSku" name="inputSku" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editNamaProduk" class="form-label">Nama Produk</label>
                                        <input type="text" class="form-control" id="editNamaProduk" name="inputNamaProduk" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editGambar">Gambar</label>
                                        <input type="file" class="form-control" id="editGambar" name="inputGambar" accept="image/*">
                                        <small id="gambarFilename" class="form-text text-muted"></small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editBarcode" class="form-label">Barcode</label>
                                        <input type="text" class="form-control" id="editBarcode" name="inputBarcode" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editSni">SNI</label>
                                        <input type="file" class="form-control" id="editSni" name="inputSni" accept="image/*">
                                        <small id="sniFilename" class="form-text text-muted"></small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- End -->
                <!-- Modal Konfirmasi Simpan -->
                <div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-labelledby="confirmSaveLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="confirmSaveLabel">Konfirmasi Simpan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin menyimpan perubahan produk ini?<br>
                                <strong id="productConfirmInfo"></strong>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="button" id="btnConfirmSave" class="btn btn-primary">Ya, Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End -->
                <!-- Modal Konfirmasi Hapus -->
                <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Apa Anda Yakin ingin menghapus produk <strong><span id="productInfo"></span></strong>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <a id="btnConfirmDelete" href="#" class="btn btn-danger">Ya, Hapus</a>
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
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Gambar</th>
                                    <th>Barcode</th>
                                    <th>SNI</th>
                                    <?php foreach ($gudang as $g) { ?>
                                    <th>Stock <?= $g->nama_gudang ?></th>
                                    <?php } ?>
                                    <th>Total Stock</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($product as $pkey => $pvalue) { ?>
                                <tr>
                                    <td><?= $pkey + 1 ?></td>
                                    <td><?= $pvalue->sku ?></td>
                                    <td><?= $pvalue->nama_produk ?></td>
                                    <td><img src="<?= base_url('assets/image/' . $pvalue->gambar) ?>" style="width: 100px; height: 100px;"></td>
                                    <td><canvas id="barcode-<?= $pvalue->idproduct ?>" width="150" height="70"></canvas></td>
                                    <td>
                                        <?php if (!empty($pvalue->sni)) { ?>
                                        <img src="<?= base_url('assets/image/' . $pvalue->sni) ?>" style="width: 100px; height: 100px;">
                                        <?php } else { ?>
                                        <i class="fa-solid fa-xmark" style="font-size: 40px; color: red;"></i>
                                        <?php } ?>
                                    </td>
                                    <?php
                                        $currentProductTotalStock = 0; // Initialize for each product
                                        foreach ($gudang as $g) {
                                            $stok = isset($stokMap[$pvalue->idproduct][$g->idgudang]) ? $stokMap[$pvalue->idproduct][$g->idgudang] : 0;
                                            $currentProductTotalStock += $stok; // Add to total
                                            ?>
                                    <td><?= $stok ?></td>
                                    <?php } ?>
                                    <td><?= $totalStokAllGudang[$pvalue->idproduct] ?? 0 ?></td>
                                    <td><?= $pvalue->created_date ?></td>
                                    <td>
                                        <?php if ($this->session->userdata('idrole') != 4 && $this->session->userdata('idrole') != 2) { ?>
                                        <button type="button" class="btn btn-warning btnEditProduk" data-idproduct="<?= $pvalue->idproduct ?>" data-sku="<?= $pvalue->sku ?>" data-nama="<?= $pvalue->nama_produk ?>" data-barcode="<?= $pvalue->barcode ?>" data-gambar="<?= $pvalue->gambar ?>" data-sni="<?= $pvalue->sni ?>" data-bs-toggle="modal" data-bs-target="#editProduct">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-danger btnDelete" data-url="<?= base_url('product/deleteProduct?idproduct=' . $pvalue->idproduct); ?>" data-nama="<?= $pvalue->nama_produk ?>" data-sku="<?= $pvalue->sku ?>" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                        <?php } ?>
                                        <a href="<?= base_url('product/stockCard?sku=' . $pvalue->sku); ?>">
                                            <button type="button" class="btn btn-success">
                                                <i class="fa-solid fa-print"></i> Kartu Stock
                                            </button>
                                        </a>
                                        <button class="btn btn-sm btn-primary" onclick="downloadCanvasAsJpeg('barcode-<?= $pvalue->idproduct ?>', 'barcode_<?= $pvalue->sku ?>', '<?= $pvalue->barcode ?>')">
                                            <i class="fa fa-download"></i> Download Barcode
                                        </button>
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
            <!-- Barcode -->
            <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

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
                        },
                        order: [
                            [9, 'dsc']
                        ] // Index kolom 'created_date', ganti sesuai urutan sebenarnya
                    });
                });

                document.addEventListener("DOMContentLoaded", function() {
                    // Image validation helper function
                    function isValidImage(file) {
                        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                        return validTypes.includes(file.type);
                    }

                    // Handle Add Product form
                    const formAdd = document.querySelector('#addProduct form');
                    const inputSku = document.getElementById('inputSku');
                    const inputNama = document.getElementById('inputNamaProduk');
                    const inputGambar = document.getElementById('inputGambar');
                    const inputSni = document.getElementById('inputSni');
                    const btnConfirmSave = document.getElementById('btnConfirmSave');
                    let currentForm = null;

                    if (formAdd) {
                        formAdd.addEventListener('submit', function(e) {
                            e.preventDefault();

                            // Validate SKU no spaces
                            if (/\s/.test(inputSku.value)) {
                                alert('SKU tidak boleh mengandung spasi.');
                                return;
                            }

                            // Validate image file if uploaded
                            if (inputGambar.files.length > 0 && !isValidImage(inputGambar.files[0])) {
                                alert('Format file Gambar harus JPG, JPEG, atau PNG.');
                                return;
                            }

                            // Validate SNI file if uploaded
                            if (inputSni.files.length > 0 && !isValidImage(inputSni.files[0])) {
                                alert('Format file SNI harus JPG, JPEG, atau PNG.');
                                return;
                            }

                            // Show confirmation modal
                            document.getElementById('productConfirmInfo').textContent = `${inputNama.value} (SKU: ${inputSku.value})`;
                            currentForm = formAdd;
                            const modal = new bootstrap.Modal(document.getElementById('confirmSaveModal'));
                            modal.show();
                        });
                    }

                    // Handle Edit Product form
                    const formEdit = document.querySelector('#editProduct form');
                    const editSku = document.getElementById('editSku');
                    const editNama = document.getElementById('editNamaProduk');
                    const editGambar = document.getElementById('editGambar');
                    const editSni = document.getElementById('editSni');

                    if (formEdit) {
                        formEdit.addEventListener('submit', function(e) {
                            e.preventDefault();

                            // Validate SKU no spaces
                            if (/\s/.test(editSku.value)) {
                                alert('SKU tidak boleh mengandung spasi.');
                                return;
                            }

                            // Validate image file if uploaded
                            if (editGambar.files.length > 0 && !isValidImage(editGambar.files[0])) {
                                alert('Format file Gambar harus JPG, JPEG, atau PNG.');
                                return;
                            }

                            // Validate SNI file if uploaded
                            if (editSni.files.length > 0 && !isValidImage(editSni.files[0])) {
                                alert('Format file SNI harus JPG, JPEG, atau PNG.');
                                return;
                            }

                            // Show confirmation modal
                            document.getElementById('productConfirmInfo').textContent = `${editNama.value} (SKU: ${editSku.value})`;
                            currentForm = formEdit;
                            const modal = new bootstrap.Modal(document.getElementById('confirmSaveModal'));
                            modal.show();
                        });
                    }

                    // Handle confirmation button click
                    if (btnConfirmSave) {
                        btnConfirmSave.addEventListener('click', function() {
                            if (currentForm) {
                                currentForm.submit();
                            }
                        });
                    }

                    // Populate edit modal with data
                    document.querySelectorAll('.btnEditProduk').forEach(button => {
                        button.addEventListener('click', function() {
                            const idproduct = this.getAttribute('data-idproduct');
                            const sku = this.getAttribute('data-sku');
                            const nama = this.getAttribute('data-nama');
                            const barcode = this.getAttribute('data-barcode');
                            const gambar = this.getAttribute('data-gambar');
                            const sni = this.getAttribute('data-sni');

                            document.getElementById('editIdProduct').value = idproduct;
                            document.getElementById('editSku').value = sku;
                            document.getElementById('editNamaProduk').value = nama;
                            document.getElementById('editBarcode').value = barcode;

                            // Extract and display current filenames
                            if (gambar) {
                                const gambarFile = gambar.split('/').pop();
                                document.getElementById('gambarFilename').innerText = "File sebelumnya: " + gambarFile;
                            } else {
                                document.getElementById('gambarFilename').innerText = "Tidak ada file sebelumnya";
                            }

                            if (sni) {
                                const sniFile = sni.split('/').pop();
                                document.getElementById('sniFilename').innerText = "File sebelumnya: " + sniFile;
                            } else {
                                document.getElementById('sniFilename').innerText = "Tidak ada file sebelumnya";
                            }
                        });
                    });

                    // Generate barcodes
                    <?php foreach ($product as $pvalue) { ?>
                    try {
                        JsBarcode("#barcode-<?= $pvalue->idproduct ?>", "<?= $pvalue->barcode ?>", {
                            format: "CODE128",
                            width: 2,
                            height: 40,
                            displayValue: true,
                            fontSize: 12
                        });
                    } catch (e) {
                        console.error("Barcode generation failed for ID <?= $pvalue->idproduct ?>", e);
                    }
                    <?php } ?>

                    // Handle delete button
                    document.querySelectorAll('.btnDelete').forEach(button => {
                        button.addEventListener('click', function() {
                            const nama = this.getAttribute('data-nama');
                            const sku = this.getAttribute('data-sku');
                            const url = this.getAttribute('data-url');

                            document.getElementById('productInfo').textContent = `${nama} (SKU: ${sku})`;
                            document.getElementById('btnConfirmDelete').href = url;
                        });
                    });
                });

                function downloadCanvasAsJpeg(canvasId, filename, barcodeText) {
                    const canvas = document.getElementById(canvasId);

                    // Regenerate barcode with better quality for download
                    JsBarcode(canvas, barcodeText, {
                        format: "CODE128",
                        width: 3,
                        height: 60,
                        displayValue: true,
                        fontSize: 14
                    });

                    // Small delay to ensure barcode is rendered
                    setTimeout(() => {
                        const image = canvas.toDataURL("image/jpeg", 1.0);
                        const link = document.createElement('a');
                        link.href = image;
                        link.download = filename + '.jpg';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }, 100);
                }
            </script>
            </body>

            </html>