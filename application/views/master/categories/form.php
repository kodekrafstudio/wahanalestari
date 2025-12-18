<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit mr-1"></i> <?= $title ?>
                </h3>
            </div>
            
            <form action="" method="post">
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Nama Kategori <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            </div>
                            <input type="text" name="category_name" class="form-control" 
                                   value="<?= $row ? $row->category_name : set_value('category_name') ?>" 
                                   placeholder="Contoh: Garam Industri" required>
                        </div>
                        <?= form_error('category_name', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Singkat</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                            </div>
                            <textarea name="description" class="form-control" rows="4" 
                                      placeholder="Penjelasan singkat tentang kategori ini..."><?= $row ? $row->description : set_value('description') ?></textarea>
                        </div>
                    </div>

                </div>
                
                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= site_url('master/categories') ?>" class="btn btn-default">
                        <i class="fas fa-arrow-left mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>