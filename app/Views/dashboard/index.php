<?= $this->extend('layouts/admin') ?>

<?= $this->section('styles') ?>
    <style>
        .chart-container {
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
<?= $this->endsection() ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-info">
      <div class="inner">
        <h3><?= $totalObat ?></h3>
        <p>Total Obat</p>
      </div>
      <div class="icon">
        <i class="fas fa-pills"></i>
      </div>
      <a href="<?= base_url('obat') ?>" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-success">
      <div class="inner">
        <h3><?= $obatMasukBulanIni ?></h3>
        <p>Obat Masuk Bulan Ini</p>
      </div>
      <div class="icon">
        <i class="fas fa-arrow-circle-down"></i>
      </div>
      <a href="<?= base_url('obat/masuk') ?>" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-warning">
      <div class="inner">
        <h3><?= $obatKeluarBulanIni ?></h3>
        <p>Obat Keluar Bulan Ini</p>
      </div>
      <div class="icon">
        <i class="fas fa-arrow-circle-up"></i>
      </div>
      <a href="<?= base_url('obat/keluar') ?>" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-danger">
      <div class="inner">
        <h3><?= $obatHampirHabis ?></h3>
        <p>Obat Hampir Habis</p>
      </div>
      <div class="icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <a href="#" class="small-box-footer">Lihat detail <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
</div>

<div class="my-4">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center fw-semibold">
                        Chart Jumlah Obat Masuk
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartObatMasuk"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center fw-semibold">
                        Chart Jumlah Obat Keluar
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartObatKeluar"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Obat Terbaru Masuk</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered" id="tableObatMasuk">
          <thead>
            <tr>
              <th>ID Obat</th>
              <th>Nama Obat</th>
              <th>Jumlah</th>
              <th>Tanggal Masuk</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($obatTerbaruMasuk as $item) : ?>
            <tr>
              <td><?= $item['id_obat'] ?></td>
              <td><?= $item['nama_obat'] ?></td>
              <td><?= $item['jumlah'] ?></td>
              <td><?= date('d/m/Y', strtotime($item['tanggal_masuk'])) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Obat Terbaru Keluar</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered" id="tableObatKeluar">
          <thead>
            <tr>
              <th>Kode Transaksi</th>
              <th>Nama Obat</th>
              <th>Jumlah</th>
              <th>Tanggal Keluar</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($obatTerbaruKeluar as $item) : ?>
            <tr>
              <td><?= $item['kode_transaksi'] ?></td>
              <td><?= $item['nama_obat'] ?></td>
              <td><?= $item['jumlah'] ?></td>
              <td><?= date('d/m/Y', strtotime($item['tanggal_penjualan'])) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#tableObatMasuk').DataTable();
        $('#tableObatKeluar').DataTable();
    });
</script>
<script>

    // Data untuk chart
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    // Sample data - ganti dengan data yang sebenarnya
    const obatMasuk = <?= json_encode($this->data['totalObatMasuk']) ?>;
    const obatKeluar = <?= json_encode($this->data['totalObatKeluar']) ?>;

    // Membuat chart
    const ctxObatMasuk = document.getElementById('chartObatMasuk').getContext('2d');
    const ctxObatKeluar = document.getElementById('chartObatKeluar').getContext('2d');

    const chartObatMasuk = new Chart(ctxObatMasuk, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Data Bulanan <?= date('Y') ?>',
                data: obatMasuk,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            title: {
                display: true,
                text: 'Data Bulanan Januari - Desember',
                fontSize: 18
            },
            legend: {
                position: 'bottom'
            }
        }
    });

    const chartObatKeluar = new Chart(ctxObatKeluar, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Data Bulanan <?= date('Y') ?>',
                data: obatKeluar,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            title: {
                display: true,
                text: 'Data Bulanan Januari - Desember',
                fontSize: 18
            },
            legend: {
                position: 'bottom'
            }
        }
    });
</script>
<?= $this->endsection() ?>
