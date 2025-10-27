<?php
include "koneksi.php";
include "session.php";

$id_kelas = $_GET['id_kelas'];
$query_nama = mysqli_query($conn, "SELECT * from kelas where id_kelas = '$id_kelas'");
$row_nama = mysqli_fetch_array($query_nama);

$query_periode = mysqli_query($conn, "SELECT * from tahun_ajaran where status_aktif = '1'");
$row_periode = mysqli_fetch_array($query_periode);

$periode_awal = $row_periode['tanggal_awal'];
$periode_akhir = $row_periode['tanggal_akhir'];
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
                <a class="navbar-brand" href="#">siswa dan kelas untuk periode depan </a>
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
                <h2 class="mb-4">Daftar Siswa Kelas</h2>

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Siswa</th>
                            <th>Nama</th>

                            <th>Kelas Tujuan</th>
                        </tr>
                    </thead>
                    <?php
                    // Pastikan parameter tidak kosong
                    if ($id_kelas && $periode_awal && $periode_akhir) {
                        $sql = "
        SELECT 
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
            presensi p
        JOIN 
            kelas_siswa ks ON p.id_pergantian_kelas = ks.id_pergantian_kelas
        JOIN 
            siswa s ON ks.id_siswa = s.id_siswa
        WHERE 
            p.tanggal BETWEEN '$periode_awal' AND '$periode_akhir'
            AND p.status_hapus = 0 
            AND ks.id_kelas = '$id_kelas'
        GROUP BY 
            s.id_siswa, s.nama
        ORDER BY 
            persen_hadir DESC;
    ";

                        $result = $conn->query($sql);
                    ?>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) {
                                $query_tahun_ajaran =  mysqli_query($conn, "SELECT t1.id_tahun_ajaran, t1.tanggal_awal, t1.tanggal_akhir
                                FROM tahun_ajaran t1
                                JOIN tahun_ajaran t2 ON t2.status_aktif = 1
                                WHERE t1.status_aktif != 1
                                AND ABS(DATEDIFF(t1.tanggal_awal, t2.tanggal_akhir)) < 365
                                ORDER BY ABS(DATEDIFF(t1.tanggal_awal, t2.tanggal_akhir)) ASC
                                LIMIT 1;");
                                $row_tahun_ajaran = mysqli_fetch_array($query_tahun_ajaran);
                                $query_coba_test = mysqli_query($conn, "SELECT ks.*, k.kelas 
FROM kelas_siswa ks
JOIN kelas k ON ks.id_kelas = k.id_kelas
WHERE ks.id_siswa = '$row[id_siswa]' 
  AND ks.id_tahun_ajaran = '$row_tahun_ajaran[id_tahun_ajaran]';
");
                                $row_kelas_tujuan = mysqli_fetch_array($query_coba_test);
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_siswa']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                    <td>
                                        <?php
                                        if (!empty($row_kelas_tujuan)) {
                                            echo htmlspecialchars($row_kelas_tujuan['kelas']);
                                        } else {
                                            echo '<span style="color: red; font-weight: bold;">Belum dinaikkan kelas</span>';
                                        }
                                        ?>
                                    </td>

                                </tr>
                        <?php }
                        } else {
                            echo "Silakan masukkan parameter id_kelas, periode_awal, dan periode_akhir.";
                        } ?>
                        </tbody>
                </table>
            </div>

            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <!-- Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>