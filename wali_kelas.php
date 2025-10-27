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
                <h2>Data Wali Kelas</h2>
                <form method="POST" action="proses_tambah_wali_kelas.php">
                    <div class="mb-3">
                        <label for="nama_wali" class="form-label">Nama Wali Kelas</label>
                        <input type="text" class="form-control" id="nama_wali" name="nama_wali" placeholder="Masukan nama wali kelas" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukan username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukan password" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_telpon" class="form-label">No Telepon</label>
                        <input type="number" class="form-control" id="no_telpon" name="no_telpon" placeholder="Masukan no telepon wali kelas" required>
                    </div>

                    <?php
                    $id = $_SESSION['id_user'];
                    $quer_jenjang = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
                    $row_jenjangg = mysqli_fetch_array($quer_jenjang);
                    $id_tingkat = $row_jenjangg['id_tingkat'];
                    ?>
                    <div class="mb-3">
                        <label for="id_kelas">Kelas</label>
                        <select name="id_kelas" id="id_kelas" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            <?php
                            // Pastikan $conn dan $id_tingkat sudah didefinisikan sebelumnya
                            $query_kelas = mysqli_query($conn, "SELECT k.id_kelas, k.kelas
                                FROM kelas k
                                LEFT JOIN wali_kelas_periode wkp 
                                    ON k.id_kelas = wkp.id_kelas 
                                    AND wkp.id_tahun_ajaran IN (SELECT id_tahun_ajaran FROM tahun_ajaran WHERE status_aktif = 1)
                                WHERE wkp.id_kelas IS NULL
                                AND k.status_hapus = 0
                                AND k.id_tingkat = '" . mysqli_real_escape_string($conn, $id_tingkat) . "'
                                ORDER BY k.kelas ASC
                            ");

                            if (mysqli_num_rows($query_kelas) > 0) {
                                while ($row_kelas = mysqli_fetch_assoc($query_kelas)) {
                                    echo '<option value="' . htmlspecialchars($row_kelas['id_kelas']) . '">' . htmlspecialchars($row_kelas['kelas']) . '</option>';
                                }
                            } else {
                                echo '<option value="" disabled>Tidak ada kelas tersedia</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3" hidden>
                        <label for="id_tingkat" class="form-label">id_tingkat</label>
                        <input type="number" class="form-control" id="id_tingkat" name="id_tingkat" value="<?php echo $id_tingkat; ?>" placeholder="Masukan no telepon wali kelas" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>

                <!-- Tabel untuk menampilkan data wali kelas -->
                <table class="table table-striped mt-4">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Wali</th>
                            <th>No Telepon</th>
                            <th>kelas</th>
                            <th>periode</th>
                            <th>aksi</th>
                            <th>data periode</th>
                            <th>histori periode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $id = $_SESSION['id_user'];
                        $quer_jenjang = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
                        $row_jenjangg = mysqli_fetch_array($quer_jenjang);
                        $id_tingkat = $row_jenjangg['id_tingkat'];
                        // Ambil data wali kelas dari database
                        $result = mysqli_query($conn, "SELECT 
                            wk.id_wali_kelas,
                            wk.nama_wali_kelas AS nama_wali, 
                            wk.no_telpon, 
                            wk.status_hapus,
                            k.kelas AS kelas_diampu, 
                            
                            CONCAT(YEAR(ta.tanggal_awal), '/', YEAR(ta.tanggal_akhir)) AS tahun_ajaran
                        FROM wali_kelas_periode wkp
                        
                        JOIN wali_kelas wk ON wkp.id_wali_kelas = wk.id_wali_kelas
                        JOIN tingkat t ON t.id_tingkat = wk.id_tingkat
                        JOIN kelas k ON wkp.id_kelas = k.id_kelas
                        JOIN tahun_ajaran ta ON wkp.id_tahun_ajaran = ta.id_tahun_ajaran
                        WHERE ta.status_aktif = 1
                        AND wk.status_hapus = '0'
                        AND t.id_tingkat = '$id_tingkat'
                        ORDER BY ta.tanggal_awal DESC, k.kelas ASC;
                        ");
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['nama_wali']}</td>
                                <td>{$row['no_telpon']}</td>
                                <td>{$row['kelas_diampu']}</td>
                                <td>{$row['tahun_ajaran']}</td>
                                <td>
                                <a href='edit_wali_kelas.php?id_wali_kelas={$row['id_wali_kelas']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='hapus_wali_kelas.php?id_wali_kelas={$row['id_wali_kelas']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus?\")'>Hapus</a>";




                            // Ambil tahun ajaran aktif
                            $sql_ta = "SELECT id_tahun_ajaran FROM tahun_ajaran WHERE status_aktif = 1";
                            $result_ta = $conn->query($sql_ta);
                            $row_ta = $result_ta->fetch_assoc();
                            $id_tahun_ajaran_aktif = $row_ta['id_tahun_ajaran'];

                            // Cari tahun ajaran mendatang yang paling dekat dengan tahun ajaran aktif
                            $sql_next_ta = "SELECT id_tahun_ajaran FROM tahun_ajaran 
                WHERE id_tahun_ajaran > $id_tahun_ajaran_aktif 
                ORDER BY tanggal_awal ASC 
                LIMIT 1";
                            $result_next_ta = $conn->query($sql_next_ta);
                            $row_next_ta = $result_next_ta->fetch_assoc();
                            $id_tahun_ajaran_next = $row_next_ta['id_tahun_ajaran'] ?? null;

                            if ($id_tahun_ajaran_next) {
                                $id_wali_kelas = $row['id_wali_kelas'];
                                $sql_check_next = "SELECT 1 FROM wali_kelas_periode 
                                                   WHERE id_wali_kelas = $id_wali_kelas 
                                                   AND id_tahun_ajaran = $id_tahun_ajaran_next 
                                                   LIMIT 1";
                                $result_check_next = $conn->query($sql_check_next);
                        
                                if ($result_check_next->num_rows == 0) {
                                    // Jika belum ada di periode berikutnya, tampilkan tombol
                        

                            echo "<a href='ubah_periode_wali_kelas.php?id_wali_kelas={$row['id_wali_kelas']}' class='btn btn-success btn-sm' onclick='return confirm(\"ubah kelas di periode selanjutnya??\")'>Pindah untuk periode depan</a>";
                                }}
                                echo "
                            </td>
                            <td>
                                <a href='periode_wali_kelas.php?id_wali_kelas={$row['id_wali_kelas']}' class='btn btn-warning btn-sm'>Lihat</a>
                            </td>
                            <td>
                                <a href='history_periode_wali_kelas.php?id_wali_kelas={$row['id_wali_kelas']}' class='btn btn-warning btn-sm'>Lihat</a>
                            </td>
                            </tr>";
                            $no++;
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