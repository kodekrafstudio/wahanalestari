<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title"><?= $title ?></h3>
    </div>
    <form action="" method="post">
        <div class="card-body">
            <div class="form-group">
                <label>Nama Produk *</label>
                <input type="text" name="name" class="form-control" value="<?= $row ? $row->name : set_value('name') ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tipe Produk</label>
                        <select name="type" class="form-control">
                            <?php 
                            $types = ['konsumsi','industri','artisan','bath_salt'];
                            foreach($types as $t): 
                            ?>
                            <option value="<?= $t ?>" <?= ($row && $row->type == $t) ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                     <div class="form-group">
                        <label>Grade / Kualitas</label>
                        <input type="text" name="grade" class="form-control" placeholder="Contoh: Premium, Krosok" value="<?= $row ? $row->grade : set_value('grade') ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                     <div class="form-group">
                        <label>Satuan (Unit)</label>
                        <input type="text" name="unit" class="form-control" placeholder="kg, sak, pack" value="<?= $row ? $row->unit : set_value('unit') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Harga Pokok (Base Cost)</label>
                        <input type="number" name="base_cost" class="form-control" value="<?= $row ? $row->base_cost : set_value('base_cost') ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Harga Jual</label>
                        <input type="number" name="sell_price" class="form-control" value="<?= $row ? $row->sell_price : set_value('sell_price') ?>" required>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-info">Simpan Produk</button>
            <a href="<?= site_url('master/products') ?>" class="btn btn-default">Kembali</a>
        </div>
    </form>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Harga Beli Terakhir (Dari Supplier)</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-light">Rp</span>
                </div>
                <input type="text" class="form-control bg-light" readonly 
                       value="<?= $row ? number_format($row->last_purchase_price, 0, ',', '.') : '0' ?>">
            </div>
            <small class="text-muted">Otomatis dari PO terakhir.</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>HPP (Modal Rata-rata) <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Rp</span>
                </div>
                <input type="number" name="base_cost" class="form-control" required 
                       value="<?= $row ? $row->base_cost : '' ?>">
            </div>
            <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Edit hanya jika perhitungan sistem salah.</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label class="text-success font-weight-bold">Harga Jual (Ke Pelanggan) <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-success text-white">Rp</span>
                </div>
                <input type="number" name="price" class="form-control border-success" required 
                       value="<?= $row ? $row->price : '' ?>">
            </div>
            <small class="text-muted">Tentukan margin keuntungan Anda.</small>
        </div>
    </div>
</div>