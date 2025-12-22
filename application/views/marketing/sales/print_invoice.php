<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVOICE #<?= $order->invoice_no ?></title>
    <style>
        /* --- VARIABLES & RESET --- */
        :root {
            --primary-color: #2c3e50;
            --accent-color: #27ae60;
            --danger-color: #c0392b;
            --border-color: #e0e0e0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px; /* Font dasar diperkecil */
            -webkit-print-color-adjust: exact;
        }

        /* --- CONTAINER (A4 Compact) --- */
        .invoice-box {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 30px; /* Padding dikurangi */
            border: 1px solid #ccc;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        /* --- HEADER --- */
        .header-table { width: 100%; margin-bottom: 20px; } /* Margin dikurangi */
        .brand-name {
            font-size: 20px; /* Font judul dikurangi */
            font-weight: 800;
            color: var(--primary-color);
            text-transform: uppercase;
            margin: 0;
        }
        .brand-sub { font-size: 11px; color: #777; margin-bottom: 5px; }
        .company-info { font-size: 11px; color: #555; line-height: 1.3; }

        .invoice-title {
            font-size: 24px; /* Font invoice dikurangi */
            font-weight: 900;
            color: #bdc3c7;
            text-align: right;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .invoice-meta { text-align: right; font-size: 11px; }
        .meta-item { margin-bottom: 3px; }
        .meta-label { font-weight: bold; color: var(--primary-color); margin-right: 5px; }

        /* --- CUSTOMER INFO --- */
        .info-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .info-title {
            font-size: 10px;
            font-weight: bold;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 3px;
            margin-bottom: 5px;
        }
        .info-content { font-size: 11px; line-height: 1.4; }
        .info-content strong { font-size: 12px; color: #000; }

        /* --- ITEM TABLE --- */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .item-table th {
            background-color: var(--primary-color);
            color: #fff;
            font-weight: 600;
            text-align: left;
            padding: 8px 10px; /* Padding header diperkecil */
        }
        .item-table td {
            padding: 6px 10px; /* Padding baris diperkecil */
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        .item-table tr:nth-child(even) { background-color: #f9f9f9; }
        .item-table tr:last-child td { border-bottom: 2px solid var(--primary-color); }
        
        .col-center { text-align: center; }
        .col-right { text-align: right; }

        /* --- TOTALS --- */
        .totals-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
            font-size: 11px;
        }
        .totals-table td { padding: 4px 0; }
        .totals-label { text-align: right; padding-right: 15px; color: #666; }
        .totals-value { text-align: right; font-weight: 600; color: #333; }
        
        .grand-total-row td {
            padding-top: 8px;
            padding-bottom: 8px;
            border-top: 1px solid #999;
            border-bottom: 1px solid #999;
        }
        .grand-total-label { font-size: 13px; font-weight: bold; color: var(--primary-color); text-align: right; padding-right: 15px; }
        .grand-total-value { font-size: 14px; font-weight: bold; color: var(--accent-color); text-align: right; }

        /* --- FOOTER --- */
        .bottom-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            border-top: 1px dashed #ccc; /* Garis pemisah footer */
            padding-top: 15px;
        }
        .payment-info { width: 60%; font-size: 11px; color: #555; }
        .bank-list { 
            display: flex; gap: 15px; flex-wrap: wrap; margin-top: 5px; 
        }
        .bank-item {
            background: #f8f9fa; border: 1px solid #ddd; 
            padding: 5px 8px; border-radius: 4px; font-size: 10px;
        }
        
        .signature-section { width: 30%; text-align: center; font-size: 11px; }
        .sign-box { height: 50px; }
        .sign-name { font-weight: bold; text-decoration: underline; }

        /* --- BADGE --- */
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-paid { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .badge-unpaid { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .badge-partial { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

        /* --- PRINT --- */
        .no-print-bar {
            position: fixed; top: 0; left: 0; right: 0;
            background: #333; padding: 8px; text-align: center;
            z-index: 999;
        }
        .btn-print {
            background: var(--accent-color); color: white; border: none;
            padding: 6px 15px; border-radius: 3px; cursor: pointer; font-size: 12px;
        }
        .btn-close {
            background: #fff; color: #333; border: none;
            padding: 6px 15px; border-radius: 3px; cursor: pointer; font-size: 12px;
        }

        @media print {
            body { background: #fff; padding: 0; margin: 0; }
            .invoice-box { border: none; box-shadow: none; padding: 0; max-width: 100%; }
            .no-print-bar { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak</button>
        <button class="btn-close" onclick="window.close()">Tutup</button>
    </div>

    <div class="invoice-box">
        
        <table class="header-table">
            <tr>
                <td width="60%" style="vertical-align: top;">
                    <div class="brand-name">PT. INSAN WAHANA LESTARI</div>
                    <div class="brand-sub">Distributor Garam Jogja</div>
                    <div class="company-info">
                        Jurugentong JG II/17 Gg.Arimbi, Banguntapan, Bantul, DIY<br>
                        WA: 0822-2692-2024 | Email: insanwahanalestari@gmail.com
                    </div>
                </td>
                <td width="40%" style="vertical-align: top;">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-meta">
                        <div class="meta-item">
                            <span class="meta-label">No:</span> <?= $order->invoice_no ?>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Tgl:</span> <?= date('d/m/Y', strtotime($order->order_date)) ?>
                        </div>
                        <div class="meta-item" style="margin-top: 5px;">
                            <?php
                                $status_badge = 'badge-unpaid';
                                if($order->payment_status == 'paid') $status_badge = 'badge-paid';
                                if($order->payment_status == 'partial') $status_badge = 'badge-partial';
                            ?>
                            <span class="badge <?= $status_badge ?>">
                                <?= $order->payment_status == 'paid' ? 'LUNAS' : ($order->payment_status == 'partial' ? 'CICILAN' : 'BELUM LUNAS') ?>
                            </span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td width="55%" style="vertical-align: top; padding-right: 20px;">
                    <div class="info-title">Ditagihkan Kepada:</div>
                    <div class="info-content">
                        <strong><?= strtoupper($order->customer_name) ?></strong><br>
                        <?= $order->address ?><br>
                        <?= $order->city ?><br>
                        Telp: <?= $order->phone ?>
                    </div>
                </td>
                <td width="45%" style="vertical-align: top;">
                    <div class="info-title">Detail Order:</div>
                    <table style="width: 100%; font-size: 11px; color: #444;">
                        <tr><td width="35%">Salesman</td><td>: <?= $order->salesman_name ?? '-' ?></td></tr>
                        <tr><td>Admin</td><td>: <?= $order->creator_name ?></td></tr>
                        <tr><td>Status</td><td>: <?= ucfirst($order->status) ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="item-table">
            <thead>
                <tr>
                    <th width="5%" class="col-center">No</th>
                    <th width="45%">Produk</th>
                    <th width="10%" class="col-center">Qty</th>
                    <th width="15%" class="col-right">Harga</th>
                    <th width="10%" class="col-right">Disc</th>
                    <th width="15%" class="col-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                if(!empty($order->items)):
                    foreach($order->items as $item): 
                ?>
                <tr>
                    <td class="col-center"><?= $no++ ?></td>
                    <td>
                        <strong><?= $item->product_name ?></strong> 
                        <span style="color:#777; font-size:10px;">(<?= $item->unit ?>)</span>
                    </td>
                    <td class="col-center"><?= ((float)$item->qty) ?></td>
                    <td class="col-right">Rp <?= number_format($item->price, 0, ',', '.') ?></td>
                    <td class="col-right text-danger"><?= $item->discount > 0 ? number_format($item->discount, 0, ',', '.') : '-' ?></td>
                    <td class="col-right"><strong><?= number_format($item->subtotal, 0, ',', '.') ?></strong></td>
                </tr>
                <?php endforeach; endif; ?>
                
                <?php if(count($order->items) < 3): ?>
                    <tr><td colspan="6" style="padding: 15px;"></td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div style="overflow: hidden;">
            <table class="totals-table">
                <tr>
                    <td class="totals-label">Subtotal</td>
                    <td class="totals-value">Rp <?= number_format($order->total_amount, 0, ',', '.') ?></td>
                </tr>
                <?php if($order->shipping_cost > 0): ?>
                <tr>
                    <td class="totals-label">Ongkir</td>
                    <td class="totals-value">Rp <?= number_format($order->shipping_cost, 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                <?php if($order->other_discount > 0): ?>
                <tr>
                    <td class="totals-label text-danger">Potongan</td>
                    <td class="totals-value text-danger">- Rp <?= number_format($order->other_discount, 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                
                <?php $grand = ($order->grand_total > 0) ? $order->grand_total : $order->total_amount; ?>
                
                <tr class="grand-total-row">
                    <td class="grand-total-label">TOTAL</td>
                    <td class="grand-total-value">Rp <?= number_format($grand, 0, ',', '.') ?></td>
                </tr>

                <tr>
                    <td class="totals-label" style="padding-top: 5px;">Bayar</td>
                    <td class="totals-value" style="color: var(--accent-color); padding-top: 5px;">Rp <?= number_format($order->total_paid, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="totals-label" style="font-weight: bold;">SISA</td>
                    <td class="totals-value" style="color: var(--danger-color);">Rp <?= number_format($grand - $order->total_paid, 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>

        <div class="bottom-section">
            <div class="payment-info">
                <strong>METODE PEMBAYARAN:</strong>
                <div style="margin-top: 5px; font-size: 10px; line-height: 1.6; color: #555;">
                    Silakan transfer pembayaran ke:<br>
                    <strong>Bank BPD DIY : 006211031536</strong> (a.n Indri Septiani)<br>
                    <strong>Bank BCA : 8465957715</strong> (a.n Ahmad Asrori)<br>
                    <strong>Bank BRI : 300801000004566</strong> (a.n PT INSAN WAHANA LESTARI)<br>
                    <em>Mohon sertakan No. Invoice pada berita transfer.</em>
                </div>
            </div>

            <div class="signature-section">
                <div>Yogyakarta, <?= date('d M Y') ?></div>
                <div style="margin-bottom: 2px;">Hormat Kami,</div>
                <div class="sign-box"></div>
                <div class="sign-name">Admin Finance</div>
            </div>
        </div>

    </div>

</body>
</html>