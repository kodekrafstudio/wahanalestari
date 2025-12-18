<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - Standar Perbankan</title>
    <style>
        body { font-family: 'Times New Roman', serif; padding: 40px; color: #000; max-width: 850px; margin: auto; font-size: 12pt; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 3px double #000; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 5px 0; font-size: 14px; }
        
        .section-header { 
            background-color: #f0f0f0; 
            font-weight: bold; 
            padding: 8px; 
            border-top: 2px solid #000;
            border-bottom: 1px solid #000; 
            margin-top: 30px; 
            text-transform: uppercase;
            font-size: 14px;
        }
        
        .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dotted #ccc; }
        .row.sub { padding-left: 25px; color: #333; }
        .row.total { font-weight: bold; border-top: 1px solid #000; border-bottom: 2px solid #000; margin-top: 5px; padding: 10px 0; background-color: #fffef0; }
        
        .ratio-box { margin-top: 40px; border: 2px solid #333; padding: 20px; page-break-inside: avoid; }
        .ratio-table { width: 100%; border-collapse: collapse; }
        .ratio-table td { padding: 8px; border-bottom: 1px solid #ddd; vertical-align: top; }

        .signatures { margin-top: 80px; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .sign-box { text-align: center; width: 250px; }
        .sign-line { border-bottom: 1px solid #000; margin-top: 80px; }
        
        @media print {
            body { padding: 0; margin: 2cm; }
            .no-print { display: none; }
            .section-header { background-color: #ddd !important; -webkit-print-color-adjust: exact; }
            button { display: none; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" style="padding: 15px 30px; margin-bottom: 30px; cursor: pointer; background: #007bff; color: white; border: none; font-size: 16px; border-radius: 5px;" class="no-print">🖨️ CETAK LAPORAN (PDF)</button>

    <div class="header">
        <h1>CV. WAHANA LESTARI</h1>
        <p>Jalan Raya Garam No. 123, Pati, Jawa Tengah</p>
        <p>Telp: (0291) 1234567 | Email: admin@wahanalestari.com</p>
    </div>

    <center>
        <h3>LAPORAN KEUANGAN (UNAUDITED)</h3>
        <p>Periode: <?= date('F Y', mktime(0,0,0,$filter['month'], 10)) ?> <?= $filter['year'] ?></p>
    </center>

    <div class="section-header">A. LAPORAN LABA RUGI (INCOME STATEMENT)</div>
    
    <div class="row">
        <span>Pendapatan Penjualan (Revenue)</span>
        <span>Rp <?= number_format($pl['revenue'], 0, ',', '.') ?></span>
    </div>
    <div class="row sub">
        <span>Beban Pokok Pendapatan (COGS)</span>
        <span>(Rp <?= number_format($pl['cogs'], 0, ',', '.') ?>)</span>
    </div>
    <div class="row total">
        <span>LABA KOTOR (GROSS PROFIT)</span>
        <span>Rp <?= number_format($pl['gross_profit'], 0, ',', '.') ?></span>
    </div>

    <div style="margin-top: 15px; font-weight: bold; font-style: italic;">Biaya Operasional:</div>
    <?php if(!empty($pl['expenses_list'])): ?>
        <?php foreach($pl['expenses_list'] as $exp): ?>
        <div class="row sub">
            <span><?= $exp->category ? $exp->category : 'Biaya Umum' ?></span>
            <span>(Rp <?= number_format($exp->total, 0, ',', '.') ?>)</span>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="row sub"><span>- Tidak ada pengeluaran tercatat -</span><span>0</span></div>
    <?php endif; ?>
    
    <div class="row total" style="font-size: 16px;">
        <span>LABA BERSIH (NET PROFIT)</span>
        <span>Rp <?= number_format($pl['net_profit'], 0, ',', '.') ?></span>
    </div>

    <div class="section-header">B. NERACA (BALANCE SHEET)</div>
    
    <div class="row"><strong>I. AKTIVA (ASSETS)</strong></div>
    <div class="row sub">
        <span>Kas & Bank (Cash Equivalent)</span>
        <span>Rp <?= number_format($bs['assets']['cash'], 0, ',', '.') ?></span>
    </div>
    <div class="row sub">
        <span>Piutang Dagang (Accounts Receivable)</span>
        <span>Rp <?= number_format($bs['assets']['piutang'], 0, ',', '.') ?></span>
    </div>
    <div class="row sub">
        <span>Persediaan Barang (Inventory)</span>
        <span>Rp <?= number_format($bs['assets']['inventory'], 0, ',', '.') ?></span>
    </div>
    <div class="row total">
        <span>TOTAL AKTIVA</span>
        <span>Rp <?= number_format($bs['assets']['total'], 0, ',', '.') ?></span>
    </div>

    <br>
    <div class="row"><strong>II. PASIVA (LIABILITIES & EQUITY)</strong></div>
    <div class="row sub">
        <span>Hutang Dagang (Accounts Payable)</span>
        <span>Rp <?= number_format($bs['liabilities']['hutang_dagang'], 0, ',', '.') ?></span>
    </div>
    <div class="row sub">
        <span>Modal Disetor (Paid-in Capital)</span>
        <span>Rp <?= number_format($bs['equity']['modal_disetor'], 0, ',', '.') ?></span>
    </div>
    <div class="row sub">
        <span>Laba Ditahan (Retained Earnings)</span>
        <span>Rp <?= number_format($bs['equity']['laba_ditahan'], 0, ',', '.') ?></span>
    </div>
    <div class="row total">
        <span>TOTAL PASIVA</span>
        <span>Rp <?= number_format($bs['liabilities']['total'] + $bs['equity']['total'], 0, ',', '.') ?></span>
    </div>

    <div class="ratio-box">
        <strong>📊 RASIO KEUANGAN UTAMA (KEY FINANCIAL RATIOS)</strong>
        <table class="ratio-table">
            <tr>
                <td width="35%"><strong>Net Profit Margin</strong><br><small>(Efisiensi Laba Bersih)</small></td>
                <td width="20%" style="font-size: 18px;"><strong><?= number_format($pl['net_margin'], 2) ?>%</strong></td>
                <td><small>Target Ideal: >10%. Menunjukkan persentase keuntungan bersih dari setiap penjualan.</small></td>
            </tr>
            <tr>
                <td><strong>Current Ratio</strong><br><small>(Likuiditas)</small></td>
                <td style="font-size: 18px;">
                    <strong>
                    <?php 
                        $cr = ($bs['liabilities']['total'] > 0) ? $bs['assets']['total'] / $bs['liabilities']['total'] : 100;
                        echo number_format($cr, 2) . " x";
                    ?>
                    </strong>
                </td>
                <td><small>Target Ideal: >1.5x. Menunjukkan kemampuan perusahaan membayar hutang jangka pendek dengan aset lancar.</small></td>
            </tr>
        </table>
    </div>

    <div class="signatures">
        <div class="sign-box">
            <p>Dibuat Oleh,</p>
            <div class="sign-line"></div>
            <p><strong>Finance Dept.</strong></p>
        </div>
        <div class="sign-box">
            <p>Disetujui Oleh,</p>
            <div class="sign-line"></div>
            <p><strong>Direktur Utama</strong></p>
        </div>
    </div>

</body>
</html>