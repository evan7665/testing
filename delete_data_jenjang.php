<?php
include "koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "UPDATE jenjang SET status_hapus = 1 WHERE id_jenjang = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil dihapus.'); window.location='data_jenjang.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='data_jenjang.php';</script>";
    }
} else {
    header("Location: data_jenjang.php");
}
?>
