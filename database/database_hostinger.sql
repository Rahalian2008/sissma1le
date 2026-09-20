-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: presensi
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_years` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` enum('Ganjil','Genap') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ganjil',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (1,'2026/2027','Ganjil',1,'2026-07-15','2026-12-20','2026-09-19 03:17:53','2026-09-19 03:17:53');
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `achievement_categories`
--

DROP TABLE IF EXISTS `achievement_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievement_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `achievement_categories_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievement_categories`
--

LOCK TABLES `achievement_categories` WRITE;
/*!40000 ALTER TABLE `achievement_categories` DISABLE KEYS */;
INSERT INTO `achievement_categories` VALUES (1,'R1','Pengembangan Keagamaan','Kegiatan keagamaan, hafalan Al-Quran/Kitab Suci, MTQ, dan aktivitas religi positif.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,'R2','Kejujuran','Tindakan terpuji menjunjung nilai kejujuran, integritas, dan amanah.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(3,'R3','Prestasi Akademis','Capaian kejuaraan sains, OSN, karya tulis, olimpiade mata pelajaran, dan peringkat sekolah.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(4,'R4','Kedisiplinan','Ketertiban kehadiran, kepatuhan tata tertib, dan keteladanan harian.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(5,'R5','Pengembangan Sosial','Aktivitas kepedulian sosial, bakti sosial, relawan kemanusiaan, dan donor darah.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(6,'R6','Kepemimpinan','Peran kepengurusan OSIS, MPK, pradana Pramuka, dan pimpinan organisasi kesiswaan.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(7,'R7','Kebangsaan','Pasukan Pengibar Bendera (Paskibra), lomba wawasan kebangsaan, dan bela negara.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(8,'R8','Keaktifan dan Prestasi','Kejuaraan olahraga (O2SN), seni budaya (FLS2N), musik, tari, teater, dan ekstrakurikuler.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(9,'R9','Peduli Lingkungan','Aktivitas Adiwiyata, konservasi lingkungan hidup sekolah, bank sampah, dan reboisasi.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(10,'R10','Kewirausahaan','Partisipasi dan prestasi dalam pameran kewirausahaan siswa, inovasi produk, dan bisnis plan.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54');
/*!40000 ALTER TABLE `achievement_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `achievement_items`
--

