<div class="row">
    <div class="col-md-6 offset-md-3">
        
        <a href="<?= site_url('master/users') ?>" class="btn btn-default btn-sm mb-3">
            <i class="fas fa-arrow-left"></i> Kembali ke List
        </a>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?= $title ?></h3>
            </div>
            <form action="" method="post">
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?= set_value('full_name', $row ? $row->full_name : '') ?>" 
                               placeholder="Contoh: Budi Santoso" required>
                        <?= form_error('full_name', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>

                    <div class="form-group">
                        <label>Email (Untuk Login) *</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= set_value('email', $row ? $row->email : '') ?>" 
                               placeholder="user@iwl.com" required>
                        <?= form_error('email', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>

                    <div class="form-group">
                        <label>No HP / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" 
                               value="<?= set_value('phone', $row ? $row->phone : '') ?>"
                               placeholder="0812...">
                        <?= form_error('phone', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>

                    <div class="form-group">
                        <label>Role (Hak Akses) *</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <?php 
                            $roles = ['owner','admin','sales','gudang','driver'];
                            foreach($roles as $r): 
                                // Logika Select: Cek inputan user dulu, baru cek data DB
                                $selected = set_select('role', $r, ($row && $row->role == $r)); 
                            ?>
                            <option value="<?= $r ?>" <?= $selected ?>>
                                <?= ucfirst($r) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Info Akses:
                            <ul class="mb-0 pl-3">
                                <li><b>Sales:</b> Order & CRM.</li>
                                <li><b>Gudang:</b> Stok & Pembelian.</li>
                                <li><b>Driver:</b> Hanya lihat rute.</li>
                                <li><b>Admin/Owner:</b> Akses Penuh.</li>
                            </ul>
                        </small>
                        <?= form_error('role', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label>Password Login</label>
                        <input type="password" name="password" class="form-control" 
                               placeholder="<?= $row ? 'Kosongkan jika tidak ingin mengganti password' : 'Wajib diisi (Min 5 karakter)' ?>">
                        <?php if($row): ?>
                            <small class="text-muted">*Biarkan kosong jika password tidak ingin diubah.</small>
                        <?php endif; ?>
                        <?= form_error('password', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>