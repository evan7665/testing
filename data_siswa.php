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
                <h3>Data Siswa</h3>
                <!-- Button to add new student -->
                <a href="tambah_siswa.php" class="btn btn-success mb-3">Tambah Data Siswa</a>
                <!-- Button to view graduated students -->
                <a href="siswa_lulus.php" class="btn btn-info mb-3">Data Siswa Lulus</a>

                <!-- Search Bar -->
                <form class="mb-3" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama siswa, angkatan, atau tingkat..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>
                <form id="bulkActionForm" method="POST" action="bulk_action.php">
                    <button type="submit" name="action" value="hapus" class="btn btn-danger mb-3"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus siswa yang dipilih?')">
                        Hapus Massal
                    </button>

                    <button type="submit" name="action" value="lulus" class="btn btn-success mb-3"
                        onclick="return confirm('Apakah Anda yakin ingin meluluskan siswa yang dipilih?')">
                        Luluskan Massal
                    </button>

                    <input type="checkbox" id="selectAll"> Pilih Semua
                    <table class="table table-striped table-bordered"></table>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Angkatan Siswa</th>
                                <th>Nomor Orang Tua</th>
                                <?php
                                if ($_SESSION['jabatan'] == 'wali_kelas') {
                                ?>
                                    <th>Kelas</th>
                                <?php
                                }
                                ?>
                                <th>RFID HEX</th>
                                <th>RFID DEC</th>
                                <?php
                                if ($_SESSION['jabatan'] == 'admin') {
                                ?>
                                    <th>Jejang</th>
                                <?php
                                }
                                ?>

                                <th>Aksi</th>
                                <th>Laporan Presensi</th>
                                <th>History Absensi</th>
                                <th>Luluskan Siswa</th>
                                <th>penginput/pengupdate terakhir</th><!-- Kolom baru untuk aksi -->
                                <th>timestamp</th>
                            </tr>
                        </thead>
                        <?php
                        $whereClause = "WHERE siswa.status_hapus = 0 AND siswa.status_lulus = 0 AND tingkat.status_hapus = 0 and siswa.status_keluar = 0 ";
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


                        } elseif ($_SESSION['jabatan'] == 'wali_kelas') {
                            $id = $_SESSION['id_user'];

                            // Cari ID Wali Kelas
                            $quer_cari_kelas = mysqli_query($conn, "SELECT * FROM wali_kelas WHERE id_user = '$id'");
                            $row_cari_kelas = mysqli_fetch_array($quer_cari_kelas);
                            $id_wali_kelas = $row_cari_kelas['id_wali_kelas'];

                            // Cari Tahun Ajaran Aktif
                            $cari_periode = mysqli_query($conn, "SELECT * FROM tahun_ajaran WHERE status_aktif = '1'");
                            $row_cari_periode = mysqli_fetch_array($cari_periode);
                            $id_tahun_ajaran = $row_cari_periode['id_tahun_ajaran'];

                            // Cari Semua Kelas yang Diampu oleh Wali Kelas
                            $quer_walikelas = mysqli_query($conn, "SELECT id_kelas FROM wali_kelas_periode WHERE id_wali_kelas = '$id_wali_kelas' AND id_tahun_ajaran = '$id_tahun_ajaran'");

                            // Simpan semua ID Kelas dalam array
                            $id_kelas_array = [];
                            while ($row_wali_kelas = mysqli_fetch_array($quer_walikelas)) {
                                $id_kelas_array[] = $row_wali_kelas['id_kelas'];
                            }

                            // Jika ada lebih dari satu kelas, buat format untuk SQL IN
                            if (!empty($id_kelas_array)) {
                                $id_kelas_list = "'" . implode("','", $id_kelas_array) . "'";
                            } else {
                                $id_kelas_list = "''"; // Default kosong agar tidak error
                            }

                            $whereClause .= " AND kelas_siswa.id_kelas IN ($id_kelas_list) and kelas_siswa.id_tahun_ajaran = '$id_tahun_ajaran' ";
                            $sql = "SELECT * FROM siswa 
                                JOIN tingkat ON siswa.id_tingkat = tingkat.id_tingkat
                                JOIN kelas_siswa ON siswa.id_siswa = kelas_siswa.id_siswa
                                join kelas on kelas.id_kelas = kelas_siswa.id_kelas
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
                                    echo "<td><input type='checkbox' name='selected_ids[]' value='" . $row['id_siswa'] . "'></td>"; // Checkbox sekarang di dalam form
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . $row['nama'] . "</td>";
                                    echo "<td>" . $row['angkatan'] . "</td>";
                                    echo "<td>" . $row['nomor_orang_tua'] . "</td>";
                                    if ($_SESSION['jabatan'] == 'wali_kelas') {
                                        echo "<td>" . $row['kelas'] . "</td>";
                                    }
                                    echo "<td>" . $row['rfid_tag_hex'] . "</td>";
                                    echo "<td>" . $row['rfid_tag_dec'] . "</td>";
                                    
                                    if ($_SESSION['jabatan'] == 'admin') {
                                        echo "<td>" . $row['tingkat'] . "</td>";
                                    }

                                    echo "<td>
                                <a href='edit_siswa.php?id_siswa=" . $row['id_siswa'] . "' class='btn btn-sm btn-warning'>Edit</a>
                                <a href='Hapus_siswa.php?id_siswa=" . $row['id_siswa'] . "' class='btn btn-sm btn-danger'>Hapus</a>
                                </td>";

                                    echo "<td>
                                <a href='report_siswa.php?id_siswa=" . $row['id_siswa'] . "' class='btn btn-sm btn-warning'>Lihat</a>
                                
                                </td>";
                                    echo "<td>
                                <a href='periode_siswa.php?id_siswa=" . $row['id_siswa'] . "' class='btn btn-sm btn-warning'>Lihat</a>
                                
                                </td>";
                                    echo "<td>
                                <select class='form-select form-select-sm' onchange='handleAction(this, " . $row['id_siswa'] . ")'>
                                    <option value=''>Pilih Aksi</option>
                                    <option value='lulus'>Lulus</option>
                                    <option value='keluar'>Keluar</option>
                                </select>
                            </td>";
                           echo "<td>" . $row['penginput_terakhir'] . "</td>";
                           echo "<td>" . $row['timestamp'] . "</td>";

                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>Tidak ada data Siswa.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>


        </div>

        <!-- Bootstrap JS -->
        <script>
            document.getElementById('selectAll').addEventListener('click', function() {
                let checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            });

            function handleAction(select, id_siswa) {
                let action = select.value;
                if (action) {
                    let confirmMessage = action === 'lulus' ? 'Apakah Anda yakin ingin meluluskan siswa ini?' : 'Apakah Anda yakin ingin mengeluarkan siswa ini?';
                    if (confirm(confirmMessage)) {
                        let url = action === 'lulus' ? `lulus_siswa.php?id_siswa=${id_siswa}` : `keluar_siswa.php?id_siswa=${id_siswa}`;
                        window.location.href = url;
                    } else {
                        select.value = '';
                    }
                }
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>