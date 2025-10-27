<?php
include "koneksi.php";
include "session.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_wali = mysqli_real_escape_string($conn, $_POST['nama_wali']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $no_telpon = mysqli_real_escape_string($conn, $_POST['no_telpon']);
    $id_tingkat = mysqli_real_escape_string($conn, $_POST['id_tingkat']);
    $id_kelas = mysqli_real_escape_string($conn, $_POST['id_kelas']);
    
    $id_user = NULL;
    
    if (!empty($username) && !empty($password)) {
        $password_hashed = md5($password);
        
        $check_user_query = "SELECT id_user FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $check_user_query);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $id_user = $row['id_user'];
        } else {
            $insert_user_query = "INSERT INTO users (username, password, jabatan, status_hapus) 
                                  VALUES ('$username', '$password_hashed', 'wali_kelas', '0')";
            if (mysqli_query($conn, $insert_user_query)) {
                $id_user = mysqli_insert_id($conn);
            }
        }
    }

    if ($id_user) {
        $query = "INSERT INTO wali_kelas (id_user, nama_wali_kelas, no_telpon, id_tingkat, status_hapus) 
                  VALUES ('$id_user', '$nama_wali', '$no_telpon', '$id_tingkat', '0')";
        
        if (mysqli_query($conn, $query)) {
            $id_wali_kelas = mysqli_insert_id($conn);
            
            $insert_wali_kelas_periode = "INSERT INTO wali_kelas_periode (id_wali_kelas, id_kelas, id_tahun_ajaran) 
                                           VALUES ('$id_wali_kelas', '$id_kelas', 
                                           (SELECT id_tahun_ajaran FROM tahun_ajaran WHERE status_aktif = 1 LIMIT 1))";
            mysqli_query($conn, $insert_wali_kelas_periode);
            
            header("Location: wali_kelas.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>
