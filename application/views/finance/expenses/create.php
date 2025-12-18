<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Form Input Pengeluaran</h3>
            </div>
            <form action="" method="post">
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori Biaya</label>
                        <select name="category" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach($categories as $c): ?>
                                <option value="<?= $c ?>"><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nominal (Rp)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="number" name="amount" class="form-control" placeholder="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Keterangan Detail</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Beli Solar 50 Liter, Token Listrik Gudang A" required></textarea>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-danger btn-block">Simpan</button>
                    <a href="<?= site_url('finance/expenses') ?>" class="btn btn-default btn-block">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>