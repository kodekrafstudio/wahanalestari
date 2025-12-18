<div class="row">
    <div class="col-12">
        
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    No Faktur: <strong><?= $order->invoice_no ?></strong>
                    <?php 
                        $badges = ['request'=>'secondary', 'preparing'=>'info', 'delivering'=>'warning', 'done'=>'success', 'canceled'=>'dark'];
                        $st = $order->status;
                    ?>
                    <span class="badge badge-<?= $badges[$st] ?> ml-2"><?= strtoupper($st) ?></span>
                </h3>
                <div class="card-tools">
                    <a href="<?= site_url('marketing/sales/print_invoice/'.$order->id) ?>" target="_blank" class="btn btn-default btn-sm mr-1"><i class="fas fa-print"></i> Cetak Invoice</a>
                    <a href="<?= site_url('marketing/sales') ?>" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
            </div>

            <div class="card-body">
                <?php if($order->payment_status == 'refunded'): ?>
                <div class="alert alert-dark">
                    <h5><i class="icon fas fa-info"></i> Informasi Pengembalian Dana (Refund)</h5>
                    Pesanan dibatalkan. Status pembayaran: <strong>REFUNDED</strong>. 
                    Dana dikembalikan: <strong>Rp <?= number_format($order->total_paid, 0, ',', '.') ?></strong>.
                    <br><small>Catatan: <?= $order->note ?></small>
                </div>
                <?php endif; ?>

                <div class="row invoice-info mb-4">
                    <div class="col-sm-4 invoice-col">
                        <strong class="text-primary">Kepada Pelanggan:</strong>
                        <address>
                            <strong><?= $order->customer_name ?></strong><br>
                            <?= $order->address ?><br>
                            <?= $order->city ?><br>
                            Telp: <?= $order->phone ?>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <strong class="text-primary">Detail Order:</strong><br>
                        <b>Tanggal:</b> <?= date('d M Y H:i', strtotime($order->order_date)) ?><br>
                        <b>Salesman:</b> <?= $order->salesman_name ? $order->salesman_name : '-' ?><br>
                        <b>Admin Input:</b> <?= $order->creator_name ?>
                    </div>
                    <div class="col-sm-4 invoice-col text-right">
                        <div class="alert alert-light border">
                            <small class="text-muted text-uppercase">Status Pembayaran</small><br>
                            <?php if($order->payment_status == 'paid'): ?>
                                <h4 class="text-success font-weight-bold">LUNAS</h4>
                            <?php elseif($order->payment_status == 'partial'): ?>
                                <h4 class="text-warning font-weight-bold">CICILAN</h4>
                            <?php elseif($order->payment_status == 'refunded'): ?>
                                <h4 class="text-secondary font-weight-bold">REFUNDED</h4>
                            <?php else: ?>
                                <h4 class="text-danger font-weight-bold">BELUM BAYAR</h4>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="bg-secondary">
                            <tr>
                                <th>No</th> <th>Produk</th> <th class="text-center">Qty</th> <th class="text-right">Harga</th> <th class="text-right">Diskon</th> <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($order->items as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item->product_name ?><br><small><?= $item->unit ?></small></td>
                                <td class="text-center font-weight-bold"><?= $item->qty ?></td>
                                <td class="text-right"><?= number_format($item->price,0,',','.') ?></td>
                                <td class="text-right"><?= number_format($item->discount,0,',','.') ?></td>
                                <td class="text-right"><?= number_format($item->subtotal,0,',','.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr><th>Subtotal:</th><td class="text-right"><?= number_format($order->total_amount,0,',','.') ?></td></tr>
                            <?php if($order->shipping_cost > 0): ?><tr><th>Ongkir:</th><td class="text-right"><?= number_format($order->shipping_cost,0,',','.') ?></td></tr><?php endif; ?>
                            <?php if($order->other_discount > 0): ?><tr><th>Potongan Lain:</th><td class="text-right text-danger"><?= number_format($order->other_discount,0,',','.') ?></td></tr><?php endif; ?>
                            <?php 
                                $grand = ($order->grand_total > 0) ? $order->grand_total : $order->total_amount;
                                $sisa = $grand - $order->total_paid;
                            ?>
                            <tr class="bg-light"><th style="font-size:1.1rem">TOTAL:</th><td class="text-right font-weight-bold" style="font-size:1.1rem"><?= number_format($grand,0,',','.') ?></td></tr>
                            <tr><th>Dibayar:</th><td class="text-right text-success font-weight-bold">(-) <?= number_format($order->total_paid,0,',','.') ?></td></tr>
                            <tr style="border-top:2px solid #ccc"><th class="text-danger">SISA:</th><td class="text-right text-danger font-weight-bold"><?= ($order->status == 'canceled') ? '<span class="badge badge-dark">DIBATALKAN</span>' : number_format($sisa,0,',','.') ?></td></tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4">
                        <h5 class="text-muted"><i class="fas fa-history"></i> Riwayat Pembayaran</h5>
                        <?php if(empty($payments)): ?>
                            <p class="text-muted small">Belum ada pembayaran.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush small">
                                <?php foreach($payments as $p): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Rp <?= number_format($p->amount,0,',','.') ?><br><span class="text-muted"><?= date('d/m/y', strtotime($p->payment_date)) ?></span></span>
                                    <span class="badge badge-light border"><?= $p->payment_method ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card card-outline card-warning">
                            <div class="card-header"><h3 class="card-title text-bold">Update Status</h3></div>
                            <div class="card-body p-3">
                                <button type="button" class="btn btn-warning btn-block btn-sm font-weight-bold" data-toggle="modal" data-target="#modalStatus">
                                    <i class="fas fa-sync-alt"></i> Update Status
                                </button>
                                <div class="mt-2 text-muted" style="font-size: 11px;">
                                    - <b>Delivering:</b> Stok Terpotong.<br>
                                    - <b>Canceled:</b> Stok Kembali & Refund.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <?php if($order->status == 'canceled'): ?>
                            <div class="alert alert-secondary text-center"><strong>Transaksi Batal</strong></div>
                        <?php elseif($sisa > 100): ?>
                            <div class="card card-outline card-success">
                                <div class="card-header"><h3 class="card-title text-bold">Input Pembayaran</h3></div>
                                <div class="card-body p-3">
                                    <form action="<?= site_url('marketing/sales/submit_payment') ?>" method="post">
                                        <input type="hidden" name="sales_order_id" value="<?= $order->id ?>">
                                        <input type="date" name="payment_date" class="form-control form-control-sm mb-2" value="<?= date('Y-m-d') ?>" required>
                                        <input type="number" name="amount" class="form-control mb-2" placeholder="Nominal" max="<?= $sisa ?>" required>
                                        <select name="payment_method" class="form-control form-control-sm mb-2">
                                            <option value="Transfer">Transfer</option><option value="Cash">Cash</option><option value="QRIS">QRIS</option>
                                        </select>
                                        <input type="text" name="note" class="form-control form-control-sm mb-2" placeholder="Catatan">
                                        <button type="submit" class="btn btn-success btn-block btn-sm font-weight-bold">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success text-center"><strong>LUNAS</strong></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalStatus">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Status Pesanan</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= site_url('marketing/sales/update_status') ?>" method="post">
                <input type="hidden" name="order_id" value="<?= $order->id ?>">
                
                <div class="modal-body">
                    <?php if($order->total_paid > 0): ?>
                        <div class="alert alert-warning p-2 small">
                            <i class="fas fa-exclamation-triangle"></i> <b>PERHATIAN:</b> Sudah dibayar <b>Rp <?= number_format($order->total_paid, 0, ',', '.') ?></b>.
                            Jika status <b>Canceled</b>, pembayaran otomatis jadi <b>REFUNDED</b>.
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Status Baru</label>
                        <select name="status" class="form-control">
                            <option value="request" <?= $order->status=='request'?'selected':'' ?>>Request</option>
                            <option value="preparing" <?= $order->status=='preparing'?'selected':'' ?>>Preparing</option>
                            <option value="delivering" <?= $order->status=='delivering'?'selected':'' ?>>Delivering</option>
                            <option value="done" <?= $order->status=='done'?'selected':'' ?>>Done</option>
                            <option value="canceled" <?= $order->status=='canceled'?'selected':'' ?> class="text-danger font-weight-bold">Canceled (Batal/Retur)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Catatan / Alasan</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Wajib diisi jika Cancel/Retur..."></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>