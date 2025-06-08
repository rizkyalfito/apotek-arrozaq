<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Tambah Data Obat Keluar</h3>
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

        <form action="<?= base_url('obat/keluar/simpan') ?>" method="post">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="id_obat">ID Obat</label>
                <select class="form-control <?= (session('errors.id_obat')) ? 'is-invalid' : '' ?>" id="id_obat" name="id_obat" required>
                  <option value="">-- Pilih Obat --</option>
                  <?php foreach ($obat as $item) : ?>
                    <option value="<?= $item['id_obat'] ?>" 
                            data-nama="<?= $item['nama_obat'] ?>" 
                            data-satuan="<?= $item['satuan'] ?>" 
                            data-stok="<?= $item['jumlah_stok'] ?>"
                            data-harga="<?= isset($item['harga_jual']) ? $item['harga_jual'] : '0' ?>">
                      <?= $item['id_obat'] ?> - <?= $item['nama_obat'] ?> (Stok: <?= $item['jumlah_stok'] ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">
                  <?= session('errors.id_obat') ?>
                </div>
              </div>
              <div class="form-group">
                <label for="nama_obat">Nama Obat</label>
                <input type="text" class="form-control" id="nama_obat" name="nama_obat" readonly>
              </div>
              <div class="form-group">
                <label for="satuan">Satuan</label>
                <input type="text" class="form-control" id="satuan" name="satuan" readonly>
              </div>
              <div class="form-group">
                <label for="stok_tersedia">Stok Tersedia</label>
                <input type="text" class="form-control" id="stok_tersedia" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="jumlah">Jumlah Keluar</label>
                <input type="number" class="form-control <?= (session('errors.jumlah')) ? 'is-invalid' : '' ?>" id="jumlah" name="jumlah" value="<?= old('jumlah') ?>" required>
                <div class="invalid-feedback">
                  <?= session('errors.jumlah') ?>
                </div>
                <small id="stok_warning" class="text-danger" style="display: none;">Jumlah melebihi stok yang tersedia!</small>
              </div>
              <div class="form-group">
                <label for="tanggal_penjualan">Tanggal Keluar</label>
                <input type="date" class="form-control" id="tanggal_penjualan" name="tanggal_penjualan" value="<?= old('tanggal_penjualan') ? old('tanggal_penjualan') : date('Y-m-d') ?>" required>
              </div>
              <div class="form-group">
                <label for="harga_jual">Harga Jual</label>
                <input type="text" class="form-control" id="harga_jual_display" readonly>
                <input type="hidden" name="harga_jual" id="harga_jual_value" value="0">
              </div>
              <div class="form-group">
                <label for="total_harga">Total Harga</label>
                <input type="text" class="form-control" name="total_harga_display" id="total_harga_display" value="Rp 0" readonly>
                <input type="hidden" name="total_harga" id="total_harga_value" value="0">
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
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  $(document).ready(function() {
    // Format currency function
    function formatCurrency(amount) {
      return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    // Parse currency function
    function parseCurrency(formatted) {
      return parseInt(formatted.replace(/[^0-9]/g, '')) || 0;
    }
    
    $('#id_obat').change(function() {
      var selectedOption = $(this).find('option:selected');
      var nama = selectedOption.data('nama');
      var satuan = selectedOption.data('satuan');
      var stok = selectedOption.data('stok');
      var harga = selectedOption.data('harga') || 0;
      
      $('#nama_obat').val(nama);
      $('#satuan').val(satuan);
      $('#stok_tersedia').val(stok);
      $('#harga_jual_display').val(formatCurrency(harga));
      $('#harga_jual_value').val(harga);
      
      $('#jumlah').val('');
      $('#total_harga_display').val('Rp 0');
      $('#total_harga_value').val(0);
      $('#stok_warning').hide();
      validateJumlah();
    });
    
    $('#jumlah').on('input', function() {
      validateJumlah();
      calculateTotal();
    });
    
    function validateJumlah() {
      var stok = parseInt($('#stok_tersedia').val()) || 0;
      var jumlah = parseInt($('#jumlah').val()) || 0;
      
      if (jumlah > stok) {
        $('#stok_warning').show();
        $('#submitBtn').prop('disabled', true);
      } else {
        $('#stok_warning').hide();
        $('#submitBtn').prop('disabled', false);
      }
    }
    
    function calculateTotal() {
      var harga = parseInt($('#harga_jual_value').val()) || 0;
      var jumlah = parseInt($('#jumlah').val()) || 0;
      var total = harga * jumlah;
      
      $('#total_harga_display').val(formatCurrency(total));
      $('#total_harga_value').val(total);
    }

    // Restore old values if form has errors
    if('<?= old('id_obat') ?>') {
      $('#id_obat').val('<?= old('id_obat') ?>');
      $('#id_obat').trigger('change');
      
      // Wait a bit for the change event to complete, then calculate total
      setTimeout(function() {
        calculateTotal();
      }, 100);
    }
  });
</script>
<?= $this->endSection() ?>