<?php
include "koneksi.php";
include "session.php";


$id_kelas = $_GET['id_kelas'];


$cari_ajaran =  mysqli_query($conn, "SELECT t1.id_tahun_ajaran, t1.tanggal_awal, t1.tanggal_akhir
FROM tahun_ajaran t1
JOIN tahun_ajaran t2 ON t2.status_aktif = 1
WHERE t1.status_aktif != 1
AND ABS(DATEDIFF(t1.tanggal_awal, t2.tanggal_akhir)) < 365
ORDER BY ABS(DATEDIFF(t1.tanggal_awal, t2.tanggal_akhir)) ASC
LIMIT 1;
");

$row_cari_ajaran = mysqli_fetch_array($cari_ajaran);
$id_tahun_ajaran = $row_cari_ajaran['id_tahun_ajaran'];
    
// Ambil daftar siswa dalam kelas saat ini
$query_siswa = mysqli_query($conn, query: "SELECT s.id_siswa, s.nama
FROM siswa s
JOIN kelas_siswa ks ON s.id_siswa = ks.id_siswa
JOIN tahun_ajaran ta ON ks.id_tahun_ajaran = ta.id_tahun_ajaran
WHERE ks.id_kelas = '$id_kelas' AND ta.status_aktif = '1'
AND NOT EXISTS (
    SELECT 1
    FROM kelas_siswa ks2
    WHERE ks2.id_tahun_ajaran = '$id_tahun_ajaran'
    AND ks2.id_siswa = s.id_siswa);
");

// Ambil daftar kelas untuk tujuan naik kelas
$query_kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY kelas ASC");
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
            <h2>Form Naik Kelas untuk periode selanjutnya pada tanggal <?php echo $row_cari_ajaran['tanggal_awal'];  ?> sampai dengan <?php echo $row_cari_ajaran['tanggal_akhir']; ?>  </h2>
            <form action="proses_naik_kelas.php" method="post">
                <input type="hidden" name="id_kelas_lama" value="<?php echo htmlspecialchars($id_kelas); ?>">

                <div class="mb-3">
                    <label for="id_kelas_baru" class="form-label">Pilih Kelas Tujuan</label>
                    <select class="form-control" name="id_kelas_baru" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while ($row_kelas = mysqli_fetch_assoc($query_kelas)) { ?>
                            <option value="<?php echo $row_kelas['id_kelas']; ?>">
                                <?php echo htmlspecialchars($row_kelas['kelas']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <input type="hidden" name="id_tahun_ajaran" value="<?php echo $id_tahun_ajaran; ?>">

                <div class="mb-3">
                    <label class="form-label">Pilih Siswa</label>
                    <?php while ($row_siswa = mysqli_fetch_assoc($query_siswa)) { ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="siswa[]" value="<?php echo $row_siswa['id_siswa']; ?>">
                            <label class="form-check-label">
                                <?php echo htmlspecialchars($row_siswa['nama']); ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>

                <button type="submit" class="btn btn-primary">Naikkan Kelas</button>
                <a href="dashboard.php" class="btn btn-secondary">Batal</a>
            </form>

        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>