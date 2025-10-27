<?php
session_start(); // Pastikan session dimulai

// Periksa apakah session jabatan bukan 'admin' atau 'jenjang'
if ($_SESSION['jabatan'] != 'admin' && $_SESSION['jabatan'] != 'jenjang' && $_SESSION['jabatan'] != 'wali_kelas') {
    header("Location: index.php");
    exit;
}
?>
