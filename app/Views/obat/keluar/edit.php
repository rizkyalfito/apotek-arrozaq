<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Edit Data Obat Keluar</h3>
      </div>
      <div class="card-body">
        <?php if (session()->has('errors')) : ?>
          <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <ul>
                <?php foreach (session('errors') as $error) : ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach ?>
              </ul>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('obat/keluar/update/' . $obatKeluar['kode_transaksi']) ?>" method="post">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="kode_transaksi">Kode Transaksi</label>
                <input type="text" class="form-control" id="kode_transaksi" value="<?= $obatKeluar['kode_transaksi'] ?>" readonly>
              </div>
              <div class="form-group">
                <label for="id_obat">ID Obat</label>
                <input type="text" class="form-control" id="id_obat" name="id_obat" value="<?= $obatKeluar['id_obat'] ?>" readonly>
              </div>
              <div class="form-group">
                <label for="nama_obat">Nama Obat</label>
                <input type="text" class="form-control" id="nama_obat" name="nama_obat" value="<?= $obatKeluar['nama_obat'] ?>" readonly>
              </div>
              <div class="form-group">
                <label for="satuan">Satuan</label>
                <input type="text" class="form-control" id="satuan" name="satuan" value="<?= $obatKeluar['satuan'] ?>" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="jumlah">Jumlah Keluar</label>
                <input type="number" class="form-control <?= (session('errors.jumlah')) ? 'is-invalid' : '' ?>" id="jumlah" name="jumlah" value="<?= old('jumlah') ? old('jumlah') : $obatKeluar['jumlah'] ?>" required min="0">
                <div class="invalid-feedback">
                  <?= session('errors.jumlah') ?>
                </div>
                <small id="stok_warning" class="text-danger" style="display: none;">Jumlah melebihi stok yang tersedia!</small>
              </div>
              <div class="form-group">
                <label for="tanggal_penjualan">Tanggal Keluar</label>
                <input type="date" class="form-control" id="tanggal_penjualan" name="tanggal_penjualan" value="<?= old('tanggal_penjualan') ? old('tanggal_penjualan') : $obatKeluar['tanggal_penjualan'] ?>" required>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <div class="form-group">
                <button type="submit" class="btn btn-success" id="submitBtn">Simpan</button>
                <a href="<?= base_url('obat/keluar') ?>" class="btn btn-secondary">Kembali</a>
              </div>
            </div>
          </div>
          
          <input type="hidden" id="jumlah_awal" value="<?= $obatKeluar['jumlah'] ?>">
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  $(document).ready(function() {
    const jumlahInput = $('#jumlah');
    const stokWarning = $('#stok_warning');
    const submitBtn = $('#submitBtn');
    
    function validateJumlah() {
      const jumlahAwal = parseInt($('#jumlah_awal').val()) || 0;
      const jumlahBaru = parseInt(jumlahInput.val()) || 0;
      const stokTersedia = parseInt($('#stok_tersedia').val()) || 0;
      
      const maksimalDiperbolehkan = stokTersedia + jumlahAwal;
      
      if (jumlahBaru > maksimalDiperbolehkan) {
        stokWarning.text(`Jumlah melebihi stok yang tersedia! Maksimal: ${maksimalDiperbolehkan}`).show();
        submitBtn.prop('disabled', true);
      } else {
        stokWarning.hide();
        submitBtn.prop('disabled', false);
      }
    }
    
    jumlahInput.on('input', validateJumlah);
    
    validateJumlah();
  });
</script>
<?= $this->endSection() ?>