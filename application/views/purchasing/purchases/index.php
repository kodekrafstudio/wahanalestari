<div class="row">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3 shadow-sm">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Pembelian</span>
                <span class="info-box-number text-lg">
                    Rp <?= number_format($summary['belanja'], 0, ',', '.') ?>
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3 shadow-sm">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-double"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Sudah Dibayar</span>
                <span class="info-box-number text-lg">
                    Rp <?= number_format($summary['lunas'], 0, ',', '.') ?>
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3 shadow-sm">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file-invoice-dollar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Sisa Hutang Supplier</span>
                <span class="info-box-number text-lg">
                    Rp <?= number_format($summary['hutang'], 0, ',', '.') ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card card-default collapsed-card shadow-sm">
    <div class="card-header border-0">
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
                        <input type="date" name="start_date" class="form-control" value="<?= $filter['start_date'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $filter['end_date'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status PO</label>
                        <select name="status" class="form-control">
                            <option value="all">Semua Status</option>
                            <option value="ordered" <?= $filter['status'] == 'ordered' ? 'selected' : '' ?>>Ordered (Menunggu)</option>
                            <option value="received" <?= $filter['status'] == 'received' ? 'selected' : '' ?>>Received (Diterima)</option>
                            <option value="canceled" <?= $filter['status'] == 'canceled' ? 'selected' : '' ?>>Canceled (Batal)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header border-0">
        <h3 class="card-title text-bold">Daftar Purchase Order (PO)</h3>
        <div class="card-tools">
            <a href="<?= site_url('purchasing/purchases/create') ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="fas fa-plus mr-1"></i> Buat PO Baru
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped text-nowrap" id="tablePO">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 15%">No PO / Tanggal</th>
                        <th style="width: 25%">Supplier</th>
                        <th style="width: 15%" class="text-right">Total Biaya</th>
                        <th style="width: 15%" class="text-center">Status Barang</th>
                        <th style="width: 15%" class="text-center">Pembayaran</th>
                        <th style="width: 15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($purchases)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Tidak ada data pembelian sesuai filter.</td></tr>
                    <?php else: ?>
                        <?php foreach($purchases as $row): ?>
                        <tr>
                            <td class="align-middle">
                                <span class="text-primary font-weight-bold"><?= $row->purchase_no ?></span><br>
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt mr-1"></i> <?= date('d M Y', strtotime($row->purchase_date)) ?>
                                </small>
                            </td>
                            
                            <td class="align-middle">
                                <div class="user-block">
                                    <span class="username ml-0" style="font-size: 14px;">
                                        <a href="#"><?= $row->supplier_name ?></a>
                                    </span>
                                    <span class="description ml-0">
                                        <i class="fas fa-box mr-1"></i> <?= $row->item_count ?> Item Barang
                                    </span>
                                </div>
                            </td>

                            <td class="align-middle text-right">
                                <h6 class="font-weight-bold mb-0 text-dark">Rp <?= number_format($row->total_cost, 0, ',', '.') ?></h6>
                            </td>
                            
                            <td class="align-middle text-center">
                                <?php 
                                    if($row->status == 'ordered') { 
                                        echo '<span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> ORDERED</span>';
                                    } elseif($row->status == 'received') { 
                                        echo '<span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> RECEIVED</span>';
                                    } elseif($row->status == 'canceled') { 
                                        echo '<span class="badge badge-dark px-2 py-1"><i class="fas fa-ban mr-1"></i> CANCELED</span>';
                                    }
                                ?>
                            </td>

                            <td class="align-middle text-center">
                                <?php 
                                    // Hitung sisa hutang
                                    $sisa = $row->total_cost - $row->total_paid;
                                    
                                    if($row->status == 'canceled') {
                                        echo '<span class="text-muted small"><i>Dibatalkan</i></span>';
                                    } elseif($row->payment_status == 'paid' || $sisa <= 100) {
                                        echo '<span class="badge badge-light text-success border border-success px-2"><i class="fas fa-check-double mr-1"></i> LUNAS</span>';
                                    } else {
                                        echo '<span class="badge badge-light text-danger border border-danger px-2 mb-1">HUTANG</span><br>';
                                        echo '<small class="text-danger font-weight-bold">Sisa: Rp '.number_format($sisa/1000, 0).'k</small>';
                                    }
                                ?>
                            </td>

                            <td class="align-middle text-center">
                                <div class="btn-group">
                                    <a href="<?= site_url('purchasing/purchases/detail/'.$row->purchase_id) ?>" 
                                       class="btn btn-info btn-sm" 
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if($row->status != 'received'): ?>
                                        <a href="<?= site_url('purchasing/purchases/delete/'.$row->purchase_id) ?>" 
                                           class="btn btn-default btn-sm text-danger"
                                           onclick="return confirm('Yakin ingin menghapus PO ini? Data yang dihapus tidak bisa dikembalikan.')"
                                           title="Hapus PO">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> 
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof $ !== 'undefined') {
        $('#tablePO').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false, // Matikan sorting default agar sesuai urutan Controller (DESC)
            "info": true,
            "autoWidth": false,
            "responsive": false, 
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json",
                "emptyTable": "Tidak ada data pembelian"
            }
        });
    }
});
</script>