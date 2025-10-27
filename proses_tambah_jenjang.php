<?php
include "koneksi.php";

// Pastikan data dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $id_tingkat = mysqli_real_escape_string($conn, $_POST['id_tingkat']);

    // Enkripsi password
    $hashed_password = md5($password); // Kamu bisa menggunakan hash lebih kuat seperti bcrypt jika diperlukan

    // Cek apakah username sudah ada
    $query_check_username = "SELECT COUNT(*) FROM users WHERE username = '$username'";
    $result_check_username = mysqli_query($conn, $query_check_username);
    $count = mysqli_fetch_row($result_check_username)[0];

    if ($count > 0) {
        // Jika username sudah ada
        echo "<script>alert('Tambah data gagal: Username sudah terdaftar.'); window.history.back();</script>";
    } else {
        // Mulai transaksi
        mysqli_begin_transaction($conn);

        try {
            // 1. Menambahkan data ke tabel users
            $query_users = "INSERT INTO users (username, password, jabatan, status_hapus) VALUES ('$username', '$hashed_password', '$jabatan', 0)";
            if (!mysqli_query($conn, $query_users)) {
                throw new Exception("Error inserting into users table: " . mysqli_error($conn));
            }

            // Ambil id_user yang baru saja dimasukkan
            $id_user = mysqli_insert_id($conn);

            // 2. Menambahkan data ke tabel jenjang
            $query_jenjang = "INSERT INTO jenjang (id_user, nama_lengkap, id_tingkat, status_hapus) VALUES ('$id_user', '$nama_lengkap', '$id_tingkat', 0)";
            if (!mysqli_query($conn, $query_jenjang)) {
                throw new Exception("Error inserting into jenjang table: " . mysqli_error($conn));
            }

            // Commit transaksi
            mysqli_commit($conn);

            // Berhasil menambahkan, arahkan kembali ke halaman admin atau beri notifikasi
            echo "<script>alert('Data berhasil ditambahkan'); window.location.href='data_jenjang.php';</script>";
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi error
            mysqli_rollback($conn);

            // Menampilkan error
            echo "<script>alert('Gagal menambahkan data: " . $e->getMessage() . "'); window.history.back();</script>";
        }
    }
} else {
    // Jika bukan request POST
    echo "<script>alert('Akses tidak valid'); window.location.href='tambah_data_jenjang.php';</script>";
}
?>
