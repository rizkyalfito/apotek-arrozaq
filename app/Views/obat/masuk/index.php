<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Data Obat Masuk</h3>
    <div class="card-tools">
      <a href="<?= base_url('obat/masuk/scan') ?>" class="btn btn-primary">
        <i class="fas fa-qrcode"></i> Scan QR Code
      </a>
      <a href="<?= base_url('obat/masuk/tambah') ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Tambah Data
      </a>
    </div>
  </div>
  <div class="card-body">
    <table id="tabelObatMasuk" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID Obat</th>
          <th>Nama Obat</th>
          <th>Jumlah</th>
          <th>Satuan</th>
          <th>Tanggal Masuk</th>
          <th>Tanggal Kadaluwarsa</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="7" class="text-center">Belum ada data</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  $(document).ready(function() {
    // Inisialisasi DataTable
    $('#tabelObatMasuk').DataTable();
  });
</script>
<?= $this->endSection() ?>