<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "presensi_iot";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>