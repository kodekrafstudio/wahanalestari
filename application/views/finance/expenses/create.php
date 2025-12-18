<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Pengeluaran</h3>
            </div>
            <form action="<?= site_url('finance/expenses/create') ?>" method="post">
                <div class="card-body">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Kategori Biaya</label>
                        <select name="category_id" id="category_id" class="form-control" required onchange="setCategoryName()">
                            <option value="">-- Pilih Kategori --</option>
                            <?php if(!empty($categories)): ?>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat->category_id ?>" data-name="<?= $cat->name ?>">
                                        <?= $cat->name ?> (<?= ucfirst($cat->type) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Kategori belum diisi di database</option>
                            <?php endif; ?>
                        </select>
                        <input type="hidden" name="category_name" id="category_name">
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Pembayaran listrik bulan ini"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Jumlah (Rp)</label>
                        <input type="text" name="amount" class="form-control" id="rupiah" placeholder="0" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= site_url('finance/expenses') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script untuk mengisi hidden input nama kategori saat dropdown dipilih
function setCategoryName() {
    var sel = document.getElementById('category_id');
    var text = sel.options[sel.selectedIndex].getAttribute('data-name');
    document.getElementById('category_name').value = text;
}
</script>