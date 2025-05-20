<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Scan Barcode Obat Keluar</h3>
      </div>
      <div class="card-body">
        <!-- Tambahan input untuk barcode scanner -->
        <div class="form-group mb-3">
          <label for="barcodeInput">Input Barcode</label>
          <input type="text" class="form-control" id="barcodeInput" placeholder="Scan barcode atau masukkan kode secara manual" autofocus>
          <small class="form-text text-muted">Gunakan alat scanner barcode atau ketik kode secara manual</small>
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
        <div class="mt-3">
          <div id="debug-info" class="alert alert-info" style="display: none;"></div>
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
          <input type="hidden" name="id_obat" id="id_obat">
          
          <div class="form-group">
            <label for="nama_obat">Nama Obat</label>
            <input type="text" class="form-control" id="nama_obat" readonly>
          </div>
          
          <div class="form-group">
            <label for="stok_tersedia">Stok Tersedia</label>
            <input type="text" class="form-control" id="stok_tersedia" readonly>
          </div>
          
          <div class="form-group">
            <label for="satuan">Satuan</label>
            <input type="text" class="form-control" id="satuan" readonly>
          </div>
          
          <div class="form-group">
            <label for="jumlah">Jumlah</label>
            <input type="number" class="form-control" name="jumlah" id="jumlah" required>
          </div>
          
          <div class="form-group">
            <label for="tanggal_penjualan">Tanggal Penjualan</label>
            <input type="date" class="form-control" name="tanggal_penjualan" id="tanggal_penjualan" value="<?= date('Y-m-d') ?>" required>
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
  #debug-info {
    max-height: 150px;
    overflow-y: auto;
    font-family: monospace;
    font-size: 12px;
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
  const debugInfo = document.getElementById('debug-info');
  
  // Set focus to barcode input when page loads
  barcodeInput.focus();
  
  // Variabel untuk menyimpan timer debounce
  let barcodeTimer = null;
  // Waktu tunggu dalam milidetik sebelum proses input barcode
  const BARCODE_DELAY = 500;

  // Handle barcode input dengan debounce untuk menunggu input lengkap
  barcodeInput.addEventListener('input', function() {
    // Clear timer sebelumnya jika ada
    if (barcodeTimer) {
      clearTimeout(barcodeTimer);
    }
    
    const barcodeValue = this.value.trim();
    if (barcodeValue) {
      // Set timer baru untuk menunggu sebelum memproses
      barcodeTimer = setTimeout(() => {
        showDebug(`Processing manual barcode: ${barcodeValue}`);
        processObatId(barcodeValue);
        this.value = ''; // Clear input setelah diproses
        this.focus(); // Kembalikan fokus ke input untuk scan berikutnya
      }, BARCODE_DELAY);
    }
  });

  // Tambahan: Handle input dengan tombol Enter untuk proses segera
  barcodeInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      if (barcodeTimer) {
        clearTimeout(barcodeTimer);
      }
      
      const barcodeValue = this.value.trim();
      if (barcodeValue) {
        showDebug(`Processing manual barcode (Enter pressed): ${barcodeValue}`);
        processObatId(barcodeValue);
        this.value = ''; // Clear input setelah diproses
        this.focus(); // Kembalikan fokus ke input untuk scan berikutnya
      }
    }
  });
  
  function showDebug(message, isError = false) {
    if (isError) {
      // Selalu tampilkan pesan error
      debugInfo.style.display = 'block';
    }
    
    if (isError || debugInfo.style.display === 'block') {
      const timestamp = new Date().toLocaleTimeString();
      debugInfo.innerHTML += `<div class="${isError ? 'text-danger' : ''}">[${timestamp}] ${message}</div>`;
      debugInfo.scrollTop = debugInfo.scrollHeight;
    }
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
        showDebug("Scanner started");
      })
      .catch(err => {
        showDebug("Error starting scanner: " + err, true);
      });
  });
  
  stopButton.addEventListener('click', () => {
    html5QrCode.stop()
      .then(() => {
        startButton.style.display = 'inline-block';
        stopButton.style.display = 'none';
        showDebug("Scanner stopped");
      })
      .catch(err => {
        showDebug("Error stopping scanner: " + err, true);
      });
  });
  
  fileInput.addEventListener('change', event => {
    if (event.target.files.length === 0) {
      return;
    }
    
    const imageFile = event.target.files[0];
    showDebug(`Processing image file: ${imageFile.name}`);
    
    if (html5QrCode.isScanning) {
      html5QrCode.stop().then(() => {
        startButton.style.display = 'inline-block';
        stopButton.style.display = 'none';
        processQrCodeFromImage(imageFile);
      }).catch(err => {
        showDebug("Error stopping scanner: " + err, true);
        processQrCodeFromImage(imageFile);
      });
    } else {
      processQrCodeFromImage(imageFile);
    }
  });
  
  function processQrCodeFromImage(imageFile) {
    html5QrCode.scanFile(imageFile, true)
      .then(decodedText => {
        showDebug(`QR code detected from image: ${decodedText}`);
        onScanSuccess(decodedText);
      })
      .catch(err => {
        showDebug("Error scanning QR code from image: " + err, true);
      });
  }
  
  function onScanSuccess(decodedText, decodedResult) {
    showDebug(`QR Code terdeteksi: ${decodedText}`);
    
    if (html5QrCode.isScanning) {
      html5QrCode.stop().then(() => {
        startButton.style.display = 'inline-block';
        stopButton.style.display = 'none';
      }).catch(err => {
        showDebug("Error stopping scanner: " + err, true);
      });
    }
    
    processObatId(decodedText);
  }
  
  function processObatId(obatIdRaw) {
    try {
      let obatId = obatIdRaw;
      
      showDebug(`Processing QR/barcode data: ${obatIdRaw}`);

      // Handle jika QR code dalam format JSON
      if (obatIdRaw.startsWith('{') && obatIdRaw.endsWith('}')) {
        try {
          const jsonData = JSON.parse(obatIdRaw);
          if (jsonData.id_obat) {
            obatId = jsonData.id_obat;
            showDebug(`Extracted ID from JSON: ${obatId}`);
          }
        } catch (jsonError) {
          showDebug(`Failed to parse JSON: ${jsonError}`, true);
        }
      }
      // Handle jika ada format dengan tanda "-" (seperti pada hasil scan yang ditampilkan)
      else if (obatIdRaw.includes('-')) {
        const parts = obatIdRaw.split('-');
        // Ambil bagian pertama yang berisi ID (contoh: "10-Biolysin" → ambil "10")
        obatId = parts[0].trim();
        showDebug(`Extracted ID from hyphenated string: ${obatId}`);
      }
      
      // Pastikan ID adalah angka
      if (!/^\d+$/.test(obatId)) {
        showDebug(`ID is not a number: ${obatId}`, true);
        alert('Format QR Code/Barcode tidak valid! ID harus berupa angka.');
        barcodeInput.focus(); // Return focus to barcode input
        return;
      }
      
      showDebug(`Sending ID to server: ${obatId}`);
      
      // Kirim ke server
      $.ajax({
        url: '<?= base_url('obat/keluar/scan-result') ?>',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id_obat: obatId }),
        success: function(response) {
          processServerResponse(response);
        },
        error: function(xhr, status, err) {
          showDebug(`AJAX error: ${err}`, true);
          showDebug(`Status: ${status}`, true);
          showDebug(`Response: ${xhr.responseText}`, true);
          alert('Terjadi kesalahan saat memproses kode');
          barcodeInput.focus(); // Return focus to barcode input
        }
      });
    } catch (error) {
      showDebug(`Processing error: ${error}`, true);
      alert('Terjadi kesalahan saat memproses kode');
      barcodeInput.focus(); // Return focus to barcode input
    }
  }
  
  function processServerResponse(response) {
    if (response.success) {
      // Sembunyikan debug info jika sukses
      debugInfo.style.display = 'none';
      
      const obat = response.obat;
      showDebug(`Data obat ditemukan: ${obat.nama_obat} (ID: ${obat.id_obat})`);
      
      $('#id_obat').val(obat.id_obat);
      $('#nama_obat').val(obat.nama_obat);
      $('#stok_tersedia').val(obat.jumlah_stok);
      $('#satuan').val(obat.satuan);
      $('#submitBtn').prop('disabled', false);
      
      $('#jumlah').attr('max', obat.jumlah_stok);
      $('#jumlah').off('input').on('input', function() {
        const jumlah = parseInt($(this).val()) || 0;
        const stok = parseInt(obat.jumlah_stok) || 0;
        
        if (jumlah > stok) {
          alert('Jumlah yang dimasukkan melebihi stok tersedia!');
          $(this).val(stok);
        }
      });
      
      // Focus on jumlah field after successful scan
      $('#jumlah').focus();
    } else {
      showDebug(`Data obat tidak ditemukan: ${response.message || 'Unknown error'}`, true);
      alert('Data obat tidak ditemukan!');
      barcodeInput.focus(); // Return focus to barcode input
    }
  }
  
  function onScanFailure(error) {
    console.warn(`Scan gagal: ${error}`);
  }
});
</script>
<?= $this->endSection() ?>