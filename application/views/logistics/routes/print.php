<!DOCTYPE html>
<html>
<head>
    <title>Surat Jalan</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #eee; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>SURAT JALAN / DELIVERY ORDER</h2>
        <h3>IWL WebGIS Logistics</h3>
    </div>

    <p>
        <strong>Tanggal:</strong> <?= date('d F Y', strtotime($route->route_date)) ?><br>
        <strong>Driver:</strong> <?= $route->driver_name ?><br>
        <strong>Kendaraan:</strong> <?= $route->vehicle ?>
    </p>

    <table>
        <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Nama Pelanggan</th>
                <th>Alamat Pengiriman</th>
                <th>Tanda Tangan Penerima</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($route->points)): foreach($route->points as $p): ?>
            <tr>
                <td style="text-align:center"><?= $p->sequence_number ?></td>
                <td>
                    <b><?= $p->customer_name ?></b><br>
                    Telp: <?= $p->phone ?>
                </td>
                <td><?= $p->address ?>, <?= $p->city ?></td>
                <td style="height: 50px;"></td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; text-align: center; width: 33%;">
                    Admin Gudang<br><br><br><br>( ....................... )
                </td>
                <td style="border: none; text-align: center; width: 33%;">
                    Driver<br><br><br><br>( <?= $route->driver_name ?> )
                </td>
                <td style="border: none; text-align: center; width: 33%;">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>