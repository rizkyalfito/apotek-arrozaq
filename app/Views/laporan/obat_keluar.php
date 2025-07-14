<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Laporan Obat Keluar</h3>
      </div>
      <div class="card-body">
        <form action="<?= base_url('laporan/obat-keluar/filter') ?>" method="post" class="mb-4">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                       value="<?= isset($tanggal_mulai) ? $tanggal_mulai : date('Y-m-01') ?>" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="tanggal_akhir">Tanggal Akhir</label>
                <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" 
                       value="<?= isset($tanggal_akhir) ? $tanggal_akhir : date('Y-m-d') ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" style="margin-top: 32px;">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-filter"></i> Filter
                </button>
                <a href="<?= base_url('laporan/obat-keluar/filter-hari-ini') ?>" class="btn btn-info">
                  <i class="fas fa-calendar-day"></i> Hari Ini
                </a>
                <a href="<?= base_url('laporan/obat-keluar/export-pdf') ?><?= isset($tanggal_mulai) ? '?tanggal_mulai='.$tanggal_mulai.'&tanggal_akhir='.$tanggal_akhir : '' ?>" class="btn btn-danger" target="_blank">
                  <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                <a href="<?= base_url('laporan/obat-keluar/export-excel') ?><?= isset($tanggal_mulai) ? '?tanggal_mulai='.$tanggal_mulai.'&tanggal_akhir='.$tanggal_akhir : '' ?>" class="btn btn-success">
                  <i class="fas fa-file-excel"></i> Export Excel
                </a>
              </div>
            </div>
          </div>
        </form>

        <!-- Info periode yang sedang ditampilkan -->
        <?php if (isset($tanggal_mulai) && isset($tanggal_akhir)): ?>
          <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Periode:</strong> <?= date('d-m-Y', strtotime($tanggal_mulai)) ?> s/d <?= date('d-m-Y', strtotime($tanggal_akhir)) ?>
            <?php if ($tanggal_mulai == date('Y-m-d') && $tanggal_akhir == date('Y-m-d')): ?>
              <span class="badge badge-success ml-2">Hari Ini</span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        
        <div class="table-responsive">
          <table id="tabelLaporanObatKeluar" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Transaksi</th>
              <th>ID Obat</th>
              <th>Nama Obat</th>
              <th>Jumlah</th>
              <th>Satuan</th>
              <th>Tanggal Penjualan</th>
              <th>Harga Jual</th>
              <th>Harga Total</th>
              <th>Tanggal Kedaluwarsa</th>
            </tr>
          </thead>
          <tbody>
            <?php if (isset($obatKeluar) && count($obatKeluar) > 0) : ?>
              <?php 
              $no = 1;
              $totalKeseluruhan = 0;
              foreach ($obatKeluar as $row): 
                $hargaJual = $row['harga_jual'] ?? 0;
                $hargaTotal = $hargaJual * $row['jumlah'];
                $totalKeseluruhan += $hargaTotal;
              ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= $row['kode_transaksi'] ?></td>
                  <td><?= $row['id_obat'] ?></td>
                  <td><?= $row['nama_obat'] ?></td>
                  <td><?= $row['jumlah'] ?></td>
                  <td><?= $row['satuan'] ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_penjualan'])) ?></td>
                  <td>Rp <?= number_format($hargaJual, 0, ',', '.') ?></td>
                  <td>Rp <?= number_format($hargaTotal, 0, ',', '.') ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_kadaluwarsa'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="10" class="text-center">Belum ada data</td>
              </tr>
            <?php endif; ?>
          </tbody>
          <?php if (isset($obatKeluar) && count($obatKeluar) > 0) : ?>
            <tfoot>
              <tr class="table-info font-weight-bold">
                <td colspan="8" class="text-right"><strong>TOTAL KESELURUHAN:</strong></td>
                <td><strong>Rp <?= number_format($totalKeseluruhan, 0, ',', '.') ?></strong></td>
                <td></td>
              </tr>
            </tfoot>
          <?php endif; ?>
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
    $('#tabelLaporanObatKeluar').DataTable({
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