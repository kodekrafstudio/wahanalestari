<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />

<div class="row">
    <div class="col-md-4">
        
        <div class="card card-primary card-outline shadow-sm mb-4">
            <div class="card-body box-profile">
                <div class="text-center mb-3">
                    <?php 
                        $initial = strtoupper(substr($row->name, 0, 1)); 
                        // Warna random untuk avatar agar tidak monoton
                        $bg_colors = ['#e3f2fd', '#e8f5e9', '#fff3e0', '#fce4ec'];
                        $text_colors = ['#1565c0', '#2e7d32', '#ef6c00', '#c2185b'];
                        $rand_idx = rand(0, 3);
                    ?>
                    <div class="d-flex align-items-center justify-content-center mx-auto shadow-sm" 
                         style="width: 100px; height: 100px; background: <?= $bg_colors[$rand_idx] ?>; color: <?= $text_colors[$rand_idx] ?>; font-size: 40px; font-weight: bold; border-radius: 50%; border: 4px solid #fff;">
                        <?= $initial ?>
                    </div>
                </div>

                <h3 class="profile-username text-center font-weight-bold"><?= $row->name ?></h3>
                <p class="text-muted text-center mb-4">
                    <span class="badge badge-light border"><?= $row->category_name ?></span>
                </p>

                <ul class="list-group list-group-unbordered mb-4">
                    <li class="list-group-item">
                        <b>Status Akun</b> 
                        <div class="float-right">
                            <?php 
                            $badge = 'secondary';
                            $icon = 'fa-question';
                            if($row->status == 'active') { $badge = 'success'; $icon = 'fa-check-circle'; }
                            if($row->status == 'prospect') { $badge = 'info'; $icon = 'fa-spinner'; }
                            if($row->status == 'blacklist') { $badge = 'danger'; $icon = 'fa-ban'; }
                            ?>
                            <span class="badge badge-<?= $badge ?> p-1 px-2"><i class="fas <?= $icon ?> mr-1"></i> <?= strtoupper($row->status) ?></span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <b>Kontak (CP)</b> <a class="float-right text-dark"><?= $row->contact_person ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>No. Telepon</b> <a class="float-right text-dark"><?= $row->phone ?></a>
                    </li>
                </ul>

                <div class="row">
                    <div class="col-5">
                        <?php 
                            $hp = $row->phone;
                            if(substr($hp, 0, 1) == '0') $hp = '62' . substr($hp, 1);
                        ?>
                        <a href="https://wa.me/<?= $hp ?>" target="_blank" class="btn btn-success btn-block shadow-sm">
                            <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                        </a>
                    </div>
                    <div class="col-3">
                        <a href="<?= site_url('marketing/customers/edit/'.$row->customer_id) ?>" class="btn btn-outline-warning btn-block shadow-sm">
                            <i class="fas fa-pencil-alt mr-1"></i> Edit
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="<?= site_url('marketing/customers') ?>" class="btn btn-outline-primary btn-block shadow-sm">
                            <i class="fas fa-back-alt mr-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-info shadow-sm">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title font-weight-bold"><i class="fas fa-map-marked-alt mr-1"></i> Lokasi Pelanggan</h3>
            </div>
            <div class="card-body">
                <?php if($row->latitude && $row->longitude): ?>
                    <div id="mini-map" class="rounded border mb-3" style="height: 220px; width: 100%;"></div>
                    
                    <div class="alert alert-light border">
                        <small class="text-muted text-uppercase font-weight-bold">Alamat:</small><br>
                        <i class="fas fa-home text-secondary mr-1"></i> <?= $row->address ?><br>
                        <i class="fas fa-city text-secondary mr-1"></i> <?= $row->district ?>, <?= $row->city ?>
                    </div>

                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $row->latitude ?>,<?= $row->longitude ?>" target="_blank" class="btn btn-primary btn-block btn-flat">
                        <i class="fas fa-directions mr-1"></i> Buka Google Maps
                    </a>
                <?php else: ?>
                    <div class="text-center py-5 bg-light rounded border border-dashed">
                        <i class="fas fa-map-slash fa-3x text-muted mb-3"></i><br>
                        <h6 class="text-muted">Koordinat belum disetting.</h6>
                        <a href="<?= site_url('marketing/customers/edit/'.$row->customer_id) ?>" class="btn btn-sm btn-info mt-2">Setting Lokasi</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="col-md-8">
        
        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-bag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Total Transaksi</span>
                        <span class="info-box-number text-dark h5 mb-0">
                            <?= number_format($stats->total_trx) ?> <small class="font-weight-normal">Faktur</small>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-wallet"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Total Belanja</span>
                        <span class="info-box-number text-dark h5 mb-0">Rp <?= number_format($stats->total_amount/1000000, 1) ?> Jt</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning elevation-1"><i class="far fa-clock text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Terakhir Order</span>
                        <span class="info-box-number text-dark h5 mb-0">
                            <?= $stats->last_order ? date('d/m/Y', strtotime($stats->last_order)) : '-' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold"><i class="fas fa-history mr-1"></i> 10 Transaksi Terakhir</h3>
                <a href="<?= site_url('marketing/sales/create?customer_id='.$row->customer_id) ?>" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Order Baru
                </a>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover table-striped table-valign-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th class="text-right">Nominal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($history)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://img.icons8.com/color/48/000000/nothing-found.png" alt="Kosong" style="opacity: 0.5;"><br>
                                    <span class="text-muted">Belum ada riwayat transaksi.</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($history as $h): ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('marketing/sales/detail/'.$h->id) ?>" class="font-weight-bold">
                                        <?= $h->invoice_no ?>
                                    </a>
                                </td>
                                <td class="text-muted">
                                    <i class="far fa-calendar-alt mr-1"></i> <?= date('d M Y', strtotime($h->order_date)) ?>
                                </td>
                                <td class="text-right font-weight-bold text-dark">
                                    Rp <?= number_format($h->total_amount, 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $cl = 'secondary';
                                        $icon = 'fa-circle';
                                        if($h->status == 'done') { $cl = 'success'; $icon = 'fa-check'; }
                                        if($h->status == 'delivering') { $cl = 'primary'; $icon = 'fa-truck'; }
                                        if($h->status == 'canceled') { $cl = 'danger'; $icon = 'fa-times'; }
                                        if($h->status == 'preparing') { $cl = 'warning'; $icon = 'fa-box'; }
                                    ?>
                                    <span class="badge badge-<?= $cl ?> p-1 px-2">
                                        <i class="fas <?= $icon ?>" style="font-size: 10px;"></i> <?= ucfirst($h->status) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('marketing/sales/detail/'.$h->id) ?>" class="btn btn-sm btn-default" title="Lihat Detail">
                                        <i class="fas fa-eye text-info"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    <?php if($row->latitude && $row->longitude): ?>
        var lat = <?= $row->latitude ?>;
        var lng = <?= $row->longitude ?>;
        
        var map = L.map('mini-map', {
            center: [lat, lng],
            zoom: 15,
            zoomControl: false, 
            attributionControl: false,
            dragging: false, // Map statis agar tidak sengaja tergeser
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        
        L.marker([lat, lng]).addTo(map)
            .bindPopup("<b><?= $row->name ?></b>").openPopup();
            
        // Fix render issue inside card
        setTimeout(function(){ map.invalidateSize(); }, 500);
    <?php endif; ?>
});
</script>