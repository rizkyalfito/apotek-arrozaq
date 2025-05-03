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
        <?php foreach ($this->data['obat'] as $obat) : ?>
            <tr>
                <td><?= $obat['id_obat'] ?></td>
                <td><?= $obat['nama_obat'] ?></td>
                <td><?= $obat['jumlah_stok'] ?></td>
                <td><?= $obat['satuan'] ?></td>
                <td>Rp. <?= number_format($obat['harga_modal'], 0, ',', '.'); ?></td>
                <td>Rp. <?= number_format($obat['harga_jual'], 0, ',', '.'); ?></td>
                <td>
                    <svg id="barcode-<?= $obat['id_obat'] ?>"></svg>
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
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(isset($this->data['obat'])) : foreach ($this->data['obat'] as $obat) : ?>
        try {
            new JsBarcode("#barcode-<?= $obat['id_obat'] ?>", "<?= $obat['id_obat'] ?>-<?= $obat['nama_obat'] ?>" , {
                format: 'CODE128',
                height: 50,
                width: 1.5,
                lineColor: '#00000',
                displayValue: false
            });
        } catch(e) {
            console.error("Exception with Barcode generation for ID: <?= $obat['id_obat'] ?>", e);
            document.getElementById("barcode-<?= $obat['id_obat'] ?>").innerHTML =
                '<span class="text-danger">Error: Barcode tidak dapat dibuat</span>';
        }
        <?php endforeach; endif; ?>
    });
</script>


<?= $this->endSection() ?>