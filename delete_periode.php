<?php
include "koneksi.php";
include "session.php";


if (isset($_GET['delete'])) {
    $id_tahun_ajaran = $_GET['delete'];

    // Check if status_aktif is 1 (active)
    $checkStatusSql = "SELECT status_aktif FROM tahun_ajaran WHERE id_tahun_ajaran = '$id_tahun_ajaran'";
    $checkResult = mysqli_query($conn, $checkStatusSql);
    
    if ($checkResult) {
        $row = mysqli_fetch_assoc($checkResult);
        echo $row['status_aktif'];

        if ($row['status_aktif'] == '1') {
            // If the status_aktif is 1 (active), do not allow delete and show a message
            echo "<script>alert('Tidak dapat menghapus tahun ajaran aktif!'); window.location.href='periode.php';</script>";
        } elseif ($row['status_aktif'] == '0') {
            // If status_aktif is not 1, proceed with delete (soft delete by updating status_hapus to 1)
            $sql = "UPDATE tahun_ajaran SET status_hapus = '1' WHERE id_tahun_ajaran = '$id_tahun_ajaran'";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Data tahun ajaran telah dihapus'); window.location.href='periode.php';</script>";
            } else {
                // echo "<script>alert('Gagal menghapus data!'); window.location.href='periode.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Query gagal dijalankan.'); window.location.href='periode.php';</script>";
    }
}
?>
