<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users mr-1"></i> Manajemen User</h3>
        <div class="card-tools">
            <a href="<?= site_url('master/users/add') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Tambah User
            </a>
        </div>
    </div>
    
    <div class="card-body table-responsive p-0">
        <table id="tableUsers" class="table table-hover text-nowrap">
            <thead class="bg-light">
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 30%">Pengguna</th>
                    <th>Kontak</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($users as $u): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px mr-3">
                                <img src="<?= base_url('assets/img/user-placeholder.jpg') ?>" 
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($u->full_name) ?>&background=random'"
                                     class="img-circle elevation-1" 
                                     style="width: 35px; height: 35px; object-fit: cover;" 
                                     alt="User">
                            </div>
                            <div>
                                <span class="font-weight-bold d-block text-dark"><?= $u->full_name ?></span>
                                <small class="text-muted">Terdaftar: <?= date('d M Y', strtotime($u->created_at)) ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <small class="text-muted"><i class="fas fa-envelope mr-1"></i> <?= $u->email ?></small>
                            <small class="text-muted"><i class="fas fa-phone mr-1"></i> <?= $u->phone ?></small>
                        </div>
                    </td>
                    <td>
                        <?php 
                            $badge_color = 'secondary';
                            if($u->role == 'admin') $badge_color = 'danger';
                            elseif($u->role == 'owner') $badge_color = 'primary';
                            elseif($u->role == 'sales') $badge_color = 'info';
                            elseif($u->role == 'driver') $badge_color = 'warning';
                        ?>
                        <span class="badge badge-<?= $badge_color ?> badge-pill px-3"><?= strtoupper($u->role) ?></span>
                    </td>
                    <td>
                        <?php if($u->is_active == 1): ?>
                            <span class="badge badge-light text-success border border-success">
                                <i class="fas fa-circle text-xs mr-1"></i> Aktif
                            </span>
                        <?php else: ?>
                            <span class="badge badge-light text-danger border border-danger">
                                <i class="fas fa-circle text-xs mr-1"></i> Non-Aktif
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <div class="btn-group">
                            <a href="<?= site_url('master/users/edit/'.$u->user_id) ?>" class="btn btn-default btn-sm" title="Edit Data">
                                <i class="fas fa-pencil-alt text-info"></i>
                            </a>

                            <?php if($u->is_active == 1): ?>
                                <a href="<?= site_url('master/users/toggle_status/'.$u->user_id.'/0') ?>" 
                                   class="btn btn-default btn-sm" 
                                   onclick="return confirm('Non-aktifkan user ini? Akses login akan dicabut.')" 
                                   title="Non-aktifkan">
                                    <i class="fas fa-power-off text-danger"></i>
                                </a>
                            <?php else: ?>
                                <a href="<?= site_url('master/users/toggle_status/'.$u->user_id.'/1') ?>" 
                                   class="btn btn-default btn-sm" 
                                   onclick="return confirm('Aktifkan kembali user ini?')" 
                                   title="Aktifkan">
                                    <i class="fas fa-check-circle text-success"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>