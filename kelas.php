<?php
include "koneksi.php";
include "session.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kelas = $_POST['kelas'];
    $id_tingkat = $_POST['id_tingkat'];

    if (!empty($kelas) && !empty($id_tingkat)) {
        $query = "INSERT INTO kelas (kelas, id_tingkat, status_hapus) VALUES ('$kelas', '$id_tingkat', '0')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Data kelas berhasil ditambahkan!'); window.location.href='data_kelas.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data kelas: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Harap isi semua field!');</script>";
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
            <h2 class="mb-4">Tambah Data Kelas</h2>
            <form action="proses_input_kelas.php" method="POST">
                <div class="mb-3">
                    <label for="kelas" class="form-label">Nama Kelas:</label>
                    <input type="text" class="form-control" id="kelas" name="kelas" required>
                </div>
                <?php
                if ($_SESSION['jabatan'] == 'admin') {
                ?>
                <div class="mb-3">
                    <label for="id_tingkat" class="form-label">Tingkat:</label>
                    <select class="form-control" id="id_tingkat" name="id_tingkat" required>
                        <option value="" disabled selected >Pilih Tingkat</option>
                        <?php
                        $query_tingkat = mysqli_query($conn,"SELECT * from tingkat where status_hapus = '0'");
                         while($row_tingkat = mysqli_fetch_array($query_tingkat)){
                        ?>
                        <option value="<?php echo $row_tingkat['id_tingkat'] ?>"><?php echo $row_tingkat['tingkat']; ?></option>
                        <?php
                         }
                        ?>
                    </select>
                </div>
                <?php
                }elseif ($_SESSION['jabatan'] == 'jenjang') {
                    $id = $_SESSION['id_user'];
                    $quer_jenjang = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
                    $row_jenjangg = mysqli_fetch_array($quer_jenjang);
                    $id_tingkat = $row_jenjangg['id_tingkat'];
                    
                ?>
                        <input type="hidden" name="id_tingkat" value="<?php echo $id_tingkat; ?>" >
                        <?php
                }
                        ?>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
            <h2 class="mt-5">Data Kelas</h2>
        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <?php
                    if ($_SESSION['jabatan'] == 'admin') {
                    ?>
                    <th>Tingkat</th>
                    <?php
                    }
                    ?>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php

if ($_SESSION['jabatan'] == 'admin') {
                $query_kelas = mysqli_query($conn, "SELECT kelas.id_kelas, kelas.kelas, tingkat.tingkat FROM kelas JOIN tingkat ON kelas.id_tingkat = tingkat.id_tingkat WHERE kelas.status_hapus = '0'");
}elseif ($_SESSION['jabatan'] == 'jenjang') {
    $id = $_SESSION['id_user'];
    $quer_jenjang = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
    $row_jenjangg = mysqli_fetch_array($quer_jenjang);
    $id_tingkat = $row_jenjangg['id_tingkat'];
    $query_kelas = mysqli_query($conn, "SELECT kelas.id_kelas, kelas.kelas, tingkat.tingkat FROM kelas JOIN tingkat ON kelas.id_tingkat = tingkat.id_tingkat WHERE kelas.id_tingkat = '$id_tingkat' and  kelas.status_hapus = '0'");
}
                $no = 1;
                while ($row_kelas = mysqli_fetch_array($query_kelas)) {
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row_kelas['kelas']; ?></td>
                    <?php
                    if ($_SESSION['jabatan'] == 'admin') {
                    ?>
                    <td><?php echo $row_kelas['tingkat']; ?></td>
                    <?php
                    }
                    ?>
                    <td>
                        <a href="edit_kelas.php?id=<?php echo $row_kelas['id_kelas']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="hapus_kelas.php?id=<?php echo $row_kelas['id_kelas']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?');">Hapus</a>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>