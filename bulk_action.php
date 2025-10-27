<?php
include "koneksi.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_ids = $_POST['selected_ids'] ?? [];
    $action = $_POST['action'] ?? '';

    if (!empty($selected_ids)) {
        $id_list = implode(",", array_map('intval', $selected_ids));

        if ($action === "hapus") {
            $sql = "UPDATE siswa SET status_hapus = 1 WHERE id_siswa IN ($id_list)";
        } elseif ($action === "lulus") {
            $sql = "UPDATE siswa SET status_lulus = 1 WHERE id_siswa IN ($id_list)";
        }

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Aksi berhasil!'); window.location.href='data_siswa.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Pilih minimal satu siswa!'); window.history.back();</script>";
    }
}
?>
