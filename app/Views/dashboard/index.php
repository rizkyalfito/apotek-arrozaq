<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-info">
      <div class="inner">
        <h3>0</h3>
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
        <h3>0</h3>
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
        <h3>0</h3>
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
        <h3>0</h3>
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

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Obat Terbaru Masuk</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID Obat</th>
              <th>Nama Obat</th>
              <th>Jumlah</th>
              <th>Tanggal Masuk</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="text-center">Belum ada data</td>
            </tr>
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
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Kode Transaksi</th>
              <th>Nama Obat</th>
              <th>Jumlah</th>
              <th>Tanggal Keluar</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="text-center">Belum ada data</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>