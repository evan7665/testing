<?php
// Include file koneksi dan session
include "koneksi.php";
include "session.php";

// Cek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $rfid_tag_hex = mysqli_real_escape_string($conn, $_POST['rfid_tag_hex']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $nomor_orang_tua = mysqli_real_escape_string($conn, $_POST['nomor_orang_tua']);
    $rfid_tag_dec = isset($_POST['rfid_tag_dec']) ? mysqli_real_escape_string($conn, $_POST['rfid_tag_dec']) : null;
    $id_tingkat = isset($_POST['id_tingkat']) ? mysqli_real_escape_string($conn, $_POST['id_tingkat']) : null;
    $id_kelas = isset($_POST['id_kelas']) ? mysqli_real_escape_string($conn, $_POST['id_kelas']) : null;
    $id_angkatan = mysqli_real_escape_string($conn, $_POST['id_angkatan']);

    // Validasi data (opsional)
    if (empty($nama) || empty($rfid_tag_hex) || empty($id_angkatan)) {
        echo "Semua field yang wajib harus diisi!";
        exit;
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Query untuk menyimpan data ke dalam tabel siswa
        $query_siswa = "INSERT INTO siswa (nama, rfid_tag_hex, rfid_tag_dec, id_tingkat, id_angkatan,tanggal_lahir,status_lulus,status_keluar,status_hapus,nomor_orang_tua,penginput_terakhir) 
                         VALUES ('$nama', '$rfid_tag_hex', '$rfid_tag_dec', '$id_tingkat', '$id_angkatan', '$tanggal_lahir', '0','0','0','$nomor_orang_tua', '$_SESSION[username]')";

        if (!mysqli_query($conn, $query_siswa)) {
            throw new Exception("Gagal menyimpan data siswa: " . mysqli_error($conn));
        }

        // Ambil id_siswa yang baru saja dimasukkan
        $id_siswa = mysqli_insert_id($conn);

        // Ambil id_tahun_ajaran yang memiliki status_aktif = 1
        $query_tahun_ajaran = "SELECT id_tahun_ajaran FROM tahun_ajaran WHERE status_aktif = 1 LIMIT 1";
        $result_tahun_ajaran = mysqli_query($conn, $query_tahun_ajaran);
        
        if (!$result_tahun_ajaran || mysqli_num_rows($result_tahun_ajaran) == 0) {
            throw new Exception("Tidak ada tahun ajaran yang aktif!");
        }
        
        $row_tahun_ajaran = mysqli_fetch_assoc($result_tahun_ajaran);
        $id_tahun_ajaran = $row_tahun_ajaran['id_tahun_ajaran'];

        // Insert ke dalam tabel kelas_siswa
        $query_kelas_siswa = "INSERT INTO kelas_siswa (id_siswa, id_tahun_ajaran, id_kelas, status_hapus) 
                               VALUES ('$id_siswa', '$id_tahun_ajaran', '$id_kelas', '0')";
        
        if (!mysqli_query($conn, $query_kelas_siswa)) {
            throw new Exception("Gagal menyimpan data kelas_siswa: " . mysqli_error($conn));
        }

        // Commit transaksi
        mysqli_commit($conn);

        // Redirect ke halaman daftar siswa
        header("Location: data_siswa.php?status=success");
        exit;
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        mysqli_rollback($conn);
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
} else {
    // Jika form tidak disubmit dengan POST, kembalikan ke form
    header("Location: tambah_siswa.php");
    exit;
}
