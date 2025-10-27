<?php
include 'koneksi.php'; // Koneksi ke database
include 'vendor/autoload.php'; // Pastikan PhpSpreadsheet terinstall

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_excel'])) {
    $file = $_FILES['file_excel']['tmp_name'];

    // Cek apakah file valid
    if (!file_exists($file) || $_FILES['file_excel']['size'] == 0) {
        echo "<script>alert('File tidak valid!'); window.location='tambah_siswa.php';</script>";
        exit;
    }

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Ambil tahun ajaran aktif
        $query_tahun_ajaran = mysqli_query($conn, "SELECT id_tahun_ajaran FROM tahun_ajaran WHERE status_aktif = '1' LIMIT 1");
        if (!$query_tahun_ajaran || mysqli_num_rows($query_tahun_ajaran) == 0) {
            echo "<script>alert('Tidak ada tahun ajaran aktif!'); window.location='tambah_siswa.php';</script>";
            exit;
        }
        $row_tahun = mysqli_fetch_assoc($query_tahun_ajaran);
        $id_tahun_ajaran = $row_tahun['id_tahun_ajaran'];

        // Mulai transaksi
        mysqli_begin_transaction($conn);

        // Mulai dari baris kedua (lewati header)
        for ($i = 19; $i < count($data); $i++) {
            $nama = mysqli_real_escape_string($conn, $data[$i][0]); // Kolom A
            $rfid_hex = mysqli_real_escape_string($conn, $data[$i][1]); // Kolom B
            $rfid_dec = mysqli_real_escape_string($conn, $data[$i][2]); // Kolom C
            $id_tingkat = mysqli_real_escape_string($conn, $data[$i][3]); // Kolom D
            $id_angkatan = mysqli_real_escape_string($conn, $data[$i][4]); // Kolom E
            $id_kelas = mysqli_real_escape_string($conn, $data[$i][5]); // Kolom F
            // Pastikan format tanggal dari Excel (MM/DD/YYYY) dikonversi ke format database (YYYY-MM-DD)
            $tanggal_lahir = mysqli_real_escape_string($conn, $data[$i][6]); // Kolom G
            $dateObject = DateTime::createFromFormat('m/d/Y', $tanggal_lahir);
            if ($dateObject) {
                $tanggal_lahir = $dateObject->format('Y-m-d');
            } else {
                $tanggal_lahir = NULL; // Jika gagal parsing, buat NULL agar tidak error
            }

            $nomor_orang_tua = mysqli_real_escape_string($conn, $data[$i][7]); // Kolom H

            // Konversi tanggal jika diperlukan
            if (preg_match('/^\d+$/', $tanggal_lahir)) {
                $tanggal_lahir = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($tanggal_lahir));
            }

            // Insert ke database
            $query = "INSERT INTO siswa (nama, rfid_tag_hex, rfid_tag_dec, id_tingkat, id_angkatan, tanggal_lahir, status_lulus, status_keluar, status_hapus, nomor_orang_tua) 
                      VALUES ('$nama', '$rfid_hex', '$rfid_dec', '$id_tingkat', '$id_angkatan', '$tanggal_lahir', '0', '0', '0', '$nomor_orang_tua')";

            if (!mysqli_query($conn, $query)) {
                throw new Exception("Gagal menyimpan data siswa: " . mysqli_error($conn));
            }

            // Ambil ID siswa yang baru dimasukkan
            $last_id = mysqli_insert_id($conn);

            // Insert ke tabel kelas_siswa
            $query_kelas = "INSERT INTO kelas_siswa (id_siswa, id_tahun_ajaran, id_kelas, status_hapus) 
                            VALUES ('$last_id', '$id_tahun_ajaran', '$id_kelas', '0')";

            if (!mysqli_query($conn, $query_kelas)) {
                throw new Exception("Gagal menyimpan data kelas_siswa: " . mysqli_error($conn));
            }
        }

        // Commit transaksi
        mysqli_commit($conn);

        echo "<script>alert('Data berhasil diimport!'); window.location='tambah_siswa.php';</script>";
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($conn);
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location='tambah_siswa.php';</script>";
    }
} else {
    echo "<script>alert('Gagal upload file!'); window.location='tambah_siswa.php';</script>";
}
