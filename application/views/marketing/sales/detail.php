<div class="row">
    <div class="col-12">
        
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    No Faktur: <strong><?= $order->invoice_no ?></strong>
                    
                    <?php 
                        $badges = [
                            'request'    => 'secondary',
                            'preparing'  => 'info',
                            'delivering' => 'warning',
                            'done'       => 'success',
                            'canceled'   => 'dark'
                        ];
                        $labels = [
                            'request'    => 'REQUEST (Pesanan Masuk)',
                            'preparing'  => 'PREPARING (Disiapkan)',
                            'delivering' => 'DELIVERING (Dikirim)',
                            'done'       => 'DONE (Selesai)',
                            'canceled'   => 'CANCELED (Batal)'
                        ];
                        $st = $order->status;
                    ?>
                    <span class="badge badge-<?= $badges[$st] ?> ml-2"><?= $labels[$st] ?></span>
                </h3>

                <div class="card-tools">
                    <a href="<?= site_url('marketing/sales/print_invoice/'.$order->id) ?>" target="_blank" class="btn btn-default btn-sm mr-1">
                        <i class="fas fa-print"></i> Cetak Invoice
                    </a>
                    <a href="<?= site_url('marketing/sales') ?>" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body">
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
                                <h4 class="text-success font-weight-bold"><i class="fas fa-check-double"></i> LUNAS</h4>
                            <?php elseif($order->payment_status == 'partial'): ?>
                                <h4 class="text-warning font-weight-bold"><i class="fas fa-hourglass-half"></i> CICILAN</h4>
                            <?php else: ?>
                                <h4 class="text-danger font-weight-bold"><i class="fas fa-times-circle"></i> BELUM BAYAR</h4>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="bg-secondary">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Nama Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Diskon Item</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach($order->items as $item): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= $item->product_name ?></strong><br>
                                    <small class="text-muted"><?= $item->unit ?></small>
                                </td>
                                <td class="text-center font-weight-bold">
                                    <?= $item->qty ?>
                                </td>
                                <td class="text-right">
                                    Rp <?= number_format($item->price, 0, ',', '.') ?>
                                </td>
                                <td class="text-right text-danger">
                                    <?= $item->discount > 0 ? '(-) Rp '.number_format($item->discount,0,',','.') : '-' ?>
                                </td>
                                <td class="text-right">
                                    Rp <?= number_format($item->subtotal, 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6"></div> <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tr>
                                    <th style="width:50%">Subtotal Barang:</th>
                                    <td class="text-right">Rp <?= number_format($order->total_amount, 0, ',', '.') ?></td>
                                </tr>
                                <?php if($order->shipping_cost > 0): ?>
                                <tr>
                                    <th>Biaya Kirim (+):</th>
                                    <td class="text-right">Rp <?= number_format($order->shipping_cost, 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($order->other_discount > 0): ?>
                                <tr>
                                    <th>Potongan Lain (-):</th>
                                    <td class="text-right text-danger">Rp <?= number_format($order->other_discount, 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php 
                                    $grand_total = ($order->grand_total > 0) ? $order->grand_total : $order->total_amount;
                                    $sisa = $grand_total - $order->total_paid;
                                ?>

                                <tr class="bg-light">
                                    <th style="font-size: 1.1rem;">TOTAL TAGIHAN:</th>
                                    <td class="text-right font-weight-bold" style="font-size: 1.1rem;">
                                        Rp <?= number_format($grand_total, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sudah Dibayar:</th>
                                    <td class="text-right text-success font-weight-bold">
                                        (-) Rp <?= number_format($order->total_paid, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <tr style="border-top: 2px solid #ccc;">
                                    <th class="text-danger">SISA PIUTANG:</th>
                                    <td class="text-right text-danger font-weight-bold" style="font-size: 1.1rem;">
                                        <?php if($order->status == 'canceled'): ?>
                                            <span class="badge badge-dark">DIBATALKAN</span>
                                        <?php else: ?>
                                            Rp <?= number_format($sisa, 0, ',', '.') ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    
                    <div class="col-md-4">
                        <h5 class="text-muted"><i class="fas fa-history"></i> Riwayat Pembayaran</h5>
                        <?php if(empty($payments)): ?>
                            <p class="text-muted small font-italic">Belum ada pembayaran.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush small">
                                <?php foreach($payments as $p): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <span class="font-weight-bold text-primary">Rp <?= number_format($p->amount, 0, ',', '.') ?></span><br>
                                        <span class="text-muted"><?= date('d/m/Y', strtotime($p->payment_date)) ?> - <?= $p->payment_method ?></span>
                                    </div>
                                    <span class="badge badge-light border"><?= $p->payment_method ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-outline card-warning">
                            <div class="card-header"><h3 class="card-title text-bold">Update Status Order</h3></div>
                            <div class="card-body p-3">
                                <form action="<?= site_url('marketing/sales/update_status') ?>" method="post">
                                    <input type="hidden" name="order_id" value="<?= $order->id ?>">
                                    
                                    <div class="form-group mb-2">
                                        <select name="status" class="form-control">
                                            <option value="request" <?= $order->status=='request'?'selected':'' ?>>Request (Pesanan Masuk)</option>
                                            <option value="preparing" <?= $order->status=='preparing'?'selected':'' ?>>Preparing (Disiapkan)</option>
                                            <option value="delivering" <?= $order->status=='delivering'?'selected':'' ?>>Delivering (Dikirim)</option>
                                            <option value="done" <?= $order->status=='done'?'selected':'' ?>>Done (Selesai)</option>
                                            <option value="canceled" <?= $order->status=='canceled'?'selected':'' ?>>Canceled (Batalkan)</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-warning btn-block btn-sm font-weight-bold" onclick="return confirm('Ubah status order? Stok akan disesuaikan otomatis.')">
                                        <i class="fas fa-sync-alt"></i> Update Status
                                    </button>
                                </form>
                                <div class="mt-2 text-muted" style="font-size: 11px; line-height: 1.2;">
                                    <i class="fas fa-info-circle"></i> Catatan:<br>
                                    - Status <b>Delivering</b> akan memotong stok fisik.<br>
                                    - Status <b>Canceled</b> akan mengembalikan stok (jika sdh terpotong).
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <?php if($order->status == 'canceled'): ?>
                            <div class="alert alert-secondary text-center">
                                <i class="fas fa-ban fa-2x mb-2"></i><br>
                                <strong>Transaksi Batal</strong><br>
                                Tidak bisa menerima pembayaran.
                            </div>
                        <?php elseif($sisa > 100): ?>
                            <div class="card card-outline card-success">
                                <div class="card-header"><h3 class="card-title text-bold">Input Pembayaran</h3></div>
                                <div class="card-body p-3">
                                    <form action="<?= site_url('marketing/sales/submit_payment') ?>" method="post">
                                        <input type="hidden" name="sales_order_id" value="<?= $order->id ?>">
                                        
                                        <div class="form-group mb-2">
                                            <input type="date" name="payment_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                                <input type="number" name="amount" class="form-control" placeholder="Nominal" max="<?= $sisa ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group mb-2">
                                            <select name="payment_method" class="form-control form-control-sm">
                                                <option value="Transfer">Transfer Bank</option>
                                                <option value="Cash">Tunai (Cash)</option>
                                                <option value="QRIS">QRIS</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-2">
                                            <input type="text" name="note" class="form-control form-control-sm" placeholder="Catatan (Opsional)">
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success btn-block btn-sm font-weight-bold">
                                            <i class="fas fa-save"></i> Simpan Pembayaran
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                                <strong>LUNAS</strong><br>
                                Tidak ada tagihan tersisa.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
            </div>
        </div>
</div>