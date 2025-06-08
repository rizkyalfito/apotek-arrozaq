<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Data Obat Keluar</h3>
    <div class="card-tools">
      <a href="<?= base_url('obat/keluar/scan') ?>" class="btn btn-primary">
        <i class="fas fa-qrcode"></i> Scan Barcode
      </a>
      <a href="<?= base_url('obat/keluar/tambah') ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Tambah Data
      </a>
    </div>
  </div>
  <div class="card-body">
    <div class="col-12">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger small my-4" role="alert">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success small my-4" role="alert">
                <?= session()->getFlashdata('success'); ?>
            </div>
        <?php endif; ?>
    </div>
    <table id="tabelObatKeluar" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Kode Transaksi</th>
          <th>ID Obat</th>
          <th>Nama Obat</th>
          <th>Jumlah</th>
          <th>Satuan</th>
          <th>Harga Modal</th>
          <th>Harga Jual</th>
          <th>Total Harga</th>
          <th>Tanggal Penjualan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($obatKeluar)): ?>
          <tr>
            <td colspan="10" class="text-center">Belum ada data</td>
          </tr>
        <?php else: ?>
          <?php foreach ($obatKeluar as $data): ?>
          <tr>
            <td><?= $data['kode_transaksi'] ?></td>
            <td><?= $data['id_obat'] ?></td>
            <td><?= $data['nama_obat'] ?></td>
            <td><?= $data['jumlah'] ?></td>
            <td><?= $data['satuan'] ?></td>
            <td>Rp <?= number_format($data['harga_modal'] ?? 0, 0, ',', '.') ?></td>
            <td>Rp <?= number_format($data['harga_jual'] ?? 0, 0, ',', '.') ?></td>
            <td>Rp <?= number_format(($data['harga_jual'] ?? 0) * $data['jumlah'], 0, ',', '.') ?></td>
            <td><?= date('d/m/Y', strtotime($data['tanggal_penjualan'])) ?></td>
            <td>
              <a href="<?= base_url('obat/keluar/edit/' . $data['kode_transaksi']) ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Ubah
              </a>
              <a href="<?= base_url('obat/keluar/hapus/' . $data['kode_transaksi']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                <i class="fas fa-trash"></i> Hapus
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  $(document).ready(function() {
    $('#tabelObatKeluar').DataTable({
      "responsive": true,
      "lengthChange": true,
      "autoWidth": false,
      "language": {
        "search": "Cari:",
        "lengthMenu": "Tampilkan _MENU_ data per halaman",
        "zeroRecords": "Tidak ada data yang ditemukan",
        "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
        "infoEmpty": "Tidak ada data yang tersedia",
        "infoFiltered": "(difilter dari _MAX_ total data)",
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