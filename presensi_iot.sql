/*
SQLyog Community v13.2.0 (64 bit)
MySQL - 10.4.28-MariaDB : Database - presensi_iot
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`presensi_iot` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `presensi_iot`;

/*Table structure for table `angkatan` */

DROP TABLE IF EXISTS `angkatan`;

CREATE TABLE `angkatan` (
  `id_angkatan` int(10) NOT NULL AUTO_INCREMENT,
  `angkatan` varchar(100) DEFAULT NULL,
  `status_hapus` int(10) DEFAULT 0,
  PRIMARY KEY (`id_angkatan`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `angkatan` */

insert  into `angkatan`(`id_angkatan`,`angkatan`,`status_hapus`) values 
(1,'2022',0),
(2,'2023',0),
(3,'2024',0);

/*Table structure for table `hari_libur` */

DROP TABLE IF EXISTS `hari_libur`;

CREATE TABLE `hari_libur` (
  `id_hari_libur` int(10) NOT NULL AUTO_INCREMENT,
  `tanggal` date DEFAULT NULL,
  `keterangan_libur` varchar(150) DEFAULT NULL,
  `jenjang` varchar(100) DEFAULT NULL,
  `status_hapus` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_hari_libur`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `hari_libur` */

insert  into `hari_libur`(`id_hari_libur`,`tanggal`,`keterangan_libur`,`jenjang`,`status_hapus`) values 
(1,'2025-01-31','hari raya imlek','2',0),
(3,'2025-02-08','testing lagi','2',0),
(4,'2025-03-19','testing semua','2',1),
(5,'2025-03-19','testing semua','3',1),
(6,'2025-03-19','testing semua','1',0),
(7,'2025-03-19','testing semua','4',0),
(8,'2025-03-19','testing semua','5',0),
(10,'2025-02-14','testing lagi','1',0);

/*Table structure for table `jenjang` */

DROP TABLE IF EXISTS `jenjang`;

CREATE TABLE `jenjang` (
  `id_jenjang` int(10) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `id_tingkat` int(10) DEFAULT NULL,
  `status_hapus` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_jenjang`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `jenjang` */

insert  into `jenjang`(`id_jenjang`,`id_user`,`nama_lengkap`,`id_tingkat`,`status_hapus`) values 
(1,2,'Herodes Mantabb',2,0),
(2,3,'Erwin Maripwan',2,0),
(3,4,'cobaa',1,0),
(4,11,'test',3,0);

/*Table structure for table `kelas` */

DROP TABLE IF EXISTS `kelas`;

CREATE TABLE `kelas` (
  `id_kelas` int(10) NOT NULL AUTO_INCREMENT,
  `kelas` varchar(100) DEFAULT NULL,
  `id_tingkat` int(10) DEFAULT NULL,
  `status_hapus` int(10) DEFAULT 0,
  PRIMARY KEY (`id_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `kelas` */

insert  into `kelas`(`id_kelas`,`kelas`,`id_tingkat`,`status_hapus`) values 
(1,'X-MIPA',1,0),
(2,'XI-MIPA',1,0),
(3,'XII-MIPA',1,0),
(4,'I-A',2,0),
(5,'I-B',2,0),
(6,'IX-MIPA',1,0);

/*Table structure for table `kelas_siswa` */

DROP TABLE IF EXISTS `kelas_siswa`;

CREATE TABLE `kelas_siswa` (
  `id_pergantian_kelas` int(10) NOT NULL AUTO_INCREMENT,
  `id_siswa` int(10) DEFAULT NULL,
  `id_tahun_ajaran` int(10) DEFAULT NULL,
  `id_kelas` int(10) DEFAULT NULL,
  `status_hapus` int(10) DEFAULT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pergantian_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `kelas_siswa` */

insert  into `kelas_siswa`(`id_pergantian_kelas`,`id_siswa`,`id_tahun_ajaran`,`id_kelas`,`status_hapus`,`time_stamp`) values 
(2,1,2,1,0,'2025-02-11 13:03:12'),
(7,2,2,2,0,'2025-02-19 09:30:08'),
(81,107,2,1,0,'2025-03-21 19:40:36');

/*Table structure for table `presensi` */

DROP TABLE IF EXISTS `presensi`;

CREATE TABLE `presensi` (
  `id_presensi` int(11) NOT NULL AUTO_INCREMENT,
  `id_pergantian_kelas` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `status_masuk` enum('Hadir','Izin','Sakit','Alfa') NOT NULL,
  `waktu_pulang` time DEFAULT NULL,
  `status_pulang` enum('pulang cepat','pulang') DEFAULT NULL,
  `status_hapus` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_presensi`),
  KEY `id_siswa` (`id_pergantian_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `presensi` */

insert  into `presensi`(`id_presensi`,`id_pergantian_kelas`,`tanggal`,`waktu`,`status_masuk`,`waktu_pulang`,`status_pulang`,`status_hapus`) values 
(93,2,'2025-02-10','09:01:00','Hadir','19:37:00','pulang',0),
(94,7,'2025-02-11','09:01:00','Hadir',NULL,NULL,0),
(95,11,'2027-07-11','09:01:00','Hadir',NULL,NULL,0),
(96,9,'2027-07-11','09:01:00','Hadir',NULL,NULL,0),
(97,12,'2028-10-10','09:01:00','Hadir',NULL,NULL,0),
(98,13,'2028-10-10','09:01:00','Hadir',NULL,NULL,0),
(99,13,'2028-10-11','07:01:00','Hadir',NULL,NULL,0),
(100,12,'2028-10-11','07:01:00','Hadir',NULL,NULL,0),
(101,13,'2028-10-12','07:01:00','Hadir',NULL,NULL,0),
(102,12,'2025-02-13','00:00:00','Alfa','00:00:00','',0),
(103,13,'2025-02-13','00:00:00','Alfa','00:00:00','',0),
(104,17,'2025-02-13','00:00:00','Alfa','00:00:00','',0),
(111,12,'2025-02-17','00:00:00','Alfa','00:00:00','',0),
(112,13,'2025-02-17','00:00:00','Alfa','00:00:00','',0),
(126,12,'2028-02-18','08:37:30','Hadir','12:04:55','pulang cepat',0),
(127,2,'2025-02-19','08:37:30','Hadir','13:19:02','pulang cepat',0),
(128,7,'2025-02-19','08:37:30','Hadir',NULL,NULL,0),
(129,2,'2025-03-13','09:57:15','Hadir','00:00:00','',0),
(130,2,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(131,7,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(132,22,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(133,27,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(134,28,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(135,29,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(136,30,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(137,31,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(138,32,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(139,33,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(140,34,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(141,35,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(142,47,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(143,48,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(144,49,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(145,50,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(146,51,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(147,52,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(148,53,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(149,54,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(150,55,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(151,56,'2025-03-18','00:00:00','Alfa','00:00:00','',0),
(152,2,'2025-03-20','08:07:20','Hadir','00:00:00','',0);

/*Table structure for table `rekap` */

DROP TABLE IF EXISTS `rekap`;

CREATE TABLE `rekap` (
  `id_rekap` int(10) NOT NULL AUTO_INCREMENT,
  `id_presensi` int(10) DEFAULT NULL,
  `id_siswa` int(10) DEFAULT NULL,
  `id_tahun_ajaran` int(10) DEFAULT NULL,
  `id_kelas` int(10) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu_masuk` varchar(100) DEFAULT NULL,
  `status_masuk` varchar(100) DEFAULT NULL,
  `waktu_pulang` varchar(100) DEFAULT NULL,
  `status_pulang` int(10) DEFAULT 0,
  PRIMARY KEY (`id_rekap`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rekap` */

/*Table structure for table `siswa` */

DROP TABLE IF EXISTS `siswa`;

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `rfid_tag_hex` varchar(255) DEFAULT NULL,
  `rfid_tag_dec` varchar(255) DEFAULT NULL,
  `id_tingkat` int(10) DEFAULT NULL,
  `id_angkatan` int(10) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `status_lulus` int(10) DEFAULT 0,
  `status_keluar` int(10) DEFAULT 0,
  `status_hapus` int(10) NOT NULL DEFAULT 0,
  `nomor_orang_tua` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_siswa`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `siswa` */

insert  into `siswa`(`id_siswa`,`nama`,`rfid_tag_hex`,`rfid_tag_dec`,`id_tingkat`,`id_angkatan`,`tanggal_lahir`,`status_lulus`,`status_keluar`,`status_hapus`,`nomor_orang_tua`) values 
(1,'Evan Santoso','4405532F6680','4405532F6680',1,1,'0000-00-00',0,0,0,'082138539834'),
(2,'Aaron Christanto','A2 DE 30 03','162 222 48 03',1,1,'0000-00-00',0,0,0,'082138539834'),
(3,'Testing','A2 DE 30 04','162 222 48 05',1,1,'0000-00-00',0,0,0,'082138539834'),
(4,'Naruto','00000002','00000001',2,3,NULL,0,0,0,'082138539834'),
(5,'coba','00000004','00000005',3,3,NULL,0,0,0,'082138539834'),
(6,'testtttt','32323232321','0000000323',2,1,NULL,0,0,0,'082138539834'),
(107,'Test_coba','32323232321','00000001',1,1,'2004-02-21',0,0,0,'082138539834');

/*Table structure for table `tahun_ajaran` */

DROP TABLE IF EXISTS `tahun_ajaran`;

CREATE TABLE `tahun_ajaran` (
  `id_tahun_ajaran` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal_awal` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `status_aktif` int(10) DEFAULT 0,
  `status_hapus` int(10) DEFAULT 0,
  PRIMARY KEY (`id_tahun_ajaran`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tahun_ajaran` */

insert  into `tahun_ajaran`(`id_tahun_ajaran`,`tanggal_awal`,`tanggal_akhir`,`status_aktif`,`status_hapus`) values 
(2,'2025-02-01','2026-07-16',1,0),
(3,'2026-08-03','2027-12-01',0,0),
(7,'2027-12-31','2028-12-01',0,0),
(8,'2029-06-13','2030-06-21',0,0);

/*Table structure for table `tingkat` */

DROP TABLE IF EXISTS `tingkat`;

CREATE TABLE `tingkat` (
  `id_tingkat` int(10) NOT NULL AUTO_INCREMENT,
  `tingkat` varchar(100) DEFAULT NULL,
  `status_hapus` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_tingkat`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tingkat` */

insert  into `tingkat`(`id_tingkat`,`tingkat`,`status_hapus`) values 
(1,'SMA',0),
(2,'SD',0),
(3,'SMP',0),
(4,'SMK1',0),
(5,'SMK2',0);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id_user` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `jabatan` enum('jenjang','wali_kelas','admin') DEFAULT NULL,
  `status_hapus` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

insert  into `users`(`id_user`,`username`,`password`,`jabatan`,`status_hapus`) values 
(1,'admin','21232f297a57a5a743894a0e4a801fc3','admin',0),
(2,'smaa','18fb9593a8fc49457bb5c4ebc2d2b402','jenjang',0),
(3,'sd','6226f7cbe59e99a90b5cef6f94f966fd','jenjang',0),
(4,'coba','c3ec0f7b054e729c5a716c8125839829','jenjang',0),
(11,'test','098f6bcd4621d373cade4e832627b4f6','jenjang',0),
(12,'wali_sma','c3ec0f7b054e729c5a716c8125839829','wali_kelas',0),
(13,'testing123','7f2ababa423061c509f4923dd04b6cf1','wali_kelas',0),
(14,'testing','ae2b1fca515949e5d54fb22b8ed95575','wali_kelas',0),
(15,'evan','98cc7d37dc7b90c14a59ef0c5caa8995','wali_kelas',0);

/*Table structure for table `wali_kelas` */

DROP TABLE IF EXISTS `wali_kelas`;

CREATE TABLE `wali_kelas` (
  `id_wali_kelas` int(10) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) DEFAULT NULL,
  `nama_wali_kelas` varchar(100) DEFAULT NULL,
  `no_telpon` varchar(100) DEFAULT NULL,
  `id_tingkat` int(10) DEFAULT NULL,
  `status_hapus` int(10) DEFAULT 0,
  PRIMARY KEY (`id_wali_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `wali_kelas` */

insert  into `wali_kelas`(`id_wali_kelas`,`id_user`,`nama_wali_kelas`,`no_telpon`,`id_tingkat`,`status_hapus`) values 
(1,12,'Sanjayaa','214748364722',1,0),
(5,14,'testing','12345678',1,0),
(6,15,'evan','09876543',1,0);

/*Table structure for table `wali_kelas_periode` */

DROP TABLE IF EXISTS `wali_kelas_periode`;

CREATE TABLE `wali_kelas_periode` (
  `id_wali_kelas_periode` int(10) NOT NULL AUTO_INCREMENT,
  `id_wali_kelas` int(10) DEFAULT NULL,
  `id_kelas` int(10) DEFAULT NULL,
  `id_tahun_ajaran` int(10) DEFAULT NULL,
  `status_hapus` int(10) DEFAULT 0,
  PRIMARY KEY (`id_wali_kelas_periode`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `wali_kelas_periode` */

insert  into `wali_kelas_periode`(`id_wali_kelas_periode`,`id_wali_kelas`,`id_kelas`,`id_tahun_ajaran`,`status_hapus`) values 
(1,1,1,2,0),
(6,5,6,2,0),
(11,5,2,3,0),
(13,6,2,2,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
