<div class="card card-primary">
    <div class="card-header"><h3 class="card-title"><?= $title ?></h3></div>
    <form action="" method="post">
        <div class="card-body">
            <div class="form-group">
                <label>Nama Supplier / Perusahaan</label>
                <input type="text" name="supplier_name" class="form-control" value="<?= $row ? $row->supplier_name : '' ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama PIC (Sales)</label>
                        <input type="text" name="pic_name" class="form-control" value="<?= $row ? $row->pic_name : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>No Telepon / WA</label>
                        <input type="text" name="phone" class="form-control" value="<?= $row ? $row->phone : '' ?>" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="address" class="form-control" rows="2"><?= $row ? $row->address : '' ?></textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('master/suppliers') ?>" class="btn btn-default">Kembali</a>
        </div>
    </form>
</div>