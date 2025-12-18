<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-search mr-1"></i> Filter Stok</h3>
    </div>
    <div class="card-body">
        <form action="" method="get">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start" class="form-control" 
                               value="<?= isset($filter['start']) ? $filter['start'] : date('Y-m-01') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end" class="form-control" 
                               value="<?= isset($filter['end']) ? $filter['end'] : date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Produk</label>
                        <select name="product_id" class="form-control select2">
                            <option value="all">Semua Produk</option>
                            <?php foreach($products as $p): ?>
                                <option value="<?= $p->product_id ?>" 
                                    <?= (isset($filter['product_id']) && $filter['product_id'] == $p->product_id) ? 'selected' : '' ?>>
                                    <?= $p->name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="btn-group btn-block">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter mr-1"></i> Tampilkan
                            </button>
                            <button type="submit" name="export" value="excel" class="btn btn-success">
                                <i class="fas fa-file-excel mr-1"></i> Export Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history mr-1"></i> Riwayat Keluar Masuk Barang</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tableStockLog" class="table table-bordered table-striped table-hover">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Waktu</th>
                        <th>Nama Produk</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-right">Jumlah</th>
                        <th>Keterangan</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    if(!empty($report)): 
                        foreach($report as $row): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row->created_at)) ?></td>
                        <td>
                            <span class="font-weight-bold"><?= $row->product_name ?></span>
                            <br><small class="text-muted"><?= isset($row->unit) ? $row->unit : '' ?></small>
                        </td>
                        <td class="text-center">
                            <?php if($row->type == 'in'): ?>
                                <span class="badge badge-success px-3">MASUK (IN)</span>
                            <?php else: ?>
                                <span class="badge badge-danger px-3">KELUAR (OUT)</span>
                            <?php endif; ?>
                        </td>
                        <td class="font-weight-bold text-right">
                            <?= number_format($row->change_qty, 0, ',', '.') ?>
                        </td>
                        <td><?= $row->description ?></td>
                        <td><small class="badge badge-light"><?= $row->user_name ?></small></td>
                    </tr>
                    <?php 
                        endforeach; 
                    else:
                    ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-search mb-2"></i><br>Tidak ada data ditemukan.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Init Select2
        $('.select2').select2({ theme: 'bootstrap4' });
        
        // Init DataTables (jika pakai)
        $('#tableStockLog').DataTable({
            "responsive": true,
            "autoWidth": false,
            "order": [[ 1, "desc" ]] // Urutkan berdasarkan Waktu (Kolom ke-1) Descending
        });
    });
</script>