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
                                    <div class="mb-3">
                                        <label for="editSku" class="form-label">SKU</label>
                                        <input type="text" class="form-control" id="editSku" name="inputSku" required readonly>
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
                                    <td><svg id="barcode-<?= $pvalue->idproduct ?>"></svg></td>
                                    <td>
                                        <?php if (!empty($pvalue->sni)) { ?>
                                        <img src="<?= base_url('assets/image/' . $pvalue->sni) ?>" style="width: 100px; height: 100px;">
                                        <?php } else { ?>
                                        <i class="fa-solid fa-xmark" style="font-size: 40px; color: red;"></i>
                                        <?php } ?>
                                    </td>
                                    <?php foreach ($gudang as $g) {
                                            $stok = isset($stokMap[$pvalue->idproduct][$g->idgudang]) ? $stokMap[$pvalue->idproduct][$g->idgudang] : 0;
                                            ?>
                                    <td><?= $stok ?></td>
                                    <?php } ?>
                                    <td>
                                    <?php if ($this->session->userdata('idrole') != 4 && $this->session->userdata('idrole') != 2) { ?>
                                        <button type="button" class="btn btn-warning btnEditProduk" data-sku="<?= $pvalue->sku ?>" data-nama="<?= $pvalue->nama_produk ?>" data-barcode="<?= $pvalue->barcode ?>" data-gambar="<?= $pvalue->gambar ?>" data-sni="<?= $pvalue->sni ?>" data-bs-toggle="modal" data-bs-target="#editProduct">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <a href="<?php echo base_url('product/deleteProduct?idproduct=' . $pvalue->idproduct); ?>">
                                            <button type="button" class="btn btn-danger">
                                                <i class="fa-solid fa-trash-can"></i> Hapus
                                            </button>
                                        </a>
                                        <?php } ?>
                                        <a href="<?= base_url('product/stockCard?sku=' . $pvalue->sku); ?>">
                                            <button type="button" class="btn btn-success">
                                                <i class="fa-solid fa-print"></i> Kartu Stock
                                            </button>
                                        </a>
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
                        }
                    });
                });

                document.addEventListener("DOMContentLoaded", function() {
                    const form = document.querySelector('#addProduct form');
                    const inputGambar = document.getElementById('inputGambar');
                    const inputSni = document.getElementById('inputSni');

                    function isValidImage(file) {
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                        return allowedTypes.includes(file.type);
                    }

                    form.addEventListener('submit', function(e) {
                        if (inputGambar.files.length > 0 && !isValidImage(inputGambar.files[0])) {
                            alert('Format file Gambar harus JPG, JPEG, atau PNG.');
                            e.preventDefault();
                            return;
                        }

                        if (inputSni.files.length > 0 && !isValidImage(inputSni.files[0])) {
                            alert('Format file SNI harus JPG, JPEG, atau PNG.');
                            e.preventDefault();
                            return;
                        }
                    });
                });

                document.addEventListener('DOMContentLoaded', function() {
                    const editButtons = document.querySelectorAll('.btnEditProduk');

                    editButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const sku = this.getAttribute('data-sku');
                            const nama = this.getAttribute('data-nama');
                            const barcode = this.getAttribute('data-barcode');
                            const gambar = this.getAttribute('data-gambar');
                            const sni = this.getAttribute('data-sni');

                            // Populate edit modal inputs
                            document.getElementById('editSku').value = sku;
                            document.getElementById('editNamaProduk').value = nama;
                            document.getElementById('editBarcode').value = barcode;
                            document.getElementById('editGambar').value = gambar;
                            document.getElementById('editSni').value = sni;
                            // Extract filename from image path
                            const gambarFile = gambar.split('/').pop();
                            const sniFile = sni.split('/').pop();

                            // Show filename under file input
                            document.getElementById('gambarFilename').innerText = "File sebelumnya: " + gambarFile;
                            document.getElementById('sniFilename').innerText = "File sebelumnya: " + sniFile;
                        });
                    });
                });

                document.addEventListener("DOMContentLoaded", function() {
                    <?php foreach ($product as $pvalue) { ?>
                    JsBarcode("#barcode-<?= $pvalue->idproduct ?>", "<?= $pvalue->barcode ?>");
                    <?php } ?>
                });
            </script>
            </body>

            </html>