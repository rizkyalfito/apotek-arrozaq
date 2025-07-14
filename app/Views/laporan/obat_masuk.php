<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-12">
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
                
                <?php 
                $tanggal_mulai_param = isset($tanggal_mulai) ? $tanggal_mulai : date('Y-m-01');
                $tanggal_akhir_param = isset($tanggal_akhir) ? $tanggal_akhir : date('Y-m-d');
                ?>
                
                <a href="<?= base_url('laporan/obat-masuk/export-pdf') ?>?tanggal_mulai=<?= $tanggal_mulai_param ?>&tanggal_akhir=<?= $tanggal_akhir_param ?>" 
                  class="btn btn-danger" target="_blank">
                  <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                
                <a href="<?= base_url('laporan/obat-masuk/export-excel') ?>?tanggal_mulai=<?= $tanggal_mulai_param ?>&tanggal_akhir=<?= $tanggal_akhir_param ?>" 
                  class="btn btn-success">
                  <i class="fas fa-file-excel"></i> Export Excel
                </a>
              </div>
            </div>
          </div>
        </form>
        
        <div class="table-responsive">
          <table id="tabelLaporanObatMasuk" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>ID Obat</th>
                <th>Nama Obat</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Kedaluwarsa</th>
              </tr>
            </thead>
            <tbody>
              <?php if (isset($obatMasuk) && count($obatMasuk) > 0) : ?>
                <?php $no = 1; foreach ($obatMasuk as $row) : ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_obat'] ?></td>
                    <td><?= $row['nama_obat'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td><?= $row['satuan'] ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal_masuk'])) ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal_kadaluwarsa'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="7" class="text-center">Belum ada data</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  $(document).ready(function() {
    $('#tabelLaporanObatMasuk').DataTable({
      language: {
        lengthMenu: "Tampilkan _MENU_ data per halaman",
        zeroRecords: "Tidak ditemukan data yang sesuai",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
        infoFiltered: "(disaring dari _MAX_ data keseluruhan)",
        search: "Cari:",
        paginate: {
          first: "Pertama",
          last: "Terakhir",
          next: "Selanjutnya",
          previous: "Sebelumnya"
        }
      }
    });
  });
</script>
<?= $this->endSection() ?>