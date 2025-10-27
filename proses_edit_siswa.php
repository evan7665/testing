<?php
session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_siswa = $_POST['id_siswa'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $rfid_tag_hex = mysqli_real_escape_string($conn, $_POST['rfid_tag_hex']);
    $rfid_tag_dec = mysqli_real_escape_string($conn, $_POST['rfid_tag_dec']);
    $nomor_orang_tua = mysqli_real_escape_string($conn, $_POST['nomor_orang_tua']);
    // Jika admin, update juga id_tingkat (kelas)
    if ($_SESSION['jabatan'] == 'admin') {
        $id_tingkat = $_POST['kelas'];
        $query = "UPDATE siswa SET nama = '$nama', id_tingkat = '$id_tingkat', rfid_tag_hex = '$rfid_tag_hex', rfid_tag_dec = '$rfid_tag_dec' , nomor_orang_tua = '$nomor_orang_tua', penginput_terakhir = '$_SESSION[username]' WHERE id_siswa = '$id_siswa'";
    } else {
        $query = "UPDATE siswa SET nama = '$nama', rfid_tag_hex = '$rfid_tag_hex', rfid_tag_dec = '$rfid_tag_dec' , nomor_orang_tua = '$nomor_orang_tua' , penginput_terakhir = '$_SESSION[username]' WHERE id_siswa = '$id_siswa'";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data siswa berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat memperbarui data.";
    }

    header("Location: data_siswa.php?id_siswa=$id_siswa");
    exit();
} else {
    header("Location: data_siswa.php");
    exit();
}
?>
