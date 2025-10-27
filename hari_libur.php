    <?php
    include "koneksi.php";
    include "session.php";

    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script>
            function toggleCheckboxes(source) {
                checkboxes = document.getElementsByName('jenjang[]');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = source.checked;
                }
            }
        </script>
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
                    <a class="navbar-brand" href="#">Hari Libur</a>
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
                <h2>Tambah Hari Libur</h2>
                <form action="proses_hari_libur.php" method="POST" onsubmit="return validateCheckbox()">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                    </div>
                    <?php
                    if ($_SESSION['jabatan'] == 'admin') {
                    ?>
                        <div class="mb-3">
                            <label class="form-label">Jenjang yang Diliburkan</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pilihSemua" onclick="toggleCheckboxes(this)">
                                <label class="form-check-label" for="pilihSemua">Pilih Semua Jenjang</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenjang[]" value="2" id="jenjang1">
                                <label class="form-check-label" for="jenjang1">SD</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenjang[]" value="3" id="jenjang2">
                                <label class="form-check-label" for="jenjang2">SMP</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenjang[]" value="1" id="jenjang3">
                                <label class="form-check-label" for="jenjang3">SMA</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenjang[]" value="4" id="jenjang4">
                                <label class="form-check-label" for="jenjang3">SMK1</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenjang[]" value="5" id="jenjang5">
                                <label class="form-check-label" for="jenjang3">SMK2</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenjang[]" value="6" id="jenjang6">
                                <label class="form-check-label" for="jenjang3">STIFERA</label>
                            </div>
                        </div>
                    <?php
                    } elseif ($_SESSION['jabatan'] == 'jenjang') {
                        $id = $_SESSION['id_user'];
                        $quer_jenjang = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
                        $row_jenjangg = mysqli_fetch_array($quer_jenjang);
                        $id_tingkat = $row_jenjangg['id_tingkat'];
                    ?>
                        <div class="form-check">
                            <input class="form-check-input" type="text" name="jenjang" value="<?php echo $id_tingkat; ?>" id="jenjang6" hidden >

                        </div>
                    <?php
                    }
                    ?>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>

                <h3 class="mt-4">Daftar Hari Libur</h3>
                <?php
                // Query to get data from the hari_libur table
                $query = "SELECT * FROM hari_libur where status_hapus = '0'  ORDER BY tanggal DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                ?>
                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Keterangan</th>
                                <?php
                                if ($_SESSION['jabatan'] == 'admin') {
                                ?>
                                    <th scope="col">Jenjang yang Diliburkan</th>
                                <?php
                                }
                                ?>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($_SESSION['jabatan'] == 'admin') {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Fetch jenjang for each holiday
                                    $id_jenjang = $row['jenjang']; // Assuming 'id_jenjang' is the column that stores the level
                                    $jenjang_query = "SELECT * FROM tingkat WHERE id_tingkat = '$id_jenjang'";
                                    $jenjang_result = mysqli_query($conn, $jenjang_query);
                                    $jenjang_data = mysqli_fetch_assoc($jenjang_result);
                            ?>
                                    <tr>
                                        <td><?php echo $row['tanggal']; ?></td>
                                        <td><?php echo $row['keterangan_libur']; ?></td>
                                        <td><?php echo $jenjang_data['tingkat']; ?></td> <!-- Adjust based on your column names -->
                                        <td>
                                            <a href='edit_hari_libur.php?id=<?php echo $row['id_hari_libur']; ?>' class='btn btn-sm btn-warning'>Edit</a>
                                            <a href='hapus_hari_libur.php?id=<?php echo $row['id_hari_libur']; ?>' class='btn btn-sm btn-danger'>Hapus</a>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } elseif ($_SESSION['jabatan'] == 'jenjang') {
                                $id = $_SESSION['id_user'];
                                $quer_jenjang = mysqli_query($conn, "SELECT * FROM jenjang WHERE id_user = '$id' and status_hapus = '0' ");
                                $row_jenjangg = mysqli_fetch_array($quer_jenjang);
                                $id_tingkat = $row_jenjangg['id_tingkat'];

                                $jenjang_query = "SELECT * FROM hari_libur WHERE jenjang = '$id_tingkat'";
                                $jenjang_result = mysqli_query($conn, $jenjang_query);
                                while ($row = mysqli_fetch_array($jenjang_result)) {
                                ?>
                                    <tr>
                                        <td><?php echo $row['tanggal']; ?></td>
                                        <td><?php echo $row['keterangan_libur']; ?></td>
                                        <td>
                                            <a href='edit_hari_libur.php?id=<?php echo $row['id_hari_libur']; ?>' class='btn btn-sm btn-warning'>Edit</a>
                                            <a href='hapus_hari_libur.php?id=<?php echo $row['id_hari_libur']; ?>' class='btn btn-sm btn-danger'>Hapus</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    echo "<p>No holidays found.</p>";
                }
                ?>

            </div>
        </div>



        </div>

        <script>
            function validateCheckbox() {
                var checkboxes = document.querySelectorAll('input[name="jenjang[]"]:checked');
                if (checkboxes.length === 0) {
                    alert('Harap pilih setidaknya satu jenjang.');
                    return false; // Mencegah formulir dikirim jika tidak ada checkbox yang dipilih
                }
                return true; // Memungkinkan formulir dikirim jika ada checkbox yang dipilih
            }
        </script>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>