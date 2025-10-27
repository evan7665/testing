<?php
include "koneksi.php";
include "session.php";

// Pastikan hanya admin yang bisa menghapus
// if ($_SESSION['jabatan'] != 'admin') {
//     echo "<script>alert('Akses ditolak!'); window.location.href='data_kelas.php';</script>";
//     exit;
// }

if (isset($_GET['id'])) {
    $id_kelas = $_GET['id'];
    
    // Cek apakah kelas ada dalam database
    $query_check = mysqli_query($conn, "SELECT * FROM kelas WHERE id_kelas = '$id_kelas' AND status_hapus = '0'");
    if (mysqli_num_rows($query_check) == 0) {
        echo "<script>alert('Data kelas tidak ditemukan!'); window.location.href='data_kelas.php';</script>";
        exit;
    }
    
    // Soft delete: update status_hapus menjadi 1
    $query_delete = "UPDATE kelas SET status_hapus = '1' WHERE id_kelas = '$id_kelas'";
    if (mysqli_query($conn, $query_delete)) {
        echo "<script>alert('Data kelas berhasil dihapus!'); window.location.href='kelas.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan, coba lagi!'); window.location.href='kelas.php';</script>";
    }
} else {
    echo "<script>alert('ID kelas tidak valid!'); window.location.href='kelas.php';</script>";
}
?>
