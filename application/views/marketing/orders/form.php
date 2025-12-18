<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title"><?= $title ?></h3>
    </div>
    <form action="" method="post">
        <div class="card-body">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pilih Pelanggan</label>
                        <select name="customer_id" class="form-control select2" required>
                            <option value="">-- Cari Pelanggan --</option>
                            <?php foreach($customers as $c): ?>
                                <option value="<?= $c->customer_id ?>" <?= ($row && $row->customer_id == $c->customer_id) ? 'selected' : '' ?>>
                                    <?= $c->name ?> (<?= $c->city ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pilih Produk</label>
                        <select name="product_id" id="product_id" class="form-control" required>
                            <option value="" data-price="0">-- Cari Produk --</option>
                            <?php foreach($products as $p): ?>
                                <option value="<?= $p->product_id ?>" 
                                        data-price="<?= $p->sell_price ?>"
                                        <?= ($row && $row->product_id == $p->product_id) ? 'selected' : '' ?>>
                                    <?= $p->name ?> (Stok: Master) - @Rp <?= number_format($p->sell_price,0,',','.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status Order</label>
                        <select name="status" class="form-control">
                            <?php 
                            $statuses = ['request','preparing','delivering','done','canceled'];
                            foreach($statuses as $s): 
                            ?>
                            <option value="<?= $s ?>" <?= ($row && $row->status == $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 bg-light p-3 border rounded">
                    <h5 class="text-muted">Rincian Harga</h5>
                    <hr>
                    
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Harga Satuan (Rp)</label>
                        <div class="col-sm-8">
                            <input type="number" name="price" id="price" class="form-control" 
                                   value="<?= $row ? $row->price : 0 ?>" required>
                            <small class="text-muted">Harga bisa diedit manual (negosiasi).</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Jumlah (Qty)</label>
                        <div class="col-sm-8">
                            <input type="number" name="quantity" id="quantity" class="form-control" 
                                   value="<?= $row ? $row->quantity : 1 ?>" required min="1">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Total (Rp)</label>
                        <div class="col-sm-8">
                            <input type="number" name="total" id="total" class="form-control font-weight-bold text-success" 
                                   style="font-size: 1.2rem;"
                                   value="<?= $row ? $row->total : 0 ?>" readonly>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Order</button>
            <a href="<?= site_url('marketing/orders') ?>" class="btn btn-default">Kembali</a>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    const productSelect = document.getElementById('product_id');
    const priceInput    = document.getElementById('price');
    const qtyInput      = document.getElementById('quantity');
    const totalInput    = document.getElementById('total');

    // 1. Fungsi Hitung Total
    function calculateTotal() {
        let price = parseFloat(priceInput.value) || 0;
        let qty   = parseFloat(qtyInput.value) || 0;
        let total = price * qty;
        totalInput.value = total;
    }

    // 2. Saat Produk diganti -> Update Harga Satuan
    productSelect.addEventListener('change', function() {
        // Ambil harga dari attribute data-price di <option>
        let selectedOption = productSelect.options[productSelect.selectedIndex];
        let masterPrice = selectedOption.getAttribute('data-price');
        
        // Isi ke input price
        priceInput.value = masterPrice;
        
        // Hitung ulang total
        calculateTotal();
    });

    // 3. Saat Qty atau Harga diketik -> Hitung ulang total live
    qtyInput.addEventListener('input', calculateTotal);
    priceInput.addEventListener('input', calculateTotal);

});
</script>