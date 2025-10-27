<?php
include "koneksi.php";
include "session.php";

// Handling form submission to add a new academic year
if (isset($_POST['submit'])) {
    $tanggal_awal = $_POST['tanggal_awal'];
    $tanggal_akhir = $_POST['tanggal_akhir'];



    // Insert into the database
    $sql = "INSERT INTO tahun_ajaran (tanggal_awal, tanggal_akhir, status_aktif, status_hapus) 
            VALUES ('$tanggal_awal', '$tanggal_akhir', '0', '0')";
    mysqli_query($conn, $sql);
}

// Handling delete request
if (isset($_GET['delete'])) {
    $id_tahun_ajaran = $_GET['delete'];

    // Check if status_aktif is 1 (active)
    $checkStatusSql = "SELECT status_aktif FROM tahun_ajaran WHERE id_tahun_ajaran = '$id_tahun_ajaran'";
    $checkResult = mysqli_query($conn, $checkStatusSql);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['status_aktif'] == '1') {
        // If the status_aktif is 1 (active), do not allow delete and show a message
        echo "<script>alert('Tidak dapat menghapus tahun ajaran aktif!'); window.location.href='periode.php';</script>";
    } else {
        // If status_aktif is not 1, proceed with delete (soft delete by updating status_hapus to 1)
        $sql = "UPDATE tahun_ajaran SET status_hapus = '1' WHERE id_tahun_ajaran = '$id_tahun_ajaran'";
        mysqli_query($conn, $sql);
        echo "<script>alert('Data tahun ajaran telah dihapus'); window.location.href='periode.php';</script>";
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
            <h3>Create Tahun Ajaran</h3>
            <form method="POST" action="periode.php">
                <div class="mb-3">
                    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                    <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" required>
                </div>
                <div class="mb-3">
                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" required>
                </div>


                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            </form>

            <hr>

            <h3>Existing Tahun Ajaran</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tanggal Awal</th>
                        <th scope="col">Tanggal Akhir</th>
                        <th scope="col">Status Aktif</th>
                        <th scope="col">aktif Periode</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch and display existing tahun ajaran records
                    $sql = "SELECT * FROM tahun_ajaran WHERE status_hapus = '0'";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) :
                        $today = date('Y-m-d');
                        $tanggal_awal = $row['tanggal_awal'];
                        $tanggal_akhir = $row['tanggal_akhir'];
                        $is_within_period = ($today >= $tanggal_awal && $today <= $tanggal_akhir);
                    ?>
                        <tr>
                            <td><?php echo $row['id_tahun_ajaran']; ?></td>
                            <td><?php echo $row['tanggal_awal']; ?></td>
                            <td><?php echo $row['tanggal_akhir']; ?></td>
                            <td><?php echo ($row['status_aktif'] == 1 ? 'Aktif' : 'Non-Aktif'); ?></td>

                            <td>
                                <?php if ($is_within_period && $row['status_aktif'] == '0'  ) { ?>
                                    <a href="proses_aktif_periode.php?id_tahun_ajaran=<?php echo $row['id_tahun_ajaran']; ?>" class="btn btn-warning btn-sm">Aktifkan</a>
                                <?php }elseif($row['status_aktif'] == '1' ){ 

                                }
                                    ?>
                            </td>
                            <td>
                                <a href="edit_periode.php?id_tahun_ajaran=<?php echo $row['id_tahun_ajaran']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_periode.php?delete=<?php echo $row['id_tahun_ajaran']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                </tbody>

            </table>
        </div>
    </div>


</body>

</html>