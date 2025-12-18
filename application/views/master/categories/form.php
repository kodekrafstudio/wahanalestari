<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?= $title ?></h3>
    </div>
    <form action="" method="post">
        <div class="card-body">
            <div class="form-group">
                <label>Nama Kategori *</label>
                <input type="text" name="category_name" class="form-control" value="<?= $row ? $row->category_name : set_value('category_name') ?>" required>
                <?= form_error('category_name', '<small class="text-danger">', '</small>') ?>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" class="form-control" rows="3"><?= $row ? $row->description : set_value('description') ?></textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('master/categories') ?>" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>