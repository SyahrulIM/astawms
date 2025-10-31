            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Analisys PO</h1>
                    </div>
                </div>

                <!-- Button trigger modal Tambah PO-->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddPo">
                    <i class="fa-solid fa-plus"></i> Tambah PO
                </button>
                <!-- End -->

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

                <!-- Modal Tambal PO-->
                <div class="modal fade modal-xl" id="modalAddPo" tabindex="-1" aria-labelledby="modalAddPoLabel" aria-hidden="true">
                    <form id="formAddPO" action="<?php echo base_url('po/insert') ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menyimpan data PO ini? Pastikan semua data sudah benar.');">
                        <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="modalAddPoLabel"><i class="fa-solid fa-plus"></i> Tambah PO</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="createNumberPo" class="form-label">Nomer PO</label>
                                            <input type="text" class="form-control" id="createNumberPo" name="createNumberPo" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="createOrderDate" class="form-label">Tanggal Pemesanan</label>
                                            <input type="date" class="form-control" id="createOrderDate" name="createOrderDate">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <div class="text-start">
                                                <button type="button" class="btn btn-sm btn-success" id="addRow" onclick="addInputRow()">
                                                    <i class="bi bi-plus"></i> Tambah Baris
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-sm align-middle text-center" style="font-size: small;">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40px;">No</th>
                                                <th>Kode Product</th>
                                                <th>Tipe SGS</th>
                                                <th>Tipe Satuan</th>
                                                <th>Stock Masuk Terakhir</th>
                                                <th>Penjualan Bulan Kemarin</th>
                                                <th colspan="4">Penjualan Mingguan</th>
                                                <th>Saldo Perhari Ini</th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th colspan="6"></th>
                                                <th>I</th>
                                                <th>II</th>
                                                <th>III</th>
                                                <th>IV</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBodyPO">
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="createIdProduct[]">
                                                        <option disabled selected>Pilih Kode Produk</option>
                                                        <?php foreach ($product as $key => $p) { ?>
                                                        <option value="<?php echo $p->idproduct; ?>"><?php echo $p->sku . '(' . $p->nama_produk . ')' ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="createTypeSgs[]">
                                                        <option disabled selected>Pilih SGS</option>
                                                        <option value="sgs">SGS</option>
                                                        <option value="non sgs">Non SGS</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="createTypeUnit[]">
                                                        <option disabled selected>Pilih Satuan</option>
                                                        <option value="pcs">Pcs</option>
                                                        <option value="gram">Gram</option>
                                                    </select>
                                                </td>
                                                <td><input type="number" class="form-control form-control-sm" name="createLatestIncomingStock[]" max="10000"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="createSaleLastMouth[]" max="10000"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="createSaleWeekOne[]" max="10000"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="createSaleWeekTwo[]" max="10000"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="createSaleWeekThree[]" max="10000"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="createSaleWeekFour[]" max="10000"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="createBalancePerToday[]" max="10000"></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- End -->

                <div class="row">
                    <div class="col">
                        <ul class="nav nav-tabs mt-4" id="listTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <?php
                                $current = $this->uri->segment(1);
                                $current_po = '';
                                $current_qty = '';
                                if ($current == 'po') {
                                    $current_po = 'active';
                                } else if ($current == 'qty_order') {
                                    $current_qty = 'active';
                                }
                                ?>
                                <a class="nav-link <?php echo $current_po; ?>" id="list-tab" type="button" href="<?php echo base_url('po/'); ?>">
                                    Data Penjualan
                                </a>
                            </li>
                            <li class="nav-item <?php echo $current_qty; ?>" role="presentation">
                                <button class="nav-link" id="all-tab" type="button">
                                    Qty Order
                                </button>
                            </li>
                        </ul>
                        <table id="tableproduct" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomer PO</th>
                                    <th>Tanggal Pesan</th>
                                    <th>User Pembuat</th>
                                    <th>Tanggal Pembuat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($data_trx as $key => $trx) { ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td><?php echo $trx->number_po ?></td>
                                    <td><?php echo $trx->order_date ?></td>
                                    <td><?php echo $trx->created_by ?></td>
                                    <td><?php echo $trx->created_date ?></td>
                                    <td></td>
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

                function addInputRow() {
                    const tableBody = document.getElementById('tableBodyPO');
                    const rowCount = tableBody.rows.length;
                    const newRow = tableBody.insertRow();

                    newRow.innerHTML = `
                    <td>${rowCount + 1}</td>
                    <td>
                        <select class="form-select form-select-sm" name="createIdProduct[]">
                            <option disabled selected>Pilih Kode Produk</option>
                            <?php foreach ($product as $key => $p) { ?>
                                <option value="<?php echo $p->idproduct; ?>"><?php echo $p->sku . '(' . $p->nama_produk . ')' ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" name="createTypeSgs[]">
                            <option disabled selected>Pilih SGS</option>
                            <option value="sgs">SGS</option>
                            <option value="non sgs">Non SGS</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" name="createTypeUnit[]">
                            <option disabled selected>Pilih Satuan</option>
                            <option value="pcs">Pcs</option>
                            <option value="gram">Gram</option>
                        </select>
                    </td>
                    <td><input type="number" class="form-control form-control-sm" name="createLatestIncomingStock[]" max="10000"></td>
                    <td><input type="number" class="form-control form-control-sm" name="createSaleLastMouth[]" max="10000"></td>
                    <td><input type="number" class="form-control form-control-sm" name="createSaleWeekOne[]" max="10000"></td>
                    <td><input type="number" class="form-control form-control-sm" name="createSaleWeekTwo[]" max="10000"></td>
                    <td><input type="number" class="form-control form-control-sm" name="createSaleWeekThree[]" max="10000"></td>
                    <td><input type="number" class="form-control form-control-sm" name="createSaleWeekFour[]" max="10000"></td>
                    <td><input type="number" class="form-control form-control-sm" name="createBalancePerToday[]" max="10000"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"><i class="fa-solid fa-trash-can"></i></button></td>
                `;
                }

                // Fungsi hapus baris
                function deleteRow(button) {
                    const row = button.closest('tr');
                    row.remove();
                    updateRowNumbers();
                }

                // Update nomor setelah hapus baris
                function updateRowNumbers() {
                    const rows = document.querySelectorAll('#tableBodyPO tr');
                    rows.forEach((row, index) => {
                        row.cells[0].textContent = index + 1;
                    });
                }
            </script>
            </body>

            </html>