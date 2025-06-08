<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Laporan Stok Obat</h3>
  </div>
  <div class="card-body">
    <form action="<?= base_url('laporan/stok-obat/filter') ?>" method="post" class="mb-4">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label for="cari">Cari Obat</label>
            <input type="text" class="form-control" id="cari" name="cari" 
                   placeholder="Cari berdasarkan nama obat atau ID obat"
                   value="<?= isset($cari) ? $cari : '' ?>">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group" style="margin-top: 32px;">
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="<?= base_url('laporan/stok-obat/export-pdf') ?><?= isset($cari) ? '?cari='.$cari : '' ?>" class="btn btn-danger" target="_blank">
              <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('laporan/stok-obat/export-excel') ?><?= isset($cari) ? '?cari='.$cari : '' ?>" class="btn btn-success">
              <i class="fas fa-file-excel"></i> Export Excel
            </a>
          </div>
        </div>
      </div>
    </form>
    
    <table id="tabelLaporanStokObat" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID Obat</th>
          <th>Nama Obat</th>
          <th>Jumlah Stok</th>
          <th>Satuan</th>
          <th>Tanggal Masuk</th>
          <th>Tanggal Kadaluwarsa</th>
        </tr>
      </thead>
      <tbody>
        <?php if (isset($stokObat) && count($stokObat) > 0): ?>
          <?php foreach ($stokObat as $row): ?>
            <tr>
              <td><?= $row['id_obat'] ?></td>
              <td><?= $row['nama_obat'] ?></td>
              <td><?= $row['jumlah_stok'] ?></td>
              <td><?= $row['satuan'] ?></td>
              <td><?= $row['tanggal_masuk'] ? date('d-m-Y', strtotime($row['tanggal_masuk'])) : '-' ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_kadaluwarsa'])) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">Belum ada data</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  $(document).ready(function() {
    $('#tabelLaporanStokObat').DataTable({
      "responsive": true,
      "lengthChange": true,
      "autoWidth": false,
      "language": {
        "search": "Cari:",
        "lengthMenu": "Tampilkan _MENU_ data per halaman",
        "zeroRecords": "Data tidak ditemukan",
        "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
        "infoEmpty": "Tidak ada data yang tersedia",
        "infoFiltered": "(filter dari _MAX_ total data)",
        "paginate": {
          "first": "Pertama",
          "last": "Terakhir",
          "next": "Selanjutnya",
          "previous": "Sebelumnya"
        }
      }
    });
  });
</script>
<?= $this->endSection() ?>