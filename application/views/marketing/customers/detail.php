<div class="row">
    <div class="col-md-4">
        
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <?php 
                        // Avatar Inisial Besar
                        $initial = strtoupper(substr($row->name, 0, 1)); 
                    ?>
                    <div style="width: 80px; height: 80px; background: #e9ecef; color: #007bff; font-size: 32px; font-weight: bold; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 3px solid #cce5ff;">
                        <?= $initial ?>
                    </div>
                </div>

                <h3 class="profile-username text-center mt-3"><?= $row->name ?></h3>
                <p class="text-muted text-center"><?= $row->category_name ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Status</b> 
                        <a class="float-right">
                            <?php 
                            $badge = 'secondary';
                            if($row->status == 'active') $badge = 'success';
                            if($row->status == 'prospect') $badge = 'info';
                            if($row->status == 'blacklist') $badge = 'danger';
                            ?>
                            <span class="badge badge-<?= $badge ?>"><?= strtoupper($row->status) ?></span>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <b>CP</b> <a class="float-right"><?= $row->contact_person ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Phone</b> <a class="float-right"><?= $row->phone ?></a>
                    </li>
                </ul>

                <?php 
                    // Format HP untuk WA
                    $hp = $row->phone;
                    if(substr($hp, 0, 1) == '0') $hp = '62' . substr($hp, 1);
                ?>
                <a href="https://wa.me/<?= $hp ?>" target="_blank" class="btn btn-success btn-block">
                    <i class="fab fa-whatsapp"></i> Chat WhatsApp
                </a>
                <a href="<?= site_url('marketing/customers/edit/'.$row->customer_id) ?>" class="btn btn-warning btn-block">
                    <i class="fas fa-pencil-alt"></i> Edit Profil
                </a>
            </div>
        </div>

        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Lokasi</h3>
            </div>
            <div class="card-body p-0">
                <?php if($row->latitude && $row->longitude): ?>
                    <div id="mini-map" style="height: 250px; width: 100%;"></div>
                    <div class="p-2 bg-light text-center small">
                        <?= $row->address ?><br>
                        <?= $row->district ?>, <?= $row->city ?>
                    </div>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $row->latitude ?>,<?= $row->longitude ?>" target="_blank" class="btn btn-default btn-block btn-sm btn-flat">
                        <i class="fas fa-directions text-primary"></i> Navigasi Google Maps
                    </a>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-map-slash fa-3x mb-2"></i><br>
                        Koordinat belum disetting.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="col-md-8">
        
        <div class="row">
            <div class="col-sm-4">
                <div class="info-box bg-light">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Transaksi</span>
                        <span class="info-box-number">
                            <?= number_format($stats->total_trx) ?>
                            <small>Faktur</small>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="info-box bg-light">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-wallet"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Belanja</span>
                        <span class="info-box-number">Rp <?= number_format($stats->total_amount/1000000, 1) ?> Jt</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="info-box bg-light">
                    <span class="info-box-icon bg-warning elevation-1"><i class="far fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Terakhir Beli</span>
                        <span class="info-box-number" style="font-size: 14px;">
                            <?= $stats->last_order ? date('d M Y', strtotime($stats->last_order)) : '-' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header p-2">
                <h3 class="card-title p-2"><i class="fas fa-history mr-1"></i> Riwayat Pembelian (10 Terakhir)</h3>
                <div class="card-tools">
                    <a href="<?= site_url('marketing/sales/create') ?>" class="btn btn-sm btn-primary mt-1">
                        <i class="fas fa-plus"></i> Order Baru
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Tanggal</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($history)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat transaksi.</td></tr>
                            <?php else: ?>
                                <?php foreach($history as $h): ?>
                                <tr>
                                    <td><a href="<?= site_url('marketing/sales/detail/'.$h->id) ?>"><?= $h->invoice_no ?></a></td>
                                    <td><?= date('d M Y', strtotime($h->order_date)) ?></td>
                                    <td class="text-right font-weight-bold">Rp <?= number_format($h->total_amount, 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?php 
                                            $cl = 'secondary';
                                            if($h->status == 'done') $cl = 'success';
                                            if($h->status == 'delivering') $cl = 'primary';
                                            if($h->status == 'canceled') $cl = 'danger';
                                        ?>
                                        <span class="badge badge-<?= $cl ?>"><?= ucfirst($h->status) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('marketing/sales/detail/'.$h->id) ?>" class="btn btn-xs btn-default"><i class="fas fa-eye"></i></a>
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
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    <?php if($row->latitude && $row->longitude): ?>
        var lat = <?= $row->latitude ?>;
        var lng = <?= $row->longitude ?>;
        
        var map = L.map('mini-map', {
            center: [lat, lng],
            zoom: 15,
            zoomControl: false, // Tampilan minimalis
            attributionControl: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        
        L.marker([lat, lng]).addTo(map)
            .bindPopup("<b><?= $row->name ?></b>").openPopup();
    <?php endif; ?>
});
</script>