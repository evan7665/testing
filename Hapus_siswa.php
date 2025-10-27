<?php
// Include koneksi database
include "koneksi.php";
include "session.php";

// Periksa apakah parameter id_siswa ada
if (isset($_GET['id_siswa']) && is_numeric($_GET['id_siswa'])) {
    $id_siswa = $_GET['id_siswa'];

    // Query untuk menghapus data siswa
    $sql = "UPDATE siswa SET status_hapus = 1 WHERE id_siswa = ?";

    // Siapkan statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameter
        $stmt->bind_param("i", $id_siswa);

        // Eksekusi statement
        if ($stmt->execute()) {
            // Redirect ke halaman data siswa dengan pesan sukses
            header("Location: data_siswa.php?message=Data siswa berhasil dihapus.");
            exit;
        } else {
            // Redirect ke halaman data siswa dengan pesan error
            header("Location: data_siswa.php?message=Gagal menghapus data siswa.");
            exit;
        }
    } else {
        // Redirect ke halaman data siswa jika query gagal disiapkan
        header("Location: data_siswa.php?message=Terjadi kesalahan pada server.");
        exit;
    }
} else {
    // Redirect ke halaman data siswa jika parameter tidak valid
    header("Location: data_siswa.php?message=ID siswa tidak valid.");
    exit;
}
?>
