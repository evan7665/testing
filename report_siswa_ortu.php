<?php
include "koneksi.php";
session_start();

$id_siswa = $_SESSION['id'];
$query_siswa = mysqli_query($conn, "SELECT * from siswa where id_siswa = '$id_siswa'");
$row_siswa = mysqli_fetch_array($query_siswa);
$nama = $row_siswa['nama'];

// Inisialisasi variabel tanggal
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';


$query_periode = mysqli_query($conn, "SELECT * from tahun_ajaran where status_aktif = '1'");
$row_periode = mysqli_fetch_array($query_periode);

$periode_awal = $row_periode['tanggal_awal'];
$periode_akhir = $row_periode['tanggal_akhir'];

$result =  mysqli_query($conn, "SELECT 
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
    p.tanggal BETWEEN '$periode_awal' AND '$periode_akhir'  -- Ubah sesuai periode
    AND p.status_hapus = 0
    AND s.id_siswa = '$id_siswa' -- Ubah sesuai ID siswa
GROUP BY 
    s.id_siswa, s.nama
ORDER BY 
    persen_hadir DESC;



");
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
        <h3 class="mb-4">Laporan Presensi Siswa Selama Periode <?php echo $periode_awal; ?> sampai <?php echo $periode_akhir; ?></h3>
<div class="row">
    <div class="col-md-6">
        <div class="row">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= $row['nama_siswa']; ?></h5>
                                <p class="card-text">
                                    <strong>Total Hadir:</strong> <?= $row['total_hadir']; ?><br>
                                    <strong>Total Terlambat dari kehadiran:</strong> <?= $row['total_terlambat']; ?><br>
                                    <strong>Total Sakit:</strong> <?= $row['total_sakit']; ?><br>
                                    <strong>Total Izin:</strong> <?= $row['total_izin']; ?><br>
                                    <strong>Total Alfa:</strong> <?= $row['total_alfa']; ?><br>
                                    <strong>Total Hari Aktif:</strong> <?= $row['total_hari_aktif']; ?><br>
                                </p>
                                <p class="card-text">
                                    <strong>Persentase Kehadiran:</strong> <?= $row['persen_hadir']; ?>%<br>
                                    <strong>Persentase Sakit:</strong> <?= $row['persen_sakit']; ?>%<br>
                                    <strong>Persentase Izin:</strong> <?= $row['persen_izin']; ?>%<br>
                                    <strong>Persentase Alfa:</strong> <?= $row['persen_alfa']; ?>%<br>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning">Tidak ada data presensi ditemukan.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6 d-flex align-items-center justify-content-center">
        <canvas id="presensiPieChart" width="200" height="200"></canvas>
    </div>
</div>


            <h3>Filter Laporan Presensi <?php echo $nama; ?></h3>
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

            <?php if ($tanggal_awal && $tanggal_akhir): ?>
                <h4>Hasil Laporan</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Waktu Masuk</th>
                            <th>Status Masuk</th>
                            <th>Waktu Pulang</th>
                            <th>Status Pulang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT tanggal, waktu, status_masuk, waktu_pulang, status_pulang, id_presensi
    FROM presensi 
    JOIN kelas_siswa ON presensi.id_pergantian_kelas = kelas_siswa.id_pergantian_kelas
    WHERE kelas_siswa.id_siswa = ? 
    AND tanggal BETWEEN ? AND ?
    AND presensi.status_hapus = 0";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("iss", $id_siswa, $tanggal_awal, $tanggal_akhir);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['tanggal']}</td>
                                        <td>{$row['waktu']}</td>
                                        <td>{$row['status_masuk']}</td>
                                        <td>{$row['waktu_pulang']}</td>
                                        <td>{$row['status_pulang']}</td>
                                        <td><a href='edit_presensi_report.php?id=" . ($row['id_presensi'] ?? 0) . "&tanggal_awal=" . htmlspecialchars($tanggal_awal) . "&tanggal_akhir=" . htmlspecialchars($tanggal_akhir) . "' class='btn btn-sm btn-warning'>Edit</a></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>Tidak ada data presensi untuk rentang tanggal tersebut.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>




    </div>
    <?php
    $result2 =  mysqli_query($conn, "SELECT 
    s.id_siswa,
    s.nama AS nama_siswa,
    COUNT(CASE WHEN p.status_masuk = 'Hadir' THEN 1 END) AS total_hadir,
    COUNT(CASE WHEN p.status_masuk = 'Sakit' THEN 1 END) AS total_sakit,
    COUNT(CASE WHEN p.status_masuk = 'Izin' THEN 1 END) AS total_izin,
    COUNT(CASE WHEN p.status_masuk = 'Alfa' THEN 1 END) AS total_alfa,
    COUNT(CASE WHEN p.status_masuk = 'Hadir' AND TIME(p.waktu) > '07:10:00' THEN 1 END) AS total_terlambat,
    COUNT(p.tanggal) AS total_hari_aktif,
    ROUND(COUNT(CASE WHEN p.status_masuk = 'Hadir' THEN 1 END) / COUNT(p.tanggal) * 100, 2) AS persen_hadir,
    ROUND(COUNT(CASE WHEN p.status_masuk = 'Sakit' THEN 1 END) / COUNT(p.tanggal) * 100, 2) AS persen_sakit,
    ROUND(COUNT(CASE WHEN p.status_masuk = 'Izin' THEN 1 END) / COUNT(p.tanggal) * 100, 2) AS persen_izin,
    ROUND(COUNT(CASE WHEN p.status_masuk = 'Alfa' THEN 1 END) / COUNT(p.tanggal) * 100, 2) AS persen_alfa,
    ROUND(COUNT(CASE WHEN p.status_masuk = 'Hadir' AND TIME(p.waktu) > '07:10:00' THEN 1 END) / COUNT(p.tanggal) * 100, 2) AS persen_terlambat
FROM 
    presensi p
JOIN 
    kelas_siswa ks ON p.id_pergantian_kelas = ks.id_pergantian_kelas
JOIN 
    siswa s ON ks.id_siswa = s.id_siswa
WHERE 
    p.tanggal BETWEEN '$periode_awal' AND '$periode_akhir' -- Tentukan periode laporan
    AND p.status_hapus = 0
    AND s.id_siswa = '$id_siswa'
GROUP BY 
    s.id_siswa, s.nama
ORDER BY 
    persen_hadir DESC;


");
    $chartData = [];
    if (mysqli_num_rows($result2) > 0) {
        while ($row2 = mysqli_fetch_assoc($result2)) {
            $chartData[] = [
                'nama_siswa' => $row2['nama_siswa'],
                'total_hadir' => $row2['total_hadir'],
                'total_terlambat' => $row2['total_terlambat'],
                'total_sakit' => $row2['total_sakit'],
                'total_izin' => $row2['total_izin'],
                'total_alfa' => $row2['total_alfa']
            ];
        }
    }
    ?>
    <script>
    // Data dari PHP ke JavaScript
    const chartData = <?php echo json_encode($chartData); ?>;

    // Total data per kategori presensi (dengan logika total hadir tidak digandakan oleh terlambat)
    const totalHadir = chartData.reduce((sum, data) => sum + parseInt(data.total_hadir), 0); // Hadir total (tidak termasuk terlambat secara terpisah)
    const totalTerlambat = chartData.reduce((sum, data) => sum + parseInt(data.total_terlambat), 0); // Terlambat hanya sebagai subset
    const totalSakit = chartData.reduce((sum, data) => sum + parseInt(data.total_sakit), 0);
    const totalIzin = chartData.reduce((sum, data) => sum + parseInt(data.total_izin), 0);
    const totalAlfa = chartData.reduce((sum, data) => sum + parseInt(data.total_alfa), 0);

    // Konfigurasi Pie Chart
    const ctx = document.getElementById('presensiPieChart').getContext('2d');
    const presensiPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alfa'],
            datasets: [{
                label: 'Distribusi Kehadiran',
                data: [totalHadir, totalSakit, totalIzin, totalAlfa], // Total data
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)', // Hadir
                    'rgba(255, 205, 86, 0.6)', // Sakit
                    'rgba(54, 162, 235, 0.6)', // Izin
                    'rgba(255, 99, 132, 0.6)'  // Alfa
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Distribusi Kehadiran Siswa'
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const label = tooltipItem.label || '';
                            const value = tooltipItem.raw;
                            if (label === 'Hadir') {
                                return `${label}: ${value} (Termasuk ${totalTerlambat} terlambat)`;
                            }
                            return `${label}: ${value}`;
                        }
                    }
                }
            }
        }
    });
</script>




    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>