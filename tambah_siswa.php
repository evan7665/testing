<?php
include "koneksi.php";
include "session.php";



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>
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
                <a class="navbar-brand" href="#">Tambah Siswa</a>
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
            <h2>Tambah Siswa via Excel</h2>
            <a href="coba_1.xlsx" class="btn btn-warning mb-3">Download Template XLSX</a>
            <form action="import_siswa.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="file_excel" class="form-label">Pilih File Excel</label>
                    <input type="file" class="form-control" id="file_excel" name="file_excel" accept=".xlsx, .xls" required>
                </div>
                <button type="submit" class="btn btn-success">Upload dan Simpan</button>
            </form>
            <br>
            <h2>Tambah Siswa</h2>
            <form action="proses_tambah_siswa.php" method="POST">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Siswa</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="rfid_tag_hex" class="form-label">RFID Tag Hex</label>
                    <input type="text" class="form-control" id="rfid_tag_hex" name="rfid_tag_hex" required>
                </div>
                <div class="mb-3">
                    <label for="rfid_tag_dec" class="form-label">RFID Tag Dec</label>
                    <input type="text" class="form-control" id="rfid_tag_dec" name="rfid_tag_dec">
                </div>
                <div class="mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal lahir</label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                </div>
                <div class="mb-3">
                        <label for="nomor_orang_tua" class="form-label">Nomor Whats App Orang Tua</label>
                        <input type="number" class="form-control" id="nomor_orang_tua" name="nomor_orang_tua">
                    </div>
                <?php
                if ($_SESSION['jabatan'] == 'admin') {
                ?>
                    <!-- Tingkat Dropdown -->
                    <div class="mb-3">
                        <label for="id_tingkat" class="form-label">Tingkat</label>
                        <select class="form-select" id="id_tingkat" name="id_tingkat" required>
                            <option value="" disabled selected>Pilih Tingkat</option>
                            <?php
                            // Query to fetch tingkat options
                            $query_tingkat = "SELECT * FROM tingkat WHERE status_hapus = 0";
                            $result_tingkat = mysqli_query($conn, $query_tingkat);
                            while ($row_tingkat = mysqli_fetch_assoc($result_tingkat)) {
                                echo "<option value='{$row_tingkat['id_tingkat']}'>{$row_tingkat['tingkat']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Kelas Dropdown (this will be populated via AJAX) -->
                    <div class="mb-3">
                        <label for="id_kelas" class="form-label">Kelas</label>
                        <select class="form-select" id="id_kelas" name="id_kelas" required>
                            <option value="" disabled selected>Pilih Kelas</option>
                        </select>
                    </div>
                    
                <?php
                } elseif ($_SESSION['jabatan'] == 'jenjang') {
                    $id = $_SESSION['id_user'];
                    $queryJenjang = mysqli_query($conn, "SELECT * FROM jenjang WHERE id_user = '$id'");
                    $rowJenjang = mysqli_fetch_array($queryJenjang);
                    $idTingkat = $rowJenjang['id_tingkat'];


                ?>



                    <div class="mb-3">
                        <label for="id_kelas" class="form-label">Kelas</label>
                        <select class="form-select" id="id_kelas" name="id_kelas" required>
                            <option value="" disabled selected>Pilih kelas</option>
                            <?php
                            $query_tingkat = "SELECT * FROM kelas WHERE status_hapus = 0 and id_tingkat = '$idTingkat' ";
                            $result_tingkat = mysqli_query($conn, $query_tingkat);
                            while ($row_tingkat = mysqli_fetch_assoc($result_tingkat)) {
                                echo "<option value='{$row_tingkat['id_kelas']}'>{$row_tingkat['kelas']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                <?php
                }
                ?>
                <div class="mb-3">
                    <label for="id_angkatan" class="form-label">Angkatan</label>
                    <select class="form-select" id="id_angkatan" name="id_angkatan" required>
                        <option value="" disabled selected>Pilih Angkatan</option>
                        <?php
                        $query = "SELECT * FROM angkatan WHERE status_hapus = 0";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['id_angkatan']}'>{$row['angkatan']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            // When the tingkat dropdown changes
            $('#id_tingkat').change(function() {
                var id_tingkat = $(this).val(); // Get selected tingkat value

                if (id_tingkat) { // If a tingkat is selected
                    $.ajax({
                        url: 'get_kelas.php', // PHP file that will return the classes
                        type: 'POST',
                        data: {
                            id_tingkat: id_tingkat
                        },
                        success: function(response) {
                            // Populate the kelas dropdown with the response
                            $('#id_kelas').html(response);
                        }
                    });
                } else {
                    $('#id_kelas').html('<option value="" disabled selected>Pilih Kelas</option>');
                }
            });
        </script>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>