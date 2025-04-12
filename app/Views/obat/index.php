<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Data Stok Obat</h3>
    <div class="card-tools">
      <a href="<?= base_url('obat/tambah') ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Tambah Obat
      </a>
    </div>
  </div>
  <div class="card-body">
    <table id="tabelObat" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID Obat</th>
          <th>Nama Obat</th>
          <th>Jumlah Stok</th>
          <th>Satuan</th>
          <th>Tanggal Kadaluwarsa</th>
          <th>QR Code</th>
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
    $('#tabelObat').DataTable();
  });
</script>
<?= $this->endSection() ?>