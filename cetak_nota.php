<?php
include_once('dbcon.php');

// Ambil ID Transaksi dari parameter GET
$id_transaksi = $_GET['id'];

// Query data transaksi
$result_transaksi = $conn->query("SELECT * FROM transaksi WHERE ID_Transaksi='$id_transaksi'");
$transaksi = $result_transaksi->fetch_assoc();

// Query detail transaksi
$result_detail = $conn->query("SELECT dt.*, p.Nama_Produk, p.Harga FROM detail_transaksi dt INNER JOIN produk p ON dt.ID_Produk = p.ID_Produk WHERE dt.ID_Transaksi='$id_transaksi'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Nota</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css ">
    <style>
        body { font-family: Arial, sans-serif; }
        .nota { border: 1px solid #000; padding: 10px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { text-align: center; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #000; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="nota">
            <div class="header">
                <h2>KEDAI DOENANG</h2>
                <hr>
                <h3>NOTA TRANSAKSI</h3>
            </div>
                <p>Tanggal: <?= $transaksi['Tanggal']; ?></p>
                <p>ID Transaksi: <?= $transaksi['ID_Transaksi']; ?></p>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $total = 0;
                    while ($row = $result_detail->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['Nama_Produk']; ?></td>
                        <td>Rp <?= number_format($row['Harga'], 0, ',', '.'); ?></td>
                        <td><?= $row['Jumlah']; ?></td>
                        <td>Rp <?= number_format($row['Subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php
                    $total += $row['Subtotal'];
                    endwhile;
                    ?>
                    <tr>
                        <td colspan="4" style="text-align: right;">Total:</td>
                        <td>Rp <?= number_format($total, 0, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="footer">
                <p>Terima kasih telah berbelanja!</p>
                <p>Silakan simpan nota ini sebagai bukti pembelian.</p>
            </div>
        </div>
    </div>
</body>
</html>