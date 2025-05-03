<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Data Obat Masuk</h3>
          <div class="card-tools">
            <a href="<?= base_url('obat/masuk/scan') ?>" class="btn btn-primary">
              <i class="fas fa-qrcode"></i> Scan Barcode
            </a>
            <a href="<?= base_url('obat/masuk/tambah') ?>" class="btn btn-success">
              <i class="fas fa-plus"></i> Tambah Data
            </a>
          </div>
      </div>
      <div class="card-body">
        <?php if (session()->getFlashdata('pesan')) : ?>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <?= session()->getFlashdata('pesan') ?>
          </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')) : ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>
        
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="tabelObatMasuk">
            <thead>
              <tr>
                <th>No</th>
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
              <?php if (empty($obatMasuk)) : ?>
                <tr>
                  <td colspan="8" class="text-center">Belum ada data</td>
                </tr>
              <?php else : ?>
                <?php $no = 1; foreach ($obatMasuk as $row) : ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_obat'] ?></td>
                    <td><?= $row['nama_obat'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td><?= $row['satuan'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_masuk'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_kadaluwarsa'])) ?></td>
                    <td>
                      <a href="<?= base_url('obat/masuk/edit/' . $row['id']) ?>" method="post" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="<?= base_url('obat/masuk/hapus/' . $row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
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
    $('#tabelObatMasuk').DataTable({
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