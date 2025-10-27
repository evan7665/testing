<?php
include "koneksi.php";
include "session.php";

// Mengecek apakah form sudah dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
      $id_kelas_lama = $_POST['id_kelas_lama'];
    $id_kelas_baru = $_POST['id_kelas_baru'];
    $siswa_terpilih = $_POST['siswa']; // Array siswa yang dipilih
    $id_tahun_ajaran = $_POST['id_tahun_ajaran'];

    // Mengecek apakah ada siswa yang dipilih
    if (empty($siswa_terpilih)) {
        echo "Tidak ada siswa yang dipilih untuk naik kelas.";
        exit;
    }

    // Memulai transaksi untuk memastikan semua proses dilakukan dengan aman
    mysqli_begin_transaction($conn); // Memulai transaksi

    try {
        // Iterasi untuk memasukkan data siswa satu per satu
        foreach ($siswa_terpilih as $id_siswa) {
            $id_siswa = mysqli_real_escape_string($conn, $id_siswa); // Untuk mencegah SQL Injection
            $sql = "INSERT INTO kelas_siswa (id_siswa, id_tahun_ajaran, id_kelas, status_hapus) 
                    VALUES ('$id_siswa', '$id_tahun_ajaran', '$id_kelas_baru', '0')";
            
            // Mengeksekusi query
            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Error executing query: " . mysqli_error($conn));
            }
        }

        // Commit transaksi jika tidak ada error
        mysqli_commit($conn);
        echo "<script>alert('Siswa berhasil dipromosikan ke kelas baru.'); window.location.href='form_naik_kelas.php?id_kelas=$id_kelas_lama';</script>";
    } catch (Exception $e) {
        // Rollback jika terjadi error
        mysqli_rollback($conn);
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>
