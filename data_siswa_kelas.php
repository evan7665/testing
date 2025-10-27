<?php
include "koneksi.php";
include "session.php";


$jabatan = $_SESSION['jabatan'];

// Query tingkat berdasarkan jabatan
if ($jabatan == "jenjang") {
    $id = $_SESSION['id_user'];
    $quer_jenjang = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
    $row_jenjangg = mysqli_fetch_array($quer_jenjang);
    $id_tingkat = $row_jenjangg['id_tingkat'];
    // Jika jabatan adalah "jenjang", hanya tampilkan tingkat tertentu (misalnya SMA dan SMP)
    $query_tingkat = "SELECT * FROM tingkat WHERE status_hapus = 0 AND id_tingkat = '$id_tingkat'";
} elseif ($jabatan == "admin") {
    // Jika bukan "jenjang", tampilkan semua tingkat
    $query_tingkat = "SELECT * FROM tingkat WHERE status_hapus = 0";
} elseif ($jabatan == "wali_kelas") {
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


    $query_cari_walkes = mysqli_query($conn, "SELECT * from kelas where id_kelas  IN ($id_kelas_list)");
    $row_cari_walkes = mysqli_fetch_array($query_cari_walkes);


    $id_tingkat = $row_cari_walkes['id_tingkat'];
    // Jika jabatan adalah "jenjang", hanya tampilkan tingkat tertentu (misalnya SMA dan SMP)
    $query_tingkat = "SELECT * FROM tingkat WHERE status_hapus = 0 AND id_tingkat = '$id_tingkat'";
}
$result_tingkat = mysqli_query($conn, $query_tingkat);



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
                <a class="navbar-brand" href="#">siswa dan kelas</a>
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

            <?php while ($row_tingkat = mysqli_fetch_assoc($result_tingkat)) : ?>
                <h2><?php echo $row_tingkat['tingkat']; ?></h2>
                <div class="row">
                    <?php
                    $id_tingkat = $row_tingkat['id_tingkat'];
                    if ($_SESSION['jabatan'] == 'wali_kelas') {
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

                        $query_kelas = "SELECT * FROM kelas WHERE id_kelas IN ($id_kelas_list) AND  status_hapus = 0";
                    } else {
                        $query_kelas = "SELECT * FROM kelas WHERE id_tingkat = '$id_tingkat' AND status_hapus = 0";
                    }
                    $result_kelas = mysqli_query($conn, $query_kelas);
                    while ($row_kelas = mysqli_fetch_assoc($result_kelas)) :
                        $id_kelas = $row_kelas['id_kelas'];
                        $query_tahun_ajaran = mysqli_query($conn, "SELECT * from tahun_ajaran where status_aktif = '1'");
                        $row_tahun_ajaran = mysqli_fetch_array($query_tahun_ajaran);
                        $id_tahun_ajaran = $row_tahun_ajaran['id_tahun_ajaran'];
                        $query_kelas =  mysqli_query($conn, "SELECT * from kelas_siswa where id_kelas = '$id_kelas' and id_tahun_ajaran = '$id_tahun_ajaran' ");
                        $hitung_kelas = mysqli_num_rows($query_kelas);
                    ?>
                        <div class="col-md-4">
                            <a href="data_kelas.php?id_kelas=<?php echo $row_kelas['id_kelas']; ?>" class="text-decoration-none">
                                <div class="card p-3 text-center mb-3">
                                    <h5><?php echo $row_kelas['kelas']; ?></h5>
                                    <h8><?php echo $hitung_kelas; ?></h8>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endwhile; ?>

        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>