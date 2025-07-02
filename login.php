<?php
session_start();

// Jika sudah login, langsung redirect
if (isset($_SESSION['ID'])) {
    header("Location: index.php");
    exit();
}

include_once('dbcon.php');
$errorMsg = "";

if (isset($_POST['submit'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = md5($conn->real_escape_string($_POST['password']));

    if (!empty($username) && !empty($password)) {
        $query = "SELECT * FROM pengguna WHERE Username = '$username' AND Password = '$password'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['ID'] = $row['ID_User'];
            $_SESSION['NAME'] = $row['Nama'];
            $_SESSION['USERNAME'] = $row['Username'];

            header("Location: index.php");
            exit();
        } else {
            $errorMsg = "Username atau password salah.";
        }
    } else {
        $errorMsg = "Username dan password harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Kedai Djoenang - Login</title>
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                            </div>

                            <?php if (!empty($errorMsg)): ?>
                                <div class="alert alert-danger"><?= $errorMsg ?></div>
                            <?php endif; ?>

                            <form method="POST" class="user">
                                <div class="form-group">
                                    <input type="text" name="username" class="form-control form-control-user"
                                        placeholder="Enter Username">
                                </div>
                                <div class="form-group position-relative">
                                    <input type="password" name="password" id="passwordInput" class="form-control form-control-user"
                                        placeholder="Password">
                                    <!-- Ikon Mata -->
                                    <span toggle="#passwordInput" class="fa fa-fw fa-eye toggle-password position-absolute"
                                          style="right: 15px; top: 17px; cursor: pointer;"></span>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary btn-user btn-block">
                                    Login
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sb-admin-2.min.js"></script>

    <!-- Script Toggle Password -->
    <script>
        $(document).ready(function () {
            $('.toggle-password').on('click', function () {
                const input = $($(this).attr('toggle'));
                const type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });
        });
    </script>

</body>

</html>