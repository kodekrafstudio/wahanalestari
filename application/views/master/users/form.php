<form action="" method="post">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-primary card-outline h-100">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-id-card mr-1"></i> Data Identitas</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" name="full_name" class="form-control" 
                                   value="<?= set_value('full_name', $row ? $row->full_name : '') ?>" 
                                   placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <?= form_error('full_name', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>

                    <div class="form-group">
                        <label>No HP / WhatsApp</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            </div>
                            <input type="text" name="phone" class="form-control" 
                                   value="<?= set_value('phone', $row ? $row->phone : '') ?>"
                                   placeholder="Contoh: 0812...">
                        </div>
                        <?= form_error('phone', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle"></i> Pastikan No HP aktif untuk menerima notifikasi sistem (jika fitur diaktifkan).
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-warning card-outline h-100">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-lock mr-1"></i> Akun Login</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Email Login <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= set_value('email', $row ? $row->email : '') ?>" 
                                   placeholder="user@perusahaan.com" required>
                        </div>
                        <?= form_error('email', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>

                    <div class="form-group">
                        <label>Role (Hak Akses) <span class="text-danger">*</span></label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Pilih Akses --</option>
                            <?php 
                            $roles = [
                                'owner'  => 'Owner (Akses Penuh)',
                                'admin'  => 'Admin (Kelola Data)',
                                'sales'  => 'Sales (Order & CRM)',
                                'gudang' => 'Gudang (Stok & Barang)',
                                'driver' => 'Driver (Pengiriman)'
                            ];
                            foreach($roles as $val => $label): 
                                $selected = set_select('role', $val, ($row && $row->role == $val)); 
                            ?>
                            <option value="<?= $val ?>" <?= $selected ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('role', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>

                    <div class="form-group mt-4">
                        <label>Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="<?= $row ? 'Isi hanya jika ingin ganti password' : 'Wajib diisi untuk user baru' ?>">
                        </div>
                        <?php if($row): ?>
                            <small class="text-muted text-xs">*Biarkan kosong jika tidak diubah.</small>
                        <?php endif; ?>
                        <?= form_error('password', '<small class="text-danger pl-1">', '</small>') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-3 mb-4">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <a href="<?= site_url('master/users') ?>" class="btn btn-default">
                        <i class="fas fa-arrow-left mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save mr-1"></i> Simpan Data User
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>