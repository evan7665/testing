<?php
include "koneksi.php";
include "session.php";

$id_jenjang= $_GET['id'];
$query_jenjang = mysqli_query($conn,"SELECT * from jenjang where id_jenjang = '$id_jenjang'");
$row_jenjang = mysqli_fetch_array($query_jenjang);
$id_user = $row_jenjang['id_user'];
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
                <a class="navbar-brand" href="#">Edit Data Jenjang</a>
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
            
        <h3>Edit Data Jenjang</h3>
        <form method="POST" action="proses_edit_jenjang.php">
        <div class="mb-3">
                <label for="id_jenjang" class="form-label">id_jenjang</label>
                <input type="text" class="form-control" id="id_jenjang" name="id_jenjang" value="<?php echo $row_jenjang['id_jenjang']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo $row_jenjang['nama_lengkap']; ?>" required>
            </div>

            <?php
            $query = mysqli_query($conn,"SELECT * from users where id_user = '$id_user'");
            $row = mysqli_fetch_array($query);
            ?>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $row['username']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="" required>
            </div>
            <div class="mb-3">
                <label for="jabatan" class="form-label">Jabatan</label>
                <input type="text" class="form-control" id="jabatan" name="jabatan" value="jenjang" readonly>
            </div>

            
            <div class="mb-3">
                <label for="id_tingkat" class="form-label">Tingkat</label>
                <select class="form-select" id="id_tingkat" name="id_tingkat" required>
                    
                    <?php
                    $query_tingkat = "SELECT id_tingkat, tingkat FROM tingkat";
                    $result_tingkat = mysqli_query($conn, $query_tingkat);
                    while ($tingkat = mysqli_fetch_assoc($result_tingkat)) {
                        $selected = $tingkat['id_tingkat'] == $row_jenjang['id_tingkat'] ? 'selected' : '';
                        echo "<option value='{$tingkat['id_tingkat']}' $selected>{$tingkat['tingkat']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tambah</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
        </div>


    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>