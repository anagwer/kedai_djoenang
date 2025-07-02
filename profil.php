<?php include('head.php'); ?>
<?php
include_once('dbcon.php');

$id_user = $_SESSION['ID']; // pastikan sesi ini sudah diatur saat login

// Ambil data user
$result = $conn->query("SELECT * FROM pengguna WHERE ID_User = '$id_user'");
$user = $result->fetch_assoc();

// Simpan perubahan
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['Password'];

    $query = "
        UPDATE pengguna SET
            Nama = '$nama',
            Username = '$username',
            Password = '$password',
        WHERE ID_User = '$id_user'
    ";
    $conn->query($query);
    echo "<script>alert('Profil berhasil diperbarui!'); location.href='edit_profil.php';</script>";
}
?>


<!-- Main Content -->
<div class="container-fluid mt-4">
    <h1 class="h4 mb-4 text-gray-800">Edit Profil</h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control" name="nama" value="<?= $user['Nama']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" value="<?= $user['Username']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password Baru <small>(Kosongkan jika tidak diubah)</small></label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
