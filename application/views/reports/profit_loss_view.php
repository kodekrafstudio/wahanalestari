<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body py-3">
                <form action="" method="get" class="form-inline justify-content-center">
                    <label class="mr-2">Periode Laporan:</label>
                    <select name="month" class="form-control mr-2">
                        <?php 
                        $months = [
                            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 
                            7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
                        ];
                        foreach($months as $k => $v): ?>
                            <option value="<?= $k ?>" <?= $filter['month'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="year" class="form-control mr-2">
                        <?php for($y=date('Y'); $y>=2024; $y--): ?>
                            <option value="<?= $y ?>" <?= $filter['year'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-outline card-success shadow-sm">
            <div class="card-header text-center bg-light">
                <h3 class="card-title w-100 font-weight-bold text-uppercase" style="font-size: 1.5rem;">
                    Laporan Laba Rugi
                </h3>
                <span class="text-muted">Periode: <?= $months[$filter['month']] ?> <?= $filter['year'] ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tr class="bg-secondary">
                            <td colspan="2"><strong>I. PENDAPATAN USAHA</strong></td>
                        </tr>
                        <tr>
                            <td class="pl-4">Total Penjualan (Omzet)</td>
                            <td class="text-right font-weight-bold text-primary">
                                Rp <?= number_format($data['revenue'], 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-4 text-muted">(-) Harga Pokok Penjualan (HPP / Modal Barang)</td>
                            <td class="text-right text-muted">
                                (Rp <?= number_format($data['hpp'], 0, ',', '.') ?>)
                            </td>
                        </tr>
                        <tr style="background-color: #e8f5e9; border-top: 2px solid #28a745;">
                            <td class="pl-4 font-weight-bold">LABA KOTOR (GROSS PROFIT)</td>
                            <td class="text-right font-weight-bold text-success" style="font-size: 1.2rem;">
                                Rp <?= number_format($calc['gross_profit'], 0, ',', '.') ?>
                            </td>
                        </tr>

                        <tr class="bg-secondary">
                            <td colspan="2"><strong>II. BIAYA OPERASIONAL</strong></td>
                        </tr>
                        
                        <?php if(empty($data['expense_list'])): ?>
                            <tr><td colspan="2" class="pl-4 font-italic text-muted">- Tidak ada pengeluaran bulan ini -</td></tr>
                        <?php else: ?>
                            <?php foreach($data['expense_list'] as $exp): ?>
                            <tr>
                                <td class="pl-4">
                                    <i class="fas fa-caret-right text-muted"></i> <?= $exp->category ?> 
                                    <small class="text-muted ml-2">(<?= $exp->description ?>)</small>
                                </td>
                                <td class="text-right text-danger">
                                    Rp <?= number_format($exp->amount, 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <tr style="border-top: 2px solid #dc3545;">
                            <td class="pl-4 font-weight-bold">Total Biaya Operasional</td>
                            <td class="text-right font-weight-bold text-danger">
                                (Rp <?= number_format($data['expenses'], 0, ',', '.') ?>)
                            </td>
                        </tr>

                        <tr class="bg-dark">
                            <td class="py-4 px-4">
                                <h3 class="m-0">LABA BERSIH (NET PROFIT)</h3>
                            </td>
                            <td class="text-right py-4 px-4">
                                <?php if($calc['net_profit'] >= 0): ?>
                                    <h2 class="m-0 text-success">Rp <?= number_format($calc['net_profit'], 0, ',', '.') ?></h2>
                                <?php else: ?>
                                    <h2 class="m-0 text-danger">(Rp <?= number_format(abs($calc['net_profit']), 0, ',', '.') ?>)</h2>
                                    <small class="text-danger">Rugi</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <button onclick="window.print()" class="btn btn-default btn-lg"><i class="fas fa-print"></i> Cetak Laporan</button>
            </div>
        </div>
    </div>
</div>