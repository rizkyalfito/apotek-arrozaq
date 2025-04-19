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
    <table id="tabelObat" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID Obat</th>
          <th>Nama Obat</th>
          <th>Jumlah Stok</th>
          <th>Satuan</th>
<!--          <th>Tanggal Kadaluwarsa</th>-->
          <th>QR Code</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if(count($this->data['obat']) < 1): ?>
            <tr>
                <td colspan="7" class="text-center">Belum ada data</td>
            </tr>
        <?php else : foreach ($this->data['obat'] as $obat) : ?>
            <tr>
                <td><?= $obat['id_obat'] ?></td>
                <td><?= $obat['nama_obat'] ?></td>
                <td><?= $obat['jumlah_stok'] ?></td>
                <td><?= $obat['satuan'] ?></td>
<!--                <td>--><?php //= $obat['tanggal_kadaluwarsa'] ?><!--</td>-->
                <td>
                    <div id="qrcode-<?= $obat['id_obat'] ?>"></div>
                </td>
                <td>
                    <a href="<?= base_url('obat/edit/' . $obat['id_obat']) ?>" class="btn btn-warning btn-sm">
                        Ubah
                    </a>
                    <a href="<?= base_url('obat/hapus/' . $obat['id_obat']) ?>" class="btn btn-danger btn-sm">
                        Hapus
                    </a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(isset($this->data['obat'])) : foreach ($this->data['obat'] as $obat) : ?>
        try {
            new QRCode(document.getElementById("qrcode-<?= $obat['id_obat'] ?>"), {
                text: "<?= $obat['id_obat'] ?>-<?= $obat['nama_obat'] ?>",
                width: 50,
                height: 50,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        } catch(e) {
            console.error("Exception with QR code generation for ID: <?= $obat['id_obat'] ?>", e);
            document.getElementById("qrcode-<?= $obat['id_obat'] ?>").innerHTML =
                '<span class="text-danger">Error: QR Code tidak dapat dibuat</span>';
        }
        <?php endforeach; endif; ?>
    });
</script>
<?= $this->endSection() ?>