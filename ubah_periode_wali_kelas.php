<?php
include "koneksi.php";
include "session.php";

$id_wali_kelas = $_GET['id_wali_kelas'];





// Ambil data wali kelas yang ada
$query_wali = "SELECT id_wali_kelas, nama_wali_kelas FROM wali_kelas";
$result_wali = mysqli_query($conn, $query_wali);

// Ambil data kelas yang tersedia
$query_kelas = "SELECT id_kelas, kelas FROM kelas";
$result_kelas = mysqli_query($conn, $query_kelas);




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
        <form method="POST" action="proses_ubah_periode_wali.php">
                <?php
                // Ambil data wali kelas
                $query_wali = "SELECT id_wali_kelas, nama_wali_kelas FROM wali_kelas";
                $result_wali = mysqli_query($conn, $query_wali);
                $row = mysqli_fetch_assoc($result_wali);
                ?>

                <!-- Input Hidden ID Wali Kelas -->
                <input type="hidden" name="id_wali_kelas" value="<?php echo $id_wali_kelas; ?>">

                <!-- Nama Wali Kelas -->

                
                <div class="mb-3">
                    <label for="id_wali_kelas" class="form-label">Nama Wali Kelas:</label>
                    <input type="text" class="form-control"  value="<?php echo $row['nama_wali_kelas']; ?>" readonly>
                </div>

                <?php
                // Ambil data kelas yang belum memiliki wali kelas di tahun ajaran mendatang
                $query_kelas = "SELECT k.* FROM kelas k
                JOIN (
                    SELECT ta1.id_tahun_ajaran FROM tahun_ajaran ta1
                    JOIN (
                        SELECT tanggal_akhir FROM tahun_ajaran WHERE status_aktif = 1 
                        ORDER BY tanggal_akhir DESC LIMIT 1
                    ) ta_aktif ON ta1.tanggal_awal > ta_aktif.tanggal_akhir
                    WHERE ta1.status_aktif = 0
                    ORDER BY ta1.tanggal_awal ASC LIMIT 1
                ) ta_next ON 1=1
                LEFT JOIN wali_kelas_periode wkp ON k.id_kelas = wkp.id_kelas AND wkp.id_tahun_ajaran = ta_next.id_tahun_ajaran
                WHERE wkp.id_kelas IS NULL";
                $result_kelas = mysqli_query($conn, $query_kelas);
                ?>

                <!-- Pilihan Kelas -->
                <div class="mb-3">
                    <label for="id_kelas" class="form-label">Pilih Kelas:</label>
                    <select class="form-select" name="id_kelas" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while ($row = mysqli_fetch_assoc($result_kelas)) { ?>
                            <option value="<?php echo $row['id_kelas']; ?>"><?php echo $row['kelas']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <?php
                // Ambil tahun ajaran yang mendekati tahun ajaran aktif
                $query_ambil_ajaran = mysqli_query($conn,"SELECT ta1.id_tahun_ajaran, ta1.tanggal_awal, ta1.tanggal_akhir,
                    CONCAT(YEAR(ta1.tanggal_awal), '/', YEAR(ta1.tanggal_akhir)) AS periode_tahun
                FROM tahun_ajaran ta1
                JOIN (
                    -- Ambil tanggal_akhir dari tahun ajaran yang status_aktif = 1
                    SELECT tanggal_akhir 
                    FROM tahun_ajaran 
                    WHERE status_aktif = 1 
                    ORDER BY tanggal_akhir DESC 
                    LIMIT 1
                ) ta_aktif ON ta1.tanggal_awal > ta_aktif.tanggal_akhir
                WHERE ta1.status_aktif = 0
                ORDER BY ta1.tanggal_awal ASC
                LIMIT 1;
                ");
                $row = mysqli_fetch_assoc($query_ambil_ajaran);
                ?>

                <!-- Tahun Ajaran -->
                <div class="mb-3" hidden >
                    <label for="id_tahun_ajaran" class="form-label">Tahun Ajaran:</label>
                    <input type="text" class="form-control" name="id_tahun_ajaran" placeholder="Contoh: 2024/2025" value="<?php echo $row['id_tahun_ajaran']; ?>" required>
                </div>
                <div class="mb-3" >
                    <label for="id_tahun_ajaran" class="form-label">Tahun Ajaran:</label>
                    <input type="text" class="form-control" placeholder="" value="<?php echo $row['periode_tahun']; ?>" readonly>
                </div>

                <!-- Tombol Submit -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>

            </form>

        </div>


    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>