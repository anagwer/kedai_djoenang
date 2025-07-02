<?php
include_once('dbcon.php');
?>
<?php include('head.php'); ?>
<!-- Main Content -->
<div class="container-fluid mt-4">
    <h1 class="h4 mb-4 text-gray-800">Laporan Penjualan</h1>

    <!-- Form Filter Tanggal -->
    <form method="post" class="mb-4">
        <div class="form-row">
            <div class="col-md-4">
                <label for="tanggal_awal">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="tanggal_akhir">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-sm btn-info btn-block" onclick="printNota()">
                    <i class="fas fa-download"></i> Export Report
                </button>
            </div>
        </div>
    </form>

    <script>
    function printNota() {
        const tanggal1 = document.getElementById('tanggal_awal').value;
        const tanggal2 = document.getElementById('tanggal_akhir').value;

        if (!tanggal1 || !tanggal2) {
            alert("Silakan isi kedua tanggal terlebih dahulu.");
            return;
        }

        const win = window.open('cetak_laporan.php?tanggal1='+tanggal1 + '&tanggal2='+ tanggal2, '_blank');
        win.onload = function () {
        win.print();
        win.onafterprint = function () {
            win.close();
        };
    };
    }
    </script>


    <!-- Tabel Data Laporan -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
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
                      // Proses filter tanggal jika tombol "Cari" ditekan
                      if (isset($_POST['filter'])) {
                          $tanggal_awal = $_POST['tanggal_awal'];
                          $tanggal_akhir = $_POST['tanggal_akhir'];

                          // Validasi input tanggal
                          if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                              // Query untuk mengambil data laporan penjualan
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
                                      p.Nama_Produk
                                  ORDER BY 
                                      t.Tanggal ASC, p.Nama_Produk ASC;
                              ";

                              $result = $conn->query($query);
                          } else {
                              $result = null;
                          }
                      } else {
                          // Jika belum ada filter, tampilkan semua data penjualan
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
                              t.Status = 'Final'
                              GROUP BY 
                                  p.Nama_Produk
                              ORDER BY 
                                  t.Tanggal ASC, p.Nama_Produk ASC;
                          ";


                          $result = $conn->query($query);
                      }
                      // Hitung total penjualan keseluruhan
                      $total_penjualan = 0;
                      while ($row = $result->fetch_assoc()){
                      ?>
                      <tr>
                          <td><?= $no++; ?></td>
                          <td><?= $row['Tanggal']; ?></td>
                          <td><?= $row['Nama_Produk']; ?></td>
                          <td><?= $row['Jumlah_Terjual']; ?></td>
                          <td>Rp <?= number_format($row['Subtotal_Penjualan'], 0, ',', '.'); ?></td>
                      </tr>
                      <?php 
                    $total_penjualan += $row['Subtotal_Penjualan'];
                    } ?>
                  </tbody>
                  

                </table>
            </div>
        </div>
    </div>

    <!-- Total Penjualan Keseluruhan -->
    <div class="card shadow mt-4">
        <div class="card-body">
            <h5 class="card-title">Total Penjualan Keseluruhan:</h5>
            <h4 class="text-right">Rp <?= number_format($total_penjualan, 0, ',', '.'); ?></h4>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>