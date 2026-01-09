            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Analisa PO</h1>
                    </div>
                </div>

                <!-- Button trigger modal Tambah PO-->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddPo">
                    <i class="fa-solid fa-plus"></i> Data Stock
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
                    <form id="formAddPO" action="<?php echo base_url('po/insert') ?>" method="post" enctype="multipart/form-data" onsubmit="return confirm('Apakah Anda yakin ingin menyimpan data PO ini? Pastikan semua data sudah benar.');">
                        <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="modalAddPoLabel"><i class="fa-solid fa-plus"></i> Tambah PO</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label for="sale_mouth" class="form-label">Type Pre Order:</label>
                                                <select name="type_po" id="type_po" class="form-select">
                                                    <option value="">--- Select Type Pre Order ---</option>
                                                    <option value="local">Local</option>
                                                    <option value="Import">Import</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label for="sale_mouth" class="form-label">Excel Rincian Penjualan per Barang per Bulan Accurate ( Penjualan bulan lalu, Penjualan bulan ini ):</label>
                                                <input type="file" class="form-control" name="sale_mouth" id="sale_mouth" accept=".xlsx">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label for="balance_for_today" class="form-label">Excel Stock perhari ini ( Saldo Perhari Ini ):</label>
                                                <input type="file" class="form-control" name="balance_for_today" id="balance_for_today" accept=".xlsx">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label for="latest_incoming_stock" class="form-label">Excel Pembelian Barang ( Stock Masuk Terakhir ):</label>
                                                <input type="file" class="form-control" name="latest_incoming_stock" id="latest_incoming_stock" accept=".xlsx">
                                            </div>
                                        </div>
                                    </div>
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

                <style>
                    .table-scroll {
                        max-height: 450px;
                        overflow-y: auto;
                        display: block;
                    }

                    .table-scroll table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    .table-scroll thead th {
                        position: sticky;
                        top: 0;
                        background: #f8f9fa;
                        z-index: 10;
                    }
                </style>

                <!-- Start Modal Detail PO -->
                <div class="modal fade modal-xl" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true" style="font-size: small;">
                    <form action="<?php echo base_url('qty/process'); ?>" method="post">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailModalLabel">Process Purchase Order</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="detailContent">Memuat data...</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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

                            $this->load->helper('preorder');
                            $count_qty = number_pre_order_qty();
                            $count_pre = number_pre_order_pre();
                            ?>

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
                                    Final
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
            <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
            <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
            <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
            <script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js"></script>
            <script src="https://cdn.datatables.net/responsive/3.0.4/js/responsive.dataTables.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
            <script src="<?php echo base_url(); ?>js/scripts.js"></script>

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

                    $('#formAddPO').on('submit', function(e) {
                        const saleMouth = $('#sale_mouth').val();
                        const balanceToday = $('#balance_for_today').val();
                        const incomingStock = $('#latest_incoming_stock').val();

                        if (!saleMouth || !balanceToday || !incomingStock) {
                            e.preventDefault();
                            alert('Harap unggah semua file yang diperlukan!');
                            return false;
                        }

                        const allowedExtensions = /(\.xlsx)$/i;
                        if (!allowedExtensions.exec(saleMouth) || !allowedExtensions.exec(balanceToday) || !allowedExtensions.exec(incomingStock)) {
                            e.preventDefault();
                            alert('Hanya file Excel (.xlsx) yang diizinkan!');
                            return false;
                        }

                        return true;
                    });

                    setTimeout(function() {
                        $('.alert').alert('close');
                    }, 5000);

                    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                        localStorage.setItem('activeTab', $(e.target).attr('href'));
                    });

                    const activeTab = localStorage.getItem('activeTab');
                    if (activeTab) {
                        const tabElement = $('.nav-tabs a[href="' + activeTab + '"]');
                        if (tabElement.length) {
                            tabElement.tab('show');
                        }
                    }
                });

                function showDetail(idanalisys_po) {
                    document.getElementById('detailContent').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat data...</p>
            </div>
        `;

                    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                    modal.show();

                    fetch('<?= base_url('qty/get_detail_analisys_po/') ?>' + idanalisys_po)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(html => {
                            document.getElementById('detailContent').innerHTML = html;
                            initializeSearch();
                            initializeTooltips();
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            document.getElementById('detailContent').innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Gagal memuat data. Silakan coba lagi.
                    </div>
                `;
                        });
                }

                // Function to initialize search functionality
                function initializeSearch() {
                    const searchInput = document.getElementById('searchDetailTable');
                    const clearButton = document.getElementById('clearSearch');
                    const searchResultInfo = document.getElementById('searchResultInfo');

                    if (!searchInput) {
                        console.log('Search input not found');
                        return;
                    }

                    console.log('Initializing search...');

                    function performSearch() {
                        const searchTerm = searchInput.value.toLowerCase().trim();
                        const tableBody = document.getElementById('detailTableBody');

                        if (!tableBody) {
                            console.error('Table body not found');
                            return;
                        }

                        // Get all rows in the table body
                        const rows = tableBody.querySelectorAll('tr');
                        let visibleCount = 0;
                        let totalRows = rows.length;

                        rows.forEach(row => {
                            // Check if this is a "no data" message row
                            const isNoDataRow = row.querySelector('td[colspan="12"]');
                            if (isNoDataRow) {
                                // For "no data" rows, show only if search is empty
                                row.style.display = searchTerm === '' ? '' : 'none';
                                if (searchTerm === '') visibleCount++;
                                return;
                            }

                            const searchData = row.getAttribute('data-search') || '';

                            if (searchTerm === '' || searchData.includes(searchTerm)) {
                                row.style.display = '';
                                visibleCount++;
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        // Update search result info
                        if (searchTerm === '') {
                            searchResultInfo.textContent = 'Menampilkan semua ' + visibleCount + ' data';
                            searchResultInfo.className = 'form-text text-muted';
                        } else {
                            const hasResults = visibleCount > 0;
                            searchResultInfo.textContent = hasResults ?
                                'Menemukan ' + visibleCount + ' dari ' + totalRows + ' data untuk "' + searchTerm + '"' :
                                'Tidak ditemukan data untuk "' + searchTerm + '"';
                            searchResultInfo.className = hasResults ? 'form-text text-success fw-bold' : 'form-text text-danger fw-bold';
                        }
                    }

                    // Add event listeners
                    searchInput.addEventListener('input', debounce(performSearch, 300));

                    if (clearButton) {
                        clearButton.addEventListener('click', function() {
                            searchInput.value = '';
                            performSearch();
                            searchInput.focus();
                        });
                    }

                    searchInput.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            performSearch();
                        }
                    });

                    // Initialize search on load
                    setTimeout(performSearch, 50);
                }

                // Function to initialize tooltips
                function initializeTooltips() {
                    // Add tooltips for previous quantity
                    const qtyInputs = document.querySelectorAll('.qty-input[title]');
                    qtyInputs.forEach(input => {
                        $(input).tooltip({
                            trigger: 'hover focus',
                            placement: 'top'
                        });
                    });
                }

                // Function to show cancel confirmation modal
                function showCancelModal(id) {
                    document.getElementById('cancelIdPo').value = id;
                    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
                    modal.show();
                }

                // Event listener for modal show (to reset search when modal opens)
                document.getElementById('detailModal').addEventListener('show.bs.modal', function() {
                    const searchInput = document.getElementById('searchDetailTable');
                    const searchResultInfo = document.getElementById('searchResultInfo');

                    if (searchInput) {
                        searchInput.value = '';
                    }
                    if (searchResultInfo) {
                        searchResultInfo.textContent = 'Menampilkan semua data';
                        searchResultInfo.className = 'form-text text-muted';
                    }
                });

                // Event listener for modal hidden (cleanup)
                document.getElementById('detailModal').addEventListener('hidden.bs.modal', function() {
                    document.getElementById('detailContent').innerHTML = 'Memuat data...';
                    // Remove any tooltips
                    $('.qty-input').tooltip('dispose');
                });

                // Confirm cancel button event listener
                document.getElementById('confirmCancelBtn').addEventListener('click', function() {
                    const id = document.getElementById('cancelIdPo').value;
                    if (id) {
                        const cancelBtn = this;
                        const originalText = cancelBtn.innerHTML;
                        cancelBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Membatalkan...';
                        cancelBtn.disabled = true;

                        window.location.href = '<?= base_url('qty/cancel/') ?>' + id;
                    }
                });

                // Global keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    // Ctrl + F for search focus (when detail modal is open)
                    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                        const searchInput = document.getElementById('searchDetailTable');
                        const detailModal = document.getElementById('detailModal');
                        if (searchInput && detailModal && detailModal.classList.contains('show')) {
                            e.preventDefault();
                            searchInput.focus();
                            searchInput.select();
                        }
                    }

                    // Escape key to close modals
                    if (e.key === 'Escape') {
                        const detailModalEl = document.getElementById('detailModal');
                        const cancelModalEl = document.getElementById('cancelModal');
                        const addPoModalEl = document.getElementById('modalAddPo');

                        if (detailModalEl && detailModalEl.classList.contains('show')) {
                            const detailModal = bootstrap.Modal.getInstance(detailModalEl);
                            if (detailModal) detailModal.hide();
                        }

                        if (cancelModalEl && cancelModalEl.classList.contains('show')) {
                            const cancelModal = bootstrap.Modal.getInstance(cancelModalEl);
                            if (cancelModal) cancelModal.hide();
                        }

                        if (addPoModalEl && addPoModalEl.classList.contains('show')) {
                            const addPoModal = bootstrap.Modal.getInstance(addPoModalEl);
                            if (addPoModal) addPoModal.hide();
                        }
                    }
                });

                // Utility function to debounce rapid function calls
                function debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }

                // Add Bootstrap tooltips on page load
                document.addEventListener('DOMContentLoaded', function() {
                    // Add Bootstrap tooltips
                    $('[title]').tooltip();
                });
            </script>
            </body>

            </html>