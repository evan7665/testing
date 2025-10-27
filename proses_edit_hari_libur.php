<?php
include "koneksi.php";
include "session.php";

// Pastikan data yang diperlukan ada
if (isset($_POST['id_hari_libur'], $_POST['tanggal'], $_POST['keterangan'], $_POST['jenjang'])) {
    // Ambil data dari form
    $id_hari_libur = $_POST['id_hari_libur'];
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $jenjang = implode(',', $_POST['jenjang']);  // Menyimpan pilihan jenjang sebagai string yang dipisahkan koma

    // Query untuk update data hari libur
    $query = "UPDATE hari_libur SET tanggal = '$tanggal', keterangan_libur = '$keterangan', jenjang = '$jenjang' WHERE id_hari_libur = '$id_hari_libur'";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data hari libur berhasil diperbarui!'); window.location.href = 'hari_libur.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data hari libur.'); window.location.href = 'hari_libur.php';</script>";
    }
} else {
    echo "<script>alert('Data tidak lengkap!'); window.location.href = 'hari_libur.php';</script>";
}
?>
