<div class="row">
    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-info"><i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Fisik Barang</span>
                <span class="info-box-number"><?= number_format($summary['total_items']) ?> <small>Unit</small></span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-success"><i class="fas fa-sack-dollar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Valuasi Aset Gudang</span>
                <span class="info-box-number">Rp <?= number_format($summary['asset_value'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Stok Menipis (<100)</span>
                <span class="info-box-number"><?= $summary['low_stock'] ?> <small>Item</small></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header border-0">
                <h3 class="card-title text-bold"><i class="fas fa-clipboard-list mr-1"></i> Posisi Stok Terkini</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle" id="tableStock">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kapasitas / Level</th>
                            <th class="text-center">Sisa Stok</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($stocks as $s): 
                            $qty = $s->quantity ? $s->quantity : 0;
                            
                            // Visualisasi Progress Bar (Asumsi Max 2000 untuk tampilan)
                            $percent = ($qty / 2000) * 100; 
                            if($percent > 100) $percent = 100;
                            
                            // Warna Progress
                            $bg_color = 'bg-success';
                            if($qty < 500) $bg_color = 'bg-warning';
                            if($qty < 100) $bg_color = 'bg-danger';
                        ?>
                        <tr>
                            <td>
                                <span class="font-weight-bold text-dark"><?= $s->name ?></span><br>
                                <small class="text-muted"><?= $s->grade ?> | <?= $s->unit ?></small>
                            </td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar <?= $bg_color ?>" style="width: <?= $percent ?>%"></div>
                                </div>
                                <small class="text-muted">Level: <?= $qty < 100 ? 'Kritis' : ($qty < 500 ? 'Menipis' : 'Aman') ?></small>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $qty < 100 ? 'badge-danger' : 'badge-success' ?>" style="font-size: 1rem;">
                                    <?= number_format($qty, 0, ',', '.') ?>
                                </span>
                            </td>
                            <td class="text-right">
                                <button class="btn btn-default btn-sm btn-adjust" 
                                        data-id="<?= $s->product_id ?>" 
                                        data-name="<?= $s->name ?>" 
                                        data-qty="<?= $qty ?>"
                                        data-toggle="modal" data-target="#modal-adjust">
                                    <i class="fas fa-edit text-primary"></i> Adjust
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title text-bold"><i class="fas fa-history mr-1"></i> Aktivitas Terakhir</h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <?php if(empty($latest_logs)): ?>
                        <li class="item text-center p-4 text-muted">- Belum ada aktivitas -</li>
                    <?php else: ?>
                        <?php foreach($latest_logs as $log): ?>
                        <li class="item">
                            <div class="product-img">
                                <?php if($log->type == 'in'): ?>
                                    <span class="badge badge-success float-left p-2"><i class="fas fa-arrow-down"></i> IN</span>
                                <?php else: ?>
                                    <span class="badge badge-danger float-left p-2"><i class="fas fa-arrow-up"></i> OUT</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info ml-5">
                                <a href="javascript:void(0)" class="product-title" style="font-size: 14px;">
                                    <?= $log->product_name ?>
                                    <span class="float-right badge badge-light border">
                                        <?= $log->type == 'in' ? '+' : '-' ?><?= number_format($log->change_qty) ?>
                                    </span>
                                </a>
                                <span class="product-description" style="white-space: normal; line-height: 1.2;">
                                    <?= $log->description ?>
                                    <br>
                                    <small class="text-muted"><i class="far fa-clock"></i> <?= date('d M H:i', strtotime($log->created_at)) ?> | <?= $log->full_name ?></small>
                                </span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="<?= site_url('inventory/stock/history') ?>" class="uppercase text-muted small">Lihat Semua History</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-adjust">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Stock Opname / Penyesuaian</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('inventory/stock/adjust') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="adj_id">
                    
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" class="form-control bg-light" id="adj_name" readonly>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Stok Sistem</label>
                                <input type="text" class="form-control bg-light" id="adj_sys_qty" readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="text-primary">Stok Fisik (Riil) <span class="text-danger">*</span></label>
                                <input type="number" name="real_qty" class="form-control font-weight-bold border-primary" placeholder="0" required autofocus>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan Penyesuaian</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Alasan: Barang rusak, selisih hitung, reset awal..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Handling Modal Data (Sama seperti sebelumnya)
    $('.btn-adjust').click(function() {
        var id   = $(this).data('id');
        var name = $(this).data('name');
        var qty  = $(this).data('qty');

        $('#adj_id').val(id);
        $('#adj_name').val(name);
        $('#adj_sys_qty').val(qty);
    });

    // 2. DATATABLE RESPONSIVE (LEBIH CANGGIH)
    if (typeof $ !== 'undefined') {
        $('#tableStock').DataTable({
            "responsive": true, // Fitur agar kolom menyusut otomatis di HP
            "autoWidth": false,
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": false,
            "info": true,
            "pageLength": 10,
            "language": {
                "search": "Cari Barang:",
                "zeroRecords": "Tidak ada data ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ barang",
                "infoEmpty": "Tidak ada data",
                "paginate": { "next": ">>", "previous": "<<" }
            }
        });
    }
});
</script>