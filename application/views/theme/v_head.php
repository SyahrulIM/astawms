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
                <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= ($current == 'dashboard') ? 'active' : ''; ?>" href="<?php echo base_url('dashboard/'); ?>">Dashboard</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= ($current == 'product') ? 'active' : ''; ?>" href="<?php echo base_url('product/'); ?>">Database Product</a>
                <!-- <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= ($current == 'customer') ? 'active' : ''; ?>" href="<?php echo base_url('customer/'); ?>">Database Customer</a> -->
                <!-- Transaksi Dropdown -->
                <div class="list-group-item p-0">
                    <a class="list-group-item list-group-item-action list-group-item-light p-3 d-flex justify-content-between align-items-center <?= in_array($current, ['verification', 'barangmasuk', 'outstock']) ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#transaksiSubmenu" role="button" aria-expanded="<?= in_array($current, ['verification', 'barangmasuk', 'outstock']) ? 'true' : 'false'; ?>" aria-controls="transaksiSubmenu">
                        Transaksi
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
                            <div>Surat Jalan</div>

                            <div class="d-flex flex-column align-items-end text-end">
                                <?php
                                $this->load->helper('transaction');
                                $verifikasi = number_pending_verification_delivery();
                                $validasi = number_pending_validasi_delivery();

                                if ($verifikasi > 0) {
                                    echo '<span class="badge rounded-pill text-bg-primary mb-1">Butuh Verifikasi</span>';
                                }

                                if ($validasi > 0) {
                                    echo '<span class="badge rounded-pill text-bg-info mb-1">Butuh Validasi</span>';
                                }
                                ?>
                                <i class="fas fa-chevron-down small"></i>
                            </div>
                        </div>
                    </a>
                    <div class="collapse <?= in_array($current, ['delivery_note']) ? 'show' : ''; ?>" id="suratjalanSubmenu">
                        <!-- <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'customer') ? 'active' : ''; ?>" href="<?= base_url('customer'); ?>">Database Pelanggan</a> -->
                        <a class="list-group-item list-group-item-action list-group-item-light ps-5 <?= ($current == 'delivery_note') ? 'active' : ''; ?>" href="<?= base_url('delivery_note'); ?>">
                            Realisasi Pengiriman
                            <?php
                            $this->load->helper('transaction');
                            $pending_verification = number_pending_verification_delivery();
                            if ($pending_verification > 0) : ?>
                                <span class="badge rounded-pill text-bg-primary"><?= $pending_verification; ?></span>
                            <?php endif; ?>
                            <?php
                            $this->load->helper('transaction');
                            $pending_validasi = number_pending_validasi_delivery();
                            if ($pending_validasi > 0) : ?>
                                <span class="badge rounded-pill text-bg-info"><?= $pending_validasi; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
                <?php if ($this->session->userdata('idrole') == 1) { ?>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3 <?= ($current == 'user') ? 'active' : ''; ?>" href="<?php echo base_url('user/'); ?>">Pengguna</a>
                <?php } ?>
            </div>
        </div>
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
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
                                    <a class="dropdown-item" href="<?php echo base_url('auth/logout'); ?>">Log Out</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>