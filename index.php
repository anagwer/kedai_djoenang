<?php include ('head.php');?>

<?php
// Include database connection
include_once('dbcon.php');

// Get today's date
$tanggal_hari_ini = date('Y-m-d');

// Query 1: Total Penjualan Hari Ini
$total_penjualan_query = $conn->query("SELECT SUM(Total) AS total_sales FROM transaksi WHERE Tanggal = '$tanggal_hari_ini'");
$total_penjualan_row = $total_penjualan_query->fetch_assoc();
$total_penjualan = isset($total_penjualan_row['total_sales']) ? number_format($total_penjualan_row['total_sales'], 0, ',', '.') : 0;

// Query 2: Total Produk
$total_produk_query = $conn->query("SELECT COUNT(*) AS total_products FROM produk");
$total_produk_row = $total_produk_query->fetch_assoc();
$total_produk = $total_produk_row['total_products'];

// Query 3: Stok Sedikit (Jumlah_Stok < 5)
$stok_sedikit_query = $conn->query("SELECT COUNT(*) AS low_stock FROM stok_barang WHERE Jumlah_Stok < 4");
$stok_sedikit_row = $stok_sedikit_query->fetch_assoc();
$stok_sedikit = $stok_sedikit_row['low_stock'];
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Card 1: Penjualan Hari Ini -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Penjualan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo $total_penjualan; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Produk -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Produk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_produk; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Stok Sedikit -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Stok Sedikit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stok_sedikit; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelola Section -->
        <div class="col-xl-12 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Kelola</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-4 text-center">
                                    <a href="produk.php" class="btn">
                                        <span class="icon text-primary">
                                            <i class="fas fa-plus" style="font-size:50px;"></i><br>
                                        </span>
                                        <span class="text" style="font-size:20px;">Tambah Produk</span>
                                    </a>
                                </div>
                                <div class="col-4 text-center">
                                    <a href="edit_produk.php" class="btn">
                                        <span class="icon text-primary">
                                            <i class="fas fa-edit" style="font-size:50px;"></i><br>
                                        </span>
                                        <span class="text" style="font-size:20px;">Edit Produk</span>
                                    </a>
                                </div>
                                <div class="col-4 text-center">
                                    <a href="hapus_produk.php" class="btn">
                                        <span class="icon text-primary">
                                            <i class="fas fa-trash" style="font-size:50px;"></i><br>
                                        </span>
                                        <span class="text" style="font-size:20px;">Hapus Produk</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Transaksi Penjualan</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-4 text-center">
                                    <a href="tambah_produk.php" class="btn">
                                        <span class="icon text-primary">
                                            <i class="fas fa-shopping-cart " style="font-size:50px;"></i><br>
                                        </span>
                                        <span class="text" style="font-size:20px;">Penjualan Baru</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- /.container-fluid -->


<?php include ('footer.php');?>