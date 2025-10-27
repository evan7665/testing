<?php
session_start();
include "koneksi.php";

// Pastikan hanya pengguna yang memiliki hak akses dapat mengakses halaman ini
if (!isset($_SESSION['jabatan']) || ($_SESSION['jabatan'] != 'admin' && $_SESSION['jabatan'] != 'jenjang')) {
    echo "<script>alert('Akses ditolak!'); window.location.href='index.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $id_tingkat = mysqli_real_escape_string($conn, $_POST['id_tingkat']);

    // Validasi input tidak boleh kosong
    if (empty($kelas) || empty($id_tingkat)) {
        echo "<script>alert('Semua field harus diisi!'); window.location.href='data_kelas.php';</script>";
        exit();
    }

    // Insert data ke dalam tabel kelas
    $query = "INSERT INTO kelas (kelas, id_tingkat, status_hapus) VALUES ('$kelas', '$id_tingkat', '0')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('Data kelas berhasil ditambahkan!'); window.location.href='kelas.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data kelas!'); window.location.href='kelas.php';</script>";
    }
} else {
    echo "<script>alert('Invalid Request!'); window.location.href='kelas.php';</script>";
}
?>
