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

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
        }

        .card h5 {
            font-size: 1.5rem;
            font-weight: bold;
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
            <div class="row g-4">
                <!-- Card: Presensi Hari Ini -->
                <?php
                if ($_SESSION['jabatan'] == 'admin') {
                    $query = mysqli_query($conn, "SELECT 
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
    presensi.tanggal = CURDATE()
    AND presensi.status_hapus = 0
    AND tahun_ajaran.status_aktif = '1'
    AND angkatan.status_hapus = 0
ORDER BY 
    presensi.waktu ASC;
");
                    $row_query = mysqli_num_rows($query);
                } elseif ($_SESSION['jabatan'] == 'jenjang') {
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
                    presensi.tanggal = CURDATE()
                    AND presensi.status_hapus = 0
                    AND tahun_ajaran.status_aktif = '1'
                    and siswa.id_tingkat = '$id_tingkat'
                    AND angkatan.status_hapus = 0
                ORDER BY 
                    presensi.waktu ASC;

                    ";
                    $query = mysqli_query($conn, $sql);
                    $row_query = mysqli_num_rows($query);
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
                    presensi.tanggal = CURDATE()
                    AND presensi.status_hapus = 0
                    AND tahun_ajaran.status_aktif = '1'
                    and kelas_siswa.id_kelas IN ($id_kelas_list)
                    AND angkatan.status_hapus = 0
                ORDER BY 
                    presensi.waktu ASC;

                    ";
                    $query = mysqli_query($conn, $sql);
                    $row_query = mysqli_num_rows($query);
                }
                ?>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Presensi Hari Ini</h5>
                            <p class="card-text fs-4 text-success"><?php echo $row_query;  ?></p>
                        </div>
                    </div>
                </div>

                <!-- Card: Total Siswa -->
                <?php
                if ($_SESSION['jabatan'] == 'admin') {
                    $query_siswa = mysqli_query($conn, "SELECT * from siswa where status_hapus = '0'");
                    $row_siswa = mysqli_num_rows($query_siswa);
                } elseif ($_SESSION['jabatan'] == 'jenjang') {
                    $id = $_SESSION['id_user'];
                    $quer_jenjang = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
                    $row_jenjangg = mysqli_fetch_array($quer_jenjang);
                    $id_tingkat = $row_jenjangg['id_tingkat'];

                    $query_siswa = mysqli_query($conn, "SELECT * from siswa where status_hapus = '0' and id_tingkat = '$id_tingkat' ");
                    $row_siswa = mysqli_num_rows($query_siswa);
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

                    $query_siswa = mysqli_query($conn, "SELECT * from kelas_siswa where status_hapus = '0' and id_kelas IN ($id_kelas_list) ");
                    $row_siswa = mysqli_num_rows($query_siswa);
                }
                ?>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Siswa</h5>
                            <p class="card-text fs-4 text-primary"><?php echo $row_siswa; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Card: Siswa Tidak Hadir -->
                <?php
                //                 if ($_SESSION['jabatan'] == 'admin') {
                //                     $query_tidak_hadir = mysqli_query($conn, "SELECT 
                //     COUNT(siswa.id_siswa) AS jumlah_siswa_tidak_presensi
                // FROM 
                //     siswa
                // LEFT JOIN 
                //     presensi ON siswa.id_siswa = presensi.id_siswa 
                //     AND presensi.tanggal = CURDATE()
                //     AND presensi.status_masuk = 'Alfa'
                // WHERE 
                //     presensi.id_siswa IS NULL;


                // ");

                //                     $row_tidak_hadir = mysqli_fetch_array($query_tidak_hadir);
                //                 } elseif ($_SESSION['jabatan'] == 'jenjang') {
                //                     $id = $_SESSION['id_user'];
                //                     $quer_jenjang3 = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
                //                     $row_jenjangg3 = mysqli_fetch_array($quer_jenjang3);
                //                     $id_tingkat3 = $row_jenjangg3['id_tingkat'];

                //                     $query_tidak_hadir = mysqli_query($conn, "SELECT 
                //     COUNT(siswa.id_siswa) AS jumlah_siswa_tidak_presensi, 
                //     tingkat.*
                // FROM 
                //     siswa
                // LEFT JOIN 
                //     presensi ON siswa.id_siswa = presensi.id_siswa 
                //     AND presensi.tanggal = CURDATE()
                //     and presensi.status_masuk = 'Alfa'
                // INNER JOIN 
                //     tingkat ON siswa.id_tingkat = tingkat.id_tingkat 
                // WHERE 
                //     presensi.id_siswa IS NULL
                //     AND tingkat.id_tingkat = '$id_tingkat3'


                // ");

                //                     $row_tidak_hadir = mysqli_fetch_array($query_tidak_hadir);
                //                 }
                ?>
                <!-- <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Siswa Tidak Hadir</h5>
                            <p class="card-text fs-4 text-danger"><?php $row_tidak_hadir['jumlah_siswa_tidak_presensi']; ?></p>
                        </div>
                    </div>
                </div> -->

                <!-- Card: Aktivitas Terbaru -->
                <?php
                if ($_SESSION['jabatan'] == 'admin') {
                    $query_aktivitas = mysqli_query($conn, "SELECT 
            presensi.*,
            siswa.nama AS nama_siswa,
            siswa.id_siswa,
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
            presensi.tanggal = CURDATE()
            AND presensi.status_hapus = 0
            AND tahun_ajaran.status_aktif = '1'
            AND angkatan.status_hapus = 0
        ORDER BY 
            presensi.waktu ASC
    ");
                } elseif ($_SESSION['jabatan'] == 'jenjang') {
                    $id = $_SESSION['id_user'];
                    $quer_jenjang2 = mysqli_query($conn, "SELECT * FROM jenjang WHERE id_user = '$id'");
                    $row_jenjangg2 = mysqli_fetch_array($quer_jenjang2);
                    $id_tingkat2 = $row_jenjangg2['id_tingkat'];
                    $query_aktivitas = mysqli_query($conn, "SELECT 
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
            presensi.tanggal = CURDATE()
            AND presensi.status_hapus = 0
            AND tahun_ajaran.status_aktif = '1'
            AND siswa.id_tingkat = '$id_tingkat2'
            AND angkatan.status_hapus = 0
        ORDER BY 
            presensi.waktu ASC
    ");
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

                    $query_aktivitas = mysqli_query($conn, "SELECT 
            presensi.*,
            siswa.nama AS nama_siswa,
            tingkat.tingkat AS tingkat_siswa,
            kelas_siswa.id_siswa,
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
            presensi.tanggal = CURDATE()
            AND presensi.status_hapus = 0
            AND tahun_ajaran.status_aktif = '1'
            AND kelas_siswa.id_kelas IN ($id_kelas_list)
            AND angkatan.status_hapus = 0
        ORDER BY 
            presensi.waktu ASC
    ");
                }
                ?>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Aktivitas Terbaru</h5>
                            <ul class="list-group list-group-flush">
                                <?php
                                if ($query_aktivitas && mysqli_num_rows($query_aktivitas) > 0) {
                                    while ($row_aktivitas = mysqli_fetch_array($query_aktivitas)) {
                                ?>
                                        <li class="list-group-item">
                                            <?php echo htmlspecialchars($row_aktivitas['nama_siswa']); ?> melakukan presensi
                                            <?php
                                            if ($_SESSION['jabatan'] == 'admin') {
                                                // Ambil tingkat dari siswa berdasarkan ID siswa di aktivitas
                                                $query_anak_jenjang  = mysqli_query(
                                                    $conn,
                                                    "SELECT tingkat.tingkat 
            FROM siswa 
            JOIN tingkat ON siswa.id_tingkat = tingkat.id_tingkat 
            WHERE siswa.id_siswa = '{$row_aktivitas['id_siswa']}'"
                                                );

                                                $row_anak_jenjang = mysqli_fetch_array($query_anak_jenjang);
                                                echo " pada tingkat " . htmlspecialchars($row_anak_jenjang['tingkat']);
                                            } elseif ($_SESSION['jabatan'] == 'wali_kelas') {
                                                $id_user = $_SESSION['id_user'];

                                                // Cari ID Wali Kelas
                                                $query_wali_kelas = mysqli_query(
                                                    $conn,
                                                    "SELECT id_wali_kelas FROM wali_kelas WHERE id_user = '$id_user'"
                                                );
                                                $row_wali_kelas = mysqli_fetch_array($query_wali_kelas);
                                                $id_wali_kelas = $row_wali_kelas['id_wali_kelas'];

                                                // Cari Tahun Ajaran Aktif
                                                $query_periode = mysqli_query(
                                                    $conn,
                                                    "SELECT id_tahun_ajaran FROM tahun_ajaran WHERE status_aktif = '1'"
                                                );
                                                $row_periode = mysqli_fetch_array($query_periode);
                                                $id_tahun_ajaran = $row_periode['id_tahun_ajaran'];

                                                // Cari Semua Kelas yang Diampu oleh Wali Kelas
                                                $query_kelas_wali = mysqli_query(
                                                    $conn,
                                                    "SELECT id_kelas FROM wali_kelas_periode 
            WHERE id_wali_kelas = '$id_wali_kelas' AND id_tahun_ajaran = '$id_tahun_ajaran'"
                                                );

                                                $id_kelas_array = [];
                                                while ($row_kelas = mysqli_fetch_array($query_kelas_wali)) {
                                                    $id_kelas_array[] = $row_kelas['id_kelas'];
                                                }

                                                // Ambil id_kelas siswa berdasarkan aktivitas
                                                $query_kelas_siswa = mysqli_query(
                                                    $conn,
                                                    "SELECT  
                                                    kelas.id_kelas,
                                                    kelas.kelas 
                                                    FROM siswa 
                                                    JOIN kelas_siswa ON siswa.id_siswa = kelas_siswa.id_siswa  -- ✅ Tambahkan JOIN ke kelas_siswa
                                                    JOIN kelas ON kelas_siswa.id_kelas = kelas.id_kelas 
                                                                WHERE siswa.id_siswa = '{$row_aktivitas['id_siswa']}'"
                                                );

                                                $row_kelas_siswa = mysqli_fetch_array($query_kelas_siswa);
                                                $kelas_siswa = $row_kelas_siswa['kelas'];

                                                // Cek apakah kelas siswa ada dalam daftar wali kelas
                                                if (in_array($row_kelas_siswa['id_kelas'], $id_kelas_array)) {
                                                    echo " pada kelas " . htmlspecialchars($kelas_siswa);
                                                } else {
                                                    echo " di kelas yang tidak diajar oleh wali kelas ini";
                                                }
                                            }
                                            ?>
                                        </li>

                                    <?php
                                    }
                                } else {
                                    ?>
                                    <li class="list-group-item">Tidak ada aktivitas terbaru hari ini.</li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>


                <!-- Card: Statistik Presensi -->
                <?php
                if ($_SESSION['jabatan'] == 'admin') {
                    $query5 = "SELECT 
        CASE 
            WHEN DAYNAME(presensi.tanggal) = 'Monday' THEN 'Senin'
            WHEN DAYNAME(presensi.tanggal) = 'Tuesday' THEN 'Selasa'
            WHEN DAYNAME(presensi.tanggal) = 'Wednesday' THEN 'Rabu'
            WHEN DAYNAME(presensi.tanggal) = 'Thursday' THEN 'Kamis'
            WHEN DAYNAME(presensi.tanggal) = 'Friday' THEN 'Jumat'
        END AS hari,
        COUNT(kelas_siswa.id_siswa) AS jumlah_presensi
    FROM 
        presensi
        INNER JOIN 
    kelas_siswa ON presensi.id_pergantian_kelas = kelas_siswa.id_pergantian_kelas
    WHERE 
        presensi.tanggal >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) + 7 DAY)
        AND presensi.tanggal <= CURDATE()
        AND DAYOFWEEK(presensi.tanggal) BETWEEN 2 AND 6
        AND presensi.status_masuk != 'alfa'
    GROUP BY 
        hari
    ORDER BY 
        FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')
";


                    $result = mysqli_query($conn, $query5);

                    // Data untuk Chart.js
                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    $data = array_fill_keys($days, 0);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $dayName = $row['hari'];
                        $data[$dayName] = (int)$row['jumlah_presensi'];
                    }

                    // Convert data to JavaScript array
                    $labels = json_encode(array_keys($data));
                    $values = json_encode(array_values($data));
                } elseif ($_SESSION['jabatan'] == 'jenjang') {
                    $id = $_SESSION['id_user'];
                    $quer_jenjang6 = mysqli_query($conn, "SELECT * FROM jenjang WHERE id_user = '$id'");
                    $row_jenjangg6 = mysqli_fetch_array($quer_jenjang6);
                    $id_tingkat6 = $row_jenjangg6['id_tingkat'];

                    $query5 = "SELECT 
        CASE 
            WHEN DAYNAME(presensi.tanggal) = 'Monday' THEN 'Senin'
            WHEN DAYNAME(presensi.tanggal) = 'Tuesday' THEN 'Selasa'
            WHEN DAYNAME(presensi.tanggal) = 'Wednesday' THEN 'Rabu'
            WHEN DAYNAME(presensi.tanggal) = 'Thursday' THEN 'Kamis'
            WHEN DAYNAME(presensi.tanggal) = 'Friday' THEN 'Jumat'
        END AS hari,
        COUNT(kelas_siswa.id_siswa) AS jumlah_presensi
    FROM 
        presensi
       INNER JOIN   
    kelas_siswa ON presensi.id_pergantian_kelas = kelas_siswa.id_pergantian_kelas
INNER JOIN 
    siswa ON kelas_siswa.id_siswa = siswa.id_siswa
        INNER JOIN 
            tingkat ON siswa.id_tingkat = tingkat.id_tingkat
    WHERE 
        presensi.tanggal >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) + 7 DAY)
        AND presensi.tanggal <= CURDATE()
        AND DAYOFWEEK(presensi.tanggal) BETWEEN 2 AND 6
        AND presensi.status_masuk != 'alfa'
        AND tingkat.id_tingkat = $id_tingkat6
    GROUP BY 
        hari
    ORDER BY 
        FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')
    ";

                    $result = mysqli_query($conn, $query5);

                    // Data untuk Chart.js
                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    $data = array_fill_keys($days, 0);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $dayName = $row['hari'];
                        $data[$dayName] = (int)$row['jumlah_presensi'];
                    }

                    // Convert data to JavaScript array
                    $labels = json_encode(array_keys($data));
                    $values = json_encode(array_values($data));
                }
                ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Statistik Presensi</h5>
                            <canvas id="chartPresensi" height="200"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Ambil data dari PHP
        const labels = <?php echo $labels; ?>;
        const data = <?php echo $values; ?>;

        // Chart.js Configuration
        const ctx = document.getElementById('chartPresensi').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Presensi',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                    }
                }
            }
        });
    </script>
</body>

</html>