DROP TABLE IF EXISTS `achievement_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievement_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_points` int NOT NULL DEFAULT '10',
  `level` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SEKOLAH',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `achievement_items_category_id_foreign` (`category_id`),
  CONSTRAINT `achievement_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `achievement_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievement_items`
--

LOCK TABLES `achievement_items` WRITE;
/*!40000 ALTER TABLE `achievement_items` DISABLE KEYS */;
INSERT INTO `achievement_items` VALUES (1,1,'R1.1','Hafal Al-Quran minimal 1 Juz / Kitab Suci',30,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,1,'R1.2','Juara I/II/III MTQ / Lomba Keagamaan Kabupaten',50,'KABUPATEN','2026-09-19 03:17:54','2026-09-19 03:17:54'),(3,1,'R1.3','Petugas Ibadah Rutin / Muadzin / Rohis Aktif',15,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(4,2,'R2.1','Menemukan dan mengembalikan barang berharga / uang',25,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(5,2,'R2.2','Mengakui kekeliruan/pelanggaran secara jujur dan berinisiatif memperbaiki',20,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(6,3,'R3.1','Juara Olimpiade Sains / OSN Tingkat Kabupaten',75,'KABUPATEN','2026-09-19 03:17:54','2026-09-19 03:17:54'),(7,3,'R3.2','Juara Olimpiade Sains / OSN Tingkat Provinsi / Nasional',150,'NASIONAL','2026-09-19 03:17:54','2026-09-19 03:17:54'),(8,3,'R3.3','Peringkat 1 Paralel Nilai Rapor Semester',40,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(9,3,'R3.4','Juara Lomba Karya Ilmiah Remaja (KIR)',60,'PROVINSI','2026-09-19 03:17:54','2026-09-19 03:17:54'),(10,4,'R4.1','Kehadiran 100% tanpa izin/sakit/alpa dalam satu semester',35,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(11,4,'R4.2','Petugas Pemimpin Upacara Bendera Hari Besar Nasional',20,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(12,5,'R5.1','Penggerak Utama Bakti Sosial Sekolah untuk Masyarakat Lengkong',30,'KECAMATAN','2026-09-19 03:17:54','2026-09-19 03:17:54'),(13,5,'R5.2','Relawan Bencana / PMR Tanggap Darurat',25,'KABUPATEN','2026-09-19 03:17:54','2026-09-19 03:17:54'),(14,6,'R6.1','Ketua / Pengurus Inti OSIS atau MPK Masa Bakti Aktif',50,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(15,6,'R6.2','Pradana / Pradani Pramuka Pangkalan SMAN 1 Lengkong',40,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(16,7,'R7.1','Anggota Paskibraka Tingkat Kabupaten Nganjuk',100,'KABUPATEN','2026-09-19 03:17:54','2026-09-19 03:17:54'),(17,7,'R7.2','Juara Lomba Cerdas Cermat 4 Pilar Kebangsaan',60,'PROVINSI','2026-09-19 03:17:54','2026-09-19 03:17:54'),(18,8,'R8.1','Juara O2SN Olahraga Tingkat Kabupaten',60,'KABUPATEN','2026-09-19 03:17:54','2026-09-19 03:17:54'),(19,8,'R8.2','Juara FLS2N Seni / Teater / Musik Tingkat Kabupaten / Provinsi',80,'PROVINSI','2026-09-19 03:17:54','2026-09-19 03:17:54'),(20,9,'R9.1','Kader Adiwiyata Aktif Pemelihara Taman & Biopori',25,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(21,9,'R9.2','Inovator Pengelolaan Bank Sampah Mandiri Sekolah',35,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54'),(22,10,'R10.1','Juara Gelar Karya Kewirausahaan Siswa (Kreativitas Produk Lokal)',45,'SEKOLAH','2026-09-19 03:17:54','2026-09-19 03:17:54');
/*!40000 ALTER TABLE `achievement_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned DEFAULT NULL,
  `achievement_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SEKOLAH',
  `points` int NOT NULL DEFAULT '10',
  `date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `certificate_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporter_id` bigint unsigned DEFAULT NULL,
  `status` enum('DRAFT','MENUNGGU_VERIFIKASI','DIVERIFIKASI','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MENUNGGU_VERIFIKASI',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `achievements_class_id_foreign` (`class_id`),
  KEY `achievements_category_id_foreign` (`category_id`),
  KEY `achievements_item_id_foreign` (`item_id`),
  KEY `achievements_reporter_id_foreign` (`reporter_id`),
  KEY `achievements_verified_by_foreign` (`verified_by`),
  KEY `achievements_student_id_status_index` (`student_id`,`status`),
  CONSTRAINT `achievements_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `achievement_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `achievements_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `school_classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `achievements_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `achievement_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `achievements_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `achievements_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `achievements_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievements`
--

LOCK TABLES `achievements` WRITE;
/*!40000 ALTER TABLE `achievements` DISABLE KEYS */;
INSERT INTO `achievements` VALUES (1,1,2,3,6,'PRESTASI-2026-001','Juara 1 Olimpiade Sains Nasional (OSN) Bidang Fisika Kabupaten Nganjuk','KABUPATEN',75,'2026-08-14','Meraih medali emas seleksi OSN-K Fisika dan mewakili Kabupaten Nganjuk ke tingkat Provinsi Jawa Timur.','achievements/sertifikat_osn_2026.pdf',3,'DIVERIFIKASI',NULL,4,'2026-08-16 03:00:00','2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,1,2,8,18,'PRESTASI-2026-002','Juara 2 O2SN Cabang Bulutangkis Tunggal Putra Kabupaten Nganjuk','KABUPATEN',60,'2026-08-28','Memperoleh medali perak cabang olahraga bulutangkis perorangan putra SMA.','achievements/sertifikat_o2sn_2026.pdf',3,'DIVERIFIKASI',NULL,6,'2026-08-30 02:15:00','2026-09-19 03:17:54','2026-09-19 03:17:54');
/*!40000 ALTER TABLE `achievements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_devices`
--

DROP TABLE IF EXISTS `attendance_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `device_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_type` enum('RFID','KIOSK','BARCODE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'RFID',
  `location_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ONLINE','OFFLINE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ONLINE',
  `last_ping_at` timestamp NULL DEFAULT NULL,
  `secret_key` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_devices_device_code_unique` (`device_code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_devices`
--

LOCK TABLES `attendance_devices` WRITE;
/*!40000 ALTER TABLE `attendance_devices` DISABLE KEYS */;
INSERT INTO `attendance_devices` VALUES (1,'RFID-GATE-01','Scanner RFID Gerbang Barat','RFID','Pintu Gerbang Utama SMAN 1 Lengkong','192.168.1.101','ONLINE','2026-09-20 07:53:32','sma1le_rfid_sec_key_2026','2026-09-19 03:17:53','2026-09-20 07:53:32'),(2,'KIOSK-LOBBY-01','Kiosk Mandiri Presensi Lobby','KIOSK','Lobby Gedung Tata Usaha','192.168.1.102','ONLINE','2026-09-20 07:53:32','sma1le_kiosk_sec_key_2026','2026-09-19 03:17:53','2026-09-20 07:53:32');
/*!40000 ALTER TABLE `attendance_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_locations`
--

DROP TABLE IF EXISTS `attendance_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `radius_meters` int unsigned NOT NULL DEFAULT '100',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_locations`
--

LOCK TABLES `attendance_locations` WRITE;
/*!40000 ALTER TABLE `attendance_locations` DISABLE KEYS */;
INSERT INTO `attendance_locations` VALUES (1,'SMA Negeri 1 Lengkong - Kampus Utama',-7.56845000,112.04612000,100,1,'Area utama SMA Negeri 1 Lengkong (Gerbang, Lapangan, Gedung KBM)','2026-09-19 03:17:53','2026-09-19 03:17:53');
/*!40000 ALTER TABLE `attendance_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_qr_logs`
--

DROP TABLE IF EXISTS `attendance_qr_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_qr_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attendance_id` bigint unsigned DEFAULT NULL,
  `session_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `scanned_at` timestamp NOT NULL,
  `qr_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT '1',
  `failure_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_qr_logs_attendance_id_foreign` (`attendance_id`),
  KEY `attendance_qr_logs_session_id_foreign` (`session_id`),
  KEY `attendance_qr_logs_student_id_foreign` (`student_id`),
  CONSTRAINT `attendance_qr_logs_attendance_id_foreign` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendance_qr_logs_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `attendance_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_qr_logs_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_qr_logs`
--

LOCK TABLES `attendance_qr_logs` WRITE;
/*!40000 ALTER TABLE `attendance_qr_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_qr_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_rfid_logs`
--

DROP TABLE IF EXISTS `attendance_rfid_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_rfid_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attendance_id` bigint unsigned DEFAULT NULL,
  `rfid_uid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned DEFAULT NULL,
  `scanned_at` timestamp NOT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT '1',
  `raw_payload` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_rfid_logs_attendance_id_foreign` (`attendance_id`),
  KEY `attendance_rfid_logs_device_id_foreign` (`device_id`),
  KEY `attendance_rfid_logs_student_id_foreign` (`student_id`),
  CONSTRAINT `attendance_rfid_logs_attendance_id_foreign` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendance_rfid_logs_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `attendance_devices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendance_rfid_logs_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_rfid_logs`
--

LOCK TABLES `attendance_rfid_logs` WRITE;
/*!40000 ALTER TABLE `attendance_rfid_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_rfid_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_selfies`
--

DROP TABLE IF EXISTS `attendance_selfies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_selfies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attendance_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `face_detected` tinyint(1) NOT NULL DEFAULT '1',
  `liveness_score` decimal(5,2) NOT NULL DEFAULT '1.00',
  `client_metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_selfies_attendance_id_foreign` (`attendance_id`),
  KEY `attendance_selfies_student_id_foreign` (`student_id`),
  CONSTRAINT `attendance_selfies_attendance_id_foreign` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_selfies_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_selfies`
--

LOCK TABLES `attendance_selfies` WRITE;
/*!40000 ALTER TABLE `attendance_selfies` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_selfies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_sessions`
--

DROP TABLE IF EXISTS `attendance_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned DEFAULT NULL,
  `subject_or_activity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `teacher_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `qr_code_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qr_expires_at` timestamp NULL DEFAULT NULL,
  `status` enum('ACTIVE','CLOSED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_sessions_qr_code_token_unique` (`qr_code_token`),
  KEY `attendance_sessions_class_id_foreign` (`class_id`),
  KEY `attendance_sessions_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `attendance_sessions_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `school_classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendance_sessions_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_sessions`
--

LOCK TABLES `attendance_sessions` WRITE;
/*!40000 ALTER TABLE `attendance_sessions` DISABLE KEYS */;
INSERT INTO `attendance_sessions` VALUES (1,2,'Fisika Wajib (Hukum Newton)',1,'2026-09-20','07:30:00','09:00:00','SMA1LE_QR_xxIZb525SvdPeNouo8TgTmSp','2026-09-20 08:38:36','ACTIVE','2026-09-19 03:17:54','2026-09-20 07:53:36');
/*!40000 ALTER TABLE `attendance_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `nis` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nisn` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `time_out` time DEFAULT NULL,
  `method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SELFIE',
  `out_method` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selfie_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `out_selfie_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `out_latitude` decimal(10,8) DEFAULT NULL,
  `out_longitude` decimal(11,8) DEFAULT NULL,
  `gps_accuracy` decimal(8,2) DEFAULT NULL,
  `device_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `out_device_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'HADIR',
  `is_early_leave` tinyint(1) NOT NULL DEFAULT '0',
  `early_leave_time` time DEFAULT NULL,
  `early_leave_reason` text COLLATE utf8mb4_unicode_ci,
  `verification_status` enum('VALID','PERLU_VERIFIKASI','DIVERIFIKASI','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VALID',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_class_id_foreign` (`class_id`),
  KEY `attendances_session_id_foreign` (`session_id`),
  KEY `attendances_student_id_date_index` (`student_id`,`date`),
  KEY `attendances_date_status_index` (`date`,`status`),
  KEY `attendances_approved_by_foreign` (`approved_by`),
  CONSTRAINT `attendances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `school_classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `attendance_sessions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
INSERT INTO `attendances` VALUES (1,1,'202411001','0089234811','Ahmad Rifai',2,'2026-09-15','06:45:00',NULL,'SELFIE',NULL,'attendance/sample_selfie.jpg',NULL,-7.56845200,112.04612400,NULL,NULL,8.50,'Mozilla/5.0 (Linux; Android 14; Mobile)',NULL,'192.168.1.150','HADIR',0,NULL,NULL,'VALID','Presensi berhasil dan tepat waktu',NULL,NULL,NULL,NULL,NULL,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,1,'202411001','0089234811','Ahmad Rifai',2,'2026-09-16','06:45:00',NULL,'SELFIE',NULL,'attendance/sample_selfie.jpg',NULL,-7.56845200,112.04612400,NULL,NULL,8.50,'Mozilla/5.0 (Linux; Android 14; Mobile)',NULL,'192.168.1.150','HADIR',0,NULL,NULL,'VALID','Presensi berhasil dan tepat waktu',NULL,NULL,NULL,NULL,NULL,'2026-09-19 03:17:54','2026-09-20 07:53:36'),(3,1,'202411001','0089234811','Ahmad Rifai',2,'2026-09-17','06:45:00',NULL,'QR_CODE',NULL,NULL,NULL,-7.56845200,112.04612400,NULL,NULL,8.50,'Mozilla/5.0 (Linux; Android 14; Mobile)',NULL,'192.168.1.150','HADIR',0,NULL,NULL,'VALID','Presensi berhasil dan tepat waktu',NULL,NULL,NULL,NULL,NULL,'2026-09-19 03:17:54','2026-09-20 07:53:36'),(4,1,'202411001','0089234811','Ahmad Rifai',2,'2026-09-18','06:45:00',NULL,'RFID',NULL,NULL,NULL,-7.56845200,112.04612400,NULL,NULL,8.50,'Mozilla/5.0 (Linux; Android 14; Mobile)',NULL,'192.168.1.150','HADIR',0,NULL,NULL,'VALID','Presensi berhasil dan tepat waktu',NULL,NULL,NULL,NULL,NULL,'2026-09-19 03:17:54','2026-09-20 07:53:36');
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auditable_id` bigint unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_action_created_at_index` (`action`,`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 03:23:37','2026-09-19 03:23:37'),(2,9,'QUICK_ROLE_SWITCH','User',9,NULL,'{\"switched_to\": \"orang_tua\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 03:23:44','2026-09-19 03:23:44'),(3,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 03:24:24','2026-09-19 03:24:24'),(4,1,'LOGOUT','User',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 03:40:00','2026-09-19 03:40:00'),(5,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 03:42:19','2026-09-19 03:42:19'),(6,1,'LOGOUT','User',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 03:58:59','2026-09-19 03:58:59'),(7,1,'LOGIN','User',1,NULL,'{\"role\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 04:15:23','2026-09-19 04:15:23'),(8,9,'QUICK_ROLE_SWITCH','User',9,NULL,'{\"switched_to\": \"orang_tua\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 04:22:52','2026-09-19 04:22:52'),(9,7,'QUICK_ROLE_SWITCH','User',7,NULL,'{\"switched_to\": \"siswa\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 04:23:16','2026-09-19 04:23:16'),(10,3,'QUICK_ROLE_SWITCH','User',3,NULL,'{\"switched_to\": \"guru\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 04:26:40','2026-09-19 04:26:40'),(11,6,'QUICK_ROLE_SWITCH','User',6,NULL,'{\"switched_to\": \"kesiswaan\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 04:27:20','2026-09-19 04:27:20'),(12,2,'QUICK_ROLE_SWITCH','User',2,NULL,'{\"switched_to\": \"admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 04:28:45','2026-09-19 04:28:45'),(13,9,'QUICK_ROLE_SWITCH','User',9,NULL,'{\"switched_to\": \"orang_tua\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 04:31:22','2026-09-19 04:31:22'),(14,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 04:38:20','2026-09-19 04:38:20'),(15,7,'QUICK_ROLE_SWITCH','User',7,NULL,'{\"switched_to\": \"siswa\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 05:17:41','2026-09-19 05:17:41'),(16,9,'QUICK_ROLE_SWITCH','User',9,NULL,'{\"switched_to\": \"orang_tua\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 05:19:00','2026-09-19 05:19:00'),(17,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 05:20:11','2026-09-19 05:20:11'),(18,9,'QUICK_ROLE_SWITCH','User',9,NULL,'{\"switched_to\": \"orang_tua\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 05:27:03','2026-09-19 05:27:03'),(19,2,'QUICK_ROLE_SWITCH','User',2,NULL,'{\"switched_to\": \"admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 05:33:16','2026-09-19 05:33:16'),(20,6,'QUICK_ROLE_SWITCH','User',6,NULL,'{\"switched_to\": \"kesiswaan\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 05:34:31','2026-09-19 05:34:31'),(21,2,'QUICK_ROLE_SWITCH','User',2,NULL,'{\"switched_to\": \"admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 05:35:05','2026-09-19 05:35:05'),(22,2,'UPDATE_APP_NAME','SchoolSetting',NULL,NULL,'{\"app_name\": \"SIS-SMA1LE\", \"app_tagline\": \"Sistem Informasi Siswa Terpadu\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 06:48:09','2026-09-19 06:48:09'),(23,2,'UPDATE_SCHOOL_LOGO','SchoolSetting',NULL,NULL,'{\"path\": \"branding/logo_1789825719.png\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 06:48:39','2026-09-19 06:48:39'),(24,2,'UPDATE_SCHOOL_LOGO','SchoolSetting',NULL,NULL,'{\"path\": \"branding/logo_1789825844.png\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 06:50:44','2026-09-19 06:50:44'),(25,2,'DELETE_SCHOOL_LOGO','SchoolSetting',NULL,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 06:50:57','2026-09-19 06:50:57'),(26,2,'UPDATE_SCHOOL_PROFILE','SchoolSetting',NULL,NULL,'{\"school_nss\": \"301020613045\", \"school_name\": \"SMA Negeri 1 Lengkong\", \"school_npsn\": \"20202265\", \"school_email\": \"sman1lengkong@gmail.com\", \"school_phone\": \"-\", \"headmaster_nip\": \"198009132008011005\", \"school_address\": \"Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat\", \"school_website\": \"https://sman1lengkong.school\", \"headmaster_name\": \"Pian Sopian, S.Pd\", \"school_accreditation\": \"A (Unggul)\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 06:52:54','2026-09-19 06:52:54'),(27,2,'UPDATE_SCHOOL_PROFILE','SchoolSetting',NULL,NULL,'{\"school_nss\": \"301020613045\", \"school_name\": \"SMA Negeri 1 Lengkong\", \"school_npsn\": \"20202265\", \"school_email\": \"sman1lengkong@gmail.com\", \"school_phone\": \"-\", \"headmaster_nip\": \"198009132008011005\", \"school_address\": \"Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat\", \"school_website\": \"https://sman1lengkong.school\", \"headmaster_name\": \"Pian Sopian, S.Pd\", \"school_accreditation\": \"A (Unggul)\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 06:52:55','2026-09-19 06:52:55'),(28,2,'UPDATE_SCHOOL_PROFILE','SchoolSetting',NULL,NULL,'{\"school_nss\": \"301020613045\", \"school_name\": \"SMA Negeri 1 Lengkong\", \"school_npsn\": \"20202265\", \"school_email\": \"sman1lengkong@gmail.com\", \"school_phone\": \"-\", \"headmaster_nip\": \"198009132008011005\", \"school_address\": \"Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat\", \"school_website\": \"https://sman1lengkong.school\", \"headmaster_name\": \"Pian Sopian, S.Pd\", \"school_accreditation\": \"A (Unggul)\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 06:53:51','2026-09-19 06:53:51'),(29,2,'UPDATE_SCHOOL_LOGO','SchoolSetting',NULL,NULL,'{\"path\": \"branding/logo_1789826167.png\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 06:56:07','2026-09-19 06:56:07'),(30,2,'UPDATE_SCHOOL_LOGO','SchoolSetting',NULL,NULL,'{\"path\": \"branding/logo_1789826358.png\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 06:59:18','2026-09-19 06:59:18'),(31,2,'UPDATE_SCHOOL_LOGO','SchoolSetting',NULL,NULL,'{\"path\": \"branding/logo_1789826392.png\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 06:59:52','2026-09-19 06:59:52'),(32,2,'LOGOUT','User',2,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 07:06:35','2026-09-19 07:06:35'),(33,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 07:08:38','2026-09-19 07:08:38'),(34,1,'LOGOUT','User',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 07:31:46','2026-09-19 07:31:46'),(35,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 07:31:52','2026-09-19 07:31:52'),(36,1,'UPDATE_SCHOOL_LOGO','SchoolSetting',NULL,NULL,'{\"path\": \"branding/logo_1789828355.png\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 07:32:35','2026-09-19 07:32:35'),(37,1,'LOGOUT','User',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:08:36','2026-09-19 08:08:36'),(38,1,'LOGIN','User',1,NULL,'{\"role\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:09:09','2026-09-19 08:09:09'),(39,3,'LOGIN','User',3,NULL,'{\"role\": \"guru\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:14:26','2026-09-19 08:14:26'),(40,2,'QUICK_ROLE_SWITCH','User',2,NULL,'{\"switched_to\": \"admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:16:34','2026-09-19 08:16:34'),(41,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:16:59','2026-09-19 08:16:59'),(42,3,'QUICK_ROLE_SWITCH','User',3,NULL,'{\"switched_to\": \"guru\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:17:04','2026-09-19 08:17:04'),(43,4,'QUICK_ROLE_SWITCH','User',4,NULL,'{\"switched_to\": \"wali_kelas\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:17:21','2026-09-19 08:17:21'),(44,5,'QUICK_ROLE_SWITCH','User',5,NULL,'{\"switched_to\": \"bk\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:18:54','2026-09-19 08:18:54'),(45,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:19:56','2026-09-19 08:19:56'),(46,1,'UPDATE_STUDENT','Student',3,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:20:20','2026-09-19 08:20:20'),(47,4,'QUICK_ROLE_SWITCH','User',4,NULL,'{\"switched_to\": \"wali_kelas\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:21:27','2026-09-19 08:21:27'),(48,7,'QUICK_ROLE_SWITCH','User',7,NULL,'{\"switched_to\": \"siswa\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:22:59','2026-09-19 08:22:59'),(49,9,'QUICK_ROLE_SWITCH','User',9,NULL,'{\"switched_to\": \"orang_tua\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:23:01','2026-09-19 08:23:01'),(50,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:23:37','2026-09-19 08:23:37'),(51,4,'QUICK_ROLE_SWITCH','User',4,NULL,'{\"switched_to\": \"wali_kelas\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:25:29','2026-09-19 08:25:29'),(52,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:26:07','2026-09-19 08:26:07'),(53,2,'QUICK_ROLE_SWITCH','User',2,NULL,'{\"switched_to\": \"admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:28:55','2026-09-19 08:28:55'),(54,2,'UPDATE_PROFILE','User',2,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:49:35','2026-09-19 08:49:35'),(55,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:49:41','2026-09-19 08:49:41'),(56,1,'LOGOUT','User',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:49:48','2026-09-19 08:49:48'),(57,1,'LOGIN','User',1,NULL,'{\"role\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:49:53','2026-09-19 08:49:53'),(58,2,'QUICK_ROLE_SWITCH','User',2,NULL,'{\"switched_to\": \"admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:50:05','2026-09-19 08:50:05'),(59,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:50:18','2026-09-19 08:50:18'),(60,2,'QUICK_ROLE_SWITCH','User',2,NULL,'{\"switched_to\": \"admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:50:38','2026-09-19 08:50:38'),(61,2,'UPDATE_PROFILE','User',2,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:51:01','2026-09-19 08:51:01'),(62,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:51:07','2026-09-19 08:51:07'),(63,1,'UPDATE_PROFILE','User',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:52:04','2026-09-19 08:52:04'),(64,1,'LOGOUT','User',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:52:24','2026-09-19 08:52:24'),(65,1,'LOGIN','User',1,NULL,'{\"role\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:53:10','2026-09-19 08:53:10'),(66,2,'QUICK_ROLE_SWITCH','User',2,NULL,'{\"switched_to\": \"admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:57:48','2026-09-19 08:57:48'),(67,3,'QUICK_ROLE_SWITCH','User',3,NULL,'{\"switched_to\": \"guru\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:57:55','2026-09-19 08:57:55'),(68,4,'QUICK_ROLE_SWITCH','User',4,NULL,'{\"switched_to\": \"wali_kelas\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:58:03','2026-09-19 08:58:03'),(69,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-19 08:58:35','2026-09-19 08:58:35'),(70,11,'LOGIN','User',11,NULL,'{\"role\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 03:56:56','2026-09-20 03:56:56'),(71,9,'QUICK_ROLE_SWITCH','User',9,NULL,'{\"switched_to\": \"orang_tua\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 03:57:15','2026-09-20 03:57:15'),(72,7,'QUICK_ROLE_SWITCH','User',7,NULL,'{\"switched_to\": \"siswa\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 03:58:07','2026-09-20 03:58:07'),(73,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 03:59:05','2026-09-20 03:59:05'),(74,9,'QUICK_ROLE_SWITCH','User',9,NULL,'{\"switched_to\": \"orang_tua\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 04:08:48','2026-09-20 04:08:48'),(75,1,'QUICK_ROLE_SWITCH','User',1,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 04:09:23','2026-09-20 04:09:23'),(76,3,'QUICK_ROLE_SWITCH','User',3,NULL,'{\"switched_to\": \"guru\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 04:09:29','2026-09-20 04:09:29'),(77,11,'QUICK_ROLE_SWITCH','User',11,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 05:17:47','2026-09-20 05:17:47'),(78,3,'QUICK_ROLE_SWITCH','User',3,NULL,'{\"switched_to\": \"guru\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 05:17:58','2026-09-20 05:17:58'),(79,11,'QUICK_ROLE_SWITCH','User',11,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 05:18:04','2026-09-20 05:18:04'),(80,3,'QUICK_ROLE_SWITCH','User',3,NULL,'{\"switched_to\": \"guru\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 05:20:12','2026-09-20 05:20:12'),(81,11,'QUICK_ROLE_SWITCH','User',11,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 05:24:47','2026-09-20 05:24:47'),(82,4,'QUICK_ROLE_SWITCH','User',4,NULL,'{\"switched_to\": \"wali_kelas\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 05:24:53','2026-09-20 05:24:53'),(83,11,'QUICK_ROLE_SWITCH','User',11,NULL,'{\"switched_to\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 05:25:08','2026-09-20 05:25:08'),(84,11,'LOGOUT','User',11,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 05:25:54','2026-09-20 05:25:54'),(85,11,'LOGIN','User',11,NULL,'{\"role\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 05:26:10','2026-09-20 05:26:10'),(86,11,'LOGIN','User',11,NULL,'{\"role\": \"super_admin\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-20 07:55:18','2026-09-20 07:55:18');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guidance_records`
--

DROP TABLE IF EXISTS `guidance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guidance_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `counselor_id` bigint unsigned DEFAULT NULL,
  `violation_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `follow_up_type` enum('BIMBINGAN_WALI_KELAS','SP1_BK','SP2_BK','SP3_BK_KESISWAAN','RAPAT_KHUSUS','KONSELING_RUTIN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BIMBINGAN_WALI_KELAS',
  `agreement_letter_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recommendation` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('DALAM_PROSES','SELESAI','PEMANTAUAN_LANJUTAN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DALAM_PROSES',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guidance_records_student_id_foreign` (`student_id`),
  KEY `guidance_records_counselor_id_foreign` (`counselor_id`),
  KEY `guidance_records_violation_id_foreign` (`violation_id`),
  CONSTRAINT `guidance_records_counselor_id_foreign` FOREIGN KEY (`counselor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `guidance_records_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guidance_records_violation_id_foreign` FOREIGN KEY (`violation_id`) REFERENCES `violations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guidance_records`
--

LOCK TABLES `guidance_records` WRITE;
/*!40000 ALTER TABLE `guidance_records` DISABLE KEYS */;
INSERT INTO `guidance_records` VALUES (1,3,5,NULL,'2026-09-19','BIMBINGAN_WALI_KELAS',NULL,'Panggilan konseling wali kelas & bimbingan personal mengenai kedisiplinan dan pergaulan.','Siswa berjanji tidak mengulangi dan bersedia membuat komitmen tertulis bersama orang tua.','DALAM_PROSES','2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,3,5,NULL,'2026-09-20','BIMBINGAN_WALI_KELAS',NULL,'Panggilan konseling wali kelas & bimbingan personal mengenai kedisiplinan dan pergaulan.','Siswa berjanji tidak mengulangi dan bersedia membuat komitmen tertulis bersama orang tua.','DALAM_PROSES','2026-09-20 07:53:36','2026-09-20 07:53:36');
/*!40000 ALTER TABLE `guidance_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habit_daily_logs`
--

DROP TABLE IF EXISTS `habit_daily_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habit_daily_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `habit_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `check_time` time DEFAULT NULL,
  `activity_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `reflection` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `habit_daily_logs_student_id_habit_id_date_unique` (`student_id`,`habit_id`,`date`),
  KEY `habit_daily_logs_habit_id_foreign` (`habit_id`),
  KEY `habit_daily_logs_student_id_date_index` (`student_id`,`date`),
  CONSTRAINT `habit_daily_logs_habit_id_foreign` FOREIGN KEY (`habit_id`) REFERENCES `habits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `habit_daily_logs_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habit_daily_logs`
--

LOCK TABLES `habit_daily_logs` WRITE;
/*!40000 ALTER TABLE `habit_daily_logs` DISABLE KEYS */;
INSERT INTO `habit_daily_logs` VALUES (1,1,1,'2026-09-19',1,'06:15:00','Bangun pukul 04:30 WIB dan merapikan kamar','Terlaksana dengan lancar','Badan terasa lebih segar dan fokus belajar meningkat.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,1,2,'2026-09-19',1,'06:15:00','Sholat Subuh berjamaah di masjid terdekat','Terlaksana dengan lancar','Badan terasa lebih segar dan fokus belajar meningkat.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(3,1,3,'2026-09-19',1,'06:15:00','Jogging pagi 20 menit keliling desa','Terlaksana dengan lancar','Badan terasa lebih segar dan fokus belajar meningkat.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(4,1,4,'2026-09-19',1,'06:15:00','Sarapan nasi pecel sayur dan telur rebus','Terlaksana dengan lancar','Badan terasa lebih segar dan fokus belajar meningkat.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(5,1,5,'2026-09-19',1,'06:15:00','Membaca modul fisika persiapan KBM','Terlaksana dengan lancar','Badan terasa lebih segar dan fokus belajar meningkat.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(6,1,6,'2026-09-19',0,NULL,NULL,'Akan dilaksanakan sore/malam nanti',NULL,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(7,1,7,'2026-09-19',0,NULL,NULL,'Akan dilaksanakan sore/malam nanti',NULL,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(8,1,1,'2026-09-20',1,'06:15:00','Bangun pukul 04:30 WIB dan merapikan kamar','Terlaksana dengan lancar','Badan terasa lebih segar dan fokus belajar meningkat.','2026-09-20 07:53:36','2026-09-20 07:53:36'),(9,1,2,'2026-09-20',1,'06:15:00','Sholat Subuh berjamaah di masjid terdekat','Terlaksana dengan lancar','Badan terasa lebih segar dan fokus belajar meningkat.','2026-09-20 07:53:36','2026-09-20 07:53:36'),(10,1,3,'2026-09-20',1,'06:15:00','Jogging pagi 20 menit keliling desa','Terlaksana dengan lancar','Badan terasa lebih segar dan fokus belajar meningkat.','2026-09-20 07:53:36','2026-09-20 07:53:36'),(11,1,4,'2026-09-20',1,'06:15:00','Sarapan nasi pecel sayur dan telur rebus','Terlaksana dengan lancar','Badan terasa lebih segar dan fokus belajar meningkat.','2026-09-20 07:53:36','2026-09-20 07:53:36'),(12,1,5,'2026-09-20',1,'06:15:00','Membaca modul fisika persiapan KBM','Terlaksana dengan lancar','Badan terasa lebih segar dan fokus belajar meningkat.','2026-09-20 07:53:36','2026-09-20 07:53:36'),(13,1,6,'2026-09-20',0,NULL,NULL,'Akan dilaksanakan sore/malam nanti',NULL,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(14,1,7,'2026-09-20',0,NULL,NULL,'Akan dilaksanakan sore/malam nanti',NULL,'2026-09-20 07:53:36','2026-09-20 07:53:36');
/*!40000 ALTER TABLE `habit_daily_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habit_notes`
--

DROP TABLE IF EXISTS `habit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habit_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `author_id` bigint unsigned NOT NULL,
  `author_role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `habit_notes_student_id_foreign` (`student_id`),
  KEY `habit_notes_author_id_foreign` (`author_id`),
  CONSTRAINT `habit_notes_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `habit_notes_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habit_notes`
--

LOCK TABLES `habit_notes` WRITE;
/*!40000 ALTER TABLE `habit_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `habit_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habit_summaries`
--

DROP TABLE IF EXISTS `habit_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habit_summaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `period_type` enum('DAILY','WEEKLY','MONTHLY','SEMESTER') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WEEKLY',
  `period_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed_count` int unsigned NOT NULL DEFAULT '0',
  `total_habits` int unsigned NOT NULL DEFAULT '7',
  `score_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('KONSISTEN','BERKEMBANG','PERLU_PEMBIASAAN','BELUM_TERPANTAU') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BELUM_TERPANTAU',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `habit_summaries_student_id_period_type_period_key_unique` (`student_id`,`period_type`,`period_key`),
  CONSTRAINT `habit_summaries_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habit_summaries`
--

LOCK TABLES `habit_summaries` WRITE;
/*!40000 ALTER TABLE `habit_summaries` DISABLE KEYS */;
INSERT INTO `habit_summaries` VALUES (1,1,'WEEKLY','2026-W38',32,35,91.40,'KONSISTEN','2026-09-19 03:17:54','2026-09-19 03:17:54');
/*!40000 ALTER TABLE `habit_summaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habits`
--

DROP TABLE IF EXISTS `habits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` tinyint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tagline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `target_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_activity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reflection_prompt` text COLLATE utf8mb4_unicode_ci,
  `is_time_restricted` tinyint(1) NOT NULL DEFAULT '1',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `habits_order_number_unique` (`order_number`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habits`
--

LOCK TABLES `habits` WRITE;
/*!40000 ALTER TABLE `habits` DISABLE KEYS */;
INSERT INTO `habits` VALUES (1,1,'Bangun Pagi','Awali hari dengan kesegaran dan kesiapan diri','Bangun sebelum subuh/fajar, merapikan tempat tidur, dan menghirup udara segar untuk menyongsong hari.','04:30 - 05:30','04:30','05:30','Bangun sebelum subuh, berdoa, dan merapikan tempat tidur','Apa yang Anda rasakan setelah berhasil bangun pagi tepat waktu hari ini?',1,'sun','2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,2,'Beribadah','Tingkatkan keimanan dan ketakwaan','Menjalankan sholat/doa wajib dan amalan ibadah sesuai dengan agama serta keyakinan masing-masing.','05:00 - 06:00','05:00','06:00','Sholat Subuh berjamaah / Berdoa pagi sesuai agama masing-masing','Hikmah atau doa kebaikan apa yang Anda panjatkan dalam ibadah pagi ini?',1,'sparkles','2026-09-19 03:17:54','2026-09-19 03:17:54'),(3,3,'Berolahraga','Tubuh bugar, pikiran cemerlang','Melakukan peregangan, senam, jalan pagi, lari ringan, atau aktivitas fisik minimal 15-30 menit.','05:30 - 06:15','05:30','06:15','Peregangan tubuh, jalan pagi, lari santai, atau senam ringan','Bagaimana kebugaran fisik dan kesiapan Anda menyongsong aktivitas sekolah hari ini?',1,'heart-pulse','2026-09-19 03:17:54','2026-09-19 03:17:54'),(4,4,'Makan Sehat dan Bergizi','Nutrisi seimbang untuk tumbuh kembang optimal','Sarapan pagi dengan menu bergizi seimbang (4 sehat 5 sempurna), minum air putih cukup, dan hindari jajanan berbahaya.','06:30 - 07:00','06:30','07:00','Sarapan pagi menu gizi seimbang dan minum air putih cukup','Menu sarapan sehat apa yang Anda konsumsi untuk energi belajar hari ini?',1,'apple','2026-09-19 03:17:54','2026-09-19 03:17:54'),(5,5,'Gemar Belajar','Rasa ingin tahu dan literasi tiada henti','Membaca buku non-pelajaran, mengulang materi KBM, mengerjakan tugas tepat waktu, atau mempelajari keterampilan baru.','19:00 - 20:30','19:00','20:30','Mengulang materi pelajaran KBM, mengerjakan tugas, atau membaca buku','Wawasan atau keterampilan baru apa yang paling berkesan Anda pelajari hari ini?',1,'book-open','2026-09-19 03:17:54','2026-09-19 03:17:54'),(6,6,'Bermasyarakat','Peduli sesama, santun dalam kebersamaan','Membantu pekerjaan orang tua di rumah, bertegur sapa santun dengan tetangga, atau aktif dalam kegiatan sosial lingkungan.','16:00 - 17:30','16:00','17:30','Membantu pekerjaan orang tua di rumah atau berinteraksi santun di lingkungan','Kebaikan atau kepedulian apa yang telah Anda bagikan kepada orang di sekitar hari ini?',1,'users','2026-09-19 03:17:54','2026-09-19 03:17:54'),(7,7,'Tidur Cepat','Istirahat berkualitas untuk regenerasi tubuh','Tidur malam maksimal pukul 21.30 atau 22.00, mematikan layar gawai 30 menit sebelum tidur demi istirahat berkualitas.','21:30 - 22:00','21:30','23:59','Mematikan gawai/layar 30 menit sebelum tidur dan istirahat berkualitas','Apakah Anda siap beristirahat dengan tenang demi pemulihan tubuh untuk hari esok?',1,'moon','2026-09-19 03:17:54','2026-09-19 03:17:54');
/*!40000 ALTER TABLE `habits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_17_000001_create_school_masters_tables',1),(5,'2026_09_17_000002_create_attendance_tables',1),(6,'2026_09_17_000003_create_achievements_and_violations_tables',1),(7,'2026_09_17_000004_create_habits_notifications_and_audit_tables',1),(8,'2026_09_17_000005_add_leaves_and_manual_fields_to_attendances_table',1),(9,'2026_09_19_000001_add_time_and_reflection_to_habits_table',2),(10,'2026_09_19_000002_add_checkout_fields_to_attendances_table',3),(11,'2026_09_20_000001_add_early_leave_fields_to_attendances_table',4),(12,'2026_09_20_000002_create_school_holidays_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('ATTENDANCE','HABIT','ACHIEVEMENT','VIOLATION','GUIDANCE','SYSTEM') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SYSTEM',
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,7,'Prestasi OSN Terverifikasi!','Selamat! Prestasi Juara 1 OSN Fisika Kabupaten telah diverifikasi oleh Wali Kelas (+75 Poin).','ACHIEVEMENT','/achievements',0,NULL,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,7,'Pengingat Jurnal 7 Kebiasaan','Jangan lupa mengisi jurnal 7 Kebiasaan Anak Indonesia Hebat untuk aktivitas malam hari ini.','HABIT','/habits',0,NULL,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(3,9,'Anak Anda Telah Hadir di Sekolah','Ahmad Rifai telah melakukan presensi di SMA Negeri 1 Lengkong pada pukul 06:45 WIB.','ATTENDANCE','/parent/dashboard',0,NULL,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(4,5,'Pemberitahuan Ambang Pembinaan Siswa','Siswa Budi Santoso (XI-IPA-1) telah mencapai total 80 poin pelanggaran. Diperlukan tindakan: Bimbingan Wali Kelas & BK.','GUIDANCE','/counselor/dashboard',0,NULL,'2026-09-19 03:17:54','2026-09-19 03:17:54');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parents`
--

DROP TABLE IF EXISTS `parents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `student_id` bigint unsigned NOT NULL,
  `relation_type` enum('AYAH','IBU','WALI') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WALI',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parents_user_id_foreign` (`user_id`),
  KEY `parents_student_id_foreign` (`student_id`),
  CONSTRAINT `parents_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `parents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parents`
--

LOCK TABLES `parents` WRITE;
/*!40000 ALTER TABLE `parents` DISABLE KEYS */;
INSERT INTO `parents` VALUES (1,9,1,'AYAH','Joko Susanto','081234567897','Desa Lengkong RT 02 RW 01, Nganjuk','Wiraswasta','2026-09-19 03:17:54','2026-09-19 03:17:54');
/*!40000 ALTER TABLE `parents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_classes`
--

DROP TABLE IF EXISTS `school_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `major` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Umum',
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `homeroom_teacher_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_classes_academic_year_id_foreign` (`academic_year_id`),
  KEY `school_classes_homeroom_teacher_id_foreign` (`homeroom_teacher_id`),
  CONSTRAINT `school_classes_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `school_classes_homeroom_teacher_id_foreign` FOREIGN KEY (`homeroom_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_classes`
--

LOCK TABLES `school_classes` WRITE;
/*!40000 ALTER TABLE `school_classes` DISABLE KEYS */;
INSERT INTO `school_classes` VALUES (1,'X-1','X','Umum',1,1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,'XI-IPA-1','XI','IPA',1,2,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(3,'XII-IPA-1','XII','IPA',1,4,'2026-09-19 03:17:54','2026-09-19 03:17:54');
/*!40000 ALTER TABLE `school_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_holidays`
--

DROP TABLE IF EXISTS `school_holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_holidays` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `type` enum('nasional','khusus','sekolah') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nasional',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_holidays_start_date_index` (`start_date`),
  KEY `school_holidays_end_date_index` (`end_date`),
  KEY `school_holidays_type_index` (`type`),
  KEY `school_holidays_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_holidays`
--

LOCK TABLES `school_holidays` WRITE;
/*!40000 ALTER TABLE `school_holidays` DISABLE KEYS */;
INSERT INTO `school_holidays` VALUES (1,'Tahun Baru Masehi 2026','2026-01-01','2026-01-01','nasional','Libur Nasional Tahun Baru Masehi',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(2,'Isra Mi\'raj Nabi Muhammad SAW','2026-01-17','2026-01-17','nasional','Peringatan Isra Mi\'raj 1447 H',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(3,'Tahun Baru Imlek 2577','2026-02-17','2026-02-17','nasional','Tahun Baru Imlek 2577 Kongzili',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(4,'Hari Suci Nyepi','2026-03-19','2026-03-19','nasional','Hari Suci Nyepi Tahun Baru Saka 1948',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(5,'Hari Raya Idul Fitri 1447 H','2026-03-20','2026-03-24','nasional','Hari Raya Idul Fitri & Cuti Bersama',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(6,'Wafat Yesus Kristus','2026-04-03','2026-04-03','nasional','Wafat Isa Al Masih',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(7,'Hari Buruh Internasional','2026-05-01','2026-05-01','nasional','Hari Buruh Internasional',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(8,'Kenaikan Yesus Kristus','2026-05-14','2026-05-14','nasional','Kenaikan Isa Al Masih',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(9,'Hari Raya Idul Adha 1447 H','2026-05-27','2026-05-28','nasional','Hari Raya Idul Adha & Cuti Bersama',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(10,'Hari Raya Waisak 2570 BE','2026-05-31','2026-05-31','nasional','Hari Raya Waisak',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(11,'Hari Lahir Pancasila','2026-06-01','2026-06-01','nasional','Hari Lahir Pancasila',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(12,'Tahun Baru Islam 1448 H','2026-06-16','2026-06-16','nasional','Tahun Baru Hijriah 1 Muharram 1448 H',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(13,'Libur Kenaikan Kelas / Akhir Semester Genap','2026-06-22','2026-07-11','sekolah','Libur Kalender Pendidikan Semester 2',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(14,'Hari Kemerdekaan RI Ke-81','2026-08-17','2026-08-17','nasional','HUT Proklamasi Kemerdekaan RI',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(15,'Maulid Nabi Muhammad SAW','2026-08-25','2026-08-25','nasional','Peringatan Maulid Nabi Muhammad SAW',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(16,'Hari Raya Natal','2026-12-25','2026-12-26','nasional','Hari Raya Natal & Cuti Bersama',1,'2026-09-20 07:53:36','2026-09-20 07:53:36'),(17,'Libur Semester Ganjil','2026-12-21','2026-12-31','sekolah','Libur Semester 1 Kalender Pendidikan',1,'2026-09-20 07:53:36','2026-09-20 07:53:36');
/*!40000 ALTER TABLE `school_holidays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_settings`
--

DROP TABLE IF EXISTS `school_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_editable` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_settings`
--

LOCK TABLES `school_settings` WRITE;
/*!40000 ALTER TABLE `school_settings` DISABLE KEYS */;
INSERT INTO `school_settings` VALUES (1,'school_name','SMA Negeri 1 Lengkong','general','Nama Resmi Sekolah',1,'2026-09-19 03:17:53','2026-09-19 16:21:42'),(2,'school_npsn','20539123','general','Nomor Pokok Sekolah Nasional (NPSN)',1,'2026-09-19 03:17:53','2026-09-19 16:21:42'),(3,'school_nss','301051408001','general','Nomor Statistik Sekolah (NSS)',1,'2026-09-19 03:17:53','2026-09-19 16:21:42'),(4,'school_accreditation','A (Unggul)','general','Akreditasi Sekolah',1,'2026-09-19 03:17:53','2026-09-19 16:21:42'),(5,'headmaster_name','Drs. H. Ahmad Sudrajat, M.Pd.','general','Nama Kepala Sekolah',1,'2026-09-19 03:17:53','2026-09-20 07:53:32'),(6,'headmaster_nip','196803151994031004','general','NIP Kepala Sekolah',1,'2026-09-19 03:17:53','2026-09-20 07:53:32'),(7,'school_address','Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat','general','Alamat Sekolah',1,'2026-09-19 03:17:53','2026-09-19 16:21:42'),(8,'school_phone','(0266) 123456','general','Nomor Telepon Sekolah',1,'2026-09-19 03:17:53','2026-09-19 16:21:42'),(9,'school_email','sman1lengkong@gmail.com','general','Email Resmi Sekolah',1,'2026-09-19 03:17:53','2026-09-19 16:21:42'),(10,'school_website','https://sman1lengkong.sch.id','general','Situs Resmi Sekolah',1,'2026-09-19 03:17:53','2026-09-19 16:21:42'),(11,'enable_attendance_selfie','1','attendance_methods','Aktifkan Presensi Selfie Realtime + GPS Geofence',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(12,'enable_attendance_qr','1','attendance_methods','Aktifkan Presensi QR Code Dinamis',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(13,'enable_attendance_rfid','1','attendance_methods','Aktifkan Presensi Kartu RFID / Kiosk',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(14,'school_lat','-7.56845000','attendance','Garis Lintang Sekolah (Latitude)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(15,'school_lng','112.04612000','attendance','Garis Bujur Sekolah (Longitude)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(16,'geofence_radius_meters','100','attendance','Radius Maksimal Presensi Selfie (Meter)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(17,'attendance_start_time','06:00','attendance','Jam Mulai Buka Presensi',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(18,'attendance_checkin_time','07:00','attendance','Batas Jam Masuk (Tepat Waktu)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(19,'attendance_late_cutoff_time','07:30','attendance','Batas Akhir Toleransi Terlambat',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(20,'attendance_end_time','16:00','attendance','Jam Tutup Presensi',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(21,'reward_tier_1_min','125','rewards','Poin Minimal Sertifikat Siswa Berprestasi (125-175)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(22,'reward_tier_2_min','176','rewards','Poin Minimal Sertifikat dan Hadiah (176-199)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(23,'reward_tier_3_min','200','rewards','Poin Anugerah Waluya Utama (>=200)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(24,'guidance_tier_1_min','75','guidance','Poin Bimbingan Wali Kelas (75-124)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(25,'guidance_tier_2_min','125','guidance','Poin SP 1 - BK (125-175)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(26,'guidance_tier_3_min','176','guidance','Poin SP 2 - BK (176-199)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(27,'guidance_tier_4_min','200','guidance','Poin SP 3 - BK & Kesiswaan (200)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(28,'guidance_tier_5_min','201','guidance','Poin Rapat Khusus Dewan Guru (>200)',1,'2026-09-19 03:17:53','2026-09-19 03:17:53'),(29,'school_branch','CABANG DINAS PENDIDIKAN WILAYAH V','general','Cabang Dinas Pendidikan Wilayah',1,'2026-09-19 05:34:02','2026-09-19 16:21:42'),(30,'school_city','Sukabumi','general','Kota/Kabupaten Administrasi',1,'2026-09-19 05:34:02','2026-09-19 16:21:42'),(31,'app_name','SIS-SMA1LE','branding','Nama resmi aplikasi',1,'2026-09-19 06:48:09','2026-09-19 06:48:09'),(32,'app_tagline','Sistem Informasi Siswa Terpadu','branding','Tagline / Deskripsi aplikasi',1,'2026-09-19 06:48:09','2026-09-19 06:48:09'),(34,'school_logo','branding/logo_1789828355.png','branding','Path logo resmi aplikasi',1,'2026-09-19 06:56:07','2026-09-19 07:32:35'),(35,'leave_request_cutoff_time','06:30','attendance','Batas Jam Pengajuan Izin/Sakit Hari H',1,'2026-09-20 07:53:32','2026-09-20 07:53:32');
/*!40000 ALTER TABLE `school_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('unoYOWUpkEqv1joMaOhfEMURLzKmHgQLIgkLA1hS',11,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJEN0ZHcENjdVhGTnBTbXlLd1V6U0c3cklCbmpUd0FFQW5ucjlYc0EwIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FjaGlldmVtZW50c1wvMiIsInJvdXRlIjoiYWNoaWV2ZW1lbnRzLnNob3cifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjExfQ==',1789882009),('WexhEk5cSbhY7afuyLdInSP8vUPzX5XUBbiMAPhw',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJyTU5vVEhQcnVVbmtVMllpNFI0VWx0azBUNVVLNHFic1FZb1VaUFEyIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2hhYml0cyJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3BvcnRhbC1vcnR1P25pc249MDA4OTIzNDgxMSIsInJvdXRlIjoicGFyZW50LnBvcnRhbCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1789877259),('xnO61nUzuDSOQnHZ1cbdbUXyX0y2zPOjj55caIey',11,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJyY1hRRDh3WTlHWmc5UjJETDYwZFlMbnFaeUl5ZnRMa0xuMTV5UXlOIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxMX0=',1789891528);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `nis` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nisn` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'L',
  `class_id` bigint unsigned DEFAULT NULL,
  `academic_year_id` bigint unsigned DEFAULT NULL,
  `rfid_uid` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_nis_unique` (`nis`),
  UNIQUE KEY `students_nisn_unique` (`nisn`),
  UNIQUE KEY `students_rfid_uid_unique` (`rfid_uid`),
  KEY `students_user_id_foreign` (`user_id`),
  KEY `students_class_id_foreign` (`class_id`),
  KEY `students_academic_year_id_foreign` (`academic_year_id`),
  CONSTRAINT `students_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `school_classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,7,'202411001','0089234811','Ahmad Rifai','L',2,1,'A4B5C6D7',NULL,'Nganjuk','2008-05-14','Desa Lengkong RT 02 RW 01, Nganjuk','081234567896',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,8,'202411002','0089234812','Dewi Maharani','P',2,1,'B2C3D4E5',NULL,'Nganjuk','2008-08-20','Desa Bangle, Lengkong, Nganjuk','081234567898',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(3,10,'202411003','0089234813','Budi Santoso','L',2,1,'C3D4E5F6',NULL,'Nganjuk','2008-01-11','Lengkong, Nganjuk','081234567899',1,'2026-09-19 03:17:54','2026-09-19 16:21:43');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'L',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teachers_nip_unique` (`nip`),
  KEY `teachers_user_id_foreign` (`user_id`),
  CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,3,'197804122005011003','Bambang Sugiantoro, S.Pd.','L','081234567892','Guru Fisika & Pembina KIR',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,4,'198205152008012004','Siti Rahmawati, M.Pd.','P','081234567893','Wali Kelas XI-IPA-1 / Guru Matematika',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(3,5,'197503102002122002','Dra. Nur Indah Pratiwi','P','081234567894','Guru BK (Bimbingan Konseling)',1,'2026-09-19 03:17:54','2026-09-20 07:53:35'),(4,6,'197208181998021001','Drs. Agus Hariyanto, M.M.','L','081234567895','Guru / Pembina Kesiswaan',1,'2026-09-19 03:17:54','2026-09-20 07:53:35'),(5,2,'198501012010011001','Admin Pengelola Sekolah','L','081234567891','Administrator Sekolah',1,'2026-09-20 07:53:33','2026-09-20 07:53:33'),(6,12,'196803151994031004','Drs. H. Ahmad Sudrajat, M.Pd.','L','081234567899','Kepala SMA Negeri 1 Lengkong',1,'2026-09-20 07:53:34','2026-09-20 07:53:34');
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'siswa',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Administrator SMAN 1 Lengkong','superadmin@sma1le.sch.id','superadmin','081234567890','super_admin',NULL,1,NULL,'$2y$12$dNXracJPtD9tl1MT.dKtuend9rakygF/QbsRe/1uZnFu1SlARc5/W','TleSQk0VzbYNTUZlZGBHWtGUIwIMxY4msAUY1jmeQ9zTcsmjar7eSgS7S44G','2026-09-19 03:17:54','2026-09-20 07:53:33'),(2,'Admin Pengelola Sekolah','admin@sma1le.sch.id','198501012010011001','081234567891','admin',NULL,1,NULL,'$2y$12$hnP2jvTEtF8ixz6aYQXHBudAtE7jIfy6fU6k9/dkfHLXRAMARdYh2',NULL,'2026-09-19 03:17:54','2026-09-20 07:53:33'),(3,'Bambang Sugiantoro, S.Pd.','guru@sma1le.sch.id','197804122005011003','081234567892','guru',NULL,1,NULL,'$2y$12$nRwKEGjN.EzGIeX3zN4euugGBtQz/JuWnybXnWpg1MvNQpKIW1bRK',NULL,'2026-09-19 03:17:54','2026-09-20 07:53:34'),(4,'Siti Rahmawati, M.Pd.','walikelas@sma1le.sch.id','198205152008012004','081234567893','wali_kelas',NULL,1,NULL,'$2y$12$/HVHYkf.RSo5F/X1A3oOIOk/0x6WtoQjIFl625MOe/XsC76BwtYyG',NULL,'2026-09-19 03:17:54','2026-09-20 07:53:34'),(5,'Dra. Nur Indah Pratiwi','bk@sma1le.sch.id','197503102002122002','081234567894','guru',NULL,1,NULL,'$2y$12$5uKfmTVzYtSQwp9Z1zUlQedzA6Q/F3Yme7BsAyb2DJfKrHotOARVa',NULL,'2026-09-19 03:17:54','2026-09-20 07:53:35'),(6,'Drs. Agus Hariyanto, M.M.','kesiswaan@sma1le.sch.id','197208181998021001','081234567895','guru',NULL,1,NULL,'$2y$12$GXgzKEzQekbEeGCemYfKduzXdL2gb65f5QICGcVUVzUkoGI3/bOEy',NULL,'2026-09-19 03:17:54','2026-09-20 07:53:35'),(7,'Ahmad Rifai','siswa@sma1le.sch.id','0089234811','081234567896','siswa',NULL,1,NULL,'$2y$12$VaVaYgo9kOL3KhZSgl0XMennidCl10I2hJV3lyns1ZQTvQNIpQdU6',NULL,'2026-09-19 03:17:54','2026-09-20 07:53:35'),(8,'Dewi Maharani','dewi@sma1le.sch.id','0089234812','081234567898','siswa',NULL,1,NULL,'$2y$12$1rbDPi6bwUXn9LJgOLiZs.ze7q5ck.WTa4V.c/cS4cV/AkPFvUIX.',NULL,'2026-09-19 03:17:54','2026-09-20 07:53:36'),(9,'Joko Susanto','orangtua@sma1le.sch.id','jokosusanto','081234567897','orang_tua',NULL,1,NULL,'$2y$12$dNXracJPtD9tl1MT.dKtuend9rakygF/QbsRe/1uZnFu1SlARc5/W',NULL,'2026-09-19 03:17:54','2026-09-20 07:53:36'),(10,'Budi Santoso','budi@sma1le.sch.id','budisantoso','081234567899','siswa',NULL,1,NULL,'$2y$12$dNXracJPtD9tl1MT.dKtuend9rakygF/QbsRe/1uZnFu1SlARc5/W',NULL,'2026-09-19 03:17:54','2026-09-20 07:53:36'),(11,'Rh Aseng','seng@sma1le.sch.id','SENKS','085887053005','super_admin',NULL,1,NULL,'$2y$12$JVhKZN5KXLRDFjrffi99QODHbeXjErSvX82qPfqev42qBxuw6i1bm',NULL,'2026-09-20 03:27:17','2026-09-20 07:53:33'),(12,'Drs. H. Ahmad Sudrajat, M.Pd.','kepsek@sma1le.sch.id','196803151994031004','081234567899','kepala_sekolah',NULL,1,NULL,'$2y$12$YFJtGpsg8JZCXfsqGJ/TCOOhiF95dr7vOkEbAiGtcriS91WYa/a.C',NULL,'2026-09-20 07:53:34','2026-09-20 07:53:34');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `violation_categories`
--

DROP TABLE IF EXISTS `violation_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `violation_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `violation_categories_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `violation_categories`
--

LOCK TABLES `violation_categories` WRITE;
/*!40000 ALTER TABLE `violation_categories` DISABLE KEYS */;
INSERT INTO `violation_categories` VALUES (1,'P1','Kehadiran','Pelanggaran terkait kedatangan, presensi, absensi tanpa keterangan, dan bolos sekolah.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,'P2','Sikap dan Perilaku','Pelanggaran etika sopan santun terhadap bapak/ibu guru, staf, maupun sesama siswa.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(3,'P3','Seragam','Pelanggaran ketentuan penggunaan pakaian seragam sekolah, kelengkapan atribut, dan sepatu.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(4,'P4','Kerapian dan Penampilan','Pelanggaran standar kerapian rambut putra, make-up berlebih putri, perhiasan, dan tato.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(5,'P5','Kedisiplinan','Penyalahgunaan gawai/HP saat KBM, merokok, vape, bermain game, dan membawa barang terlarang.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54'),(6,'P6','Pelanggaran Berat','Perkelahian, narkoba, miras, asusila, pencurian, bullying, dan tindakan melawan hukum.',1,'2026-09-19 03:17:54','2026-09-19 03:17:54');
/*!40000 ALTER TABLE `violation_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `violation_items`
--

DROP TABLE IF EXISTS `violation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `violation_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_points` int NOT NULL DEFAULT '5',
  `guidance_recommendation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `violation_items_category_id_foreign` (`category_id`),
  CONSTRAINT `violation_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `violation_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `violation_items`
--

LOCK TABLES `violation_items` WRITE;
/*!40000 ALTER TABLE `violation_items` DISABLE KEYS */;
INSERT INTO `violation_items` VALUES (1,1,'P1.1','Terlambat hadir ke sekolah 1 s/d 15 menit',5,'Peringatan lisan dan pembiasaan disiplin pagi.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(2,1,'P1.2','Terlambat hadir ke sekolah lebih dari 15 menit',10,'Pembinaan piket dan penugasan edukatif literasi.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(3,1,'P1.3','Meninggalkan kelas/sekolah tanpa izin (Membolos)',20,'Pemanggilan oleh Wali Kelas dan pemberitahuan orang tua.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(4,1,'P1.4','Tidak masuk sekolah tanpa keterangan sah (Alpa)',15,'Konfirmasi orang tua dan peringatan wali kelas.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(5,2,'P2.1','Bersikap tidak sopan / membangkang arahan guru/karyawan',25,'Bimbingan konseling dan surat pernyataan santun.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(6,2,'P2.2','Mengeluarkan kata-kata kotor / umpatan di sekolah',15,'Edukasi tutur kata santun dan refleksi karakter.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(7,2,'P2.3','Mengolok-olok / mengejek kekurangan fisik teman',20,'Konseling anti-perundungan dan mediasi kekeluargaan.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(8,3,'P3.1','Mengenakan seragam tidak sesuai ketentuan hari',5,'Peringatan dan pembetulan tata cara berpakaian.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(9,3,'P3.2','Atribut seragam (badge OSIS, lokasi, dasi, ikat pinggang) tidak lengkap',5,'Wajib melengkapi atribut saat upacara/KBM berikutnya.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(10,3,'P3.3','Mengenakan sepatu warna warni / tidak sesuai ketentuan hitam',5,'Teguran lisan dan penggantian sepatu bertali hitam.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(11,3,'P3.4','Baju dikeluarkan tidak rapi / baju ketat tidak standar',5,'Merapikan pakaian langsung di tempat.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(12,4,'P4.1','Rambut gondrong / tidak rapi bagi siswa putra (melebihi kerah/telinga)',10,'Batas waktu 2 hari untuk mencukur rapi model 3-2-1.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(13,4,'P4.2','Mewarnai rambut dengan cat rambut mencolok (pirang/merah)',20,'Wajib mengembalikan ke warna hitam alami dalam 3 hari.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(14,4,'P4.3','Memakai perhiasan emas berlebihan / make-up tebal bagi siswi',10,'Barang diamankan sementara dan dikembalikan kepada orang tua.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(15,4,'P4.4','Tindik bagi siswa putra atau tato di anggota badan',50,'Konseling khusus BK bersama orang tua.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(16,5,'P5.1','Menggunakan gawai / HP saat pelajaran tanpa instruksi guru',10,'HP dititipkan ke meja guru sampai jam pelajaran usai.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(17,5,'P5.2','Merokok atau membawa rokok di lingkungan sekolah / masih berseragam',50,'Pemanggilan orang tua ke sekolah dan pembinaan BK intensif.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(18,5,'P5.3','Membawa rokok elektrik / vape / pod ke lingkungan sekolah',40,'Penyitaan perangkat dan pembuatan surat komitmen disiplin.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(19,5,'P5.4','Bermain game online / judi online di lingkungan sekolah',30,'Pembinaan BK dan pendampingan digital sehat.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(20,6,'P6.1','Terlibat perkelahian / tawuran antar siswa atau antar sekolah',100,'SP 1 / SP 2 langsung, skorsing pembinaan bersama orang tua.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(21,6,'P6.2','Membawa, mengonsumsi, atau mengedarkan minuman keras / narkoba',200,'Rapat Pleno Dewan Guru untuk sanksi maksimal dikembalikan ke ortu.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(22,6,'P6.3','Melakukan tindakan asusila / pelecehan seksual',150,'Rapat khusus dewan guru dan penanganan hukum terpadu.','2026-09-19 03:17:54','2026-09-19 03:17:54'),(23,6,'P6.4','Melakukan pemerasan / intimidasi fisik (bullying berat)',100,'SP 2 BK dan pendampingan psikologis korban & pelaku.','2026-09-19 03:17:54','2026-09-19 03:17:54');
/*!40000 ALTER TABLE `violation_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `violations`
--

DROP TABLE IF EXISTS `violations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `violations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned DEFAULT NULL,
  `violation_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int NOT NULL DEFAULT '5',
  `date` date NOT NULL,
  `time` time DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chronology` text COLLATE utf8mb4_unicode_ci,
  `reporter_id` bigint unsigned DEFAULT NULL,
  `evidence_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guidance_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('DRAFT','MENUNGGU_VERIFIKASI','DIVERIFIKASI','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MENUNGGU_VERIFIKASI',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `violations_class_id_foreign` (`class_id`),
  KEY `violations_category_id_foreign` (`category_id`),
  KEY `violations_item_id_foreign` (`item_id`),
  KEY `violations_reporter_id_foreign` (`reporter_id`),
  KEY `violations_verified_by_foreign` (`verified_by`),
  KEY `violations_student_id_status_index` (`student_id`,`status`),
  CONSTRAINT `violations_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `violation_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `violations_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `school_classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `violations_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `violation_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `violations_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `violations_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `violations_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `violations`
--

LOCK TABLES `violations` WRITE;
/*!40000 ALTER TABLE `violations` DISABLE KEYS */;
INSERT INTO `violations` VALUES (1,1,2,1,1,'PELANGGARAN-2026-001','Terlambat Hadir Pagi Hari (P1.1)',5,'2026-09-19','07:18:00','Gerbang Masuk SMAN 1 Lengkong','Siswa tiba di gerbang pukul 07:18 WIB saat bel apel pagi telah selesai.',3,NULL,'Diingatkan agar berangkat 15 menit lebih awal.','DIVERIFIKASI',NULL,4,'2026-09-20 07:53:36','2026-09-19 03:17:54','2026-09-20 07:53:36'),(2,1,2,3,9,'PELANGGARAN-2026-002','Atribut Dasi Sekolah Tertinggal Saat Upacara (P3.2)',5,'2026-09-13','06:50:00','Lapangan Upacara','Tidak mengenakan dasi abu-abu saat upacara hari Senin.',4,NULL,NULL,'DIVERIFIKASI',NULL,4,'2026-09-20 07:53:36','2026-09-19 03:17:54','2026-09-20 07:53:36'),(3,3,2,5,17,'PELANGGARAN-2026-080','Membawa dan Menghisap Rokok di Warung Dekat Sekolah (P5.2)',50,'2026-09-17','13:30:00','Warung Belakang Sekolah','Tertangkap patroli guru piket sedang berseragam sekolah.',3,NULL,NULL,'DIVERIFIKASI',NULL,6,'2026-09-20 07:53:36','2026-09-19 03:17:54','2026-09-20 07:53:36'),(4,3,2,1,3,'PELANGGARAN-2026-081','Meninggalkan KBM Tanpa Izin / Membolos (P1.3)',30,'2026-09-18','11:00:00','Pagar Samping Sekolah','Melompati pagar saat jam istirahat kedua dan tidak kembali ke kelas.',4,NULL,NULL,'DIVERIFIKASI',NULL,4,'2026-09-20 07:53:36','2026-09-19 03:17:54','2026-09-20 07:53:36');
/*!40000 ALTER TABLE `violations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-20 15:06:20
