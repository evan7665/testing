<?php
// Include file koneksi dan session
include "koneksi.php";
include "session.php";

// Cek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_jenjang = mysqli_real_escape_string($conn, $_POST['id_jenjang']); // Ambil id_jenjang dari parameter URL
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $id_tingkat = mysqli_real_escape_string($conn, $_POST['id_tingkat']);

    // Jika password kosong, biarkan password tidak berubah
    if (!empty($password)) {
        $hashed_password = md5($password); // Enkripsi password
        $query_password = ", password = '$hashed_password'";
    } else {
        $query_password = ""; // Jika password kosong, jangan ubah password
    }

    // Query untuk update data di tabel jenjang
    $query = "UPDATE jenjang 
              JOIN users ON users.id_user = jenjang.id_user
              SET jenjang.nama_lengkap = '$nama_lengkap', 
                  users.username = '$username', 
                  jenjang.id_tingkat = '$id_tingkat'
                  $query_password
              WHERE jenjang.id_jenjang = '$id_jenjang'";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Jika berhasil, redirect ke halaman data jenjang
        header("Location: data_jenjang.php?status=success");
        exit;
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Gagal memperbarui data: " . mysqli_error($conn);
    }
} else {
    // Jika form tidak disubmit dengan POST, kembalikan ke form
    header("Location: edit_jenjang.php");
    exit;
}
?>
