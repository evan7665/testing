<?php
include "koneksi.php"; // Make sure to include the database connection

if (isset($_POST['id_tingkat'])) {
    $id_tingkat = $_POST['id_tingkat'];

    // Query to get classes based on selected tingkat
    $query = "SELECT * FROM kelas WHERE id_tingkat = '$id_tingkat' AND status_hapus = 0";
    $result = mysqli_query($conn, $query);

    // Check if any classes were found
    if (mysqli_num_rows($result) > 0) {
        // Loop through the results and create <option> elements
        echo '<option value="" disabled selected>Pilih Kelas</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['id_kelas']}'>{$row['kelas']}</option>";
        }
    } else {
        echo '<option value="" disabled>No classes found</option>';
    }
} else {
    echo '<option value="" disabled>Pilih Kelas</option>';
}
?>
