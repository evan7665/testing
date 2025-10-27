<?php
// Include the database connection file
include('koneksi.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get the form data
    $tanggal_awal = $_POST['tanggal_awal'];
    $tanggal_akhir = $_POST['tanggal_akhir'];
    $id_siswa = $_POST['id_siswa'];
    $id_presensi = $_POST['id_presensi'];
    echo $waktu_masuk = $_POST['waktu_masuk'];
    $status_masuk = $_POST['status_masuk'];
    $waktu_pulang = $_POST['waktu_pulang'];
    $status_pulang = $_POST['status_pulang'];

    // Prepare the SQL query to update the presensi record
    $query = "UPDATE presensi SET 
                waktu = '$waktu_masuk', 
                status_masuk = '$status_masuk', 
                waktu_pulang = '$waktu_pulang', 
                status_pulang = '$status_pulang' 
              WHERE id_presensi = $id_presensi";

    // Execute the query
    if ($conn->query($query) === TRUE) {
        // Redirect to the dashboard page after a successful update
        header("Location: data_presensi.php?id_siswa=$id_siswa&tanggal_awal=$tanggal_awal&tanggal_akhir=$tanggal_akhir");
        exit;
    } else {
        // Error executing the query
        echo "Error updating presensi: " . $conn->error;
    }

} else {
    // If the form is not submitted, redirect to the dashboard
    header("Location: data_presensi.php?id_siswa=$id_siswa&tanggal_awal=$tanggal_awal&tanggal_akhir=$tanggal_akhir");
    exit;
}

// Close the database connection
$conn->close();
?>
