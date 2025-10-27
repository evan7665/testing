<?php
include "koneksi.php";
include "session.php";

// Pastikan parameter id_tahun_ajaran ada di URL
if (isset($_GET['id_tahun_ajaran'])) {
    $id_tahun_ajaran = $_GET['id_tahun_ajaran'];

    // Set semua status_aktif menjadi 0
    $reset_sql = "UPDATE tahun_ajaran SET status_aktif = '0'";
    mysqli_query($conn, $reset_sql);

    // Aktifkan hanya periode yang dipilih
    $activate_sql = "UPDATE tahun_ajaran SET status_aktif = '1' WHERE id_tahun_ajaran = '$id_tahun_ajaran'";
    if (mysqli_query($conn, $activate_sql)) {
        echo "<script>alert('Periode berhasil diaktifkan!'); window.location.href='periode.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan, coba lagi!'); window.location.href='periode.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak ditemukan!'); window.location.href='periode.php';</script>";
}

?>
