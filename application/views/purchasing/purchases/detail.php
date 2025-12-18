<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-info">
            <div class="card-header">
    <h3 class="card-title">
        No PO: <strong><?= $po->purchase_no ?></strong>
        
        <?php if($po->status == 'ordered'): ?>
            <span class="badge badge-warning ml-2">ORDERED</span>
        <?php elseif($po->status == 'received'): ?>
            <span class="badge badge-success ml-2">RECEIVED</span>
        <?php else: ?>
            <span class="badge badge-dark ml-2">CANCELED</span>
        <?php endif; ?>
    </h3>

    <div class="card-tools">
        <a href="<?= site_url('purchasing/purchases/print_po/'.$po->purchase_id) ?>" target="_blank" class="btn btn-default btn-sm mr-1">
            <i class="fas fa-print"></i> Cetak PO
        </a>
        
        <a href="<?= site_url('purchasing/purchases') ?>" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-4">
                        <label>Supplier:</label><br>
                        <strong><?= $po->supplier_name ?></strong><br>
                        <?= $po->address ?><br>
                        Telp: <?= $po->phone ?>
                    </div>
                    <div class="col-sm-4">
                        <label>Tanggal PO:</label><br>
                        <?= date('d M Y', strtotime($po->purchase_date)) ?><br>
                        <br>
                        <label>Status Pembayaran:</label><br>
                        <?php if($po->payment_status=='paid'): ?>
                            <span class="badge badge-success">LUNAS</span>
                        <?php elseif($po->payment_status=='partial'): ?>
                            <span class="badge badge-warning">CICILAN</span>
                        <?php else: ?>
                            <span class="badge badge-danger">BELUM BAYAR</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-4 text-right">
                        <?php if($po->status == 'ordered'): ?>
                            <a href="<?= site_url('purchasing/purchases/receive/'.$po->purchase_id) ?>" 
                               class="btn btn-success mb-2 w-100" 
                               onclick="return confirm('Barang sudah sampai gudang? Stok akan bertambah otomatis.')">
                                <i class="fas fa-box-open"></i> Terima Barang (Receive)
                            </a>
                            <a href="<?= site_url('purchasing/purchases/cancel/'.$po->purchase_id) ?>" 
                               class="btn btn-danger w-100"
                               onclick="return confirm('Yakin batalkan PO ini?')">
                                <i class="fas fa-times"></i> Batalkan PO
                            </a>
                        <?php elseif($po->status == 'received'): ?>
                            <div class="alert alert-success text-center">
                                <i class="fas fa-check-circle"></i> Barang Diterima pada:<br>
                                <strong><?= date('d M Y H:i', strtotime($po->received_date)) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Harga Beli</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $i): ?>
                        <tr>
                            <td>
                                <?= $i->product_name ?>
                                <br><small class="text-muted"><?= $i->unit ?></small>
                            </td>
                            <td class="text-center"><?= number_format($i->qty, 0, ',', '.') ?></td>
                            <td class="text-right">
                                Rp <?= number_format($i->cost, 0, ',', '.') ?>
                            </td>
                            <td class="text-right">
                                <?php 
                                    $subtotal_view = $i->qty * $i->cost; 
                                ?>
                                Rp <?= number_format($subtotal_view, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total Tagihan</th>
                            <th class="text-right text-lg">Rp <?= number_format($po->total_cost, 0, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
                
                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Riwayat Pembayaran</h5>
                        <table class="table table-sm">
                            <?php foreach($payments as $pay): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($pay->payment_date)) ?></td>
                                <td><?= $pay->payment_method ?></td>
                                <td class="text-right">Rp <?= number_format($pay->amount, 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <?php if($po->status == 'canceled'): ?>
                            <div class="alert alert-secondary text-center">
                                <i class="fas fa-ban"></i><br>
                                <strong>PO DIBATALKAN</strong><br>
                                Tidak ada tagihan yang perlu dibayar.
                            </div>
                        
                        <?php elseif(($po->total_cost - $po->total_paid) > 0): ?>
                            <div class="card bg-light">
                                <div class="card-body pt-2 pb-2">
                                    <h6>Input Pembayaran Baru</h6>
                                    <form action="<?= site_url('purchasing/purchases/submit_payment') ?>" method="post">
                                        <input type="hidden" name="purchase_id" value="<?= $po->purchase_id ?>">
                                        <div class="form-group mb-2">
                                            <input type="date" name="payment_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                                        </div>
                                        <div class="form-group mb-2">
                                            <input type="number" name="amount" class="form-control form-control-sm" placeholder="Nominal" max="<?= $po->total_cost - $po->total_paid ?>" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <select name="payment_method" class="form-control form-control-sm">
                                                <option value="Cash">Cash</option>
                                                <option value="Transfer">Transfer</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-2">
                                            <input type="text" name="note" class="form-control form-control-sm" placeholder="Catatan">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm w-100">Simpan Pembayaran</button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success text-center">
                                <i class="fas fa-check-circle"></i><br>
                                <strong>LUNAS</strong><br>
                                Semua tagihan sudah terbayar.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>