<?php
// Include file koneksi dan session
include "koneksi.php";
include "session.php";

// Cek apakah ID siswa diterima melalui GET atau POST
if (isset($_GET['id_siswa'])) {
    // Ambil ID siswa
    $id_siswa = mysqli_real_escape_string($conn, $_GET['id_siswa']);

    // Query untuk mengubah status_lulus menjadi 1
    $query = "UPDATE siswa SET status_keluar = 1 WHERE id_siswa = '$id_siswa'";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Jika berhasil, tampilkan alert dan redirect ke halaman daftar siswa
        echo "
        <script>
            alert('Siswa berhasil keluar!');
            window.location.href = 'data_siswa.php';
        </script>";
    } else {
        // Jika gagal, tampilkan pesan error
        echo "
        <script>
            alert('Gagal keluar siswa: " . mysqli_error($conn) . "');
            window.location.href = 'data_siswa.php';
        </script>";
    }
} else {
    // Jika tidak ada ID siswa, redirect ke halaman daftar siswa
    echo "
    <script>
        alert('ID siswa tidak ditemukan!');
        window.location.href = 'data_siswa.php';
    </script>";
}
?>
