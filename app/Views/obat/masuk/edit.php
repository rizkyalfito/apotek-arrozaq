<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Edit Data Obat Masuk</h3>
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

        <form action="<?= base_url('obat/masuk/update/' . $obatMasuk['id']) ?>" method="post">
          <input type="hidden" name="id_lama" value="<?= $obatMasuk['id_obat'] ?>">
          <input type="hidden" name="jumlah_lama" value="<?= $obatMasuk['jumlah'] ?>">
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="id_obat">ID Obat</label>
                <input type="text" class="form-control" id="id_obat" name="id_obat" value="<?= $obatMasuk['id_obat'] ?>" readonly>
              </div>
              <div class="form-group">
                <label for="nama_obat">Nama Obat</label>
                <input type="text" class="form-control" id="nama_obat" name="nama_obat" value="<?= $obatMasuk['nama_obat'] ?>" readonly>
              </div>
              <div class="form-group">
                <label for="satuan">Satuan</label>
                <input type="text" class="form-control" id="satuan" name="satuan" value="<?= $obatMasuk['satuan'] ?>" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="jumlah">Jumlah Masuk</label>
                <input type="number" class="form-control <?= (session('errors.jumlah')) ? 'is-invalid' : '' ?>" id="jumlah" name="jumlah" value="<?= old('jumlah') ? old('jumlah') : $obatMasuk['jumlah'] ?>" required>
                <div class="invalid-feedback">
                  <?= session('errors.jumlah') ?>
                </div>
              </div>
              <div class="form-group">
                <label for="tanggal_masuk">Tanggal Masuk</label>
                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?= old('tanggal_masuk') ? old('tanggal_masuk') : $obatMasuk['tanggal_masuk'] ?>" required>
              </div>
              <div class="form-group">
                <label for="tanggal_kadaluwarsa">Tanggal Kedaluwarsa</label>
                <input type="date" class="form-control <?= (session('errors.tanggal_kadaluwarsa')) ? 'is-invalid' : '' ?>" id="tanggal_kadaluwarsa" name="tanggal_kadaluwarsa" value="<?= old('tanggal_kadaluwarsa') ? old('tanggal_kadaluwarsa') : $obatMasuk['tanggal_kadaluwarsa'] ?>" required>
                <div class="invalid-feedback">
                  <?= session('errors.tanggal_kadaluwarsa') ?>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <div class="form-group">
                <button type="submit" class="btn btn-success">Update</button>
                <a href="<?= base_url('obat/masuk') ?>" class="btn btn-secondary">Kembali</a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>