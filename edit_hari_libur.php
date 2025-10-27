<?php
include "koneksi.php";
include "session.php";

// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $id_hari_libur = $_GET['id'];

    // Query to fetch data for the specified holiday
    $query = "SELECT * FROM hari_libur WHERE id_hari_libur = '$id_hari_libur'";
    $result = mysqli_query($conn, $query);

    // Check if data exists
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan!'); window.location.href = 'index.php';</script>";
    }
} else {
    echo "<script>alert('ID Hari Libur tidak diberikan!'); window.location.href = 'index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hari Libur</title>
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
                <a class="navbar-brand" href="#">Edit Hari Libur</a>
            </div>
        </nav>

        <div class="container mt-4">
            <h2>Edit Hari Libur</h2>
            <form action="proses_edit_hari_libur.php" method="POST" onsubmit="return validateCheckbox()">
                <input type="hidden" name="id_hari_libur" value="<?php echo $row['id_hari_libur']; ?>">

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo $row['tanggal']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?php echo $row['keterangan_libur']; ?>" required>
                </div>

                <?php
                if ($_SESSION['jabatan'] == 'admin') {
                    // Query untuk mengambil data jenjang dari database
                    $query_jenjang = mysqli_query($conn, "SELECT * FROM tingkat WHERE status_hapus = 0");
                    $selected_jenjang = explode(',', $row['jenjang']); // Ambil data jenjang yang sudah dipilih sebelumnya
                ?>
                    <div class="mb-3">
                        <label class="form-label">Jenjang yang Diliburkan</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pilihSemua" onclick="toggleCheckboxes(this)">
                            <label class="form-check-label" for="pilihSemua">Pilih Semua Jenjang</label>
                        </div>
                        <?php while ($jenjang = mysqli_fetch_assoc($query_jenjang)) { ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="jenjang[]" value="<?php echo $jenjang['id_tingkat']; ?>" id="jenjang<?php echo $jenjang['id_tingkat']; ?>" <?php echo in_array($jenjang['id_tingkat'], $selected_jenjang) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="jenjang<?php echo $jenjang['id_tingkat']; ?>"><?php echo $jenjang['tingkat']; ?></label>
                            </div>
                        <?php } ?>
                    </div>
                <?php
                } elseif ($_SESSION['jabatan'] == 'jenjang') {
                    $id = $_SESSION['id_user'];
                    $quer_jenjang = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
                    $row_jenjangg = mysqli_fetch_array($quer_jenjang);
                    $id_tingkat = $row_jenjangg['id_tingkat'];
                ?>
                    <div class="form-check">
                        <input class="form-check-input" type="text" name="jenjang" value="<?php echo $id_tingkat; ?>" id="jenjang6" hidden>
                    </div>
                <?php
                }
                ?>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
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