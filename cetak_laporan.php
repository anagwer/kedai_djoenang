<?php
include_once('dbcon.php');

$tanggal_awal = isset($_GET['tanggal1']) ? $_GET['tanggal1'] : '';
$tanggal_akhir = isset($_GET['tanggal2']) ? $_GET['tanggal2'] : '';

// Validasi input tanggal
if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $query = "
        SELECT 
            t.Tanggal,
            p.Nama_Produk,
            SUM(dt.Jumlah) AS Jumlah_Terjual,
            SUM(dt.Subtotal) AS Subtotal_Penjualan
        FROM 
            transaksi t
        JOIN 
            detail_transaksi dt ON t.ID_Transaksi = dt.ID_Transaksi
        JOIN 
            produk p ON dt.ID_Produk = p.ID_Produk
        WHERE 
            t.Tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
            AND t.Status = 'Final'
        GROUP BY 
            t.Tanggal, p.Nama_Produk
        ORDER BY 
            t.Tanggal ASC, p.Nama_Produk ASC;
    ";
    $result = $conn->query($query);
} else {
    die("Parameter tanggal tidak lengkap.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        h2, h4 { text-align: center; }
        .total { text-align: right; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

    <h2>LAPORAN PENJUALAN KEDAI DJOENANG</h2>
    <h4>Periode: <?= date('d-m-Y', strtotime($tanggal_awal)); ?> s/d <?= date('d-m-Y', strtotime($tanggal_akhir)); ?></h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Produk</th>
                <th>Jumlah Terjual</th>
                <th>Subtotal Penjualan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $total_penjualan = 0;

            while ($row = $result->fetch_assoc()) {
                $total_penjualan += $row['Subtotal_Penjualan'];
                echo "<tr>
                    <td>{$no}</td>
                    <td>" . date('d-m-Y', strtotime($row['Tanggal'])) . "</td>
                    <td>{$row['Nama_Produk']}</td>
                    <td>{$row['Jumlah_Terjual']}</td>
                    <td>Rp " . number_format($row['Subtotal_Penjualan'], 0, ',', '.') . "</td>
                </tr>";
                $no++;
            }
            ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="4" style="text-align:center;">Total Penjualan</th>
            <th>Rp <?= number_format($total_penjualan, 0, ',', '.'); ?></th>
        </tr>
        </tfoot>
    </table>

    <p class="total">Total Penjualan Keseluruhan: Rp <?= number_format($total_penjualan, 0, ',', '.'); ?></p>

</body>
</html>
