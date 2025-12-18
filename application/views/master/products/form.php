<form action="" method="post">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-box mr-1"></i> Informasi Produk</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            </div>
                            <input type="text" name="name" class="form-control" 
                                   value="<?= $row ? $row->name : set_value('name') ?>" 
                                   placeholder="Contoh: Garam Halus Premium 500g" required>
                        </div>
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
                                    <option value="<?= $t ?>" <?= ($row && $row->type == $t) ? 'selected' : '' ?>>
                                        <?= ucfirst(str_replace('_', ' ', $t)) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Grade / Kualitas</label>
                                <input type="text" name="grade" class="form-control" 
                                       placeholder="K1/K2/Premium" value="<?= $row ? $row->grade : set_value('grade') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Satuan (Unit)</label>
                                <input type="text" name="unit" class="form-control" 
                                       placeholder="Kg/Sak/Pack" value="<?= $row ? $row->unit : set_value('unit') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calculator mr-1"></i> Pengaturan Harga</h3>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-4 border-right">
                            <div class="form-group">
                                <label class="text-muted font-weight-normal">Harga Beli Terakhir</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-transparent border-0 pl-0">Rp</span>
                                    </div>
                                    <input type="text" class="form-control bg-transparent border-0 font-weight-bold" readonly 
                                           value="<?= ($row && isset($row->last_purchase_price)) ? number_format($row->last_purchase_price, 0, ',', '.') : '0' ?>">
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i> Referensi dari PO Supplier terakhir.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-4 border-right">
                            <div class="form-group">
                                <label>HPP (Modal) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" name="base_cost" class="form-control" required 
                                           value="<?= $row ? $row->base_cost : '' ?>" placeholder="0">
                                </div>
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Ubah jika hitungan sistem tidak sesuai.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-success font-weight-bold">Harga Jual <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white">Rp</span>
                                    </div>
                                    <input type="number" name="sell_price" class="form-control border-success font-weight-bold" required 
                                           value="<?= $row ? $row->sell_price : '' ?>" placeholder="0">
                                </div>
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i> Harga yang dilihat pelanggan.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-flex justify-content-between p-3">
                    <a href="<?= site_url('master/products') ?>" class="btn btn-default">
                        <i class="fas fa-arrow-left mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save mr-1"></i> Simpan Produk
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>