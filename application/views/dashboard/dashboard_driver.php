<div class="row">
    <div class="col-12">
        <div class="info-box bg-gradient-dark">
            <span class="info-box-icon"><i class="fas fa-truck"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Rute Hari Ini: <?= date('d M Y') ?></span>
                <div class="progress">
                    <?php 
                        $percent = ($total_drop > 0) ? ($done_drop / $total_drop) * 100 : 0;
                    ?>
                    <div class="progress-bar bg-success" style="width: <?= $percent ?>%"></div>
                </div>
                <span class="progress-description">
                    Selesai: <b><?= $done_drop ?></b> / <?= $total_drop ?> Titik
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?php if(empty($my_route)): ?>
            <div class="callout callout-danger text-center py-5">
                <i class="fas fa-mug-hot fa-3x text-muted mb-3"></i>
                <h4>Tidak ada jadwal pengiriman hari ini.</h4>
                <p>Silakan standby atau hubungi Admin Logistik.</p>
            </div>
        <?php else: ?>
            
            <div class="card">
                <div class="card-header border-0 bg-light">
                    <h3 class="card-title text-primary"><i class="fas fa-map-signs"></i> Urutan Pengiriman</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-success">MULAI (GUDANG)</span>
                        </div>

                        <?php foreach($my_route->points as $p): ?>
                        <div>
                            <i class="fas fa-store bg-<?= $p->status == 'delivered' ? 'gray' : 'blue' ?>"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> Urutan #<?= $p->sequence_number ?></span>
                                
                                <h3 class="timeline-header">
                                    <a href="#"><?= $p->customer_name ?></a>
                                    <?php if($p->status == 'delivered'): ?>
                                        <span class="badge badge-success ml-2">SELESAI</span>
                                    <?php endif; ?>
                                </h3>

                                <div class="timeline-body">
                                    <?= $p->address ?> <br>
                                    <small class="text-muted"><i class="fas fa-phone"></i> <?= $p->phone ?></small>
                                </div>
                                
                                <div class="timeline-footer">
                                    <?php if($p->status == 'pending'): ?>
                                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode($p->address) ?>" target="_blank" class="btn btn-primary btn-sm">
                                            <i class="fas fa-directions"></i> Navigasi Maps
                                        </a>
                                        <button class="btn btn-success btn-sm float-right">
                                            <i class="fas fa-check"></i> Tandai Sampai
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted text-sm font-italic">Paket telah diterima.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <div>
                            <i class="fas fa-flag-checkered bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>