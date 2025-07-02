<?php include 'head.php'; ?>
<?php
include 'dbcon.php';

$id_transaksi = $_GET['id'];

// Ambil data transaksi
$transaksi = $conn->query("SELECT * FROM transaksi WHERE ID_Transaksi = '$id_transaksi'")->fetch_assoc();
$status = $transaksi['Status'];

// Tambah Detail Transaksi (jika masih Draf)
if (isset($_POST['tambah_detail']) && $status === 'Draft') {
    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah'];

    $produk = $conn->query("SELECT * FROM produk p JOIN stok_barang s ON p.ID_Produk = s.ID_Produk WHERE p.ID_Produk = '$id_produk'")->fetch_assoc();

    if ($produk['Jumlah_Stok'] >= $jumlah) {
        $subtotal = $produk['Harga'] * $jumlah;

        $conn->query("INSERT INTO detail_transaksi (ID_Transaksi, ID_Produk, Jumlah, Subtotal) 
                      VALUES ('$id_transaksi', '$id_produk', '$jumlah', '$subtotal')");

        // Update stok
        $conn->query("UPDATE stok_barang SET Jumlah_Stok = Jumlah_Stok - $jumlah WHERE ID_Produk = '$id_produk'");
    } else {
        $error = "Stok tidak mencukupi!";
    }
}

// Hapus item detail (jika masih Draf)
if (isset($_GET['hapus']) && $status === 'Draft') {
    $id_detail = $_GET['hapus'];

    // Ambil detail untuk restore stok
    $detail = $conn->query("SELECT * FROM detail_transaksi WHERE ID_Detail = '$id_detail'")->fetch_assoc();
    $conn->query("UPDATE stok_barang SET Jumlah_Stok = Jumlah_Stok + {$detail['Jumlah']} WHERE ID_Produk = '{$detail['ID_Produk']}'");

    // Hapus
    $conn->query("DELETE FROM detail_transaksi WHERE ID_Detail = '$id_detail'");
    echo "<script>location.href='detail_transaksi.php?id=$id_transaksi';</script>";
}

// Simpan Transaksi
if (isset($_POST['selesai']) && $status === 'Draft') {
    $total = $conn->query("SELECT SUM(Subtotal) as total FROM detail_transaksi WHERE ID_Transaksi = '$id_transaksi'")->fetch_assoc()['total'];
    $conn->query("UPDATE transaksi SET Total = '$total', Status = 'Final' WHERE ID_Transaksi = '$id_transaksi'");
    echo "<script>location.href='transaksi.php';</script>";
}
?>


<div class="container-fluid mt-4">
    <h1 class="h4 mb-4 text-gray-800">Detail Transaksi #<?= $id_transaksi; ?></h1>
    <a href="transaksi.php" class="btn btn-danger mb-2"><i class="fas fa-arrow-left"></i> Kembali</a>
    <!-- Form Tambah Detail -->
    <div class="card shadow">
        <div class="card-body">
        <?php if ($status === 'Draft'): ?>
        <form method="post" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    <select name="id_produk" class="form-control" required>
                        <option value="">Pilih Produk</option>
                        <?php
                        $produk = $conn->query("SELECT * FROM produk p JOIN stok_barang s ON p.ID_Produk = s.ID_Produk");
                        while ($row = $produk->fetch_assoc()):
                        ?>
                        <option value="<?= $row['ID_Produk']; ?>">
                            <?= $row['Nama_Produk']; ?> - Stok: <?= $row['Jumlah_Stok']; ?> - Rp<?= number_format($row['Harga']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="tambah_detail" class="btn btn-success">Tambah</button>
                </div>
            </div>
            <?php if (isset($error)): ?>
                <div class="text-danger mt-2"><?= $error; ?></div>
            <?php endif; ?>
        </form>
        <?php else: ?>
            <div class="alert alert-info">Transaksi ini sudah <strong>FINAL</strong>, tidak dapat diubah.</div>
        <?php endif; ?>

        <!-- Tabel Detail Transaksi -->
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <?php if ($status === 'Draft'): ?><th>Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $detail = $conn->query("SELECT dt.*, p.Nama_Produk FROM detail_transaksi dt 
                                        JOIN produk p ON dt.ID_Produk = p.ID_Produk 
                                        WHERE dt.ID_Transaksi = '$id_transaksi'");
                while ($row = $detail->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['Nama_Produk']; ?></td>
                    <td><?= $row['Jumlah']; ?></td>
                    <td>Rp <?= number_format($row['Subtotal']); ?></td>
                    <?php if ($status === 'Draft'): ?>
                    <td>
                        <a href="detail_transaksi.php?id=<?= $id_transaksi; ?>&hapus=<?= $row['ID_Detail']; ?>"
                        onclick="return confirm('Yakin ingin menghapus item ini?');"
                        class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile ?>
            </tbody>
        </table>

        <!-- Tombol Simpan Transaksi -->
        <?php if ($status === 'Draft'): ?>
        <form method="post">
            <button type="submit" name="selesai" class="btn btn-primary mt-3">Simpan Transaksi</button>
        </form>
        <?php endif; ?>
    </div>
    </div>
    </div>

<?php include 'footer.php'; ?>
