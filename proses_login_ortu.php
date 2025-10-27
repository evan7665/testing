<?php
session_start();

include "koneksi.php";

 $nama =  $_POST['nama'];

$nomor_orang_tua = $_POST['nomor_orang_tua'];


$query = mysqli_query($conn, "SELECT * from siswa where nama = '$nama' and nomor_orang_tua = '$nomor_orang_tua' ");
$row = mysqli_num_rows($query);
if($row > 0){   
    $rrow = mysqli_fetch_array($query);
    $_SESSION['id'] = $rrow['id_siswa'];
    
    header("Location:report_siswa_ortu.php");

}else{
    header("Location:login_ortu.php");
}

?>