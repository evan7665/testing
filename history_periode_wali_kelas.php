<?php
include "koneksi.php";
include "session.php";

$id_wali_kelas = mysqli_real_escape_string($conn, $_GET['id_wali_kelas']);




$id_tahun_ajaran = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '';

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
                <a class="navbar-brand" href="#">siswa dan kelas</a>
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
            <div class="container mt-4 ">




                <form method="GET" class="row g-3">
                    <input type="hidden" name="id_wali_kelas" value="<?= $id_wali_kelas ?>">
                    <div class="col-md-5">
                        <select name="tahun_ajaran" id="tahun_ajaran" class="form-select">
                            <option value="" disabled selected>--pilih tahun ajaran--</option>
                            <?php
                            $query_tahun_ajaran = mysqli_query($conn, "SELECT *,CONCAT(YEAR(tahun_ajaran.tanggal_awal), '/', YEAR(tahun_ajaran.tanggal_akhir)) AS tahun_ajaran_format from tahun_ajaran where status_hapus = 0");
                            while ($row_tahun_ajaran = mysqli_fetch_array($query_tahun_ajaran)) {
                            ?>
                                <option value="<?php echo $row_tahun_ajaran['id_tahun_ajaran']; ?>"><?php echo $row_tahun_ajaran['tahun_ajaran_format']; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </div>

                </form>
                <?php if ($id_tahun_ajaran): ?>


                    <?php

                    $querykelas  = mysqli_query($conn, "SELECT * FROM wali_kelas_periode WHERE id_wali_kelas = '$id_wali_kelas' AND id_tahun_ajaran = '$id_tahun_ajaran'");
                    $rowkelas = mysqli_fetch_assoc($querykelas);

                    if (!$rowkelas) {
                        echo "<p class='text-danger'>Data wali kelas tidak ditemukan.</p>";
                    } else {
                        $id_kelas = $rowkelas['id_kelas'];

                        $query_nama = mysqli_query($conn, "SELECT * FROM kelas WHERE id_kelas = '$id_kelas'");
                        $row_nama = mysqli_fetch_array($query_nama);

                        $query_periode = mysqli_query($conn, "SELECT * FROM tahun_ajaran WHERE id_tahun_ajaran = '$id_tahun_ajaran'");
                        $row_periode = mysqli_fetch_array($query_periode);

                        $id_tahun_ajaran = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '';
                        $periode_awal = $row_periode['tanggal_awal'];
                        $periode_akhir = $row_periode['tanggal_akhir'];
                    }





                    $query = "SELECT 
                        s.id_siswa,
                        s.nama AS nama_siswa,
                        COUNT(CASE WHEN p.status_masuk = 'Hadir' THEN 1 END) AS total_hadir,
                        COUNT(CASE WHEN p.status_masuk = 'Sakit' THEN 1 END) AS total_sakit,
                        COUNT(CASE WHEN p.status_masuk = 'Izin' THEN 1 END) AS total_izin,
                        COUNT(CASE WHEN p.status_masuk = 'Alfa' THEN 1 END) AS total_alfa,
                        COUNT(CASE WHEN p.status_masuk = 'Hadir' AND TIME(p.waktu) > '07:10:00' THEN 1 END) AS total_terlambat,
                        COUNT(p.tanggal) AS total_hari_aktif,
                        ROUND(COUNT(CASE WHEN p.status_masuk = 'Hadir' THEN 1 END) / NULLIF(COUNT(p.tanggal), 0) * 100, 2) AS persen_hadir,
                        ROUND(COUNT(CASE WHEN p.status_masuk = 'Sakit' THEN 1 END) / NULLIF(COUNT(p.tanggal), 0) * 100, 2) AS persen_sakit,
                        ROUND(COUNT(CASE WHEN p.status_masuk = 'Izin' THEN 1 END) / NULLIF(COUNT(p.tanggal), 0) * 100, 2) AS persen_izin,
                        ROUND(COUNT(CASE WHEN p.status_masuk = 'Alfa' THEN 1 END) / NULLIF(COUNT(p.tanggal), 0) * 100, 2) AS persen_alfa,
                        ROUND(COUNT(CASE WHEN p.status_masuk = 'Hadir' AND TIME(p.waktu) > '07:10:00' THEN 1 END) / NULLIF(COUNT(p.tanggal), 0) * 100, 2) AS persen_terlambat
                    FROM 
                        siswa s
                    LEFT JOIN 
                        kelas_siswa ks ON s.id_siswa = ks.id_siswa
                    LEFT JOIN 
                        presensi p ON ks.id_pergantian_kelas = p.id_pergantian_kelas 
                        AND p.tanggal BETWEEN ? AND ?
                        AND p.status_hapus = 0
                    WHERE 
                        ks.id_kelas = ?
                    GROUP BY 
                        s.id_siswa, s.nama
                    ORDER BY 
                        persen_hadir DESC;

                        ";


                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssi", $periode_awal, $periode_akhir, $id_kelas);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    ?>
                    <?php
                    if (!$id_kelas) {
                    } else {
                        echo "<h4>Hasil Laporan Kelas " . htmlspecialchars($row_nama['kelas']) . "</h4>";
                    }
                    ?>
                    <table class="table table-bordered">
                        <div class="mt-3 mb-3 ">

                            <a href="export_rekap.php?id_kelas=<?php echo htmlspecialchars($id_kelas); ?>" class="btn btn-primary">Download Rekap Kelas</a>
                        </div>
                        <thead class="table-dark"></thead>
                        <tr>
                            <th>ID Siswa</th>
                            <th>Nama</th>
                            <th>Hadir</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alfa</th>
                            <th>Terlambat</th>
                            <th>Hari Aktif</th>
                            <th>% Hadir</th>
                            <th>% Sakit</th>
                            <th>% Izin</th>
                            <th>% Alfa</th>
                            <th>% Terlambat</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php


                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
            <td>{$row['id_siswa']}</td>
            <td>{$row['nama_siswa']}</td>
            <td>{$row['total_hadir']}</td>
            <td>{$row['total_sakit']}</td>
            <td>{$row['total_izin']}</td>
            <td>{$row['total_alfa']}</td>
            <td>{$row['total_terlambat']}</td>
            <td>{$row['total_hari_aktif']}</td>
            <td>{$row['persen_hadir']}%</td>
            <td>{$row['persen_sakit']}%</td>
            <td>{$row['persen_izin']}%</td>
            <td>{$row['persen_alfa']}%</td>
            <td>{$row['persen_terlambat']}%</td>
        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='13' class='text-center'>Tidak ada data tersedia</td></tr>";
                            }


                            ?>

                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>