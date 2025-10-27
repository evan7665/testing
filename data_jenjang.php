<?php
include "koneksi.php";
include "session.php";

if ($_SESSION['jabatan'] != 'admin') {
    header("Location: index.php");
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
                <a class="navbar-brand" href="#"> Data Jenjang</a>
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
            <h3>Data Jenjang</h3>
            <div class="mb-3">
                <a href="tambah_data_jenjang.php" class="btn btn-primary">Tambah Data</a>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Tingkat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mengambil data jenjang
                    $query = "
                        SELECT 
                            jenjang.id_jenjang,
                            jenjang.nama_lengkap, 
                            users.username, 
                            tingkat.tingkat 
                        FROM jenjang 
                        JOIN users ON jenjang.id_user = users.id_user 
                        JOIN tingkat ON jenjang.id_tingkat = tingkat.id_tingkat 
                        WHERE jenjang.status_hapus = 0
                    ";
                    $result = mysqli_query($conn, $query);
                    if ($result) {
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['nama_lengkap']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['tingkat']}</td>
                                <td>
                                    <a href='edit_data_jenjang.php?id={$row['id_jenjang']}' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='delete_data_jenjang.php?id={$row['id_jenjang']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Delete</a>
                                </td>
                            </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>Data tidak ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>