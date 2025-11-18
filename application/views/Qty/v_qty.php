            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Analisa PO</h1>
                    </div>
                </div>

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

                <!-- Start Modal Detail PO -->
                <div class="modal fade modal-xl" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                    <form action="<?php echo base_url('qty/process'); ?>" method="post" onsubmit="return confirm('Pastikan semua data sudah benar, Apakah Anda yakin ingin meproses data PO ini?');">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailModalLabel">Process Produk PO</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="detailContent">Memuat data...</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary">Tutup</button>
                                    <button type="submit" class="btn btn-info">Process</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- End -->

                <!-- Start Modal Batal Pemesanan -->
                <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="cancelModalLabel">Konfirmasi Pembatalan</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Apakah Anda yakin ingin membatalkan pemesanan ini?</p>
                                <input type="hidden" id="cancelIdPo">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                <button type="button" class="btn btn-danger" id="confirmCancelBtn">Ya, Batalkan</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End -->

                <div class="row">
                    <div class="col">
                        <ul class="nav nav-tabs mt-4" id="listTabs" role="tablist">
                            <?php
                            $current = $this->uri->segment(1);
                            $current_po = $current_qty = $current_pre = $current_finish = '';

                            if ($current == 'po') {
                                $current_po = 'active';
                            } elseif ($current == 'qty') {
                                $current_qty = 'active';
                            } elseif ($current == 'pre') {
                                $current_pre = 'active';
                            } elseif ($current == 'finish') {
                                $current_finish = 'active';
                            }

                            // load helper preorder
                            $this->load->helper('preorder');
                            $count_qty = number_pre_order_qty();
                            $count_pre = number_pre_order_pre();
                            ?>

                            <li class="nav-item position-relative">
                                <a class="nav-link <?php echo $current_po; ?>" id="list-tab" type="button" href="<?php echo base_url('po/'); ?>">
                                    Data Stock
                                </a>
                            </li>

                            <li class="nav-item position-relative">
                                <a class="nav-link <?php echo $current_qty; ?>" id="list-tab" type="button" href="<?php echo base_url('qty/'); ?>">
                                    Performa PO
                                    <?php if ($count_qty > 0) : ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo $count_qty; ?>
                                    </span>
                                    <?php endif; ?>
                                </a>
                            </li>

                            <li class="nav-item position-relative">
                                <a class="nav-link <?php echo $current_finish; ?>" id="list-tab" type="button" href="<?php echo base_url('finish/'); ?>">
                                    Finish
                                </a>
                            </li>
                        </ul>
                        <table id="tableproduct" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomer PO</th>
                                    <th>Container</th>
                                    <th>Tanggal Pesan</th>
                                    <th>User Pembuat</th>
                                    <th>Tanggal Pembuat</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($data_trx as $key => $trx) { ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td>
                                        <?php if ($trx->number_po) {
                                                echo $trx->number_po;
                                            } else {
                                                echo 'In Progress';
                                            } ?>
                                    </td>
                                    <td>
                                        <?php if ($trx->name_container) {
                                                echo $trx->name_container;
                                            } else {
                                                echo 'In Progress';
                                            } ?>
                                    </td>
                                    <td>
                                        <?php if ($trx->order_date) {
                                                echo $trx->order_date;
                                            } else {
                                                echo 'In Progress';
                                            } ?>
                                        <?php echo $trx->order_date ?></td>
                                    <td><?php echo $trx->created_by ?></td>
                                    <td><?php echo $trx->created_date ?></td>
                                    <td>
                                        <?php if ($trx->status_progress == 'Listing') { ?>
                                        <?php echo '<span class="badge text-bg-primary">Terlisting</span>'; ?>
                                        <?php } elseif ($trx->status_progress == 'Cancel') { ?>
                                        <?php echo '<span class="badge text-bg-danger">Tercancel</span>'; ?>
                                        <?php } elseif ($trx->status_progress == 'Finish') { ?>
                                        <?php echo '<span class="badge text-bg-success">Finish</span>'; ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($trx->status_progress == 'Listing' || $trx->status_progress == 'Finish') { ?>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="showDetail(<?= $trx->idanalisys_po ?>)">
                                            <i class="fa-solid fa-bars"></i> Detail
                                        </button>
                                        <?php } ?>
                                        <?php if ($trx->status_progress == 'Listing' || $trx->status_progress == 'Qty') { ?>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="showCancelModal(<?= $trx->idanalisys_po ?>)">
                                            <i class="fa-solid fa-trash-can"></i> Batal Pemesanan
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

                function showDetail(idanalisys_po) {
                    // tampilkan modal dan loading
                    document.getElementById('detailContent').innerHTML = 'Memuat data...';
                    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                    modal.show();

                    // ambil data dari controller
                    fetch(`<?= base_url('qty/get_detail_analisys_po/') ?>${idanalisys_po}`)
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('detailContent').innerHTML = html;
                        })
                        .catch(() => {
                            document.getElementById('detailContent').innerHTML = '<div class="text-danger">Gagal memuat data.</div>';
                        });
                }

                function showCancelModal(id) {
                    document.getElementById('cancelIdPo').value = id;
                    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
                    modal.show();
                }

                document.getElementById('confirmCancelBtn').addEventListener('click', function() {
                    const id = document.getElementById('cancelIdPo').value;
                    window.location.href = `<?= base_url('qty/cancel/') ?>${id}`;
                });
            </script>
            </body>

            </html>