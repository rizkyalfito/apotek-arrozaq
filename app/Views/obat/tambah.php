<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger my-4" role="alert">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Form Input Obat</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('obat/simpan') ?>" method="post">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="obat" class="form-label">Nama Obat / BMHP</label>
                                    <input type="text" class="form-control" id="obat" placeholder="Masukkan nama obat" name="nama_obat" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="jumlah" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" id="jumlah" placeholder="Masukkan jumlah" name="jumlah" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="satuan" class="form-label">Satuan</label>
                                    <select name="satuan" id="satuan" class="form-control" required>
                                        <option selected value="">--- Pilih Satuan ---</option>
                                        <option value="Ampul">Ampul</option>
                                        <option value="BKS">BKS</option>
                                        <option value="Botol">Botol</option>
                                        <option value="Box">Box</option>
                                        <option value="Dus">Dus</option>
                                        <option value="Inj">Inj</option>
                                        <option value="Kamar">Kamar</option>
                                        <option value="Kapsul">Kapsul</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="harga_modal" class="form-label">Harga Modal</label>
                                    <input type="number" class="form-control" id="harga_modal" placeholder="Masukkan Harga Modal" name="harga_modal" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="harga_jual" class="form-label">Harga Jual</label>
                                    <input type="number" class="form-control" id="harga_jual" placeholder="Masukkan Harga Jual" name="harga_jual" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="expired" class="form-label">Expired</label>
                                    <input type="date" class="form-control" id="expired" name="expired" required>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>