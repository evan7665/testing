<?php
include "koneksi.php";
include "session.php";


$searchQuery = ""; // Inisialisasi variabel search query
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
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
            <h3>Data Siswa Lulus</h3>
           
             <!-- Search Bar -->
             <form class="mb-3" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama siswa, angkatan, atau tingkat..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>Angkatan Siswa</th>
                        <th>RFID HEX</th>
                        <th>RFID DEC</th>
                        <?php
                        if ($_SESSION['jabatan'] == 'admin') {
                        ?>
                            <th>Jejang</th>
                        <?php
                        }
                        ?>
                        
                        <th>Aksi</th> <!-- Kolom baru untuk aksi -->
                    </tr>
                </thead>
                <?php
                $whereClause = "WHERE siswa.status_hapus = 0 AND siswa.status_lulus = 1 AND tingkat.status_hapus = 0 ";
                if (!empty($searchQuery)) {
                    $whereClause .= " AND (siswa.nama LIKE '%$searchQuery%' 
                                      OR angkatan.angkatan LIKE '%$searchQuery%' 
                                      OR tingkat.tingkat LIKE '%$searchQuery%')";
                }

                if ($_SESSION['jabatan'] == 'admin') {
                    $sql = "SELECT * FROM siswa 
                            JOIN tingkat ON siswa.id_tingkat = tingkat.id_tingkat
                            JOIN angkatan ON siswa.id_angkatan = angkatan.id_angkatan 
                            $whereClause";
                } elseif ($_SESSION['jabatan'] == 'jenjang') {
                    $id = $_SESSION['id_user'];
                    $queryJenjang = mysqli_query($conn, "SELECT * FROM jenjang WHERE id_user = '$id'");
                    $rowJenjang = mysqli_fetch_array($queryJenjang);
                    $idTingkat = $rowJenjang['id_tingkat'];
                    $whereClause .= " AND siswa.id_tingkat = '$idTingkat'";
                    $sql = "SELECT * FROM siswa 
                            JOIN tingkat ON siswa.id_tingkat = tingkat.id_tingkat
                            JOIN angkatan ON siswa.id_angkatan = angkatan.id_angkatan 
                            $whereClause";
                }
                $result = $conn->query($sql);
                ?>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . $row['nama'] . "</td>";
                            echo "<td>" . $row['angkatan'] . "</td>";
                            echo "<td>" . $row['rfid_tag_hex'] . "</td>";
                            echo "<td>" . $row['rfid_tag_dec'] . "</td>";
                            if ($_SESSION['jabatan'] == 'admin') {
                                echo "<td>" . $row['tingkat'] . "</td>";
                            }
                            
                            echo "<td>
                            -
                            </td>";
                            
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>Tidak ada data Siswa Lulus.</td></tr>";
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