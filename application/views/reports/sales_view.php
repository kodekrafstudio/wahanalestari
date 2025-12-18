<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-search"></i> Filter Laporan</h3>
    </div>
    <div class="card-body">
        <form action="" method="get">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start" class="form-control" value="<?= $filter['start'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end" class="form-control" value="<?= $filter['end'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status Order</label>
                        <select name="status" class="form-control">
                            <option value="all">Semua Status</option>
                            <?php $statuses = ['request','preparing','delivering','done','canceled']; foreach($statuses as $s): ?>
                                <option value="<?= $s ?>" <?= $filter['status'] == $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="btn-group btn-block">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Tampilkan</button>
                            <button type="submit" name="export" value="excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Hasil Laporan</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tgl Faktur</th>
                        <th>No Invoice</th>
                        <th>Pelanggan</th>
                        <th>Sales</th>
                        <th>Status Order</th>
                        <th>Status Bayar</th>
                        <th>Total Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grand_total = 0;
                    foreach($report as $row): 
                        // Hitung Grand Total (Hanya yang tidak canceled)
                        if($row->status != 'canceled') {
                            $grand_total += $row->total_amount;
                        }
                    ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row->order_date)) ?></td>
                        <td><?= $row->invoice_no ?></td>
                        <td><?= $row->customer_name ?></td>
                        <td><?= $row->sales_name ?></td>
                        <td><?= ucfirst($row->status) ?></td>
                        <td>
                            <?php if($row->payment_status == 'paid'): ?>
                                <span class="badge badge-success">Lunas</span>
                            <?php elseif($row->payment_status == 'partial'): ?>
                                <span class="badge badge-warning">Cicilan</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Unpaid</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">Rp <?= number_format($row->total_amount, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-light font-weight-bold">
                        <td colspan="6" class="text-right">TOTAL OMZET (Periode Ini):</td>
                        <td class="text-right" style="font-size: 1.2em;">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>