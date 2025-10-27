<?php
include "koneksi.php";
include "session.php";
// Pastikan ada parameter ID yang dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID Kelas tidak ditemukan!'); window.location.href='data_kelas.php';</script>";
    exit;
}

$id_kelas = $_GET['id'];

// Ambil data kelas berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM kelas WHERE id_kelas = '$id_kelas' AND status_hapus = '0'");
$row = mysqli_fetch_array($query);

if (!$row) {
    echo "<script>alert('Data kelas tidak ditemukan!'); window.location.href='data_kelas.php';</script>";
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
                <a class="navbar-brand" href="#">Data Kelas</a>
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
            <h2 class="mb-4">Edit Data Kelas</h2>
            <form action="proses_edit_kelas.php" method="POST">
                <div class="mb-3">
                    <label for="kelas" class="form-label">Nama Kelas:</label>
                    <input type="hidden" class="form-control" id="id_kelas" name="id_kelas" value="<?php echo $row['id_kelas']; ?>" required>
                    <input type="text" class="form-control" id="kelas" name="kelas" value="<?php echo $row['kelas']; ?>" required>
                </div>
                <?php if ($_SESSION['jabatan'] == 'admin') { ?>
                    <div class="mb-3">
                        <label for="id_tingkat" class="form-label">Tingkat:</label>
                        <select class="form-control" id="id_tingkat" name="id_tingkat" required>
                            <?php
                            $query_tingkat = mysqli_query($conn, "SELECT * FROM tingkat WHERE status_hapus = '0'");
                            while ($row_tingkat = mysqli_fetch_array($query_tingkat)) {
                                $selected = ($row_tingkat['id_tingkat'] == $row['id_tingkat']) ? "selected" : "";
                            ?>
                                <option value="<?php echo $row_tingkat['id_tingkat']; ?>" <?php echo $selected; ?>><?php echo $row_tingkat['tingkat']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="data_kelas.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>