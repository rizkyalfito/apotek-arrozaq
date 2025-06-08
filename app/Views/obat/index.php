<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php 
// Load barcode helper untuk view
helper('barcode'); 
?>
<div class="row">
  <div class="col-md-12">
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
        <?php if (session()->getFlashdata('success')) : ?>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <?= session()->getFlashdata('success') ?>
          </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')) : ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>
        
        <div class="table-responsive">
          <table id="tabelObat" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>ID Obat</th>
                <th>Nama Obat / BMHP</th>
                <th>Jumlah Stok</th>
                <th>Satuan</th>
                <th>Harga Modal</th>
                <th>Harga Jual</th>
                <th>Barcode</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($obat)) : ?>
                <tr>
                  <td colspan="9" class="text-center">Belum ada data</td>
                </tr>
              <?php else : ?>
                <?php $no = 1; foreach ($obat as $item) : ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $item['id_obat'] ?></td>
                    <td><?= $item['nama_obat'] ?></td>
                    <td><?= $item['jumlah_stok'] ?></td>
                    <td><?= $item['satuan'] ?></td>
                    <td>Rp <?= number_format($item['harga_modal'], 0, ',', '.') ?>,-</td>
                    <td>Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?>,-</td>
                    <?= barcode_table_cell($item['id_obat']) ?>
                    <td>
                      <a href="<?= base_url('obat/edit/' . $item['id_obat']) ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="<?= base_url('obat/hapus/' . $item['id_obat']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus obat ini?')">
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
<!-- Include JsBarcode CDN menggunakan helper -->
<?= $jsbarcode_cdn ?>

<script>
  $(document).ready(function() {
    $('#tabelObat').DataTable({
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

<!-- Generate barcode script menggunakan helper -->
<script>
<?= $barcode_script ?>
</script>

<?= $this->endSection() ?>