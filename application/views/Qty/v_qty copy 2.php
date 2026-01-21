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
                // Variabel global untuk produk tambahan
                let additionalPOProducts = [];
                let allPOProducts = [];
                let productSearchTimeout = null;

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
                });

                // Global function to show detail modal
                function showDetail(idanalisys_po) {
                    // Reset variabel
                    additionalPOProducts = [];
                    allPOProducts = [];

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
                            initializePONumberValidation();
                            setupFormValidation();
                            initializePOProductAddition(); // Inisialisasi fitur tambah produk
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

                // Fungsi untuk inisialisasi fitur tambah produk di PO
                function initializePOProductAddition() {
                    console.log('Initializing PO Product Addition...');

                    // Ambil data semua produk dari hidden input
                    const allProductsData = document.getElementById('all_products_data');
                    if (allProductsData && allProductsData.value) {
                        try {
                            allPOProducts = JSON.parse(allProductsData.value);
                            console.log('Total produk tersedia:', allPOProducts.length);
                        } catch (error) {
                            console.error('Error parsing products data:', error);
                        }
                    }

                    // Tombol tambah produk
                    const btnTambahProdukPO = document.getElementById('btnTambahProdukPO');
                    if (btnTambahProdukPO) {
                        // Remove existing event listeners
                        const newBtn = btnTambahProdukPO.cloneNode(true);
                        btnTambahProdukPO.parentNode.replaceChild(newBtn, btnTambahProdukPO);

                        // Add new event listener
                        document.getElementById('btnTambahProdukPO').addEventListener('click', function() {
                            console.log('Tambah produk clicked');
                            showProductSearchForm();
                        });
                    } else {
                        console.log('btnTambahProdukPO not found');
                    }

                    // Clear search produk
                    const clearProductSearch = document.getElementById('clearProductSearch');
                    if (clearProductSearch) {
                        clearProductSearch.addEventListener('click', function() {
                            document.getElementById('searchProductPO').value = '';
                            filterProductListPO();
                        });
                    }

                    // Batal cari produk
                    const btnBatalCariProdukPO = document.getElementById('btnBatalCariProdukPO');
                    if (btnBatalCariProdukPO) {
                        btnBatalCariProdukPO.addEventListener('click', function() {
                            hideProductSearchForm();
                        });
                    }

                    // Pencarian produk dengan debounce
                    const searchProductPO = document.getElementById('searchProductPO');
                    if (searchProductPO) {
                        searchProductPO.addEventListener('input', function() {
                            // Clear timeout sebelumnya
                            if (productSearchTimeout) {
                                clearTimeout(productSearchTimeout);
                            }

                            // Set timeout baru
                            productSearchTimeout = setTimeout(function() {
                                filterProductListPO();
                            }, 300);
                        });

                        // Enter untuk search
                        searchProductPO.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                filterProductListPO();
                            }
                        });
                    }

                    // Event delegation untuk tombol pilih produk
                    const modalContent = document.getElementById('detailContent');
                    if (modalContent) {
                        modalContent.addEventListener('click', function(e) {
                            // Tombol pilih produk
                            if (e.target.classList.contains('btn-pilih-produk-po')) {
                                selectProductPO(e.target);
                            } else if (e.target.closest('.btn-pilih-produk-po')) {
                                selectProductPO(e.target.closest('.btn-pilih-produk-po'));
                            }

                            // Tombol hapus produk tambahan
                            if (e.target.classList.contains('btn-hapus-produk-po')) {
                                removeAdditionalProductPO(e.target);
                            } else if (e.target.closest('.btn-hapus-produk-po')) {
                                removeAdditionalProductPO(e.target.closest('.btn-hapus-produk-po'));
                            }
                        });

                        // Event untuk input qty di produk tambahan
                        modalContent.addEventListener('input', function(e) {
                            if (e.target.classList.contains('qty-additional')) {
                                updateAdditionalProductQty(e.target);
                            }
                        });

                        // Event untuk select type_sgs di produk tambahan
                        modalContent.addEventListener('change', function(e) {
                            if (e.target.classList.contains('type-sgs-additional')) {
                                updateAdditionalProductTypeSgs(e.target);
                            }
                        });
                    }
                }

                // Tampilkan form pencarian produk
                function showProductSearchForm() {
                    const searchForm = document.getElementById('productSearchForm');
                    const searchResults = document.getElementById('productSearchResultsPO');
                    const btnTambah = document.getElementById('btnTambahProdukPO');

                    if (searchForm) searchForm.style.display = 'block';
                    if (searchResults) searchResults.style.display = 'block';
                    if (btnTambah) btnTambah.style.display = 'none';

                    filterProductListPO();

                    const searchInput = document.getElementById('searchProductPO');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }

                // Sembunyikan form pencarian produk
                function hideProductSearchForm() {
                    const searchForm = document.getElementById('productSearchForm');
                    const searchResults = document.getElementById('productSearchResultsPO');
                    const btnTambah = document.getElementById('btnTambahProdukPO');

                    if (searchForm) searchForm.style.display = 'none';
                    if (searchResults) searchResults.style.display = 'none';
                    if (btnTambah) btnTambah.style.display = 'block';

                    const searchInput = document.getElementById('searchProductPO');
                    if (searchInput) {
                        searchInput.value = '';
                    }
                }

                // Filter daftar produk berdasarkan pencarian
                function filterProductListPO() {
                    const searchInput = document.getElementById('searchProductPO');
                    const productListPO = document.getElementById('productListPO');

                    if (!searchInput || !productListPO) {
                        console.log('Search elements not found');
                        return;
                    }

                    const searchTerm = searchInput.value.toLowerCase().trim();
                    productListPO.innerHTML = '';

                    if (allPOProducts.length === 0) {
                        productListPO.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada produk tersedia</td></tr>';
                        return;
                    }

                    // Dapatkan ID produk yang sudah ada di tabel utama
                    const existingProductIds = new Set();
                    const mainTableRows = document.querySelectorAll('#detailTableBody tr[data-product-id]');
                    mainTableRows.forEach(tr => {
                        const productId = tr.getAttribute('data-product-id');
                        if (productId) {
                            existingProductIds.add(productId.toString());
                        }
                    });

                    // Dapatkan ID produk yang sudah ditambahkan
                    additionalPOProducts.forEach(product => {
                        if (product && product.id) {
                            existingProductIds.add(product.id.toString());
                        }
                    });

                    // Filter produk
                    const filteredProducts = allPOProducts.filter(product => {
                        // Skip jika produk tidak valid
                        if (!product || !product.id) return false;

                        // Skip jika produk sudah ada
                        if (existingProductIds.has(product.id.toString())) {
                            return false;
                        }

                        // Filter berdasarkan pencarian
                        if (searchTerm === '') {
                            return true;
                        }

                        const sku = (product.sku || '').toLowerCase();
                        const nama = (product.nama || '').toLowerCase();
                        const text = (product.text || '').toLowerCase();

                        return sku.includes(searchTerm) ||
                            nama.includes(searchTerm) ||
                            text.includes(searchTerm);
                    });

                    if (filteredProducts.length === 0) {
                        productListPO.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">' +
                            (searchTerm ? 'Tidak ada produk ditemukan untuk "' + searchTerm + '"' : 'Semua produk sudah ditambahkan') +
                            '</td></tr>';
                        return;
                    }

                    // Tampilkan produk (maksimal 20 hasil)
                    const displayProducts = filteredProducts.slice(0, 20);

                    displayProducts.forEach((product, index) => {
                        const row = document.createElement('tr');
                        row.className = 'product-item';
                        row.innerHTML = `
                <td><strong>${product.sku || ''}</strong></td>
                <td>${product.nama || ''}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-primary btn-pilih-produk-po" 
                            data-id="${product.id}" 
                            data-sku="${product.sku || ''}" 
                            data-nama="${product.nama || ''}">
                        <i class="fa-solid fa-plus me-1"></i> Pilih
                    </button>
                </td>
            `;
                        productListPO.appendChild(row);
                    });

                    // Tampilkan pesan jika hasil lebih dari 20
                    if (filteredProducts.length > 20) {
                        const infoRow = document.createElement('tr');
                        infoRow.innerHTML = `
                <td colspan="3" class="text-center text-info">
                    <small>Menampilkan 20 dari ${filteredProducts.length} hasil. Gunakan kata kunci lebih spesifik.</small>
                </td>
            `;
                        productListPO.appendChild(infoRow);
                    }
                }

                // Pilih produk untuk ditambahkan
                function selectProductPO(button) {
                    if (!button) return;

                    const productId = button.getAttribute('data-id');
                    const sku = button.getAttribute('data-sku');
                    const nama = button.getAttribute('data-nama');

                    if (!productId) {
                        console.error('Invalid product ID');
                        return;
                    }

                    // Cek apakah produk sudah ditambahkan
                    if (additionalPOProducts.some(p => p && p.id == productId)) {
                        alert('Produk ini sudah ditambahkan');
                        return;
                    }

                    // Ambil tipe PO untuk menentukan default qty_packing_list
                    const currentTypePO = document.getElementById('current_type_po');
                    const currentTypePOValue = currentTypePO ? currentTypePO.value : '';
                    const isLocalPO = currentTypePOValue === 'local';

                    // Tambahkan ke array
                    additionalPOProducts.push({
                        id: productId,
                        sku: sku,
                        nama: nama,
                        type_sgs: '',
                        qty_order: 1,
                        qty_packing_list: isLocalPO ? 1 : 0,
                        price: 0,
                        description: ''
                    });

                    // Update tabel produk tambahan
                    updateAdditionalProductsTable();

                    // Sembunyikan form pencarian
                    hideProductSearchForm();

                    // Tampilkan tabel produk tambahan jika ada produk
                    const tableContainer = document.getElementById('additionalProductsTableContainer');
                    if (tableContainer && additionalPOProducts.length > 0) {
                        tableContainer.style.display = 'block';
                    }

                    // Refresh daftar pencarian
                    setTimeout(filterProductListPO, 100);

                    // Tampilkan notifikasi
                    showNotification('success', 'Produk "' + sku + '" berhasil ditambahkan');
                }

                // Update tabel produk tambahan
                function updateAdditionalProductsTable() {
                    const tbody = document.getElementById('additionalProductsBody');
                    if (!tbody) {
                        console.log('additionalProductsBody not found');
                        return;
                    }

                    tbody.innerHTML = '';

                    if (additionalPOProducts.length === 0) {
                        const tableContainer = document.getElementById('additionalProductsTableContainer');
                        if (tableContainer) {
                            tableContainer.style.display = 'none';
                        }
                        return;
                    }

                    // Ambil tipe PO untuk menentukan tampilan
                    const currentTypePO = document.getElementById('current_type_po');
                    const currentTypePOValue = currentTypePO ? currentTypePO.value : '';
                    const isLocalPO = currentTypePOValue === 'local';

                    additionalPOProducts.forEach((product, index) => {
                        if (!product) return;

                        const row = document.createElement('tr');
                        row.setAttribute('data-index', index);

                        if (isLocalPO) {
                            // Untuk local PO, qty_packing_list sama dengan qty_order
                            row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${product.sku || ''}</td>
                    <td>${product.nama || ''}</td>
                    <td>
                        <select class="form-select form-select-sm type-sgs-additional" 
                                name="additional_products[${index}][type_sgs]"
                                data-index="${index}">
                            <option value="">Pilih SGS</option>
                            <option value="sgs" ${product.type_sgs === 'sgs' ? 'selected' : ''}>SGS</option>
                            <option value="non sgs" ${product.type_sgs === 'non sgs' ? 'selected' : ''}>Non SGS</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" 
                               class="form-control form-control-sm qty-additional" 
                               name="additional_products[${index}][qty_order]" 
                               value="${product.qty_order || 1}" 
                               min="1" 
                               data-index="${index}"
                               required>
                        <input type="hidden" name="additional_products[${index}][qty_packing_list]" value="${product.qty_order || 1}">
                        <input type="hidden" name="additional_products[${index}][idproduct]" value="${product.id}">
                        <input type="hidden" name="additional_products[${index}][price]" value="${product.price || 0}">
                        <small class="text-muted">Auto qty packing list: ${product.qty_order || 1}</small>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-hapus-produk-po" 
                                data-index="${index}"
                                title="Hapus produk">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                `;
                        } else {
                            // Untuk import PO, qty_packing_list terpisah
                            row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${product.sku || ''}</td>
                    <td>${product.nama || ''}</td>
                    <td>
                        <select class="form-select form-select-sm type-sgs-additional" 
                                name="additional_products[${index}][type_sgs]"
                                data-index="${index}">
                            <option value="">Pilih SGS</option>
                            <option value="sgs" ${product.type_sgs === 'sgs' ? 'selected' : ''}>SGS</option>
                            <option value="non sgs" ${product.type_sgs === 'non sgs' ? 'selected' : ''}>Non SGS</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" 
                               class="form-control form-control-sm qty-additional" 
                               name="additional_products[${index}][qty_order]" 
                               value="${product.qty_order || 1}" 
                               min="1" 
                               data-index="${index}"
                               required>
                        <input type="hidden" name="additional_products[${index}][qty_packing_list]" value="${product.qty_packing_list || 0}">
                        <input type="hidden" name="additional_products[${index}][idproduct]" value="${product.id}">
                        <input type="hidden" name="additional_products[${index}][price]" value="${product.price || 0}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-hapus-produk-po" 
                                data-index="${index}"
                                title="Hapus produk">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                `;
                        }
                        tbody.appendChild(row);
                    });
                }

                // Update qty produk tambahan di array
                function updateAdditionalProductQty(input) {
                    const index = parseInt(input.getAttribute('data-index'));
                    if (isNaN(index) || index < 0 || index >= additionalPOProducts.length) {
                        console.error('Invalid index:', index);
                        return;
                    }

                    const qty = parseInt(input.value) || 1;

                    if (qty < 1) {
                        input.value = 1;
                        showNotification('warning', 'Qty minimal 1');
                        return;
                    }

                    if (additionalPOProducts[index]) {
                        additionalPOProducts[index].qty_order = qty;

                        // Jika local PO, update juga qty_packing_list
                        const currentTypePO = document.getElementById('current_type_po');
                        const currentTypePOValue = currentTypePO ? currentTypePO.value : '';
                        if (currentTypePOValue === 'local') {
                            additionalPOProducts[index].qty_packing_list = qty;

                            // Update hidden input qty_packing_list
                            const parent = input.parentNode;
                            const hiddenInput = parent.querySelector('input[name*="qty_packing_list"]');
                            if (hiddenInput) {
                                hiddenInput.value = qty;
                            }

                            // Update text info
                            const infoText = parent.querySelector('small.text-muted');
                            if (infoText) {
                                infoText.textContent = 'Auto qty packing list: ' + qty;
                            }
                        }
                    }
                }

                // Update type_sgs produk tambahan di array
                function updateAdditionalProductTypeSgs(select) {
                    const index = parseInt(select.getAttribute('data-index'));
                    if (isNaN(index) || index < 0 || index >= additionalPOProducts.length) {
                        return;
                    }

                    const typeSgs = select.value;

                    if (additionalPOProducts[index]) {
                        additionalPOProducts[index].type_sgs = typeSgs;
                    }
                }

                // Hapus produk tambahan
                function removeAdditionalProductPO(button) {
                    if (!button) return;

                    const index = parseInt(button.getAttribute('data-index'));
                    if (isNaN(index) || index < 0 || index >= additionalPOProducts.length) {
                        console.error('Invalid index:', index);
                        return;
                    }

                    if (!confirm('Apakah Anda yakin ingin menghapus produk ini dari daftar tambahan?')) {
                        return;
                    }

                    // Hapus dari array
                    const removedProduct = additionalPOProducts[index];
                    additionalPOProducts.splice(index, 1);

                    // Update tabel
                    updateAdditionalProductsTable();

                    // Refresh daftar pencarian
                    setTimeout(filterProductListPO, 100);

                    // Tampilkan notifikasi
                    if (removedProduct) {
                        showNotification('info', 'Produk "' + (removedProduct.sku || '') + '" telah dihapus');
                    }
                }

                // Tampilkan notifikasi
                function showNotification(type, message) {
                    // Cek jika sudah ada notifikasi
                    const existingNotification = document.getElementById('product-notification');
                    if (existingNotification) {
                        existingNotification.remove();
                    }

                    // Buat notifikasi
                    const notification = document.createElement('div');
                    notification.id = 'product-notification';
                    notification.className = 'alert alert-' + type + ' alert-dismissible fade show';
                    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';

                    notification.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';

                    document.body.appendChild(notification);

                    // Auto hide setelah 3 detik
                    setTimeout(() => {
                        if (notification.parentNode) {
                            const bsAlert = new bootstrap.Alert(notification);
                            bsAlert.close();
                        }
                    }, 3000);
                }

                // Function to initialize PO number validation
                function initializePONumberValidation() {
                    const poInput = document.getElementById('number_po');
                    if (!poInput) {
                        console.log('PO number input not found');
                        return;
                    }

                    // Get existing PO numbers from data attribute
                    const existingPOsData = poInput.getAttribute('data-existing-po');
                    if (!existingPOsData) {
                        console.log('No existing PO data found');
                        return;
                    }

                    try {
                        const existingPOs = JSON.parse(existingPOsData);
                        const currentPO = poInput.getAttribute('data-current-po') || '';

                        // Normalize PO numbers (trim and lowercase for comparison)
                        const normalizedExisting = existingPOs.map(po => {
                            return po ? po.toString().trim().toLowerCase() : '';
                        }).filter(po => po !== ''); // Remove empty strings

                        console.log('Existing POs loaded:', normalizedExisting);
                        console.log('Current PO:', currentPO);

                        function validatePONumber() {
                            const inputValue = poInput.value.trim();
                            const normalizedInput = inputValue.toLowerCase();

                            // Reset validation
                            poInput.classList.remove('is-valid', 'is-invalid');

                            if (inputValue === '') {
                                // Empty input - show normal state
                                poInput.classList.remove('is-valid', 'is-invalid');
                                return false;
                            }

                            // Check if it's the same as current PO (allow during edit)
                            if (currentPO && normalizedInput === currentPO.toLowerCase()) {
                                poInput.classList.add('is-valid');
                                poInput.classList.remove('is-invalid');
                                return true;
                            }

                            // Check if PO already exists
                            if (normalizedExisting.includes(normalizedInput)) {
                                poInput.classList.add('is-invalid');
                                poInput.classList.remove('is-valid');
                                return false;
                            }

                            // PO is available
                            poInput.classList.add('is-valid');
                            poInput.classList.remove('is-invalid');
                            return true;
                        }

                        // Event listeners
                        poInput.addEventListener('input', function() {
                            validatePONumber();
                        });

                        poInput.addEventListener('blur', function() {
                            validatePONumber();
                        });

                        poInput.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                validatePONumber();
                            }
                        });

                        // Initial validation
                        validatePONumber();

                    } catch (error) {
                        console.error('Error parsing PO data:', error);
                    }
                }

                // Function to setup form validation
                function setupFormValidation() {
                    const form = document.querySelector('#detailModal form');
                    if (!form) return;

                    form.addEventListener('submit', function(event) {
                        if (!validateFormBeforeSubmit(event)) {
                            event.preventDefault();
                            return false;
                        }
                        return true;
                    });
                }

                // Function to validate form before submission
                function validateFormBeforeSubmit(event) {
                    const poInput = document.getElementById('number_po');
                    const moneyCurrency = document.getElementById('money-currency');
                    const orderDate = document.getElementById('order_date');
                    const nameSupplier = document.getElementById('name_supplier');

                    // Validasi field required
                    const errors = [];

                    if (!moneyCurrency || !moneyCurrency.value) {
                        errors.push('Pilih Money Currency terlebih dahulu.');
                    }

                    if (!orderDate || !orderDate.value) {
                        errors.push('Isi Order Date terlebih dahulu.');
                    }

                    if (!nameSupplier || !nameSupplier.value.trim()) {
                        errors.push('Isi Supplier terlebih dahulu.');
                    }

                    // Validasi PO number
                    if (!poInput || !poInput.value.trim()) {
                        errors.push('Isi No Purchase Order terlebih dahulu.');
                    } else {
                        // Check duplicate PO
                        const existingPOsData = poInput.getAttribute('data-existing-po');
                        const currentPO = poInput.getAttribute('data-current-po') || '';

                        if (existingPOsData) {
                            try {
                                const existingPOs = JSON.parse(existingPOsData);
                                const inputValue = poInput.value.trim().toLowerCase();
                                const normalizedExisting = existingPOs.map(po => {
                                    return po ? po.toString().trim().toLowerCase() : '';
                                }).filter(po => po !== '');

                                // Skip validation if it's the same as current PO during edit
                                if (!(currentPO && inputValue === currentPO.toLowerCase())) {
                                    if (normalizedExisting.includes(inputValue)) {
                                        errors.push('Nomor PO sudah digunakan sebelumnya. Silakan gunakan nomor lain.');
                                    }
                                }
                            } catch (error) {
                                console.error('Error validating PO:', error);
                            }
                        }
                    }

                    // Show errors if any
                    if (errors.length > 0) {
                        event.preventDefault();
                        alert(errors.join('\n'));
                        return false;
                    }

                    // Validasi minimal satu produk memiliki qty > 0 (baik di tabel utama maupun tambahan)
                    const qtyInputs = document.querySelectorAll('.qty-input');
                    const additionalQtyInputs = document.querySelectorAll('.qty-additional');

                    let hasQty = false;

                    qtyInputs.forEach(input => {
                        const qtyValue = parseInt(input.value) || 0;
                        if (qtyValue > 0) {
                            hasQty = true;
                        }
                    });

                    additionalQtyInputs.forEach(input => {
                        const qtyValue = parseInt(input.value) || 0;
                        if (qtyValue > 0) {
                            hasQty = true;
                        }
                    });

                    if (!hasQty) {
                        const confirmSubmit = confirm('Tidak ada produk dengan Qty Order > 0. Apakah Anda yakin ingin melanjutkan?');
                        if (!confirmSubmit) {
                            event.preventDefault();
                            return false;
                        }
                    }

                    // Validasi tambahan: pastikan semua produk tambahan memiliki type_sgs jika diisi
                    const additionalTypeSgsSelects = document.querySelectorAll('.type-sgs-additional');
                    let hasInvalidTypeSgs = false;

                    additionalTypeSgsSelects.forEach(select => {
                        // Jika type_sgs diisi, pastikan valid
                        if (select.value && !['sgs', 'non sgs'].includes(select.value)) {
                            hasInvalidTypeSgs = true;
                            select.classList.add('is-invalid');
                        } else {
                            select.classList.remove('is-invalid');
                        }
                    });

                    if (hasInvalidTypeSgs) {
                        alert('Type SGS untuk produk tambahan harus "SGS" atau "Non SGS".');
                        event.preventDefault();
                        return false;
                    }

                    // Show loading indicator
                    const submitBtn = event.target.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                        submitBtn.disabled = true;
                    }

                    return true;
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

                // Event listener untuk modal hidden (cleanup)
                const detailModal = document.getElementById('detailModal');
                if (detailModal) {
                    detailModal.addEventListener('hidden.bs.modal', function() {
                        // Reset variabel
                        additionalPOProducts = [];
                        allPOProducts = [];

                        // Clear timeout
                        if (productSearchTimeout) {
                            clearTimeout(productSearchTimeout);
                            productSearchTimeout = null;
                        }

                        // Hapus notifikasi
                        const notification = document.getElementById('product-notification');
                        if (notification) {
                            notification.remove();
                        }
                    });
                }

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

                // Add Bootstrap tooltips on page load
                document.addEventListener('DOMContentLoaded', function() {
                    // Add Bootstrap tooltips
                    $('[title]').tooltip();
                });
            </script>
            </body>

            </html>