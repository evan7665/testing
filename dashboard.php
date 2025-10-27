<?php
include "koneksi.php";
include "session.php";







?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
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
                <a class="navbar-brand" href="#">Dashboard</a>
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

            <h3>Presensi Hari Ini</h3>


            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                    <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="<?php echo isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-d'); ?>">
                </div>
                <div class="col-md-5">
                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="<?php echo isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d'); ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </form>
            <br>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <?php
                        if ($_SESSION['jabatan'] == 'wali_kelas') {
                        ?>
                            <th>kelas</th>
                        <?php
                        }
                        ?>
                        <th>Tahun Ajaran</th>
                        <th>Angkatan</th>
                        <?php
                        if ($_SESSION['jabatan'] == 'admin') {
                        ?>
                            <th>Jenjang</th>
                        <?php
                        }
                        ?>
                        <th>Tanggal</th>
                        <th>Waktu Masuk</th>
                        <th>Status Masuk</th>
                        <th>Waktu Pulang</th>
                        <th>Status Pulang</th>
                        <th>diubah terakhir oleh</th>
                        <th>time stamp</th>
                        <th>Aksi</th> <!-- Kolom baru untuk aksi -->
                    </tr>
                </thead>
                <?php
                if ($_SESSION['jabatan'] == 'admin') {
                    $tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-d');
                    $tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
                    $sql = "SELECT 
                        presensi.*,
                        siswa.nama AS nama_siswa,
                        tingkat.tingkat AS tingkat_siswa,
                        angkatan.angkatan AS angkatan_siswa,
                        CONCAT(YEAR(tahun_ajaran.tanggal_awal), '/', YEAR(tahun_ajaran.tanggal_akhir)) AS tahun_ajaran_format
                    FROM 
        presensi
    INNER JOIN 
        kelas_siswa ON presensi.id_pergantian_kelas = kelas_siswa.id_pergantian_kelas
    INNER JOIN 
        siswa ON kelas_siswa.id_siswa = siswa.id_siswa
    INNER JOIN 
        tahun_ajaran ON kelas_siswa.id_tahun_ajaran = tahun_ajaran.id_tahun_ajaran
    INNER JOIN 
        tingkat ON siswa.id_tingkat = tingkat.id_tingkat
    INNER JOIN 
        angkatan ON siswa.id_angkatan = angkatan.id_angkatan
    WHERE 
                        presensi.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                        AND presensi.status_hapus = 0
                        AND tahun_ajaran.status_aktif = '1'
                        AND angkatan.status_hapus = 0
                    ORDER BY 
                        presensi.waktu ASC;
                    ";

                    $result = $conn->query($sql);
                } elseif ($_SESSION['jabatan'] == 'jenjang') {
                    $tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-d');
                    $tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');


                    $id = $_SESSION['id_user'];
                    $quer_jenjang = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
                    $row_jenjangg = mysqli_fetch_array($quer_jenjang);
                    $id_tingkat = $row_jenjangg['id_tingkat'];
                    $sql = "SELECT 
                        presensi.*,
                        siswa.nama AS nama_siswa,
                        tingkat.tingkat AS tingkat_siswa,
                        angkatan.angkatan AS angkatan_siswa,
                        CONCAT(YEAR(tahun_ajaran.tanggal_awal), '/', YEAR(tahun_ajaran.tanggal_akhir)) AS tahun_ajaran_format
                                FROM 
                        presensi
                    INNER JOIN 
                        kelas_siswa ON presensi.id_pergantian_kelas = kelas_siswa.id_pergantian_kelas
                    INNER JOIN 
                        siswa ON kelas_siswa.id_siswa = siswa.id_siswa
                    INNER JOIN 
                        tahun_ajaran ON kelas_siswa.id_tahun_ajaran = tahun_ajaran.id_tahun_ajaran
                    INNER JOIN 
                        tingkat ON siswa.id_tingkat = tingkat.id_tingkat
                    INNER JOIN 
                        angkatan ON siswa.id_angkatan = angkatan.id_angkatan
                    WHERE 
                                        presensi.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                                        AND presensi.status_hapus = 0
                                        AND tahun_ajaran.status_aktif = '1'
                                        and siswa.id_tingkat = '$id_tingkat'
                                        AND angkatan.status_hapus = 0
                                        
                                    ORDER BY 
                                        presensi.waktu ASC;

                        ";
                    $result = $conn->query($sql);
                } elseif ($_SESSION['jabatan'] == 'wali_kelas') {
                    $id = $_SESSION['id_user'];
                    
        
                    $tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-d');
                    $tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');



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

                    // Query Presensi dengan filter untuk banyak kelas
                    $sql = "SELECT 
                    presensi.*,
                    siswa.nama AS nama_siswa,
                    tingkat.tingkat AS tingkat_siswa,
                    angkatan.angkatan AS angkatan_siswa,
                    kelas.kelas, 
                    CONCAT(YEAR(tahun_ajaran.tanggal_awal), '/', YEAR(tahun_ajaran.tanggal_akhir)) AS tahun_ajaran_format
                FROM 
                    presensi
                INNER JOIN 
                    kelas_siswa ON presensi.id_pergantian_kelas = kelas_siswa.id_pergantian_kelas
                INNER JOIN 
                    kelas ON kelas_siswa.id_kelas = kelas.id_kelas
                INNER JOIN 
                    siswa ON kelas_siswa.id_siswa = siswa.id_siswa
                INNER JOIN 
                    tahun_ajaran ON kelas_siswa.id_tahun_ajaran = tahun_ajaran.id_tahun_ajaran
                INNER JOIN 
                    tingkat ON siswa.id_tingkat = tingkat.id_tingkat
                INNER JOIN 
                    angkatan ON siswa.id_angkatan = angkatan.id_angkatan
                WHERE 
                    presensi.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                    AND presensi.status_hapus = 0
                    AND tahun_ajaran.status_aktif = '1'
                    AND kelas_siswa.id_kelas IN ($id_kelas_list)
                    AND angkatan.status_hapus = 0
                ORDER BY 
                    presensi.waktu ASC";

                    $result = $conn->query($sql);
                }
                ?>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . $row['nama_siswa'] . "</td>";
                            if ($_SESSION['jabatan'] == 'wali_kelas') {
                                echo "<td>" . $row['kelas'] . "</td>";
                            }
                            echo "<td>" . $row['tahun_ajaran_format'] . "</td>";
                            echo "<td>" . $row['angkatan_siswa'] . "</td>";
                            if ($_SESSION['jabatan'] == 'admin') {
                                echo "<td>" . $row['tingkat_siswa'] . "</td>";
                            }
                            echo "<td>" . $row['tanggal'] . "</td>";
                            echo "<td>" . $row['waktu'] . "</td>";
                            echo "<td>" . $row['status_masuk'] . "</td>";
                            echo "<td>" . ($row['waktu_pulang'] ?? '-') . "</td>";
                            echo "<td>" . ($row['status_pulang'] ?? '-') . "</td>";
                            echo "<td>" . ($row['pengubah'] ?? '-') . "</td>";
                            echo "<td>" . ($row['time_stamp'] ?? '-') . "</td>";
                            echo "<td><a href='edit_presensi.php?id=" . $row['id_presensi'] . "&tanggal_awal=" . $tanggal_awal . "&tanggal_akhir=" . $tanggal_akhir . "' class='btn btn-sm btn-warning'>Edit</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>Tidak ada data presensi untuk hari ini.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>