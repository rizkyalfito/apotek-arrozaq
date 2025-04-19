<!-- app/Views/layouts/partials/navbar.php -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Notifications Dropdown Menu -->
      <?php
      $dataStokObatModel = new \App\Models\DataStokObatModel();

      $dataNotification = $dataStokObatModel->notificationMinimumStock();
      ?>
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge"><?= count($dataNotification) ?></span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">Notifikasi Stok</span>
        <div class="dropdown-divider"></div>
          <?php if (count($dataNotification) < 1) : ?>
              <a href="<?= base_url('obat') ?>" class="dropdown-item">
                  <i class="fas fa-exclamation-triangle mr-2"></i> Belum ada notifikasi
              </a>
          <?php else : foreach ($dataNotification as $notification) : ?>
              <a href="<?= base_url('obat') ?>" class="dropdown-item">
                  <i class="fas fa-exclamation-triangle mr-2"></i> Stok obat <?= $notification['nama_obat'] ?> sisa sedikit.
              </a>
          <?php endforeach; endif; ?>
        <div class="dropdown-divider"></div>
        <a href="<?= base_url('obat') ?>" class="dropdown-item dropdown-footer">Lihat Semua Stok</a>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
  </ul>
</nav>