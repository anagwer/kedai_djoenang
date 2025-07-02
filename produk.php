<?php
include_once('dbcon.php');

// Handle Image Upload
function handleImageUpload($fileInputName) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'upload/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileName = basename($_FILES[$fileInputName]['name']);
        $filePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $filePath);
        return $fileName; // Return only the file name to save in the database
    }
    return null; // No file uploaded
}

// Simpan Tambah
if (isset($_POST['save'])) {
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['jumlah_stok'];
    $gambar = handleImageUpload('gambar'); // Handle image upload

    $conn->query("INSERT INTO produk (Nama_Produk, Harga, Kategori, Gambar) VALUES ('$nama', '$harga', '$kategori', '$gambar')");
    $last_id = $conn->insert_id;
    $conn->query("INSERT INTO stok_barang (ID_Produk, Jumlah_Stok) VALUES ('$last_id', '$stok')");
    echo "<script>location.href='produk.php';</script>";
}

// Simpan Edit
if (isset($_POST['update'])) {
    $id = $_POST['id_produk'];
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['jumlah_stok'];
    $gambar = handleImageUpload('gambar'); // Handle image upload

    if ($gambar) {
        // If a new image is uploaded, update the database with the new file name
        $conn->query("UPDATE produk SET Nama_Produk='$nama', Harga='$harga', Kategori='$kategori', Gambar='$gambar' WHERE ID_Produk='$id'");
    } else {
        // If no new image is uploaded, keep the existing image
        $conn->query("UPDATE produk SET Nama_Produk='$nama', Harga='$harga', Kategori='$kategori' WHERE ID_Produk='$id'");
    }

    $conn->query("UPDATE stok_barang SET Jumlah_Stok='$stok', Tanggal_Update=NOW() WHERE ID_Produk='$id'");
    echo "<script>location.href='produk.php';</script>";
}

// Hapus
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM produk WHERE ID_Produk='$id'");
    $conn->query("DELETE FROM stok_barang WHERE ID_Produk='$id'");
    echo "<script>location.href='produk.php';</script>";
}
?>
<?php include ('head.php');?>
<!-- Main Content -->
<div class="container-fluid mt-4">
    <h1 class="h4 mb-4 text-gray-800">Manajemen Produk</h1>

    <button class="btn btn-sm btn-primary mb-3" data-toggle="modal" data-target="#modalTambah"><i class="fas fa-plus"></i> Tambah Produk</button>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $result = $conn->query("SELECT p.*, s.Jumlah_Stok FROM produk p LEFT JOIN stok_barang s ON p.ID_Produk = s.ID_Produk ORDER BY p.ID_Produk DESC");
                        while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td class="text-center"><img src="upload/<?= $row['Gambar']; ?>" alt="<?= $row['Nama_Produk']; ?>" style="width: 150px;"></td>
                            <td><?= $row['Nama_Produk']; ?></td>
                            <td>Rp <?= number_format($row['Harga'], 0, ',', '.'); ?></td>
                            <td><?= $row['Kategori']; ?></td>
                            
                            <td>
                              <span class="badge 
                                  <?= $row['Jumlah_Stok'] == 0 ? 'badge-danger' : ($row['Jumlah_Stok'] < 4 ? 'badge-warning' : 'badge-success'); ?>">
                                  <?= $row['Jumlah_Stok']; ?>
                              </span>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $row['ID_Produk']; ?>"><i class="fas fa-edit"></i></button>
                                <a href="?delete=<?= $row['ID_Produk']; ?>" onclick="return confirm('Yakin ingin menghapus produk ini?');" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editModal<?= $row['ID_Produk']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $row['ID_Produk']; ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                  <form method="post" enctype="multipart/form-data">
                                      <div class="modal-header">
                                          <h5 class="modal-title" id="editModalLabel<?= $row['ID_Produk']; ?>">Edit Produk</h5>
                                          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                      </div>
                                      <div class="modal-body">
                                          <input type="hidden" name="id_produk" value="<?= $row['ID_Produk']; ?>">
                                          <div class="form-group">
                                              <label>Nama Produk</label>
                                              <input type="text" class="form-control" name="nama_produk" value="<?= $row['Nama_Produk']; ?>" required>
                                          </div>
                                          <div class="form-group">
                                              <label>Harga</label>
                                              <input type="number" class="form-control" name="harga" value="<?= $row['Harga']; ?>" required>
                                          </div>
                                          <div class="form-group">
                                              <label>Kategori</label>
                                              <select name="kategori" class="form-control" required>
                                                  <option value="Minuman" <?= $row['Kategori'] == 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
                                                  <option value="Makanan" <?= $row['Kategori'] == 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
                                                  <option value="Snack" <?= $row['Kategori'] == 'Snack' ? 'selected' : ''; ?>>Snack</option>
                                              </select>
                                          </div>
                                          <div class="form-group">
                                              <label>Jumlah Stok</label>
                                              <input type="number" class="form-control" name="jumlah_stok" value="<?= $row['Jumlah_Stok']; ?>" required>
                                          </div>
                                          <div class="form-group">
                                              <label>Gambar</label>
                                              <input type="file" class="form-control-file" name="gambar">
                                              <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                          <button type="submit" name="update" class="btn btn-success">Update</button>
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" class="form-control" name="nama_produk" required>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" class="form-control" name="harga" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" class="form-control" required>
                        <option value="Minuman">Minuman</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Snack">Snack</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah Stok</label>
                    <input type="number" class="form-control" name="jumlah_stok" required>
                </div>
                <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" class="form-control-file" name="gambar" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="submit" name="save" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>