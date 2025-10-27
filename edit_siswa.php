<?php
include "koneksi.php";
include "session.php";

$id_siswa = $_GET['id_siswa'];

// Ambil data siswa berdasarkan id_siswa
$sql = "SELECT * FROM siswa WHERE id_siswa = '$id_siswa' and status_hapus = '0'";
$result = mysqli_query($conn, $sql);
$siswa = mysqli_fetch_assoc($result);

if (!$siswa) {
    echo "Data siswa tidak ditemukan.";
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
                <a class="navbar-brand" href="#">Edit Data Siswa</a>
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
            <form action="proses_edit_siswa.php" method="POST">
                <input type="hidden" name="id_siswa" value="<?= $siswa['id_siswa']; ?>">

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Siswa</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($siswa['nama']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nomor_orang_tua" class="form-label">Nomor Whats App Orang Tua</label>
                    <input type="text" class="form-control" id="nomor_orang_tua" name="nomor_orang_tua" value="<?= htmlspecialchars($siswa['nomor_orang_tua']); ?>" required>
                </div>
                <?php
                if ($_SESSION['jabatan'] == 'admin') {
                ?>
                    <div class="mb-3">
                        <label for="kelas" class="form-label">Kelas</label>
                        <select class="form-control" id="kelas" name="kelas" required>
                            <?php
                            // Ambil data tingkat
                            $tingkatQuery = mysqli_query($conn, "SELECT * FROM tingkat WHERE status_hapus = 0");
                            while ($tingkat = mysqli_fetch_assoc($tingkatQuery)) {
                                // Cocokkan dengan id_tingkat dari siswa
                                $selected = $tingkat['id_tingkat'] == $siswa['id_tingkat'] ? 'selected' : '';
                                echo "<option value='{$tingkat['id_tingkat']}' $selected>{$tingkat['tingkat']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                <?php
                }
                ?>


               

                <div class="mb-3">
                    <label for="rfid_tag_hex" class="form-label">RFID Tag Hex</label>
                    <input type="text" class="form-control" id="rfid_tag_hex" name="rfid_tag_hex" value="<?= htmlspecialchars($siswa['rfid_tag_hex']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="rfid_tag_dec" class="form-label">RFID Tag Dec</label>
                    <input type="text" class="form-control" id="rfid_tag_dec" name="rfid_tag_dec" value="<?= htmlspecialchars($siswa['rfid_tag_dec']); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>


    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>