<?php
file_put_contents("debug.log", print_r($_POST, true));

header('Access-Control-Allow-Origin: *');  // Agar dapat diakses dari perangkat lain
// Set zona waktu ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');
include "koneksi.php";

$jam_sekarang = date('H:i:s');
$tanggal = date('Y-m-d');
$hari_ini = date('l');  // Mengambil nama hari (misal: Monday, Tuesday, etc.)

// Batas waktu presensi masuk dan pulang
$jam_masuk_mulai = '05:00:00';
$jam_masuk_selesai = '10:00:00';
$jam_pulang_mulai = '12:00:00';
$jam_pulang_selesai = '20:00:00';
$batas_pulang_cepat = '16:00:00'; 
$batas_terlambat = '07:15:00'; // Batas waktu terlambat
// Batas waktu pulang cepat

// Ambil ID kartu RFID dari request
if (isset($_POST['uid'])) {
    $rfid = $_POST['uid'];

    // Cek apakah tanggal hari ini berada dalam rentang tanggal_awal dan tanggal_akhir yang status_aktif nya = 1
    $sql_tahun_ajaran = "SELECT * FROM tahun_ajaran WHERE status_aktif = '1' AND '$tanggal' BETWEEN tanggal_awal AND tanggal_akhir";
    $result_tahun_ajaran = $conn->query($sql_tahun_ajaran);
    
    if ($result_tahun_ajaran->num_rows > 0) {
        // Jika tahun ajaran aktif
        $row_tahun_ajaran = $result_tahun_ajaran->fetch_assoc();
        
        $sql_siswa = "SELECT id_siswa, id_tingkat, nama , nomor_orang_tua FROM siswa WHERE rfid_tag_hex='$rfid'";
        $result_siswa1 = $conn->query($sql_siswa);
        
        if ($result_siswa1->num_rows > 0) {
            $row = $result_siswa1->fetch_assoc();
            $id_siswa = $row['id_siswa'];
            $id_tingkat = $row['id_tingkat'];        
            $nama = $row['nama'];
            $nomor_orang_tua = $row['nomor_orang_tua'];
            $sql_maintable = "SELECT 
                                id_pergantian_kelas, 
                                s.id_siswa, 
                                s.nama, 
                                s.id_tingkat, 
                                s.rfid_tag_hex,
                                ta.status_aktif
                            FROM 
                                kelas_siswa ks 
                                JOIN siswa s ON ks.id_siswa = s.id_siswa
                                JOIN tahun_ajaran ta ON ta.id_tahun_ajaran = ks.id_tahun_ajaran
                            WHERE 
                                ta.status_aktif = '1' 
                                AND s.rfid_tag_hex = '$rfid'
                                AND s.id_siswa = '$id_siswa'";
            $result_siswa = $conn->query($sql_maintable);
            
            if ($result_siswa->num_rows > 0) {

                $row = $result_siswa->fetch_assoc();

                $id_pergantian_kelas = $row['id_pergantian_kelas'];
                // Cek apakah hari ini adalah hari libur untuk tingkat siswa ini
                $sql_libur = "SELECT * FROM hari_libur WHERE tanggal = '$tanggal' AND jenjang = '$id_tingkat' AND status_hapus = '0'";
                $result_libur = $conn->query($sql_libur);
                
                // Cek apakah hari ini adalah Sabtu atau Minggu
                if ($hari_ini == 'Saturday' || $hari_ini == 'Sunday') {
                    echo "Hari ini adalah akhir pekan, presensi tidak dapat dilakukan.";
                    exit;
                }

                // Jika hari bukan Sabtu atau Minggu, lanjutkan pengecekan hari libur
                if ($result_libur->num_rows > 0) {
                    echo "Hari ini adalah hari libur, presensi tidak dapat dilakukan.";
                    exit;
                }

                // Cek apakah presensi sudah ada untuk hari ini
                $sql_check = "SELECT id_presensi, status_masuk, status_pulang FROM presensi WHERE id_pergantian_kelas = '$id_pergantian_kelas' AND tanggal = '$tanggal'";
                $result_check = $conn->query($sql_check);
                $row_check = $result_check->fetch_assoc();

                if ($jam_sekarang >= $jam_masuk_mulai && $jam_sekarang <= $jam_masuk_selesai) {
                    if ($jam_sekarang > $batas_terlambat) {
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://api.fonnte.com/send',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array(
                                'target' => $nomor_orang_tua,
                                'message' =>  'Halo orang tua dari ' . $nama . ',
                                Pada hari ini ' . $nama . ' terlambat masuk',
                                'countryCode' => '62', //optional
                            ),
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: czRfrq_oq+B8U4Lpro6f' //change TOKEN to your actual token
                            ),
                        ));

                        $response = curl_exec($curl);
                        if (curl_errno($curl)) {
                            $error_msg = curl_error($curl);
                        }
                        curl_close($curl);

                        if (isset($error_msg)) {
                            echo $error_msg;
                        }
                    }
                    // Presensi masuk hanya bisa sekali
                    if ($result_check->num_rows == 0) {
                        $query_ambil_tahun = mysqli_query($conn, "SELECT * from tahun_ajaran where status_aktif = '1'");
                        $row_tahun_ajaran = mysqli_fetch_array($query_ambil_tahun);
                        $id_tahun_ajaran = $row_tahun_ajaran['id_tahun_ajaran'];
                        
                        $sql_insert = "INSERT INTO presensi (id_pergantian_kelas, tanggal, waktu, status_masuk, waktu_pulang, status_pulang, status_hapus) 
                                       VALUES ('$id_pergantian_kelas', '$tanggal', '$jam_sekarang', 'Hadir', '', '', '0')";
                        if ($conn->query($sql_insert) === TRUE) {
                            echo "$nama berhasil Presensi masuk pada pukul $jam_sekarang";
                        } else {
                            echo "Error: " . $conn->error;
                        }
                    } else {
                        echo "Anda sudah melakukan presensi masuk hari ini.";
                    }
                } elseif ($jam_sekarang >= $jam_pulang_mulai && $jam_sekarang <= $jam_pulang_selesai) {
                    // Presensi pulang hanya bisa sekali
                    if ($result_check->num_rows > 0 && empty($row_check['status_pulang'])) {
                        $status_pulang = ($jam_sekarang < $batas_pulang_cepat) ? 'Pulang Cepat' : 'Pulang';
                        
                        $sql_update = "UPDATE presensi SET waktu_pulang = '$jam_sekarang', status_pulang = '$status_pulang' WHERE id_presensi = '{$row_check['id_presensi']}'";
                        if ($conn->query($sql_update) === TRUE) {
                            echo "$nama berhasil Presensi pulang pada $jam_sekarang";
                            echo "&";
                            echo "1";
                        } else {
                            echo "Error: " . $conn->error;
                        }
                    } else {
                        echo "Anda sudah melakukan presensi pulang hari ini atau belum melakukan presensi masuk."; //responsemsg
                        echo "$"; //delimiter
                        echo "0"; //responsecode
                    }
                } else {
                    echo "Presensi hanya bisa dilakukan antara 05:00-10:00 (masuk) atau 12:00-20:00 (pulang).";
                    echo "$";
                    echo "0";
                }
            } else {
                echo "Kartu RFID tidak terdaftar.";
                echo "$";
                echo "0";
            }
        } else {
            echo "Kartu RFID tidak terdaftar.";
            echo "$";
            echo "0";
        }
    } else {
        echo "Tidak ada tahun ajaran aktif untuk $tanggal.";
        echo "$";
        echo "0";
    }
} else {
    echo "RFID tidak ditemukan dalam request.";
    echo "$";
    echo "0";
}

$conn->close();
?>
