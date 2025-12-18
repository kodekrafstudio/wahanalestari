<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Omzet (Gross)</span>
                <span class="info-box-number">Rp <?= number_format($summary['omzet'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Uang Masuk (Cash-In)</span>
                <span class="info-box-number">Rp <?= number_format($summary['lunas'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-hand-holding-usd"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Piutang (Unpaid)</span>
                <span class="info-box-number">Rp <?= number_format($summary['piutang'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-chart-pie"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Rasio Bayar</span>
                <span class="info-box-number">
                    <?php 
                        $persen = ($summary['omzet'] > 0) ? ($summary['lunas'] / $summary['omzet']) * 100 : 0;
                        echo number_format($persen, 1) . '%';
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-secondary collapsed-card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Data</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
        </div>
    </div>
    <div class="card-body" style="display: none;">
        <form action="" method="get">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="<?= isset($filter['start_date']) ? $filter['start_date'] : date('Y-m-01') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="<?= isset($filter['end_date']) ? $filter['end_date'] : date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status Order</label>
                        <select name="status" class="form-control">
                            <option value="all">Semua Status</option>
                            <?php 
                            $statuses = ['request','preparing','delivering','done','canceled']; 
                            foreach($statuses as $s): 
                                $selected = (isset($filter['status']) && $filter['status'] == $s) ? 'selected' : '';
                            ?>
                                <option value="<?= $s ?>" <?= $selected ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Tampilkan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Data Transaksi Penjualan</h3>
        <div class="card-tools">
            <a href="<?= site_url('marketing/sales/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Transaksi Baru
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0"> 
        <table class="table table-hover table-striped text-nowrap" id="tableSales">
            <thead>
                <tr>
                    <th style="width: 15%">Invoice / Tgl</th>
                    <th style="width: 20%">Pelanggan</th>
                    <th style="width: 15%" class="text-right">Total Tagihan</th>
                    <th style="width: 15%" class="text-center">Pembayaran</th>
                    <th style="width: 15%" class="text-center">Status Order</th>
                    <th style="width: 20%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($orders) && count($orders) > 0): ?>
                    <?php foreach($orders as $o): ?>
                    <tr>
                        <td>
                            <strong><?= $o->invoice_no ?></strong><br>
                            <small class="text-muted"><i class="far fa-clock"></i> <?= date('d/m/y H:i', strtotime($o->order_date)) ?></small>
                        </td>
                        
                        <td>
                            <span class="font-weight-bold"><?= $o->customer_name ?></span><br>
                            <small class="text-muted"><i class="fas fa-user-tie mr-1"></i> Sales: <?= isset($o->sales_name) ? $o->sales_name : '-' ?></small>
                        </td>

                        <td class="text-right">
                            <?php $grand_total = ($o->grand_total > 0) ? $o->grand_total : $o->total_amount; ?>
                            <span style="font-size: 1.1em;">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                        </td>
                        
                        <td class="text-center">
                            <?php if($o->status == 'canceled' && $o->payment_status != 'refunded'): ?>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-times-circle mr-1"></i> DIBATALKAN
                                </span>

                            <?php elseif($o->payment_status == 'refunded'): ?>
                                <span class="badge badge-secondary"><i class="fas fa-undo mr-1"></i> REFUNDED</span>
                                
                            <?php elseif($o->payment_status == 'paid'): ?>
                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> LUNAS</span>
                                
                            <?php elseif($o->payment_status == 'partial'): ?>
                                <span class="badge badge-warning">CICILAN</span><br>
                                <?php $sisa = $grand_total - $o->total_paid; ?>
                                <small class="text-danger font-weight-bold" style="font-size: 10px;">Sisa: <?= number_format($sisa/1000) ?>k</small>
                                
                            <?php else: ?>
                                <span class="badge badge-danger">BELUM BAYAR</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <?php 
                            $badge = 'secondary';
                            $icon  = 'fa-circle';
                            if($o->status == 'request') { $badge = 'secondary'; $icon = 'fa-clock'; }
                            if($o->status == 'preparing') { $badge = 'info'; $icon = 'fa-box-open'; }
                            if($o->status == 'delivering') { $badge = 'primary'; $icon = 'fa-truck'; }
                            if($o->status == 'done') { $badge = 'success'; $icon = 'fa-check'; }
                            if($o->status == 'canceled') { $badge = 'dark'; $icon = 'fa-ban'; }
                            ?>
                            <span class="badge badge-<?= $badge ?> p-2" style="font-size: 0.9em;">
                                <i class="fas <?= $icon ?> mr-1"></i> <?= ucfirst($o->status) ?>
                            </span>
                        </td>
                        
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="<?= site_url('marketing/sales/detail/'.$o->id) ?>" class="btn btn-info btn-sm" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= site_url('marketing/sales/print_invoice/'.$o->id) ?>" target="_blank" class="btn btn-default btn-sm" title="Cetak Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
    </div> 
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof $ !== 'undefined') {
        $('#tableSales').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false, // Order by ID DESC dari Controller
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json",
                "emptyTable": "Tidak ada data penjualan"
            },
            "columnDefs": [
                { "targets": [5], "orderable": false } // Kolom Aksi tidak bisa diurutkan
            ]
        });
    }
});
</script>