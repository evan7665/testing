<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi ke database sudah benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_wali_kelas = $_POST['id_wali_kelas'];
    $id_kelas = $_POST['id_kelas'];
    $id_tahun_ajaran = $_POST['id_tahun_ajaran'];
    $status_hapus = 0; // Default status_hapus adalah 0

    

    // Insert data ke dalam tabel wali_kelas_periode
    $query_insert = "INSERT INTO wali_kelas_periode (id_wali_kelas_periode, id_wali_kelas, id_kelas, id_tahun_ajaran, status_hapus) 
                     VALUES ('$id_wali_kelas_periode', '$id_wali_kelas', '$id_kelas', '$id_tahun_ajaran', '$status_hapus')";

    if (mysqli_query($conn, $query_insert)) {
        $_SESSION['success'] = "Data wali kelas berhasil ditambahkan.";
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat menyimpan data.";
    }

    header("Location: wali_kelas.php"); // Redirect ke halaman daftar wali kelas
    exit();
} else {
    header("Location: ubah_periode_wali_kelas.php");
    exit();
}
?>
