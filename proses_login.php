<?php
    // Ambil koneksi dan sesi
    session_start();
    include "koneksi.php";

    // Nangkap data dari form di index.php
    $username = $_POST['username'];
    $kata_sandi = $_POST['password'];

    // Enkripsi password yang dimasukkan untuk mencocokkan dengan yang ada di database
    $encrypted_password = md5($kata_sandi);

    // Query ke database untuk mencocokkan username dan password yang terenkripsi
    $login = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$encrypted_password' and status_hapus = '0' ");
    $cek = mysqli_num_rows($login);

    if ($cek > 0) {
        $data = mysqli_fetch_array($login);

        // Cek jabatan/user role setelah login
        if ($data['jabatan'] == "admin") {
            // Buat session login dan username untuk admin
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['username'] = $username;
            $_SESSION['jabatan'] = "admin";
            // Alihkan ke halaman dashboard admin
            header("location:data_dashboard.php");
        } else if ($data['jabatan'] == "jenjang") {
            // Buat session login dan username untuk jenjang
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['username'] = $username;
            $_SESSION['jabatan'] = "jenjang";
            // Alihkan ke halaman dashboard jenjang
            header("location:data_dashboard.php");
        } else if ($data['jabatan'] == "wali_kelas") {
            // Buat session login dan username untuk siswa
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['username'] = $username;
            $_SESSION['jabatan'] = "wali_kelas";
            // Alihkan ke halaman dashboard siswa
            header("location:data_dashboard.php");
        } else {
            // Alihkan ke halaman login jika jabatan tidak terdefinisi
            header("location:index.php");
        }
    } else {
        // Alihkan ke halaman login jika login gagal
        header("location:index.php");
    }
    ?>
