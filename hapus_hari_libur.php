<?php
include "koneksi.php"; // Pastikan koneksi ke database sudah ada
include "session.php"; // Untuk mengelola sesi login

// Memastikan ID hari libur diterima melalui GET
if (isset($_GET['id'])) {
    $id_hari_libur = $_GET['id'];

    // Mengupdate status_hapus menjadi 1 (terhapus)
    $query = "UPDATE hari_libur SET status_hapus = 1 WHERE id_hari_libur = '$id_hari_libur'";

    // Mengeksekusi query
    if (mysqli_query($conn, $query)) {
        // Redirect ke halaman utama setelah sukses
        header("Location: hari_libur.php"); // Ganti dengan halaman yang sesuai
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "ID Hari Libur tidak ditemukan.";
}
?>
