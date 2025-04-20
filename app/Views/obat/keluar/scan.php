<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Scan QR Code Obat Keluar</h3>
      </div>
      <div class="card-body">
        <div class="text-center mb-3">
          <div id="qr-reader" style="width: 100%"></div>
        </div>
        <div class="text-center">
          <button class="btn btn-primary" id="startButton">Mulai Scan</button>
          <button class="btn btn-danger" id="stopButton" style="display: none;">Berhenti Scan</button>
        </div>
        <div class="mt-3">
          <div class="form-group">
            <label for="qrCodeImage">Atau unggah gambar QR Code</label>
            <input type="file" class="form-control-file" id="qrCodeImage" accept="image/*">
          </div>
        </div>
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
    const manualIdBtn = document.getElementById('processManualId');
    const debugInfo = document.getElementById('debug-info');
    
    function showDebug(message, isError = false) {
      debugInfo.style.display = 'block';
      const timestamp = new Date().toLocaleTimeString();
      debugInfo.innerHTML += `<div class="${isError ? 'text-danger' : ''}">[${timestamp}] ${message}</div>`;
      debugInfo.scrollTop = debugInfo.scrollHeight;
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
    
    manualIdBtn.addEventListener('click', () => {
      const manualId = document.getElementById('manualId').value.trim();
      if (manualId) {
        showDebug(`Processing manual ID: ${manualId}`);
        processObatId(manualId);
      } else {
        showDebug("ID Obat tidak boleh kosong", true);
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
      showDebug(`QR Code detected: ${decodedText}`);
      
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
    
    showDebug(`Processing QR data: ${obatIdRaw}`);

    if (obatIdRaw.startsWith('{') && obatIdRaw.endsWith('}')) {
      try {
        const jsonData = JSON.parse(obatIdRaw);
        if (jsonData.id_obat) {
          obatId = jsonData.id_obat;
        }
      } catch (jsonError) {
        showDebug(`Failed to parse JSON: ${jsonError}`, true);
      }
    }
    else if (obatIdRaw.includes('-')) {
      obatId = obatIdRaw.split('-')[0];
    }
    
    if (!/^\d+$/.test(obatId)) {
      showDebug(`ID is not a number: ${obatId}`, true);
      alert('Format QR Code tidak valid! ID harus berupa angka.');
      return;
    }
    
    $.ajax({
      url: '<?= base_url('obat/keluar/scan-result') ?>',
      type: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ id_obat: obatId }),
      success: function(response) {
        debugInfo.style.display = 'none';
        processServerResponse(response);
      },
      error: function(xhr, status, err) {
        showDebug(`AJAX error: ${err}`, true);
        showDebug(`Status: ${status}`, true);
        showDebug(`Response: ${xhr.responseText}`, true);
        alert('Terjadi kesalahan saat memproses QR Code');
      }
    });
  } catch (error) {
    showDebug(`Processing error: ${error}`, true);
    alert('Terjadi kesalahan saat memproses QR Code');
  }
}

function onScanSuccess(decodedText, decodedResult) {
  showDebug(`QR Code terdeteksi`);
  
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

function processServerResponse(response) {
  if (response.success) {
    const obat = response.obat;
    
    $('#id_obat').val(obat.id_obat);
    $('#nama_obat').val(obat.nama_obat);
    $('#stok_tersedia').val(obat.jumlah_stok);
    $('#satuan').val(obat.satuan);
    $('#submitBtn').prop('disabled', false);
    

    $('#jumlah').attr('max', obat.jumlah_stok);
    $('#jumlah').on('input', function() {
      const jumlah = parseInt($(this).val()) || 0;
      const stok = parseInt(obat.jumlah_stok) || 0;
      
      if (jumlah > stok) {
        alert('Jumlah yang dimasukkan melebihi stok tersedia!');
        $(this).val(stok);
      }
    });
  } else {
    showDebug('Data obat tidak ditemukan!', true);
    alert('Data obat tidak ditemukan!');
  }
}

function showDebug(message, isError = false) {

  if (isError || debugInfo.style.display === 'block') {
    debugInfo.style.display = 'block';
    const timestamp = new Date().toLocaleTimeString();
    debugInfo.innerHTML += `<div class="${isError ? 'text-danger' : ''}">[${timestamp}] ${message}</div>`;
    debugInfo.scrollTop = debugInfo.scrollHeight;
  }
}
    
    function processServerResponse(response) {
      
      if (response.success) {
        const obat = response.obat;
        
        $('#id_obat').val(obat.id_obat);
        $('#nama_obat').val(obat.nama_obat);
        $('#stok_tersedia').val(obat.jumlah_stok);
        $('#satuan').val(obat.satuan);
        $('#submitBtn').prop('disabled', false);
        
        $('#jumlah').attr('max', obat.jumlah_stok);
        $('#jumlah').on('input', function() {
          const jumlah = parseInt($(this).val()) || 0;
          const stok = parseInt(obat.jumlah_stok) || 0;
          
          if (jumlah > stok) {
            alert('Jumlah yang dimasukkan melebihi stok tersedia!');
            $(this).val(stok);
          }
        });
      } else {
        alert('Data obat tidak ditemukan!');
      }
    }
    
    function onScanFailure(error) {
      console.warn(`Scan gagal: ${error}`);
    }
  });
</script>
<?= $this->endSection() ?>