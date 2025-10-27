<?php
include "koneksi.php";
include "session.php";

if (isset($_GET['id_wali_kelas'])) {
    $id_wali_kelas = $_GET['id_wali_kelas'];

    // Soft delete: Mengubah status_hapus menjadi 1
    $query = "UPDATE wali_kelas SET status_hapus = '1' WHERE id_wali_kelas = '$id_wali_kelas'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>
                alert('Data berhasil dihapus!');
                window.location.href = 'wali_kelas.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus data!');
                window.location.href = 'wali_kelas.php';
              </script>";
    }
} else {
    echo "<script>
            alert('ID tidak ditemukan!');
            window.location.href = 'wali_kelas.php';
          </script>";
}
?>
