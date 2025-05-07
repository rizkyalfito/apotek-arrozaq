<!-- app/Views/layouts/partials/sidebar.php -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="<?= base_url() ?>" class="brand-link">
    <img src="<?= base_url('assets/adminlte/dist/img/logo-apotek.jpg') ?>" alt="Ar-Rozaq Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-bold">APOTEK AR-ROZZAQ</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
      <i class="fas fa-user-circle img-circle elevation-2" style="font-size: 2.1rem; color: #c2c7d0;"></i>
      </div>
      <div class="info">
        <a href="#" class="d-block">Admin</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Dashboard -->
        <li class="nav-item">
          <a href="<?= base_url('dashboard') ?>" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        
        <!-- Data Stok Obat -->
        <li class="nav-item">
          <a href="<?= base_url('obat') ?>" class="nav-link">
            <i class="nav-icon fas fa-pills"></i>
            <p>Data Stok Obat</p>
          </a>
        </li>
        
        <!-- Obat Masuk -->
        <li class="nav-item">
          <a href="<?= base_url('obat/masuk') ?>" class="nav-link">
            <i class="nav-icon fas fa-arrow-circle-down"></i>
            <p>Obat Masuk</p>
          </a>
        </li>
        
        <!-- Obat Keluar -->
        <li class="nav-item">
          <a href="<?= base_url('obat/keluar') ?>" class="nav-link">
            <i class="nav-icon fas fa-arrow-circle-up"></i>
            <p>Obat Keluar</p>
          </a>
        </li>
        
        <!-- Laporan -->
        <?php
            $userLevel = session()->get('level');
            if  ($userLevel === 'owner') :
        ?>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>
                        Laporan
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?= base_url('laporan/obat-masuk') ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Laporan Obat Masuk</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('laporan/obat-keluar') ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Laporan Obat Keluar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('laporan/stok-obat') ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Laporan Stok Obat</p>
                        </a>
                    </li>
                </ul>
            </li>
       <?php endif; ?>
        
        <!-- Logout -->
        <li class="nav-item">
          <a href="<?= base_url('logout') ?>" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>