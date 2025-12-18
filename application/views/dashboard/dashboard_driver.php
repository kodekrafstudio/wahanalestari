<div class="row">
    <div class="col-12">
        <div class="callout callout-info">
            <h5><i class="fas fa-user-circle"></i> Halo, <?= $this->session->userdata('full_name') ?>!</h5>
            <p>Tetap hati-hati di jalan dan cek kondisi kendaraan.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-map-marker-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Titik</span>
                <span class="info-box-number"><?= $total_drop ?> Lokasi</span>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Selesai</span>
                <span class="info-box-number"><?= $done_drop ?> Lokasi</span>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-truck"></i> Rute Hari Ini (<?= date('d M Y') ?>)
        </h3>
    </div>
    <div class="card-body text-center">
        <?php if($my_route): ?>
            <h3 class="text-primary mb-3">
                <?= $done_drop ?> / <?= $total_drop ?> Selesai
            </h3>
            <div class="progress mb-4" style="height: 20px;">
                <?php $persen = ($total_drop > 0) ? ($done_drop/$total_drop)*100 : 0; ?>
                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: <?= $persen ?>%"></div>
            </div>
            
            <p class="text-muted">Kendaraan: <b><?= $my_route->vehicle ?></b></p>
            
            <a href="<?= site_url('logistics/routes/view/'.$my_route->route_id) ?>" class="btn btn-lg btn-primary btn-block">
                <i class="fas fa-play"></i> LIHAT DAFTAR PENGIRIMAN
            </a>
        <?php else: ?>
            <div class="py-5">
                <i class="fas fa-coffee fa-4x text-gray mb-3"></i>
                <h4>Tidak ada jadwal pengiriman hari ini.</h4>
                <p>Silakan istirahat atau hubungi admin logistik.</p>
            </div>
        <?php endif; ?>
    </div>
</div>