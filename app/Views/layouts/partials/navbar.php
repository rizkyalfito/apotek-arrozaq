<!-- app/Views/layouts/partials/navbar.php -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
    <!-- Notifications Dropdown Menu -->
    <?php
    $dataStokObatModel = new \App\Models\DataStokObatModel();
    
    // Ambil semua notifikasi (stok minimum + obat kadaluarsa)
    $allNotifications = $dataStokObatModel->getAllNotifications();
    $dataNotificationStock = $allNotifications['minimum_stock'];
    $dataNotificationExpiry = $allNotifications['expiring_medicine'];
    $totalNotifications = $allNotifications['total_count'];
    ?>
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            <span class="badge badge-danger navbar-badge"><?= $totalNotifications ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-item dropdown-header"><?= $totalNotifications ?> Notifikasi</span>
            
            <?php if ($totalNotifications < 1) : ?>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('obat') ?>" class="dropdown-item">
                    <i class="fas fa-check-circle mr-2 text-success"></i> Belum ada notifikasi
                </a>
            <?php else : ?>
                
                <!-- Notifikasi Obat Kadaluarsa -->
                <?php if (count($dataNotificationExpiry) > 0) : ?>
                    <div class="dropdown-divider"></div>
                    <span class="dropdown-item dropdown-header text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Obat Mendekati Kadaluarsa
                    </span>
                    <?php foreach ($dataNotificationExpiry as $expiry) : ?>
                        <a href="<?= base_url('obat') ?>" class="dropdown-item">
                            <?php if ($expiry['level_urgency'] == 'critical') : ?>
                                <i class="fas fa-times-circle mr-2 text-danger"></i>
                            <?php elseif ($expiry['level_urgency'] == 'high') : ?>
                                <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                            <?php else : ?>
                                <i class="fas fa-clock mr-2 text-info"></i>
                            <?php endif; ?>
                            
                            <div class="media">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        <?= $expiry['nama_obat'] ?>
                                        <span class="float-right text-sm 
                                            <?= $expiry['level_urgency'] == 'critical' ? 'text-danger' : 
                                                ($expiry['level_urgency'] == 'high' ? 'text-warning' : 'text-info') ?>">
                                            <?= $expiry['hari_tersisa'] == 0 ? 'Hari ini' : $expiry['hari_tersisa'] . ' hari' ?>
                                        </span>
                                    </h3>
                                    <p class="text-sm"><?= $expiry['status_kadaluarsa'] ?></p>
                                    <p class="text-sm text-muted">
                                        <i class="far fa-calendar-alt mr-1"></i> 
                                        Kadaluarsa: <?= date('d/m/Y', strtotime($expiry['tanggal_kadaluwarsa'])) ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Notifikasi Stok Minimum -->
                <?php if (count($dataNotificationStock) > 0) : ?>
                    <span class="dropdown-item dropdown-header text-warning">
                        <i class="fas fa-box"></i> Stok Minimum
                    </span>
                    <?php foreach ($dataNotificationStock as $stock) : ?>
                        <a href="<?= base_url('obat') ?>" class="dropdown-item">
                            <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                            <div class="media">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        <?= $stock['nama_obat'] ?>
                                        <span class="float-right text-sm text-warning">
                                            Stok: <?= $stock['jumlah_stok'] ?>
                                        </span>
                                    </h3>
                                    <p class="text-sm">Stok obat sisa sedikit</p>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php endif; ?>
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