<?php
include "session.php";

include "koneksi.php";

// Ambil id_presensi dari URL
$id_presensi = $_GET['id'];
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-d');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');


// Ambil data presensi berdasarkan id_presensi
$sql = "SELECT *,
    CONCAT(YEAR(tahun_ajaran.tanggal_awal), '/', YEAR(tahun_ajaran.tanggal_akhir)) AS tahun_ajaran_format
FROM 
    presensi
INNER JOIN 
    kelas_siswa ON presensi.id_pergantian_kelas = kelas_siswa.id_pergantian_kelas
INNER JOIN 
    siswa ON siswa.id_siswa = kelas_siswa.id_siswa
INNER JOIN 
    tingkat ON siswa.id_tingkat = tingkat.id_tingkat
INNER JOIN 
    tahun_ajaran ON kelas_siswa.id_tahun_ajaran = tahun_ajaran.id_tahun_ajaran
WHERE 
    presensi.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
    AND presensi.status_hapus = 0
    AND tahun_ajaran.status_aktif = '1'
    AND presensi.id_presensi = ?
ORDER BY 
    presensi.waktu ASC;

";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_presensi);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah data ditemukan
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger'>Data presensi tidak ditemukan.</div>";
    exit;
}
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
            <h3>Edit Presensi</h3>
            <form action="proses_edit_presensi.php" method="POST">
                <!-- ID Presensi (Hidden) -->
                <input type="hidden" name="id_presensi" value="<?php echo $data['id_presensi']; ?>">
                <div class="mb-3">
                    <label for="nama_siswa" class="form-label">Nama Siswa</label>
                    <input type="text" id="nama_siswa" name="nama_siswa" class="form-control" value="<?php echo $data['nama']; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="waktu_masuk" class="form-label">Waktu Masuk</label>
                    <input type="time" id="waktu_masuk" name="waktu_masuk" class="form-control" value="<?php echo $data['waktu']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="status_masuk" class="form-label">Status Masuk</label>
                    <select id="status_masuk" name="status_masuk" class="form-select" required>
                        <option value="Hadir" <?php if ($data['status_masuk'] == 'Hadir') echo 'selected'; ?>>Hadir</option>
                        <option value="Izin" <?php if ($data['status_masuk'] == 'Izin') echo 'selected'; ?>>Izin</option>
                        <option value="Alfa" <?php if ($data['status_masuk'] == 'Alfa') echo 'selected'; ?>>Alpa</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="waktu_pulang" class="form-label">Waktu Pulang</label>
                    <input type="time" id="waktu_pulang" name="waktu_pulang" class="form-control" value="<?php echo $data['waktu_pulang']; ?>">
                </div>

                 <div class="mb-3">
                    <label for="tanggal_awal" class="form-label">tanggal_awal</label>
                    <input type="text" id="tanggal_awal" name="tanggal_awal" class="form-control" value="<?php echo $tanggal_awal; ?>">
                </div>
                <div class="mb-3">
                    <label for="tanggal_akhir" class="form-label">tanggal_akhir</label>
                    <input type="text" id="tanggal_akhir" name="tanggal_akhir" class="form-control" value="<?php echo $tanggal_akhir; ?>">
                </div>

                <div class="mb-3">
                    <label for="status_pulang" class="form-label">Status Pulang</label>
                    <select id="status_pulang" name="status_pulang" class="form-select">

                        <option value="pulang" <?php if ($data['status_pulang'] == 'pulang') echo 'selected'; ?>>pulang</option>
                        <option value="pulang cepat" <?php if ($data['status_pulang'] == 'pulang cepat') echo 'selected'; ?>>pulang cepat</option>
                        <option value='null' <?php if ($data['status_pulang'] == null) echo 'selected'; ?>>-</option>

                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="dashboard.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>


    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>