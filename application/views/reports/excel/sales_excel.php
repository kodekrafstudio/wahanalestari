<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
</head>
<body>
    <h3>LAPORAN PENJUALAN</h3>
    <p>Periode: <?= $period ?></p>
    
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>Tanggal</th>
                <th>No Invoice</th>
                <th>Pelanggan</th>
                <th>Sales</th>
                <th>Status</th>
                <th>Bayar</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            foreach($report as $row): 
                if($row->status != 'canceled') $total += $row->total_amount;
            ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($row->order_date)) ?></td>
                <td><?= $row->invoice_no ?></td>
                <td><?= $row->customer_name ?></td>
                <td><?= $row->sales_name ?></td>
                <td><?= ucfirst($row->status) ?></td>
                <td><?= ucfirst($row->payment_status) ?></td>
                <td align="right"><?= $row->total_amount ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="6" align="right"><strong>TOTAL OMZET</strong></td>
                <td align="right"><strong><?= $total ?></strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>