<div class="row mb-3">
    <div class="col-12">
        <div class="callout callout-info">
            <h5><i class="fas fa-briefcase"></i> Investor Dashboard</h5>
            <p>Laporan ini disusun secara otomatis berdasarkan standar akuntansi dasar (Derivatif). Gunakan data ini untuk presentasi kinerja bisnis.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Net Profit Margin</span>
                <span class="info-box-number"><?= number_format($net_margin, 1) ?>%</span>
                <div class="progress">
                    <div class="progress-bar" style="width: <?= $net_margin ?>%"></div>
                </div>
                <span class="progress-description">Efisiensi Profitabilitas</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-wallet"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Posisi Kas (Cash)</span>
                <span class="info-box-number">Rp <?= number_format($bs['cash']/1000000, 1) ?> Jt</span>
                <span class="progress-description">Liquiditas Tersedia</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Piutang</span>
                <span class="info-box-number">Rp <?= number_format($bs['receivables']/1000000, 1) ?> Jt</span>
                <span class="progress-description">Uang di Pelanggan</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-gradient-danger">
            <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Hutang</span>
                <span class="info-box-number">Rp <?= number_format($bs['payables']/1000000, 1) ?> Jt</span>
                <span class="progress-description">Kewajiban Supplier</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title text-bold">Laporan Laba Rugi (Income Statement)</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">Periode: <?= $filter['month'] ?>/<?= $filter['year'] ?></span>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <tr>
                        <td><strong>PENDAPATAN (REVENUE)</strong></td>
                        <td class="text-right text-success text-bold">Rp <?= number_format($pl['revenue'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td class="pl-4">(-) HPP (Cost of Goods Sold)</td>
                        <td class="text-right text-danger">Rp <?= number_format($pl['hpp'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="bg-light">
                        <td><strong>LABA KOTOR (GROSS PROFIT)</strong></td>
                        <td class="text-right text-bold">Rp <?= number_format($pl['gross_profit'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td class="pl-4">(-) Biaya Operasional (OpEx)</td>
                        <td class="text-right text-danger">Rp <?= number_format($pl['expense'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="bg-success">
                        <td style="font-size: 1.2rem;"><strong>LABA BERSIH (NET INCOME)</strong></td>
                        <td class="text-right text-bold" style="font-size: 1.2rem;">Rp <?= number_format($pl['net_profit'], 0, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-dark card-outline">
            <div class="card-header">
                <h3 class="card-title text-bold">Neraca Keuangan (Balance Sheet)</h3>
                <div class="card-tools">
                    <span class="badge badge-dark">Posisi: Per Hari Ini</span>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm">
                    <thead class="bg-secondary">
                        <tr><th colspan="2">ASET (ASSETS)</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Kas & Setara Kas (Estimasi)</td>
                            <td class="text-right">Rp <?= number_format($bs['cash'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Piutang Usaha (Receivables)</td>
                            <td class="text-right">Rp <?= number_format($bs['receivables'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Persediaan Barang (Inventory)</td>
                            <td class="text-right">Rp <?= number_format($bs['inventory'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="bg-light font-weight-bold">
                            <td>TOTAL ASET</td>
                            <td class="text-right">Rp <?= number_format($bs['total_assets'], 0, ',', '.') ?></td>
                        </tr>
                    </tbody>

                    <thead class="bg-secondary">
                        <tr><th colspan="2">KEWAJIBAN & EKUITAS</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Hutang Usaha (Payables)</td>
                            <td class="text-right text-danger">Rp <?= number_format($bs['payables'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Modal Bersih (Equity)</td>
                            <td class="text-right text-success">Rp <?= number_format($bs['equity'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="bg-light font-weight-bold">
                            <td>TOTAL KEWAJIBAN + MODAL</td>
                            <td class="text-right">Rp <?= number_format($bs['payables'] + $bs['equity'], 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>