<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title"><?= $title ?></h3>
    </div>
    <form action="" method="post">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Gunakan fitur ini untuk mencatat:
                <ul>
                    <li>Barang baru masuk dari produksi/supplier.</li>
                    <li>Barang rusak/expired (musnahkan).</li>
                    <li>Koreksi stok manual (selisih hitungan fisik).</li>
                </ul>
                <small>*Untuk penjualan ke pelanggan, sebaiknya gunakan modul Order Penjualan agar tercatat di invoice.</small>
            </div>

            <div class="form-group">
                <label>Pilih Produk</label>
                <select name="product_id" class="form-control" required>
                    <option value="">-- Pilih Produk --</option>
                    <?php foreach($products as $p): ?>
                        <option value="<?= $p->product_id ?>"><?= $p->name ?> (Satuan: <?= $p->unit ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Perubahan</label>
                        <select name="type" class="form-control" required>
                            <option value="in" class="text-success font-weight-bold">MASUK (+ Tambah Stok)</option>
                            <option value="out" class="text-danger font-weight-bold">KELUAR (- Kurang Stok)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jumlah (Quantity)</label>
                        <input type="number" name="change_qty" class="form-control" placeholder="Contoh: 500" min="0.01" step="0.01" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Keterangan / Alasan</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Contoh: Kiriman dari pabrik batch A1, atau Barang kemasan rusak" required></textarea>
            </div>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="<?= site_url('inventory/stock') ?>" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>