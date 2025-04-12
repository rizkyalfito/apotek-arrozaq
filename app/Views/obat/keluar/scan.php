<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Scan QR Code Obat</h3>
      </div>
      <div class="card-body">
        <div class="text-center mb-3">
          <div id="qr-reader" style="width: 100%"></div>
        </div>
        <div class="text-center">
          <button class="btn btn-primary" id="startButton">Mulai Scan</button>
          <button class="btn btn-danger" id="stopButton" style="display: none;">Berhenti Scan</button>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Data Obat Keluar</h3>
      </div>
      <div class="card-body">
        <form action="<?= base_url('obat/keluar/simpan') ?>" method="post">
          <div class="form-group">
            <label for="id_obat">ID Obat</label>
            <input type="text" class="form-control" id="id_obat" name="id_obat" readonly>
          </div>
          <div class="form-group">
            <label for="nama_obat">Nama Obat</label>
            <input type="text" class="form-control" id="nama_obat" name="nama_obat" readonly>
          </div>
          <div class="form-group">
            <label for="stok_tersedia">Stok Tersedia</label>
            <input type="text" class="form-control" id="stok_tersedia" readonly>
          </div>
          <div class="form-group">
            <label for="satuan">Satuan</label>
            <input type="text" class="form-control" id="satuan" name="satuan" readonly>
          </div>
          <div class="form-group">
            <label for="jumlah">Jumlah Keluar</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
          </div>
          <div class="form-group">
            <label for="tanggal_penjualan">Tanggal Penjualan</label>
            <input type="date" class="form-control" id="tanggal_penjualan" name="tanggal_penjualan" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-success" id="submitBtn" disabled>Simpan</button>
            <a href="<?= base_url('obat/keluar') ?>" class="btn btn-secondary">Kembali</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
  #qr-reader {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
  }
  #qr-reader__scan_region {
    background: white;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
  $(document).ready(function() {
    const html5QrCode = new Html5Qrcode("qr-reader");
    const startButton = document.getElementById('startButton');
    const stopButton = document.getElementById('stopButton');
    
    startButton.addEventListener('click', () => {
      html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess,
        onScanFailure)
        .then(() => {
          startButton.style.display = 'none';
          stopButton.style.display = 'inline-block';
        });
    });
    
    stopButton.addEventListener('click', () => {
      html5QrCode.stop()
        .then(() => {
          startButton.style.display = 'inline-block';
          stopButton.style.display = 'none';
        });
    });
    
    function onScanSuccess(decodedText, decodedResult) {
      // Berhenti scan setelah mendapatkan hasil
      html5QrCode.stop();
      startButton.style.display = 'inline-block';
      stopButton.style.display = 'none';
      
      // Ambil data obat dari server berdasarkan ID
      try {
        const obatId = decodedText;
        fetch('<?= base_url('obat/keluar/scan-result') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ id_obat: obatId })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Isi form dengan data dari server
            $('#id_obat').val(data.obat.id_obat);
            $('#nama_obat').val(data.obat.nama_obat);
            $('#stok_tersedia').val(data.obat.jumlah_stok);
            $('#satuan').val(data.obat.satuan);
            $('#submitBtn').prop('disabled', false);
            
            // Set max value untuk jumlah keluar
            $('#jumlah').attr('max', data.obat.jumlah_stok);
          } else {
            alert('Data obat tidak ditemukan!');
          }
        });
      } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan saat memproses QR Code');
      }
    }
    
    function onScanFailure(error) {
      // Handle ketika scan gagal
      console.warn(`Scan gagal: ${error}`);
    }
    
    // Validasi jumlah keluar tidak melebihi stok
    $('#jumlah').on('input', function() {
      const stokTersedia = parseInt($('#stok_tersedia').val());
      const jumlahKeluar = parseInt($(this).val());
      
      if (jumlahKeluar > stokTersedia) {
        alert('Jumlah keluar tidak boleh melebihi stok tersedia!');
        $(this).val(stokTersedia);
      }
    });
  });
</script>
<?= $this->endSection() ?>