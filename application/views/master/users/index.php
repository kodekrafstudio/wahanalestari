<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Daftar Pengguna Sistem</h3>
        <div class="card-tools">
            <a href="<?= site_url('master/users/add') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Tambah User
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped projects">
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Kontak (Email/HP)</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td>
                        <span class="font-weight-bold"><?= $u->full_name ?></span>
                        <?php if($u->is_active == 0): ?>
                            <span class="badge badge-danger ml-2">NON-AKTIF</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small><i class="fas fa-envelope"></i> <?= $u->email ?></small><br>
                        <small><i class="fas fa-phone"></i> <?= $u->phone ?></small>
                    </td>
                    <td>
                        <span class="badge badge-info"><?= strtoupper($u->role) ?></span>
                    </td>
                    <td><small><?= date('d M Y', strtotime($u->created_at)) ?></small></td>
                    <td>
                        <a href="<?= site_url('master/users/edit/'.$u->user_id) ?>" class="btn btn-info btn-xs" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                        <?php if($u->is_active == 1): ?>
                            <a href="<?= site_url('master/users/toggle_status/'.$u->user_id.'/0') ?>" 
                               class="btn btn-danger btn-xs" 
                               onclick="return confirm('Non-aktifkan user ini? Dia tidak akan bisa login lagi.')" title="Non-aktifkan">
                                <i class="fas fa-power-off"></i>
                            </a>
                        <?php else: ?>
                            <a href="<?= site_url('master/users/toggle_status/'.$u->user_id.'/1') ?>" 
                               class="btn btn-success btn-xs" 
                               onclick="return confirm('Aktifkan kembali user ini?')" title="Aktifkan">
                                <i class="fas fa-check"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>