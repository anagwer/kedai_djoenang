<?php include 'head.php'; ?>

<?php
include 'dbcon.php';

$id_user = $_SESSION['ID'];
// Tambah Transaksi
if (isset($_POST['buat_transaksi'])) {
    $tanggal = date('Y-m-d');
    $total = 0;
    $status = 'Draft';

    $conn->query("INSERT INTO transaksi (Tanggal, Total, ID_User, Status) 
                  VALUES ('$tanggal', '$total', '$id_user', '$status')");
    $last_id = $conn->insert_id;
    echo "<script>location.href='detail_transaksi.php?id=" . $last_id . "';</script>";
}

// Delete Transaksi (jika status masih Draf)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $cek = $conn->query("SELECT Status FROM transaksi WHERE ID_Transaksi='$id'")->fetch_assoc();

    if ($cek['Status'] === 'Draft') {
        $conn->query("DELETE FROM detail_transaksi WHERE ID_Transaksi = '$id'");
        $conn->query("DELETE FROM transaksi WHERE ID_Transaksi = '$id'");
    }

    echo "<script>location.href='transaksi.php';</script>";
}
?>



<div class="container-fluid mt-4">
    <h1 class="h4 mb-4 text-gray-800">Manajemen Transaksi</h1>

    <!-- Tombol Tambah -->
    <form method="post">
        <button type="submit" name="buat_transaksi" class="btn btn-sm btn-primary mb-3">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </button>
    </form>

    <!-- Tabel Transaksi -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $result = $conn->query("SELECT * FROM transaksi ORDER BY ID_Transaksi DESC");
                        while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['Tanggal']; ?></td>
                            <td>Rp <?= number_format($row['Total'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="badge 
                                    <?= $row['Status'] === 'Final' ? 'badge-success' : 'badge-secondary'; ?>">
                                    <?= $row['Status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <!-- Tombol Edit -->
                                <a href="detail_transaksi.php?id=<?= $row['ID_Transaksi']; ?>" 
                                   class="btn btn-sm btn-warning">
                                   <i class="fas fa-edit"></i>
                                </a>

                                <!-- Tombol Hapus -->
                                <?php if ($row['Status'] === 'Draft'): ?>
                                    <a href="?delete=<?= $row['ID_Transaksi']; ?>" 
                                       onclick="return confirm('Yakin ingin menghapus transaksi ini?');"
                                       class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($row['Status'] === 'Final'): ?>
                                    <button class="btn btn-sm btn-info" onclick="printNota(<?= $row['ID_Transaksi']; ?>)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
function printNota(id) {
    const win = window.open('cetak_nota.php?id=' + id, '_blank');
    win.onload = function () {
        win.print();
        win.onafterprint = function () {
            win.close();
        };
    };
}
</script>

<?php include 'footer.php'; ?>
