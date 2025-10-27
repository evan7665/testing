<?php
require 'vendor/autoload.php'; // Pastikan Composer sudah diinstall

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include "koneksi.php"; // Koneksi database

$id_kelas = $_GET['id_kelas'];

$query_kelas = mysqli_query($conn,"SELECT * from kelas where id_kelas = '$id_kelas'");
$row_kelas = mysqli_fetch_array($query_kelas);
$kelas = $row_kelas['kelas'];




$query_periode = mysqli_query($conn, "SELECT * FROM tahun_ajaran WHERE status_aktif = '1'");
$row_periode = mysqli_fetch_array($query_periode);
$periode_awal = $row_periode['tanggal_awal'];
$periode_akhir = $row_periode['tanggal_akhir'];

$query = "SELECT 
                        s.id_siswa,
                        s.nama AS nama_siswa,
                        COUNT(CASE WHEN p.status_masuk = 'Hadir' THEN 1 END) AS total_hadir,
                        COUNT(CASE WHEN p.status_masuk = 'Sakit' THEN 1 END) AS total_sakit,
                        COUNT(CASE WHEN p.status_masuk = 'Izin' THEN 1 END) AS total_izin,
                        COUNT(CASE WHEN p.status_masuk = 'Alfa' THEN 1 END) AS total_alfa,
                        COUNT(CASE WHEN p.status_masuk = 'Hadir' AND TIME(p.waktu) > '07:10:00' THEN 1 END) AS total_terlambat,
                        COUNT(p.tanggal) AS total_hari_aktif,
                        ROUND(COUNT(CASE WHEN p.status_masuk = 'Hadir' THEN 1 END) / NULLIF(COUNT(p.tanggal), 0) * 100, 2) AS persen_hadir,
                        ROUND(COUNT(CASE WHEN p.status_masuk = 'Sakit' THEN 1 END) / NULLIF(COUNT(p.tanggal), 0) * 100, 2) AS persen_sakit,
                        ROUND(COUNT(CASE WHEN p.status_masuk = 'Izin' THEN 1 END) / NULLIF(COUNT(p.tanggal), 0) * 100, 2) AS persen_izin,
                        ROUND(COUNT(CASE WHEN p.status_masuk = 'Alfa' THEN 1 END) / NULLIF(COUNT(p.tanggal), 0) * 100, 2) AS persen_alfa,
                        ROUND(COUNT(CASE WHEN p.status_masuk = 'Hadir' AND TIME(p.waktu) > '07:10:00' THEN 1 END) / NULLIF(COUNT(p.tanggal), 0) * 100, 2) AS persen_terlambat
                    FROM 
                        siswa s
                    LEFT JOIN 
                        kelas_siswa ks ON s.id_siswa = ks.id_siswa
                    LEFT JOIN 
                        presensi p ON ks.id_pergantian_kelas = p.id_pergantian_kelas 
                        AND p.tanggal BETWEEN '$periode_awal' AND '$periode_akhir'
                        AND p.status_hapus = 0
                    WHERE 
                        ks.id_kelas = '$id_kelas'
                    GROUP BY 
                        s.id_siswa, s.nama
                    ORDER BY 
                        persen_hadir DESC;

";

$result = $conn->query($query);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$sheet->setCellValue('A1', 'ID Siswa');
$sheet->setCellValue('B1', 'Nama Siswa');
$sheet->setCellValue('C1', 'Hadir');
$sheet->setCellValue('D1', 'Sakit');
$sheet->setCellValue('E1', 'Izin');
$sheet->setCellValue('F1', 'Alfa');
$sheet->setCellValue('G1', 'Terlambat');
$sheet->setCellValue('H1', 'Hari Aktif');
$sheet->setCellValue('I1', '% Hadir');
$sheet->setCellValue('J1', '% Sakit');
$sheet->setCellValue('K1', '% Izin');
$sheet->setCellValue('L1', '% Alfa');
$sheet->setCellValue('M1', '% Terlambat');

$rowIndex = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowIndex, $row['id_siswa']);
    $sheet->setCellValue('B' . $rowIndex, $row['nama_siswa']);
    $sheet->setCellValue('C' . $rowIndex, $row['total_hadir']);
    $sheet->setCellValue('D' . $rowIndex, $row['total_sakit']);
    $sheet->setCellValue('E' . $rowIndex, $row['total_izin']);
    $sheet->setCellValue('F' . $rowIndex, $row['total_alfa']);
    $sheet->setCellValue('G' . $rowIndex, $row['total_terlambat']);
    $sheet->setCellValue('H' . $rowIndex, $row['total_hari_aktif']);
    $sheet->setCellValue('I' . $rowIndex, $row['persen_hadir'] . '%');
    $sheet->setCellValue('J' . $rowIndex, $row['persen_sakit'] . '%');
    $sheet->setCellValue('K' . $rowIndex, $row['persen_izin'] . '%');
    $sheet->setCellValue('L' . $rowIndex, $row['persen_alfa'] . '%');
    $sheet->setCellValue('M' . $rowIndex, $row['persen_terlambat'] . '%');
    $rowIndex++;
}

$filename = "Rekap_Kelas_" . $kelas . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
