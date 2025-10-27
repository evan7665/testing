<?php
include "koneksi.php";
include "session.php";



// Inisialisasi variabel tanggal
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';


$query_periode = mysqli_query($conn, "SELECT * from tahun_ajaran where status_aktif = '1'");
$row_periode = mysqli_fetch_array($query_periode);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
        }

        .sidebar a:hover {
            background-color: #495057;
            border-radius: 5px;
        }

        .content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .navbar {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <?php include "navbar.php"; ?>

    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"> Data Siswa</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Notifications</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-4">



            <h3>Filter Laporan Presensi</h3>
            <form method="GET" class="row g-3">

                <div class="col-md-5">
                    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                    <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal">
                </div>
                <div class="col-md-5">
                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </form>

            <hr>
            <?php if (isset($tanggal_awal) && isset($tanggal_akhir)): ?>
                <h4>Hasil Laporan</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <?php if ($_SESSION['jabatan'] == 'wali_kelas'): ?>
                                <th>Kelas</th>
                            <?php endif; ?>
                            <th>Tanggal</th>
                            <th>Waktu Masuk</th>
                            <th>Status Masuk</th>
                            <?php if ($_SESSION['jabatan'] == 'admin'): ?>
                                <th>Tingkat</th>
                            <?php endif; ?>
                            <th>Waktu Pulang</th>
                            <th>Status Pulang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($_SESSION['jabatan'] == 'admin') {
                            $query = "SELECT 
    p.tanggal, 
    p.waktu, 
    p.status_masuk, 
    p.waktu_pulang, 
    p.status_pulang, 
    p.id_presensi, 
    s.nama AS nama_siswa, 
    t.tingkat AS tingkat_siswa
FROM presensi p
JOIN kelas_siswa ks ON p.id_pergantian_kelas = ks.id_pergantian_kelas
JOIN siswa s ON ks.id_siswa = s.id_siswa
JOIN tingkat t ON s.id_tingkat = t.id_tingkat
WHERE p.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
  AND p.status_hapus = 0
ORDER BY p.tanggal ASC;

";

                            $query_con = mysqli_query($conn, $query);
                        } elseif ($_SESSION['jabatan'] == 'jenjang') {
                            $id = $_SESSION['id_user'];
                            $quer_jenjang = mysqli_query($conn, "SELECT * FROM jenjang WHERE id_user = '$id'");
                            $row_jenjangg = mysqli_fetch_array($quer_jenjang);
                            $id_tingkat = $row_jenjangg['id_tingkat'];

                            $query = "SELECT 
                            p.tanggal, 
                            p.waktu, 
                            p.status_masuk, 
                            p.waktu_pulang, 
                            p.status_pulang, 
                            p.id_presensi, 
                            s.nama AS nama_siswa
                        FROM presensi p
                       JOIN kelas_siswa ks ON p.id_pergantian_kelas = ks.id_pergantian_kelas
JOIN siswa s ON ks.id_siswa = s.id_siswa
JOIN tingkat t ON s.id_tingkat = t.id_tingkat
                        WHERE p.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                        AND p.status_hapus = 0
                        AND s.id_tingkat = '$id_tingkat'
                        ORDER BY p.tanggal ASC";

                            $query_con = mysqli_query($conn, $query);
                        } elseif ($_SESSION['jabatan'] == 'wali_kelas') {
                            $id = $_SESSION['id_user'];

                            // Cari ID Wali Kelas
                            $quer_cari_kelas = mysqli_query($conn, "SELECT * FROM wali_kelas WHERE id_user = '$id'");
                            $row_cari_kelas = mysqli_fetch_array($quer_cari_kelas);
                            $id_wali_kelas = $row_cari_kelas['id_wali_kelas'];

                            // Cari Tahun Ajaran Aktif
                            $cari_periode = mysqli_query($conn, "SELECT * FROM tahun_ajaran WHERE status_aktif = '1'");
                            $row_cari_periode = mysqli_fetch_array($cari_periode);
                            $id_tahun_ajaran = $row_cari_periode['id_tahun_ajaran'];

                            // Cari Semua Kelas yang Diampu oleh Wali Kelas
                            $quer_walikelas = mysqli_query($conn, "SELECT id_kelas FROM wali_kelas_periode WHERE id_wali_kelas = '$id_wali_kelas' AND id_tahun_ajaran = '$id_tahun_ajaran'");

                            // Simpan semua ID Kelas dalam array
                            $id_kelas_array = [];
                            while ($row_wali_kelas = mysqli_fetch_array($quer_walikelas)) {
                                $id_kelas_array[] = $row_wali_kelas['id_kelas'];
                            }

                            // Jika ada lebih dari satu kelas, buat format untuk SQL IN
                            if (!empty($id_kelas_array)) {
                                $id_kelas_list = "'" . implode("','", $id_kelas_array) . "'";
                            } else {
                                $id_kelas_list = "''"; // Default kosong agar tidak error
                            }


                            $query = "SELECT 
                            p.tanggal, 
                            p.waktu, 
                            p.status_masuk, 
                            p.waktu_pulang, 
                            p.status_pulang, 
                            p.id_presensi, 
                            k.kelas,
                            s.nama AS nama_siswa
                        FROM presensi p
                       JOIN kelas_siswa ks ON p.id_pergantian_kelas = ks.id_pergantian_kelas
                       JOIN kelas k ON k.id_kelas = ks.id_kelas
                        JOIN siswa s ON ks.id_siswa = s.id_siswa
                        JOIN tingkat t ON s.id_tingkat = t.id_tingkat
                        WHERE p.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                        AND p.status_hapus = 0
                        AND ks.id_kelas IN ($id_kelas_list)
                        ORDER BY p.tanggal ASC";

                            $query_con = mysqli_query($conn, $query);
                        }

                        if (isset($query_con) && mysqli_num_rows($query_con) > 0) {
                            while ($row = mysqli_fetch_assoc($query_con)) {
                                echo "<tr>
                            <td>{$row['nama_siswa']}</td>
                            ";

                                if ($_SESSION['jabatan'] == 'wali_kelas') {
                                    echo "<td>{$row['kelas']}</td>";
                                }

                                echo "
                            <td>{$row['tanggal']}</td>
                            <td>{$row['waktu']}</td>
                            <td>{$row['status_masuk']}</td>";

                                if ($_SESSION['jabatan'] == 'admin') {
                                    echo "<td>{$row['tingkat_siswa']}</td>";
                                }

                                echo "<td>{$row['waktu_pulang']}</td>
                          <td>{$row['status_pulang']}</td>
                          <td>
                              <a href='edit_presensi_presensi.php?id=" . ($row['id_presensi'] ?? 0) . "&tanggal_awal=" . htmlspecialchars($tanggal_awal) . "&tanggal_akhir=" . htmlspecialchars($tanggal_akhir) . "' 
                                 class='btn btn-sm btn-warning'>Edit</a>
                          </td>
                          </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>Tidak ada data presensi untuk rentang tanggal tersebut.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>


        </div>




    </div>






    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>