<div class="row">
    <div class="col-md-7">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-coins"></i> Riwayat Modal & Prive</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#modalCapital">
                        <i class="fas fa-plus"></i> Input Modal/Prive
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 400px;">
                <table class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Tipe</th>
                            <th class="text-right">Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($capitals as $c): ?>
                        <tr>
                            <td><?= $c->date ?></td>
                            <td><?= $c->description ?></td>
                            <td>
                                <?php if($c->type == 'capital_in'): ?>
                                    <span class="badge badge-success">Setor Modal</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Tarik Prive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right font-weight-bold">
                                Rp <?= number_format($c->amount, 0, ',', '.') ?>
                            </td>
                            <td>
                                <a href="<?= site_url('finance/settings/delete_capital/'.$c->id) ?>" class="text-danger" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tags"></i> Kategori Biaya</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#modalCategory">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 400px;">
                <table class="table table-head-fixed">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Tipe</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $cat): ?>
                        <tr>
                            <td><?= $cat->name ?></td>
                            <td><?= ucfirst($cat->type) ?></td>
                            <td>
                                <a href="<?= site_url('finance/settings/delete_category/'.$cat->category_id) ?>" class="text-danger" onclick="return confirm('Hapus kategori ini? Pastikan tidak ada transaksi terkait!')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCapital">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Input Transaksi Modal</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= site_url('finance/settings/add_capital') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Transaksi</label>
                        <select name="type" class="form-control" required>
                            <option value="capital_in">Setor Modal (Uang Masuk)</option>
                            <option value="prive">Tarik Prive (Uang Keluar untuk Owner)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="description" class="form-control" placeholder="Contoh: Suntikan dana investor A" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah (Rp)</label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCategory">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Kategori Biaya</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= site_url('finance/settings/add_category') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Biaya Renovasi" required>
                    </div>
                    <div class="form-group">
                        <label>Sifat Biaya</label>
                        <select name="type" class="form-control">
                            <option value="fixed">Fixed (Tetap - Gaji, Sewa)</option>
                            <option value="variable">Variable (Berubah - Bensin, Makan)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>