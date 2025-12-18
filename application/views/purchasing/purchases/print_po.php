<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PO: <?= $po->purchase_no ?></title>
    <style>
        /* RESET & BASIC STYLE */
        body { font-family: sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        
        /* LAYOUT UTAMA MENGGUNAKAN TABLE AGAR TIDAK GESER SAAT DIPRINT */
        .container { width: 100%; max-width: 800px; margin: auto; }
        
        table { width: 100%; border-collapse: collapse; }
        
        /* HEADER */
        .header-title { font-size: 24px; font-weight: bold; color: #007bff; text-transform: uppercase; margin-bottom: 5px; }
        .header-subtitle { font-size: 14px; color: #555; margin-bottom: 20px; }
        .po-number { font-size: 18px; font-weight: bold; text-align: right; }
        
        /* INFO BOX (SUPPLIER & BUYER) */
        .info-table { margin-bottom: 30px; margin-top: 20px; }
        .info-table td { vertical-align: top; padding: 5px; }
        .info-title { font-weight: bold; text-decoration: underline; margin-bottom: 5px; display: block; }
        
        /* TABEL ITEM BARANG */
        .item-table { width: 100%; border: 1px solid #000; margin-bottom: 20px; }
        .item-table th { background-color: #f2f2f2; border: 1px solid #000; padding: 10px; text-align: left; }
        .item-table td { border: 1px solid #000; padding: 8px; }
        
        /* ALIGNMENT HELPER */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        
        /* TOTAL & TANDA TANGAN */
        .total-section { margin-top: 10px; }
        .signature-section { margin-top: 60px; }
        .sign-box { text-align: center; }
        .sign-line { margin-top: 60px; border-top: 1px solid #333; width: 80%; margin-left: auto; margin-right: auto; }

        /* HANYA MUNCUL DI LAYAR (HILANG SAAT PRINT) */
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #f8f9fa; padding: 10px; text-align: right; border-bottom: 1px solid #ddd; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">🖨️ Cetak Dokumen</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Tutup</button>
    </div>

    <div class="container">
        
        <table>
            <tr>
                <td width="60%">
                    <div class="header-title">IWL WEBGIS</div>
                    <div class="header-subtitle">
                        Jl. Raya Utama No. 123, Semarang<br>
                        Telp: (024) 123-4567 | Email: admin@iwl.com
                    </div>
                </td>
                <td width="40%" class="text-right" style="vertical-align: top;">
                    <div class="header-title" style="font-size: 20px; color: #333;">PURCHASE ORDER</div>
                    <div class="po-number"><?= $po->purchase_no ?></div>
                    <div>Tanggal: <?= date('d M Y', strtotime($po->purchase_date)) ?></div>
                    <div>Status: <strong><?= strtoupper($po->status) ?></strong></div>
                </td>
            </tr>
        </table>

        <hr style="border: 0; border-top: 2px solid #333;">

        <table class="info-table">
            <tr>
                <td width="50%">
                    <span class="info-title">KEPADA (SUPPLIER):</span>
                    <strong><?= $po->supplier_name ?></strong><br>
                    <?= $po->address ?><br>
                    Telp: <?= $po->phone ?><br>
                    PIC: <?= isset($po->contact_person) ? $po->contact_person : '-' ?>
                </td>
                <td width="50%">
                    <span class="info-title">DIKIRIM KE:</span>
                    <strong>Gudang Utama IWL</strong><br>
                    Jl. Pergudangan Industri Blok A1<br>
                    Semarang, Jawa Tengah<br>
                    UP: Bagian Penerimaan (Receiving)
                </td>
            </tr>
        </table>

        <table class="item-table" cellspacing="0">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="45%">Nama Barang / Deskripsi</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="20%" class="text-right">Harga Satuan</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                if(!empty($items)):
                    foreach($items as $i => $item): 
                        $grand_total += $item->subtotal;
                ?>
                <tr>
                    <td class="text-center"><?= $i+1 ?></td>
                    <td>
                        <strong><?= $item->product_name ?></strong><br>
                        <small>Satuan: <?= $item->unit ?></small>
                    </td>
                    <td class="text-center"><?= $item->qty ?></td>
                    <td class="text-right">Rp <?= number_format($item->cost, 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($item->subtotal, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; endif; ?>
                
                <?php for($x=0; $x<3; $x++): ?>
                <tr>
                    <td style="color:white">.</td><td></td><td></td><td></td><td></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <table>
            <tr>
                <td width="60%" style="vertical-align: top; padding-right: 20px;">
                    <strong>Catatan / Instruksi:</strong>
                    <p style="font-size: 12px; color: #555; border: 1px dashed #ccc; padding: 10px; margin-top: 5px;">
                        1. Harap cantumkan Nomor PO pada Invoice dan Surat Jalan.<br>
                        2. Barang harus sesuai dengan spesifikasi yang diminta.<br>
                        3. Hubungi kami jika ada perubahan harga atau stok.
                    </p>
                </td>
                <td width="40%" style="vertical-align: top;">
                    <table class="item-table" style="margin-bottom: 0;">
                        <tr>
                            <td class="text-bold" style="background: #f9f9f9;">SUBTOTAL</td>
                            <td class="text-right">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="text-bold" style="background: #f9f9f9;">TOTAL TAGIHAN</td>
                            <td class="text-right text-bold" style="font-size: 16px;">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="signature-section">
            <tr>
                <td width="33%" class="sign-box">
                    Disetujui Oleh,<br>
                    (Supplier)
                    <div class="sign-line"></div>
                </td>
                <td width="33%" class="sign-box">
                    Mengetahui,<br>
                    (Keuangan/Manager)
                    <div class="sign-line"></div>
                </td>
                <td width="33%" class="sign-box">
                    Dibuat Oleh,<br>
                    (Purchasing)
                    <div class="sign-line"></div>
                </td>
            </tr>
        </table>
        
        <div style="text-align: center; font-size: 10px; margin-top: 40px; color: #999;">
            Dicetak secara otomatis oleh Sistem IWL WEBGIS pada <?= date('d/m/Y H:i') ?>
        </div>

    </div>

    <script>
        // Opsional: Otomatis muncul dialog print saat dibuka
        window.onload = function() { window.print(); }
    </script>
</body>
</html>