<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title><?= $set->nama ?> Dashboard Management</title>
  <link rel="shortcut icon" type="image/png" href="<?= base_url("cdn/assets/img/" . $set->favicon) ?>"/>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  
  <!-- Fonts (Poppins) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- CDN Bootstrap 5 & FontAwesome 6 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Chartist CSS CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chartist-plugin-tooltip/0.0.11/chartist-plugin-tooltip.css">

  <!-- Chartist JS CDN (Wajib ditaruh sebelum script grafik dijalankan) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist-plugin-tooltip/0.0.11/chartist-plugin-tooltip.min.js"></script>
  
  <!-- SweetAlert2 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- CSS CORE READY BOOTSTRAP (Built-in Style) -->
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Poppins', sans-serif !important;
      background-color: #f9fbfd;
      margin: 0;
      padding: 0;
      color: #2a2b2d;
    }

    /* Layout Structure */
    .wrapper {
      min-height: 100vh;
      position: relative;
    }

    /* Main Header */
    .main-header {
      background: #ffffff;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1001;
      height: 62px;
      display: flex;
      align-items: center;
      border-bottom: 1px solid #eef2f6;
    }

    .logo-header {
      width: 250px;
      height: 62px;
      padding: 0 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-right: 1px solid #eef2f6;
      background: #fff;
    }

    .navbar-header {
      flex: 1;
      padding: 0 20px;
    }

    /* Sidebar Style */
    .sidebar {
      position: fixed;
      top: 62px;
      bottom: 0;
      left: 0;
      width: 250px;
      display: block;
      z-index: 1000;
      background: #ffffff;
      border-right: 1px solid #eef2f6;
      box-shadow: 2px 0 10px rgba(154, 161, 171, 0.05);
      overflow-y: auto;
    }

    .sidebar .nav {
      padding: 10px 0 30px 0;
      list-style: none;
      margin: 0;
      display: block;
    }

    .sidebar .nav-title {
      font-size: 11px;
      font-weight: 700;
      color: #8d9498;
      padding: 18px 25px 6px 25px;
      letter-spacing: 0.6px;
      text-transform: uppercase;
    }

    .sidebar .nav-item {
      display: block;
      width: 100%;
    }

    .sidebar .nav-item a {
      display: flex;
      align-items: center;
      padding: 10px 25px;
      color: #575962;
      text-decoration: none;
      font-size: 13.5px;
      font-weight: 400;
      transition: all 0.2s ease;
    }

    .sidebar .nav-item a:hover {
      color: #1572e8;
      background: #f8f9fa;
    }

    .sidebar .nav-item.active a {
      color: #1572e8;
      font-weight: 600;
      background: #f4f5f8;
      border-left: 4px solid #1572e8;
    }

    .sidebar .nav-item a i {
      width: 25px;
      font-size: 16px;
      margin-right: 10px;
      text-align: center;
    }

    .sidebar .nav-item a p {
      margin: 0;
      white-space: nowrap;
    }

    /* Main Content Area */
    .main-panel {
      position: relative;
      width: calc(100% - 250px);
      min-height: 100vh;
      float: right;
      padding-top: 80px;
      padding-left: 20px;
      padding-right: 20px;
    }

    /* Utility Helpers */
    .tabs .tabs-item.active, .tabs .tabs-item:hover {
      border-bottom: 3px solid black;
      color: black;
    }
    .select2-container {
      width: 100% !important;
    }

    /* Responsive Screen Mobile/Tablet */
    @media screen and (max-width: 991px) {
      .main-panel {
        width: 100% !important;
      }
      .sidebar {
        left: -250px;
        transition: all 0.3s ease;
      }
      .nav-open .sidebar {
        left: 0 !important;
      }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="main-header">
      <div class="logo-header d-flex align-items-center justify-content-between px-3" style="width: 100%;">

        <!-- Bagian Kiri: Hamburger + Logo -->
        <div class="d-flex align-items-center gap-3 flex-shrink-0">
          <button class="navbar-toggler sidenav-toggler p-0 border-0 bg-transparent d-lg-none" type="button">
            <i class="fa-solid fa-bars fs-4 text-dark"></i>
          </button>
          
          <a href="<?= site_url() ?>" class="logo flex-shrink-0">
            <img src="<?= base_url("cdn/assets/img/" . $set->logo) ?>" style="height: 52px; max-width: 220px; object-fit: contain;" alt="Logo" />
          </a>
        </div>

        <!-- Bagian Kanan: Ikon Globe + Profil (Dibuat lebih lega dengan gap-3) -->
        <ul class="navbar-nav topbar-nav d-flex flex-row align-items-center gap-3 m-0 p-0 flex-shrink-0">
          <li class="nav-item">
            <a class="nav-link text-secondary p-0" href="<?= $mainsite ?>" target="_blank" title="View Site">
              <i class="fas fa-globe-asia fs-5"></i>
            </a>
          </li>
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle profile-pic p-0 d-flex align-items-center" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
              <!-- Ukuran profil dikembalikan ke 36px agar nyaman di-klik -->
              <img src="<?= base_url('cdn/assets/img/user.png') ?>" alt="user-img" width="36" height="36" class="rounded-circle border">
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
              <li>
                <a class="dropdown-item" href="javascript:void(0)" onclick="bootstrap.Modal.getOrCreateInstance('#modalgantipass').show();">
                  <i class="fas fa-unlock text-warning me-2"></i> Ganti Password
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="logout()">
                  <i class="fas fa-power-off text-danger me-2"></i> Logout
                </a>
              </li>
            </ul>
          </li>
        </ul>

      </div>
    </div>

    <div class="sidebar" id="sidebar">
      <div class="sidebar-wrapper">
        <ul class="nav">
          <li class="nav-item <?= (isset($menu) && $menu == 1) ? 'active' : '' ?>">
            <a href="<?= site_url('admin') ?>">
              <i class="fas fa-tachometer-alt text-primary"></i>
              <p>Dashboard</p>
            </a>
          </li>
          
          <li class="nav-title">DATA PESANAN</li>
          <li class="nav-item <?= (isset($menu) && $menu == 2) ? 'active' : '' ?>">
            <a href="<?= site_url('admin/pesanan') ?>" class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <i class="fas fa-dolly-flatbed text-success"></i>
                <p>Pesanan</p>
              </div>
              <?php if (isset($jmlPesanan) && $jmlPesanan > 0) { ?>
                <span class="badge bg-danger rounded-pill"><?= $jmlPesanan ?></span>
              <?php } ?>
            </a>
          </li>
          
          <li class="nav-title">MARKETING</li>
          <li class="nav-item <?= (isset($menu) && $menu == 5) ? 'active' : '' ?>">
            <a href="<?= site_url('admin/slider') ?>">
              <i class="fas fa-images text-info"></i>
              <p>Home Slider</p>
            </a>
          </li>

          <li class="nav-title">LAPORAN</li>
          <li class="nav-item <?= (isset($menu) && $menu == 14) ? 'active' : '' ?>">
            <a href="<?= site_url('admin/laporantransaksi') ?>">
              <i class="fas fa-chart-area text-primary"></i>
              <p>Transaksi</p>
            </a>
          </li>
          <li class="nav-item <?= (isset($menu) && $menu == 15) ? 'active' : '' ?>">
            <a href="<?= site_url('admin/laporanuser') ?>">
              <i class="fas fa-user-clock text-primary"></i>
              <p>Transaksi User</p>
            </a>
          </li>
          <li class="nav-item <?= (isset($menu) && $menu == 19) ? 'active' : '' ?>">
            <a href="<?= site_url('admin/laporanproduk') ?>">
              <i class="fas fa-gifts text-primary"></i>
              <p>Penjualan Produk</p>
            </a>
          </li>

          <li class="nav-title">DATA PRODUK</li>
          <li class="nav-item <?= (isset($menu) && $menu == 6) ? 'active' : '' ?>">
            <a href="<?= site_url('admin/produk') ?>">
              <i class="fas fa-boxes text-success"></i>
              <p>Daftar Produk</p>
            </a>
          </li>
          <li class="nav-item <?= (isset($menu) && $menu == 7) ? 'active' : '' ?>">
            <a href="<?= site_url('admin/kategori') ?>">
              <i class="fas fa-clipboard-list text-primary"></i>
              <p>Kategori Produk</p>
            </a>
          </li>

          <li class="nav-title">DATA USER</li>
          <li class="nav-item <?= (isset($menu) && $menu == 10) ? 'active' : '' ?>">
            <a href="<?= site_url('admin/usermanager') ?>">
              <i class="fas fa-users text-info"></i>
              <p>User</p>
            </a>
          </li>
          
          <li class="nav-title">PENGATURAN</li>
          <li class="nav-item <?= (isset($menu) && $menu == 24) ? 'active' : '' ?>">
            <a href="<?= site_url('gudang') ?>">
              <i class="fas fa-warehouse text-primary"></i>
              <p>Lokasi Gudang</p>
            </a>
          </li>
          <li class="nav-item <?= (isset($menu) && $menu == 20) ? 'active' : '' ?>">
            <a href="<?= site_url('admin/paketkurir') ?>">
              <i class="fas fa-shipping-fast text-success"></i>
              <p>Custom Kurir</p>
            </a>
          </li>
          
          <?php 
          $session = session();
          if ($session->get('level') == 2) { 
          ?>
            <li class="nav-item <?= (isset($menu) && $menu == 12) ? 'active' : '' ?>">
              <a href="<?= site_url('admin/pengaturan') ?>">
                <i class="fas fa-cogs text-primary"></i>
                <p>Pengaturan</p>
              </a>
            </li>
          <?php } ?>

          <li class="nav-item">
            <a href="javascript:void(0)" onclick="logout()">
              <i class="fas fa-power-off text-danger"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="main-panel">
      <div class="content">
        <div class="container-fluid">

  <!-- JS CDN Bootstrap 5 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Toggle Sidebar JS (Mobile) -->
  <script>
    $(document).ready(function() {
      $('.sidenav-toggler').on('click', function() {
        $('.wrapper').toggleClass('nav-open');
      });
    });
  </script>