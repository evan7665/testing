    <?php
    include "koneksi.php";
    include "session.php";

    $id_siswa = $_GET['id_siswa'];

    $query_siswa = mysqli_query($conn, "SELECT * from siswa where id_siswa = '$id_siswa'");
    $row_siswa = mysqli_fetch_array($query_siswa);
    $nama = $row_siswa['nama'];

    $tahun_ajaran = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '';
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <a class="navbar-brand" href="#">Laporan Periode Siswa</a>
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
                <h3>Filter Periode <?php echo $nama; ?> </h3>
                <form method="GET" class="row g-3">
                    <input type="hidden" name="id_siswa" value="<?= $id_siswa ?>">
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

                <hr>

                <?php if ($tahun_ajaran): ?>
                    <h4>Hasil Laporan</h4>

                    <?php
                    $query_id_tahun = mysqli_query($conn, "SELECT * from tahun_ajaran where id_tahun_ajaran = '$tahun_ajaran'");
                    $row_id_tahun = mysqli_fetch_array($query_id_tahun);
                    $tanggal_awal = $row_id_tahun['tanggal_awal'];
                    $tanggal_akhir = $row_id_tahun['tanggal_akhir'];

                    $query = "SELECT 
            s.id_siswa,
            ks.id_kelas,
            k.kelas,
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
    kelas k ON k.id_kelas = ks.id_kelas
	JOIN 
    siswa s ON ks.id_siswa = s.id_siswa
        WHERE 
            p.tanggal BETWEEN ? AND ? -- Tentukan periode laporan
            AND p.status_hapus = 0
            AND s.id_siswa = ?
        GROUP BY 
            s.id_siswa, s.nama
        ORDER BY 
            persen_hadir DESC;";


                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssi", $tanggal_awal, $tanggal_akhir, $id_siswa);
                    $stmt->execute();
                    $result = $stmt->get_result();


                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <div class="row">
                                <!-- Bagian Laporan -->
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= $row['nama_siswa']; ?> di Kelas : <?php echo $row['kelas']; ?> </h5>
                                            <p class="card-text">
                                                <strong>Total Hadir:</strong> <?= $row['total_hadir']; ?><br>
                                                <strong>Total Terlambat:</strong> <?= $row['total_terlambat']; ?><br>
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

                                <!-- Bagian Chart -->
                                <div class="col-md-6 d-flex align-items-center justify-content-center">
                                    <canvas id="presensiPieChart" width="200" height="200"></canvas>
                                </div>





                                <h3> Daftar Alfa, Izin, Terlambat</h3>


                                <table class="table table-bordered">
                                   
                                    <thead class="table-dark">
                                        <tr>
                                            
                                            <th>Nama</th>
                                            <th>Kelas</th>
                                            <th>tanggal</th>
                                            <th>Waktu Masuk</th>
                                            <th>Status Masuk</th>

                                        </tr>
                                    </thead>
                                    <?php
                                    // Pastikan parameter tidak kosong
                                    if ($id_siswa && $tanggal_awal && $tanggal_akhir) {
                                        $sql = "SELECT 
    s.id_siswa, 
    ks.id_siswa,
    s.nama, 
    k.kelas,
    p.tanggal, 
    p.waktu AS waktu_masuk,
    CASE 
        WHEN p.waktu > '07:10:00' THEN 'Terlambat' 
        ELSE p.status_masuk 
    END AS status_masuk
FROM presensi p
JOIN kelas_siswa ks ON p.id_pergantian_kelas = ks.id_pergantian_kelas
JOIN siswa s ON s.id_siswa = ks.id_siswa
JOIN kelas k ON k.id_kelas = ks.id_kelas
WHERE ks.id_siswa = '$id_siswa'
AND p.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
AND (
    p.status_masuk = 'Alfa'
    OR p.waktu > '07:10:00'
    OR p.status_masuk = 'Izin'
)
ORDER BY p.tanggal DESC, p.waktu ASC;


                                    ";

                                        $result = $conn->query($sql);
                                    ?>
                                        <tbody>
                                            <?php while ($row = $result->fetch_assoc()) { ?>
                                                <?php
                                                echo "<tr>
                               
                                 <td>{$row['nama']}</td>
                                 <td>{$row['kelas']}</td>
                                 <td>{$row['tanggal']}</td>
                                 <td>{$row['waktu_masuk']}</td>
                                 <td>{$row['status_masuk']}</td>
                                 
                               </tr>";
                                                ?>
                                        <?php

                                            }
                                        } else {
                                            echo "Silakan masukkan parameter id_kelas, periode_awal, dan periode_akhir.";
                                        }

                                        ?>
                                        </tbody>
                                </table>













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
                p.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' -- Tentukan periode laporan
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
                        }
                    } else {
                        echo "<p class='text-center'>Tidak ada data presensi untuk rentang tanggal tersebut.</p>";
                    }
                    ?>

                <?php endif; ?>
            </div>





        </div>
        <?php

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
                            'rgba(255, 99, 132, 0.6)' // Alfa
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