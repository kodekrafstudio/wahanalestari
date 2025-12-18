<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-truck mr-1"></i> <?= $title ?>
                </h3>
            </div>
            
            <form action="" method="post">
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Nama Supplier / Perusahaan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                            </div>
                            <input type="text" name="supplier_name" class="form-control" 
                                   value="<?= $row ? $row->supplier_name : '' ?>" 
                                   placeholder="Contoh: PT. Sumber Makmur" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama PIC (Sales)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                    </div>
                                    <input type="text" name="pic_name" class="form-control" 
                                           value="<?= $row ? $row->pic_name : '' ?>" 
                                           placeholder="Nama sales/kontak">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No Telepon / WhatsApp <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" name="phone" class="form-control" 
                                           value="<?= $row ? $row->phone : '' ?>" 
                                           placeholder="08..." required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            </div>
                            <textarea name="address" class="form-control" rows="3" 
                                      placeholder="Jalan, Kota, Provinsi..."><?= $row ? $row->address : '' ?></textarea>
                        </div>
                    </div>

                </div>
                
                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= site_url('master/suppliers') ?>" class="btn btn-default">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>