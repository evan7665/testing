<?php
date_default_timezone_set('Asia/Jakarta');
include "koneksi.php";

$jam_sekarang = date('H:i:s');
$jam_mulai = '23:00:00';
$jam_selesai = '23:59:59';

if ($jam_sekarang >= $jam_mulai && $jam_sekarang <= $jam_selesai) {
    $hari_ini = date('N');
    $tanggal = date('Y-m-d');

    if ($hari_ini >= 1 && $hari_ini <= 5) {
        // Cek hari libur
        $sql_hari_libur = "SELECT keterangan_libur, jenjang FROM hari_libur WHERE tanggal = '$tanggal'";
        $result_hari_libur = $conn->query($sql_hari_libur);

        $jenjang_libur = null;
        if ($result_hari_libur->num_rows > 0) {
            $row_hari_libur = $result_hari_libur->fetch_assoc();
            $keterangan_libur = $row_hari_libur['keterangan_libur'];
            $jenjang_libur = $row_hari_libur['jenjang'];
            echo "Hari ini adalah hari libur. Alasan: $keterangan_libur.<br>";
        }

        // Jika hari libur, tidak perlu melanjutkan ke proses presensi
        if ($jenjang_libur !== null) {
            echo "Presensi tidak diinput karena hari ini adalah hari libur.<br>";
            exit; // Menghentikan eksekusi script lebih lanjut
        }

        // Cek apakah tanggal saat ini berada dalam rentang tahun ajaran aktif
        $sql_tahun_ajaran = "SELECT tanggal_awal, tanggal_akhir FROM tahun_ajaran WHERE status_aktif = 1";
        $result_tahun_ajaran = $conn->query($sql_tahun_ajaran);

        $tahun_ajaran_valid = false;
        if ($result_tahun_ajaran->num_rows > 0) {
            $row_tahun_ajaran = $result_tahun_ajaran->fetch_assoc();
            $tanggal_awal = $row_tahun_ajaran['tanggal_awal'];
            $tanggal_akhir = $row_tahun_ajaran['tanggal_akhir'];

            // Mengecek apakah tanggal hari ini berada di antara tanggal_awal dan tanggal_akhir
            if ($tanggal >= $tanggal_awal && $tanggal <= $tanggal_akhir) {
                $tahun_ajaran_valid = true;
            }
        }

        // Jika tidak dalam rentang tahun ajaran aktif, hentikan proses
        if (!$tahun_ajaran_valid) {
            echo "Tanggal hari ini tidak dalam rentang tahun ajaran aktif. Presensi tidak diinput.<br>";
            exit; // Menghentikan eksekusi script lebih lanjut
        }

        // Menggunakan kelas_siswa untuk memeriksa siswa yang terdaftar
        $sql_siswa = "SELECT ks.id_siswa, ks.id_kelas, ks.id_pergantian_kelas, s.status_hapus, s.status_lulus, ta.id_tahun_ajaran, ta.status_aktif,s.nomor_orang_tua, s.nama
                    FROM kelas_siswa ks
                    JOIN siswa s ON ks.id_siswa = s.id_siswa
                    JOIN tahun_ajaran ta ON ta.id_tahun_ajaran = ks.id_tahun_ajaran
                    WHERE s.status_hapus = '0' AND s.status_lulus = '0' AND ta.status_aktif = '1'";
        $result_siswa = $conn->query($sql_siswa);

        if ($result_siswa->num_rows > 0) {
            while ($row = $result_siswa->fetch_assoc()) {
                $id_siswa = $row['id_siswa'];
                $id_kelas = $row['id_kelas'];
                $nomor_orang_tua = $row['nomor_orang_tua'];
                $nama = $row['nama'];
                $id_pergantian_kelas = $row['id_pergantian_kelas'];  // Ambil id_pergantian_kelas

                // Mengecek jenjang libur dan id_kelas
                if ($jenjang_libur == 1 && $id_kelas == 1) {
                    continue; // Lewati siswa dengan id_kelas = 1 jika jenjang libur = 1
                }

                $sql_check = "SELECT COUNT(*) FROM presensi WHERE tanggal = '$tanggal' AND id_pergantian_kelas = $id_pergantian_kelas";
                $result_check = $conn->query($sql_check);
                $presensi_count = $result_check->fetch_row()[0];

                if ($presensi_count == 0) {
                    $sql_insert = "INSERT INTO presensi (id_pergantian_kelas, tanggal, waktu, status_masuk, waktu_pulang, status_pulang,status_hapus,pengubah) 
                                VALUES ($id_pergantian_kelas, '$tanggal', '', 'Alfa', '', '','','')";
                    if ($conn->query($sql_insert) === TRUE) {
                        echo "Presensi otomatis berhasil diinput untuk id_pergantian_kelas = $id_pergantian_kelas pada tanggal $tanggal.<br>";

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
                                Pada hari ini ' . $nama . 'tidak memasuki kelas dengan status alfa',
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
                    } else {
                        echo "Error: " . $conn->error . "<br>";
                    }
                } else {
                    echo "Presensi sudah ada untuk id_pergantian_kelas = $id_pergantian_kelas pada tanggal $tanggal.<br>";
                }
            }
        } else {
            echo "Tidak ada siswa yang ditemukan.";
        }
    } else {
        echo "Hari ini adalah Sabtu atau Minggu. Presensi tidak diinput.<br>";
    }
} else {
    echo "Skrip hanya dapat dijalankan antara pukul 23:00:00 hingga 23:59:59.<br>";
}

$conn->close();
