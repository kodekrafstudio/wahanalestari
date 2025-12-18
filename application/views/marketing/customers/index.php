<style>
    /* Custom Style untuk Avatar Inisial */
    .avatar-initial {
        width: 40px; height: 40px;
        background-color: #e9ecef;
        color: #495057;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 16px;
        margin-right: 10px;
    }
    
    /* Tabel Modern: Padding lebih lega */
    .table-modern td { vertical-align: middle !important; padding: 15px 10px; }
    
    /* Tombol Aksi Minimalis */
    .btn-icon { color: #6c757d; font-size: 16px; margin: 0 5px; transition: 0.2s; }
    .btn-icon:hover { color: #007bff; transform: scale(1.1); }
    .btn-icon.delete:hover { color: #dc3545; }
</style>

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header border-0">
        <h3 class="card-title text-dark font-weight-bold">
            <i class="fas fa-users text-primary mr-2"></i> Database Pelanggan
        </h3>
        <div class="card-tools">
            <a href="<?= site_url('marketing/customers/add') ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="fas fa-plus"></i> Tambah Baru
            </a>
            <a href="<?= site_url('marketing/customers/map') ?>" class="btn btn-default btn-sm rounded-pill px-3 shadow-sm ml-1">
                <i class="fas fa-map-marked-alt text-info"></i> Lihat Peta
            </a>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern text-nowrap" id="tableCust">
                <thead class="bg-light text-muted text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                    <tr>
                        <th style="width: 30%; padding-left: 20px;">Pelanggan</th>
                        <th style="width: 20%">Kontak</th>
                        <th style="width: 20%">Lokasi</th>
                        <th style="width: 15%">Total Belanja</th>
                        <th style="width: 10%" class="text-center">Status</th>
                        <th style="width: 5%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($customers as $c): ?>
                    <tr>
                        <td style="padding-left: 20px;">
                            <div class="d-flex align-items-center">
                                <?php 
                                    // Ambil Inisial Huruf Depan
                                    $initial = strtoupper(substr($c->name, 0, 1)); 
                                    $bg_colors = ['#e3f2fd', '#f3e5f5', '#e8f5e9', '#fff3e0', '#ffebee'];
                                    $text_colors = ['#1976d2', '#7b1fa2', '#388e3c', '#f57c00', '#d32f2f'];
                                    $rand = rand(0, 4);
                                ?>
                                <div class="avatar-initial" style="background: <?= $bg_colors[$rand] ?>; color: <?= $text_colors[$rand] ?>;">
                                    <?= $initial ?>
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark"><?= $c->name ?></div>
                                    <small class="text-muted"><?= $c->category_name ?></small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <?php 
                                $hp = $c->phone;
                                if(substr($hp, 0, 1) == '0') $hp = '62' . substr($hp, 1);
                            ?>
                            <div class="d-flex flex-column">
                                <span><?= $c->contact_person ?></span>
                                <a href="https://wa.me/<?= $hp ?>" target="_blank" class="text-success small font-weight-bold mt-1">
                                    <i class="fab fa-whatsapp"></i> <?= $c->phone ?>
                                </a>
                            </div>
                        </td>

                        <td>
                            <div class="text-truncate" style="max-width: 150px;" title="<?= $c->address ?>">
                                <i class="fas fa-map-marker-alt text-secondary mr-1"></i> <?= $c->district ?>, <?= $c->city ?>
                            </div>
                            <?php if($c->latitude && $c->longitude): ?>
                                <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $c->latitude ?>,<?= $c->longitude ?>" target="_blank" class="text-primary small ml-4">
                                    <i class="fas fa-directions"></i> Rute
                                </a>
                            <?php endif; ?>
                        </td>

                        <td>
                            <h6 class="mb-0 font-weight-bold text-dark">Rp <?= number_format($c->total_spent/1000, 0) ?>k</h6>
                            <small class="text-muted">
                                <?php if($c->last_order): ?>
                                    <?php 
                                        $days = (strtotime(date('Y-m-d')) - strtotime(date('Y-m-d', strtotime($c->last_order)))) / (60 * 60 * 24);
                                        if($days > 30) echo "<span class='text-danger'>● Absen $days hari</span>";
                                        else echo "<span class='text-success'>● $days hari lalu</span>";
                                    ?>
                                <?php else: ?>
                                    <span class="text-secondary">- Baru -</span>
                                <?php endif; ?>
                            </small>
                        </td>

                        <td class="text-center">
                            <?php 
                            $badge = 'secondary';
                            if($c->status == 'active') $badge = 'success';
                            if($c->status == 'prospect') $badge = 'info';
                            if($c->status == 'blacklist') $badge = 'danger';
                            ?>
                            <span class="badge badge-<?= $badge ?> rounded-pill px-3 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">
                                <?= $c->status ?>
                            </span>
                        </td>

                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="<?= site_url('marketing/customers/detail/'.$c->customer_id) ?>">
                                        <i class="fas fa-pencil-alt text-info mr-2"></i> Detail
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="return confirm('Hapus pelanggan ini?') ? window.location.href='<?= site_url('marketing/customers/delete/'.$c->customer_id) ?>' : false;">
                                        <i class="fas fa-trash text-danger mr-2"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof $ !== 'undefined') {
        $('#tableCust').DataTable({
            "responsive": false, 
            "autoWidth": false,
            "order": [[ 3, "desc" ]], 
            "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json" },
            // Styling Pagination agar rapi
            "dom": '<"row"<"col-sm-6"f><"col-sm-6 text-right"l>>t<"row"<"col-sm-6"i><"col-sm-6"p>>'
        });
    }
});
</script>