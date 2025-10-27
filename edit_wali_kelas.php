<?php
include "koneksi.php";
include "session.php";




// Cek apakah parameter id_wali_kelas ada di URL
if (isset($_GET['id_wali_kelas'])) {
    $id_wali_kelas = $_GET['id_wali_kelas'];
    
    // Ambil data wali kelas berdasarkan ID
    $query = "SELECT * FROM wali_kelas WHERE id_wali_kelas = '$id_wali_kelas'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location='wali_kelas.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location='wali_kelas.php';</script>";
    exit;
}

// Proses update data jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_wali = $_POST['nama_wali'];
    $no_telpon = $_POST['no_telpon'];

    $update_query = "UPDATE wali_kelas SET nama_wali_kelas = '$nama_wali', no_telpon = '$no_telpon' WHERE id_wali_kelas = '$id_wali_kelas'";
    
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='wali_kelas.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
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
            <h2>Edit Wali Kelas</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="nama_wali" class="form-label">Nama Wali Kelas</label>
                    <input type="text" class="form-control" id="nama_wali" name="nama_wali" value="<?php echo $row['nama_wali_kelas']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="no_telpon" class="form-label">No Telepon</label>
                    <input type="number" class="form-control" id="no_telpon" name="no_telpon" value="<?php echo $row['no_telpon']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="wali_kelas.php" class="btn btn-secondary">Batal</a>
            </form>

        </div>


    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>