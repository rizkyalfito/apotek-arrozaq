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
            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                   value="<?= isset($tanggal_mulai) ? $tanggal_mulai : date('Y-m-01') ?>" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label for="tanggal_akhir">Tanggal Akhir</label>
            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" 
                   value="<?= isset($tanggal_akhir) ? $tanggal_akhir : date('Y-m-d') ?>" required>
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
        <?php if (isset($obatMasuk) && count($obatMasuk) > 0): ?>
          <?php foreach ($obatMasuk as $row): ?>
            <tr>
              <td><?= $row['id_obat'] ?></td>
              <td><?= $row['nama_obat'] ?></td>
              <td><?= $row['jumlah'] ?></td>
              <td><?= $row['satuan'] ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_masuk'])) ?></td>
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
    $('#tabelLaporanObatMasuk').DataTable({
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