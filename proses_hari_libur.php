<?php
include "koneksi.php";
include "session.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    if ($_SESSION['jabatan'] == 'admin') {
        if (isset($_POST['jenjang'])) {
            foreach ($_POST['jenjang'] as $jenjang) {
                $jenjang = mysqli_real_escape_string($conn, $jenjang);
                $query = "INSERT INTO hari_libur (tanggal, keterangan_libur,  jenjang, status_hapus) VALUES ('$tanggal', '$keterangan', '$jenjang', '0')";
                mysqli_query($conn, $query);
            }
        }
    } elseif ($_SESSION['jabatan'] == 'jenjang') {
        $id = $_SESSION['id_user'];
        $quer_jenjang = mysqli_query($conn, "SELECT * FROM jenjang WHERE id_user = '$id'");
        $row_jenjangg = mysqli_fetch_array($quer_jenjang);
        $id_tingkat = $row_jenjangg['id_tingkat'];

        $query = "INSERT INTO hari_libur (tanggal, keterangan_libur, jenjang, status_hapus) VALUES ('$tanggal', '$keterangan', '$id_tingkat', '0')";
        mysqli_query($conn, $query);
    }
    
    header("Location: hari_libur.php?success=1");
    exit();
} else {
    header("Location: hari_libur.php");
    exit();
}
