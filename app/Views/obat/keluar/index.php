<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Data Obat Keluar</h3>
    <div class="card-tools">
      <a href="<?= base_url('obat/keluar/scan') ?>" class="btn btn-primary">
        <i class="fas fa-qrcode"></i> Scan QR Code
      </a>
      <a href="<?= base_url('obat/keluar/tambah') ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Tambah Data
      </a>
    </div>
  </div>
  <div class="card-body">
    <table id="tabelObatKeluar" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Kode Transaksi</th>
          <th>ID Obat</th>
          <th>Nama Obat</th>
          <th>Jumlah</th>
          <th>Satuan</th>
          <th>Tanggal Penjualan</th>
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
    $('#tabelObatKeluar').DataTable();
  });
</script>
<?= $this->endSection() ?>