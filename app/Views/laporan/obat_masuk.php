<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Laporan Obat Masuk</h3>
  </div>
  <div class="card-body">
    <form action="<?= base_url('laporan/obat-masuk/filter') ?>" method="post" class="mb-4">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="tanggal_mulai">Tanggal Mulai</label>
            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= date('Y-m-01') ?>" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label for="tanggal_akhir">Tanggal Akhir</label>
            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="<?= date('Y-m-d') ?>" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group" style="margin-top: 32px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?= base_url('laporan/obat-masuk/export-pdf') ?>" class="btn btn-danger" target="_blank">
              <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('laporan/obat-masuk/export-excel') ?>" class="btn btn-success">
              <i class="fas fa-file-excel"></i> Export Excel
            </a>
          </div>
        </div>
      </div>
    </form>
    
    <table id="tabelLaporanObatMasuk" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID Obat</th>
          <th>Nama Obat</th>
          <th>Jumlah</th>
          <th>Satuan</th>
          <th>Tanggal Masuk</th>
          <th>Tanggal Kadaluwarsa</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="6" class="text-center">Belum ada data</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  $(document).ready(function() {
    $('#tabelLaporanObatMasuk').DataTable();
  });
</script>
<?= $this->endSection() ?>