<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Scan Barcode Obat</h3>
      </div>
      <div class="card-body">
        <!-- Tambahan input untuk barcode scanner -->
        <div class="form-group mb-3">
          <label for="barcodeInput">Input Barcode </label>
          <input type="text" class="form-control" id="barcodeInput" placeholder="Scan barcode atau masukkan kode secara manual" autofocus>
          <small class="form-text text-muted">Gunakan alat scanner barcode atau ketik kode secara manual, lalu tekan Enter</small>
        </div>

        <div class="text-center mb-3">
          <div id="qr-reader" style="width: 100%"></div>
        </div>
        <!-- <div class="text-center">
          <button class="btn btn-primary" id="startButton">Mulai Scan</button>
          <button class="btn btn-danger" id="stopButton" style="display: none;">Berhenti Scan</button>
        </div> -->
        <!-- <div class="mt-3">
          <div class="form-group">
            <label for="qrCodeImage">Atau unggah gambar Barcode</label>
            <input type="file" class="form-control-file" id="qrCodeImage" accept="image/*">
          </div>
        </div> -->
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Data Obat</h3>
      </div>
      <div class="card-body">
        <form action="<?= base_url('obat/masuk/simpan') ?>" method="post">
          <div class="form-group">
            <label for="id_obat">ID Obat</label>
            <input type="text" class="form-control" id="id_obat" name="id_obat" readonly>
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
            <label for="jumlah">Jumlah Masuk</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
          </div>
          <div class="form-group">
                <label for="tanggal_masuk">Tanggal Masuk</label>
                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?= old('tanggal_masuk') ? old('tanggal_masuk') : date('Y-m-d') ?>" required>
              </div>
          <div class="form-group">
            <label for="tanggal_kadaluwarsa">Tanggal Kadaluwarsa</label>
            <input type="date" class="form-control" id="tanggal_kadaluwarsa" name="tanggal_kadaluwarsa" required>
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-success" id="submitBtn" disabled>Simpan</button>
            <a href="<?= base_url('obat/masuk') ?>" class="btn btn-secondary">Kembali</a>
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
  const fileInput = document.getElementById('qrCodeImage');
  const barcodeInput = document.getElementById('barcodeInput');
  
  // Set focus to barcode input when page loads
  barcodeInput.focus();

  // Handle barcode input dengan event keydown untuk Enter
  barcodeInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const barcodeValue = this.value.trim();
      if (barcodeValue) {
        processBarcode(barcodeValue);
        this.value = ''; // Clear input setelah diproses
      }
    }
  });
  
  // Tambahkan event untuk focus out (misalnya jika user mengklik di luar input)
  barcodeInput.addEventListener('blur', function() {
    const barcodeValue = this.value.trim();
    if (barcodeValue) {
      processBarcode(barcodeValue);
      this.value = ''; // Clear input setelah diproses
    }
    // Jangan kembalikan fokus di sini untuk mencegah loop
  });

  function processBarcode(barcodeValue) {
    // Hapus karakter non-alphanumeric yang mungkin menyebabkan masalah
    const cleanedBarcode = barcodeValue.replace(/[^\w-]/g, '');
    console.log("Processing barcode:", cleanedBarcode);
    fetchMedicineData(cleanedBarcode);
  }
  
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
  
  fileInput.addEventListener('change', event => {
    if (event.target.files.length === 0) {
      return;
    }
    
    const imageFile = event.target.files[0];
    
    if (html5QrCode.isScanning) {
      html5QrCode.stop().then(() => {
        startButton.style.display = 'inline-block';
        stopButton.style.display = 'none';
        processQrCodeFromImage(imageFile);
      });
    } else {
      processQrCodeFromImage(imageFile);
    }
  });
  
  function processQrCodeFromImage(imageFile) {
    html5QrCode.scanFile(imageFile, true)
      .then(decodedText => {
        onScanSuccess(decodedText);
      })
      .catch(err => {
        alert("Error scanning QR code from image: " + err);
      });
  }
  
  function onScanSuccess(decodedText, decodedResult) {
    console.log("Scan result:", decodedText);
    if (html5QrCode.isScanning) {
      html5QrCode.stop();
      startButton.style.display = 'inline-block';
      stopButton.style.display = 'none';
    }
    
    // Proses hasil scan
    processBarcode(decodedText);
  }
  
  function fetchMedicineData(obatId) {
    try {
      // Tambahkan logging untuk troubleshooting
      console.log("Fetching data for obat ID:", obatId);
      
      fetch('<?= base_url('obat/masuk/scan-result') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id_obat: obatId })
      })
      .then(response => {
        console.log("Server response status:", response.status);
        return response.json();
      })
      .then(data => {
        console.log("Server response data:", data);
        if (data.success) {
          $('#id_obat').val(data.obat.id_obat);
          $('#nama_obat').val(data.obat.nama_obat);
          $('#satuan').val(data.obat.satuan);
          $('#submitBtn').prop('disabled', false);
          
          // Focus on jumlah field after successful scan
          $('#jumlah').focus();
        } else {
          alert('Data obat tidak ditemukan: ' + obatId);
          barcodeInput.focus(); // Return focus to barcode input
        }
      })
      .catch(error => {
        console.error("Fetch error:", error);
        alert('Terjadi kesalahan saat memproses kode: ' + error.message);
        barcodeInput.focus(); // Return focus to barcode input
      });
    } catch (error) {
      console.error("General error:", error);
      alert('Terjadi kesalahan: ' + error.message);
      barcodeInput.focus(); // Return focus to barcode input
    }
  }
  
  function onScanFailure(error) {
    console.warn(`Scan gagal: ${error}`);
  }
});
</script>
<?= $this->endSection() ?>