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
                        <h4>Form Edit Obat</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('obat/update/' . $this->data['obat']['id_obat']) ?>" method="post">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="obat" class="form-label">Nama Obat</label>
                                    <input type="text" class="form-control" id="obat" placeholder="Masukkan nama obat" name="nama_obat" value="<?= $this->data['obat']['nama_obat'] ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="jumlah" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" id="jumlah" placeholder="Masukkan jumlah" name="jumlah" value="<?= $this->data['obat']['jumlah_stok'] ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="satuan" class="form-label">Satuan</label>
                                    <?php
                                    $satuan_options = ['Ampul', 'BKS', 'Botol', 'Box', 'Dus', 'Inj', 'Kamar', 'Kapsul'];
                                    $selected_satuan = $this->data['obat']['satuan'] ?? '';
                                    ?>

                                    <select name="satuan" id="satuan" class="form-control" required>
                                        <?php foreach ($satuan_options as $option): ?>
                                            <option value="<?= $option ?>" <?= ($selected_satuan === $option) ? 'selected' : '' ?>>
                                                <?= $option ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="expired" class="form-label">Expired</label>
                                    <input type="date" class="form-control" id="expired" name="expired"  value="<?= $this->data['obat']['tanggal_kadaluwarsa'] ?>" required>
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