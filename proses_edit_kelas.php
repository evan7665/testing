<?php
include "koneksi.php";
include "session.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_kelas = $_POST['id_kelas'];
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);

    // Cek apakah kelas ada dalam database
    $query_check = mysqli_query($conn, "SELECT * FROM kelas WHERE id_kelas = '$id_kelas' AND status_hapus = '0'");
    if (mysqli_num_rows($query_check) == 0) {
        echo "<script>alert('Data kelas tidak ditemukan!'); window.location.href='data_kelas.php';</script>";
        exit;
    }

    // Jika user adalah admin, update juga tingkatnya
    if ($_SESSION['jabatan'] == 'admin' && isset($_POST['id_tingkat'])) {
        $id_tingkat = $_POST['id_tingkat'];
        $query_update = "UPDATE kelas SET kelas = '$kelas', id_tingkat = '$id_tingkat' WHERE id_kelas = '$id_kelas'";
    } else {
        $query_update = "UPDATE kelas SET kelas = '$kelas' WHERE id_kelas = '$id_kelas'";
    }

    // Jalankan query update
    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Data kelas berhasil diperbarui!'); window.location.href='kelas.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan, coba lagi!'); window.location.href='edit_kelas.php?id=$id_kelas';</script>";
    }
} else {
    echo "<script>alert('Akses tidak valid!'); window.location.href='data_kelas.php';</script>";
}
?>
