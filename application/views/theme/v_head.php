<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Asta WMS - <?php echo $title; ?></title>
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="<?php echo base_url(); ?>css/styles.css" rel="stylesheet" />
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.4/css/responsive.dataTables.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Font Libre Barcode -->
    <link href='https://fonts.googleapis.com/css?family=Libre Barcode 39' rel='stylesheet'>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url('assets/image/favicon.ico'); ?>">
    <!-- Barcode  -->
    <script src="https://unpkg.com/bwip-js/dist/bwip-js-min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar-->
        <div class="border-end bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom bg-light text-center"><img src="<?php echo base_url('assets/image/logo A asta biru.png') ?>" alt="Asta Logo" width="20px">WMS</div>
            <?php
            $current = $this->uri->segment(1); // Get the first segment of URI
            ?>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= ($current == 'dashboard') ? 'active' : ''; ?>" href="<?php echo base_url('dashboard/'); ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= ($current == 'product') ? 'active' : ''; ?>" href="<?php echo base_url('product/'); ?>"><i class="fa-solid fa-tags"></i> Database Product</a>
                <!-- Transaksi Dropdown -->
                <div class="list-group-item p-0">
                    <a class="list-group-item list-group-item-action list-group-item-light p-3 d-flex justify-content-between align-items-center <?= in_array($current, ['verification', 'barangmasuk', 'outstock']) ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#transaksiSubmenu" role="button" aria-expanded="<?= in_array($current, ['verification', 'barangmasuk', 'outstock']) ? 'true' : 'false'; ?>" aria-controls="transaksiSubmenu"><i class="fa-solid fa-right-left"></i> Transaksi
                        <?php
                        $this->load->helper('transaction');
                        if (number_pending_verification() > 0) {
                            ?>
                        <span class="badge rounded-pill text-bg-danger">Butuh Verifikasi</span>
                        <?php } ?>
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?= in_array($current, ['verification', 'barangmasuk', 'outstock']) ? 'show' : ''; ?>" id="transaksiSubmenu">
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'verification') ? 'active' : ''; ?>" href="<?= base_url('verification'); ?>">
                            Verifikasi Transaksi
                            <?php
                            $this->load->helper('transaction');
                            $pending = number_pending_verification();
                            if ($pending > 0) {
                                echo '<span class="badge text-bg-danger">' . $pending . '</span>';
                            }
                            ?>
                        </a>
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'barangmasuk') ? 'active' : ''; ?>" href="<?= base_url('barangmasuk'); ?>">Barang Masuk</a>
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'outstock') ? 'active' : ''; ?>" href="<?= base_url('outstock'); ?>">Barang Keluar</a>
                    </div>
                </div>
                <div class="list-group-item p-0">
                    <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= in_array($current, ['delivery_note']) ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#suratjalanSubmenu" role="button" aria-expanded="<?= in_array($current, ['customer', 'delivery_note']) ? 'true' : 'false'; ?>" aria-controls="suratjalanSubmenu">
                        <div class="d-flex justify-content-between align-items-start w-100">
                            <div><i class="fa-solid fa-truck"></i> Surat Jalan</div>

                            <div class="d-flex flex-column align-items-end text-end">
                                <?php
                                $this->load->helper('transaction');
                                $verifikasi = number_pending_verification_delivery_note() + number_pending_verification_delivery_manual();
                                $validasi = number_pending_validasi_delivery_note() + number_pending_validasi_delivery_manual();
                                $final = number_pending_final_delivery_note() + number_pending_final_delivery_manual();

                                if ($verifikasi > 0) {
                                    echo '<div><span class="badge rounded-pill text-bg-primary">Butuh Verifikasi</span></div>';
                                }

                                if ($validasi > 0) {
                                    echo '<div><span class="badge rounded-pill text-bg-info">Butuh Validasi</span></div>';
                                }

                                if ($final > 0) {
                                    echo '<div><span class="badge rounded-pill text-bg-success">Butuh Final</span></div>';
                                }
                                ?>
                                <i class="fas fa-chevron-down small mt-1"></i>
                            </div>
                        </div>
                    </a>
                    <div class="collapse <?= in_array($current, ['delivery_note', 'delivery_manual']) ? 'show' : ''; ?>" id="suratjalanSubmenu">
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'delivery_note') ? 'active' : ''; ?>" href="<?= base_url('delivery_note'); ?>">
                            Realisasi Pengiriman
                            <?php
                            $this->load->helper('transaction');
                            $pending_verification = number_pending_verification_delivery_note();
                            if ($pending_verification > 0) : ?>
                            <span class="badge rounded-pill text-bg-primary"><?= $pending_verification; ?></span>
                            <?php endif; ?>
                            <?php
                            $this->load->helper('transaction');
                            $pending_validasi = number_pending_validasi_delivery_note();
                            if ($pending_validasi > 0) : ?>
                            <span class="badge rounded-pill text-bg-info"><?= $pending_validasi; ?></span>
                            <?php endif; ?>
                            <?php
                            $this->load->helper('transaction');
                            $pending_final = number_pending_final_delivery_note();
                            if ($pending_final > 0) : ?>
                            <span class="badge rounded-pill text-bg-success"><?= $pending_final; ?></span>
                            <?php endif; ?>
                        </a>
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'delivery_manual') ? 'active' : ''; ?>" href="<?= base_url('delivery_manual'); ?>">
                            Realisasi Pengiriman Manual
                            <?php
                            $this->load->helper('transaction');
                            $manual_pending_verification = number_pending_verification_delivery_manual();
                            if ($manual_pending_verification > 0) : ?>
                            <span class="badge rounded-pill text-bg-primary"><?= $manual_pending_verification; ?></span>
                            <?php endif; ?>
                            <?php
                            $manual_pending_validasi = number_pending_validasi_delivery_manual();
                            if ($manual_pending_validasi > 0) : ?>
                            <span class="badge rounded-pill text-bg-info"><?= $manual_pending_validasi; ?></span>
                            <?php endif; ?>
                            <?php
                            $manual_pending_final = number_pending_final_delivery_manual();
                            if ($manual_pending_final > 0) : ?>
                            <span class="badge rounded-pill text-bg-success"><?= $manual_pending_final; ?></span>
                            <?php endif; ?>
                        </a>
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'delivery_file') ? 'active' : ''; ?>" href="<?= base_url('delivery_file'); ?>">
                            File Surat Jalan Supplier Barang Masuk
                        </a>
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'delivery_mutasi') ? 'active' : ''; ?>" href="<?= base_url('delivery_mutasi'); ?>">
                            Mutasi
                            <?php
                            $this->load->helper('transaction');
                            $manual_pending_verification = number_pending_verification_delivery_manual();
                            if ($manual_pending_verification > 0) : ?>
                            <span class="badge rounded-pill text-bg-primary"><?= $manual_pending_verification; ?></span>
                            <?php endif; ?>
                            <?php
                            $manual_pending_validasi = number_pending_validasi_delivery_manual();
                            if ($manual_pending_validasi > 0) : ?>
                            <span class="badge rounded-pill text-bg-info"><?= $manual_pending_validasi; ?></span>
                            <?php endif; ?>
                            <?php
                            $manual_pending_final = number_pending_final_delivery_manual();
                            if ($manual_pending_final > 0) : ?>
                            <span class="badge rounded-pill text-bg-success"><?= $manual_pending_final; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
                <?php if ($this->session->userdata('idrole') == 1) { ?>
                <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= in_array($current, ['qty', 'finish']) ? 'active' : ''; ?>" href="<?php echo base_url('qty/'); ?>"><i class="fa-solid fa-chart-simple"></i> Analisa PO</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= ($current == 'user') ? 'active' : ''; ?>" href="<?php echo base_url('user/'); ?>"><i class="fa-solid fa-users"></i> Pengguna</a>
                <?php } ?>
            </div>
        </div>
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Start Modal Change Username -->
            <div class="modal fade" id="changeUsernameModal" tabindex="-1" aria-labelledby="changeUsernameModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?= base_url('user/changeUsername'); ?>" method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changeUsernameModalLabel">Change Username</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="mb-3">
                                    <label class="form-label" for="inputUsernameLabel" name="inputUsernameLabel" id="inputUsernameLabel">Masukan Username</label>
                                    <input type="text" class="form-control" name="inputUsername" id="inputUsername" value="<?php echo $this->session->userdata('username'); ?>">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End -->
            <!-- Start Modal Change Password -->
            <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?= base_url('user/changePassword'); ?>" method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changePasswordModalLabel">Change Username</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="mb-3">
                                    <label class="form-label" for="inputPasswordLabel" name="inputPasswordLabel" id="inputPasswordLabel">Masukan Password</label>
                                    <div class="input-group">
                                        <!-- icon kiri -->
                                        <span class="input-group-text">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                                <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z" />
                                                <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                            </svg>
                                        </span>

                                        <!-- input password -->
                                        <input type="password" class="form-control" name="inputPassword" id="inputPassword">

                                        <!-- tombol eye -->
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End -->
            <!-- Start Modal Upload Foto -->
            <div class="modal fade" id="changeFotoModal" tabindex="-1" aria-labelledby="changeFotoModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?= base_url('user/change_foto'); ?>" method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changeFotoModalLabel">Upload Foto Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="<?php echo base_url('assets/image/user/' . $this->session->userdata('foto')); ?>" alt="Preview Foto" class="img-thumbnail mb-3" width="120px" id="previewFoto">
                                <input type="file" class="form-control" name="foto" id="fotoInput" accept="image/*" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom position-relative">
                <div class="container-fluid d-flex align-items-center justify-content-between">
                    <!-- Tombol Menu (kiri) -->
                    <button class="btn btn-primary d-inline d-xl-none" id="sidebarToggle">Menu</button>

                    <!-- Marquee untuk mobile: inline, tampil hanya di mobile -->
                    <?php
                    $this->load->helper('transaction');
                    $pending_all = total_pending_verification_all();
                    if ($pending_all > 0) {
                        ?>
                    <marquee class="d-inline d-lg-none ms-2" style="font-weight: bold; color: white;
                                    background-color: #dc3545;
                                    border-radius: 10px;">

                        <?php
                            $this->load->helper('transaction');
                            $pending = number_pending_verification();
                            if ($pending > 0) {
                                echo "Ada " . $pending . " Pending Verifikasi Transaksi";
                            }
                            ?> <?php
                                    $this->load->helper('transaction');
                                    $pending = total_pending_delivery();
                                    if ($pending > 0) {
                                        echo "| ada " . $pending . " Dikirim Surat Jalan";
                                    }
                                    ?>
                    </marquee>
                    <?php } ?>

                    <!-- Marquee untuk desktop: posisi absolute di tengah, hanya tampil di desktop -->
                    <marquee class="d-none d-lg-block position-absolute" style="left: 50%; transform: translateX(-50%); width: 50%; font-weight: bold; color: white;
                                    background-color: #dc3545;
                                    border-radius: 10px;">
                        <?php
                        $this->load->helper('transaction');
                        $pending = number_pending_verification();
                        if ($pending > 0) {
                            echo "Ada " . $pending . " Pending Verifikasi Transaksi";
                        }
                        ?> <?php
                            $this->load->helper('transaction');
                            $pending = total_pending_delivery();
                            if ($pending > 0) {
                                echo "| Ada " . $pending . " Dikirim Surat Jalan";
                            }
                            ?>
                    </marquee>

                    <!-- Profile (kanan) -->
                    <div class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <ul class="navbar-nav d-flex align-items-center">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="<?php echo base_url('assets/image/user/' . $this->session->userdata('foto')); ?>" alt="" width="21px" height="21px" style="border-radius: 50%; object-fit: cover; margin-right: 0.5rem;">
                                    <?php echo $this->session->userdata('username'); ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changeUsernameModal">Change Username</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changeFotoModal">Change Foto</a>
                                    <a class="dropdown-item" href="<?php echo base_url('auth/logout'); ?>">Log Out</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <script>
                document.getElementById("fotoInput").addEventListener("change", function(event) {
                    const [file] = event.target.files;
                    if (file) {
                        document.getElementById("previewFoto").src = URL.createObjectURL(file);
                    }
                });

                const togglePassword = document.querySelector('#togglePassword');
                const passwordField = document.querySelector('#inputPassword');
                const icon = togglePassword.querySelector('i');

                togglePassword.addEventListener('click', function() {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);

                    // ganti icon
                    if (type === 'password') {
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    } else {
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    }
                });
            </script>