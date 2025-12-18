<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>INVOICE: <?= $order->invoice_no ?></title>
    <style>
        /* RESET & BASIC STYLE - Desain Klasik & Bersih */
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        .container { width: 100%; max-width: 800px; margin: auto; }
        
        table { width: 100%; border-collapse: collapse; }
        
        /* HEADER */
        .header-brand { font-size: 24px; font-weight: bold; color: #28a745; text-transform: uppercase; margin-bottom: 5px; }
        .header-subtitle { font-size: 14px; color: #555; margin-bottom: 20px; line-height: 1.4; }
        
        .invoice-title { font-size: 28px; font-weight: bold; text-align: right; color: #333; letter-spacing: 2px; }
        .invoice-details { text-align: right; font-size: 14px; margin-top: 5px; }
        
        /* INFO BOX */
        .info-table { margin-top: 30px; margin-bottom: 30px; }
        .info-label { font-weight: bold; font-size: 12px; color: #777; text-transform: uppercase; letter-spacing: 1px; }
        .info-content { font-size: 15px; font-weight: 500; line-height: 1.5; margin-top: 5px; }
        
        /* ITEM TABLE */
        .item-table { width: 100%; margin-bottom: 20px; border: 1px solid #ddd; }
        .item-table th { background-color: #f8f9fa; border-bottom: 2px solid #ddd; padding: 12px; text-align: left; font-weight: bold; color: #444; }
        .item-table td { border-bottom: 1px solid #eee; padding: 10px; vertical-align: middle; }
        .item-table tr:last-child td { border-bottom: none; }
        
        /* UTILITIES */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        
        /* TOTAL SECTION */
        .total-table { width: 100%; }
        .total-table td { padding: 5px 10px; }
        .grand-total { font-size: 18px; font-weight: bold; background-color: #f8f9fa; color: #28a745; }
        
        /* FOOTER & SIGNATURE */
        .footer-note { font-size: 12px; color: #777; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
        .signature-box { text-align: center; margin-top: 50px; }
        .sign-line { margin-top: 70px; border-top: 1px solid #333; width: 60%; margin-left: auto; margin-right: auto; }
        
        /* PRINT CONTROL */
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right; padding: 10px; background: #f1f1f1; border-bottom: 1px solid #ccc; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer; background: #28a745; color: white; border: none; border-radius: 4px;">🖨️ Cetak Invoice</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; border: 1px solid #ccc; background: white; border-radius: 4px;">Tutup</button>
    </div>

    <div class="container">
        
        <table>
            <tr>
                <td width="60%" style="vertical-align: top;">
                    <div class="header-brand">PT. IWL WEBGIS</div>
                    <div class="header-subtitle">
                        Jurugentong JG II/17 Gg.Arimbi, Banguntapan, Bantul, DIY<br>
                        Telp: 0822-2692-2024 | Email: admin@iwl.com
                    </div>
                </td>
                <td width="40%" style="vertical-align: top;">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-details">
                        No: <strong><?= $order->invoice_no ?></strong><br>
                        Tanggal: <?= date('d M Y', strtotime($order->order_date)) ?><br>
                        Status: <?= strtoupper($order->status) ?>
                    </div>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td width="55%" style="vertical-align: top; padding-right: 20px;">
                    <div class="info-label">TAGIHAN KEPADA:</div>
                    <div class="info-content">
                        <strong><?= $order->customer_name ?></strong><br>
                        <?= $order->address ?><br>
                        <?= $order->city ?><br>
                        Telp: <?= $order->phone ?>
                    </div>
                </td>
                <td width="45%" style="vertical-align: top;">
                    <div class="info-label">INFO PENGIRIMAN:</div>
                    <div class="info-content">
                        Salesman: <?= $order->salesman_name ?? '-' ?><br>
                        Admin: <?= $order->creator_name ?><br>
                        Metode Bayar: <?= $order->payment_status == 'paid' ? 'LUNAS' : ($order->payment_status == 'partial' ? 'CICILAN' : 'BELUM LUNAS') ?>
                    </div>
                </td>
            </tr>
        </table>

        <table class="item-table" cellspacing="0">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="40%">Deskripsi Produk</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Harga</th>
                    <th width="15%" class="text-right">Diskon</th>
                    <th width="15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                if(!empty($order->items)):
                    foreach($order->items as $item): 
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td>
                        <strong><?= $item->product_name ?></strong><br>
                        <small style="color: #666;"><?= $item->unit ?></small>
                    </td>
                    <td class="text-center"><?= $item->qty ?></td>
                    <td class="text-right">Rp <?= number_format($item->price, 0, ',', '.') ?></td>
                    <td class="text-right text-danger"><?= $item->discount > 0 ? number_format($item->discount, 0, ',', '.') : '-' ?></td>
                    <td class="text-right text-bold">Rp <?= number_format($item->subtotal, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; endif; ?>
                
                <?php for($x=0; $x<(5 - count($order->items)); $x++): ?>
                <tr><td colspan="6" style="padding: 15px;">&nbsp;</td></tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <table>
            <tr>
                <td width="60%" style="vertical-align: top; padding-right: 30px;">
                    <div class="info-label">METODE PEMBAYARAN:</div>
                    <div style="margin-top: 5px; font-size: 13px; line-height: 1.6; color: #555;">
                        Silakan transfer pembayaran ke:<br>
                        <strong>Bank BCA: 123-456-7890</strong> (a.n PT IWL WEBGIS)<br>
                        <strong>Bank Mandiri: 987-654-3210</strong> (a.n PT IWL WEBGIS)<br>
                        <em>Mohon sertakan No. Invoice pada berita transfer.</em>
                    </div>
                </td>
                <td width="40%" style="vertical-align: top;">
                    <table class="total-table">
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-right">Rp <?= number_format($order->total_amount, 0, ',', '.') ?></td>
                        </tr>
                        <?php if($order->shipping_cost > 0): ?>
                        <tr>
                            <td>Ongkir (+)</td>
                            <td class="text-right">Rp <?= number_format($order->shipping_cost, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($order->other_discount > 0): ?>
                        <tr>
                            <td>Potongan Lain (-)</td>
                            <td class="text-right text-danger">Rp <?= number_format($order->other_discount, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php 
                             $grand = ($order->grand_total > 0) ? $order->grand_total : $order->total_amount;
                        ?>
                        <tr>
                            <td class="grand-total" style="border-top: 2px solid #ccc; padding-top: 10px;">TOTAL TAGIHAN</td>
                            <td class="text-right grand-total" style="border-top: 2px solid #ccc; padding-top: 10px;">
                                Rp <?= number_format($grand, 0, ',', '.') ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td style="padding-top: 10px;">Sudah Dibayar</td>
                            <td class="text-right text-success" style="padding-top: 10px;">(-) Rp <?= number_format($order->total_paid, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="text-bold text-danger">SISA</td>
                            <td class="text-right text-bold text-danger">Rp <?= number_format($grand - $order->total_paid, 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table style="margin-top: 50px;">
            <tr>
                <td width="33%" class="signature-box">
                    Penerima,<br><br><br><br>
                    <div class="sign-line"></div>
                </td>
                <td width="33%"></td>
                <td width="33%" class="signature-box">
                    Hormat Kami,<br><br><br><br>
                    <div class="sign-line"></div>
                    <strong>Admin Finance</strong>
                </td>
            </tr>
        </table>

        <div class="footer-note center">
            Terima kasih atas kepercayaan Anda berbisnis dengan kami.
        </div>

    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>