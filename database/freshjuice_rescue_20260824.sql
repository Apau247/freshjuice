-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: freshjuice
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `freshjuice`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `freshjuice` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `freshjuice`;

--
-- Table structure for table `accident_reports`
--

DROP TABLE IF EXISTS `accident_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accident_reports` (
  `AccidentID` varchar(50) NOT NULL,
  `IncidentDate` datetime NOT NULL,
  `Location` varchar(150) NOT NULL,
  `IncidentType` varchar(100) NOT NULL,
  `Description` text NOT NULL,
  `Injuries` text DEFAULT NULL,
  `RootCause` text DEFAULT NULL,
  `CorrectiveAction` text DEFAULT NULL,
  `ReportedBy` varchar(50) DEFAULT NULL,
  `Status` enum('Reported','Under Investigation','Closed') DEFAULT 'Reported',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`AccidentID`),
  KEY `ReportedBy` (`ReportedBy`),
  KEY `idx_ar_date` (`IncidentDate`),
  CONSTRAINT `accident_reports_ibfk_1` FOREIGN KEY (`ReportedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accident_reports`
--

LOCK TABLES `accident_reports` WRITE;
/*!40000 ALTER TABLE `accident_reports` DISABLE KEYS */;
INSERT INTO `accident_reports` VALUES ('ACC-001','2025-06-28 10:30:00','Production Floor A','Near Miss','Worker almost caught sleeve in juicer conveyor','None','Missing emergency stop button cover','Install new e-stop covers and retrain','USR-003','Closed','2026-07-21 15:33:12'),('ACC-002','2025-07-05 14:15:00','Packaging Area','First Aid','Minor cut from loose metal edge on carton stack','Small laceration on left hand','Damaged carton stacker edge','Repair carton stacker, inspect all equipment','USR-003','Under Investigation','2026-07-21 15:33:12'),('ACC-003','2025-07-09 09:00:00','Mixing Area','Property Damage','Chemical spill during cleaning chemical transfer','None - evacuated area','Improper funnel usage','Chemical transfer training, install proper dispensing equipment','USR-005','Reported','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `accident_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `AttendanceID` varchar(50) NOT NULL,
  `StaffID` varchar(50) NOT NULL,
  `ShiftID` varchar(50) DEFAULT NULL,
  `Date` date NOT NULL,
  `ClockIn` time DEFAULT NULL,
  `ClockOut` time DEFAULT NULL,
  `Status` enum('Present','Absent','Late','Leave') DEFAULT 'Present',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`AttendanceID`),
  KEY `ShiftID` (`ShiftID`),
  KEY `idx_att_date` (`Date`),
  KEY `idx_att_staff` (`StaffID`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`StaffID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`ShiftID`) REFERENCES `shifts` (`ShiftID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_trail`
--

DROP TABLE IF EXISTS `audit_trail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_trail` (
  `AuditID` bigint(20) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(50) DEFAULT NULL,
  `Action` varchar(50) NOT NULL,
  `Module` varchar(50) NOT NULL,
  `RecordID` varchar(50) DEFAULT NULL,
  `Details` text DEFAULT NULL,
  `IPAddress` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`AuditID`),
  KEY `idx_at_user` (`UserID`),
  KEY `idx_at_module` (`Module`),
  KEY `idx_at_date` (`created_at`),
  CONSTRAINT `audit_trail_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_trail`
--

LOCK TABLES `audit_trail` WRITE;
/*!40000 ALTER TABLE `audit_trail` DISABLE KEYS */;
INSERT INTO `audit_trail` VALUES (1,'USR-004','CREATE','Raw Materials','RM-001','Added Fresh Mangoes stock: 1200 kg','192.168.1.10','2026-07-21 15:33:12'),(2,'USR-003','CREATE','Production Batch','BAT-001','Created batch FJ-20250710-001 for 500L Mango','192.168.1.20','2026-07-21 15:33:12'),(3,'USR-005','UPDATE','Quality Inspection','QI-001','Incoming inspection passed for BAT-001','192.168.1.30','2026-07-21 15:33:12'),(4,'USR-006','CREATE','Sales Order','ORD-001','Order placed for 200 bottles Mango','192.168.1.40','2026-07-21 15:33:12'),(5,'USR-008','UPDATE','Maintenance','MNT-004','Emergency repair on MCH-006 in progress','192.168.1.50','2026-07-21 15:33:12'),(6,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-07-21 15:33:19'),(7,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-07-22 12:06:05'),(8,'USR-001','CREATE','Documents','DOC-20260722-7DFAF','Created document','::1','2026-07-22 12:18:46'),(9,'USR-001','LOGOUT','Auth','USR-001','User logged out','::1','2026-07-22 13:09:52'),(10,'USR-007','LOGIN_FAILED','Auth','USR-007','Invalid credentials','::1','2026-07-22 13:29:18'),(11,'USR-007','LOGIN_FAILED','Auth','USR-007','Invalid credentials','::1','2026-07-22 13:29:33'),(12,'USR-007','LOGIN','Auth','USR-007','User logged in','::1','2026-07-22 13:29:52'),(13,'USR-001','LOGIN_FAILED','Auth','USR-001','Invalid credentials','::1','2026-07-24 15:50:27'),(14,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-07-24 15:50:39'),(15,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-07-29 10:19:26'),(16,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-02 15:13:25'),(17,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-02 15:38:27'),(18,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-02 15:38:32'),(19,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-02 15:38:36'),(20,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-02 15:39:57'),(21,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-02 15:40:18'),(22,'USR-006','LOGIN','Auth','USR-006','User logged in','::1','2026-08-02 15:40:21'),(23,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-02 15:40:41'),(24,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-02 15:41:58'),(25,'USR-001','CREATE','Documents','DOC-20260802-D8BC2','Created document','::1','2026-08-02 15:41:58'),(26,'USR-001','DELETE','Documents','DOC-20260802-D8BC2','Deleted document','::1','2026-08-02 15:41:58'),(27,'USR-001','LOGOUT','Auth','USR-001','User logged out','::1','2026-08-02 15:41:58'),(28,'USR-001','LOGOUT','Auth','USR-001','User logged out','::1','2026-08-02 16:29:00'),(29,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-02 16:29:20'),(30,'USR-001','UPDATE','Suppliers','SUP-001','Updated supplier','::1','2026-08-02 16:29:43'),(31,'USR-001','CREATE','Users','USR-222','Created user','::1','2026-08-02 16:32:33'),(32,'USR-001','LOGOUT','Auth','USR-001','User logged out','::1','2026-08-02 16:32:43'),(33,'USR-222','LOGIN','Auth','USR-222','User logged in','::1','2026-08-02 16:32:57'),(34,'USR-222','UPDATE','Sales','ORD-003','Updated sales order','::1','2026-08-02 16:33:40'),(35,'USR-222','UPDATE','Sales','ORD-003','Updated sales order','::1','2026-08-02 16:33:49'),(36,'USR-222','UPDATE','Sales','ORD-004','Updated sales order','::1','2026-08-02 16:33:57'),(37,'USR-222','LOGOUT','Auth','USR-222','User logged out','::1','2026-08-02 16:34:06'),(38,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-02 16:34:21'),(39,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-03 15:22:11'),(40,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-03 16:11:27'),(41,'USR-001','LOGOUT','Auth','USR-001','User logged out','::1','2026-08-03 16:16:06'),(42,'USR-001','LOGIN_FAILED','Auth','USR-001','Invalid credentials','::1','2026-08-03 16:26:54'),(43,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-03 16:27:04'),(44,'USR-001','LOGOUT','Auth','USR-001','User logged out','::1','2026-08-03 16:29:58'),(45,'USR-008','LOGIN','Auth','USR-008','User logged in','::1','2026-08-03 16:30:09'),(46,'USR-008','LOGOUT','Auth','USR-008','User logged out','::1','2026-08-03 16:51:17'),(47,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-03 16:51:35'),(48,'USR-001','LOGOUT','Auth','USR-001','User logged out','::1','2026-08-03 16:52:30'),(49,'USR-001','LOGIN','Auth','USR-001','User logged in','::1','2026-08-04 11:50:13'),(50,NULL,'LOGIN_FAILED','Auth','USR-SMOKE','Invalid credentials','::1','2026-08-24 10:33:14'),(51,NULL,'LOGIN','Auth','USR-SMOKE','User logged in','::1','2026-08-24 10:34:09'),(52,NULL,'EXPORT','Reports','production','Exported Production Report CSV','::1','2026-08-24 10:34:10'),(53,NULL,'LOGIN','Auth','USR-SMOKE','User logged in','::1','2026-08-24 10:34:39'),(54,NULL,'LOGIN','Auth','USR-SMOKE','User logged in','::1','2026-08-24 10:35:19'),(55,NULL,'EXPORT','Reports','production','Exported Production Report CSV','::1','2026-08-24 10:35:20'),(56,NULL,'LOGIN','Auth','USR-SMOKE','User logged in','::1','2026-08-24 10:35:26'),(57,NULL,'LOGIN','Auth','USR-SMOKE','User logged in','::1','2026-08-24 10:35:47'),(58,NULL,'EXPORT','Reports','production','Exported Production Report CSV','::1','2026-08-24 10:35:48');
/*!40000 ALTER TABLE `audit_trail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certifications`
--

DROP TABLE IF EXISTS `certifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certifications` (
  `CertID` varchar(50) NOT NULL,
  `CertName` varchar(150) NOT NULL,
  `CertType` varchar(100) NOT NULL,
  `IssuingAuthority` varchar(150) DEFAULT NULL,
  `IssueDate` date NOT NULL,
  `ExpiryDate` date NOT NULL,
  `DocumentPath` varchar(500) DEFAULT NULL,
  `Status` enum('Active','Expired','Pending Renewal') DEFAULT 'Active',
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`CertID`),
  KEY `idx_cert_expiry` (`ExpiryDate`),
  KEY `idx_cert_type` (`CertType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certifications`
--

LOCK TABLES `certifications` WRITE;
/*!40000 ALTER TABLE `certifications` DISABLE KEYS */;
INSERT INTO `certifications` VALUES ('CERT-001','FDA Food Safety Registration','FDA','Food & Drugs Authority Ghana','2024-01-01','2026-01-01',NULL,'Active',NULL,'2026-07-21 15:33:12'),('CERT-002','HACCP Certification','HACCP','Ghana Standards Authority','2024-03-15','2025-03-15',NULL,'Expired',NULL,'2026-07-21 15:33:12'),('CERT-003','ISO 22000 Food Safety','ISO 22000','SGS Ghana','2024-06-01','2027-06-01',NULL,'Active',NULL,'2026-07-21 15:33:12'),('CERT-004','GSA Product Certification','GSA','Ghana Standards Authority','2024-02-01','2026-02-01',NULL,'Active',NULL,'2026-07-21 15:33:12'),('CERT-005','Environmental Permit','Environmental','Environmental Protection Agency','2024-01-15','2025-07-15',NULL,'Active','','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `certifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `CustomerID` varchar(50) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Contact` varchar(100) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Phone` varchar(30) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `Type` varchar(50) DEFAULT 'Retailer',
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`CustomerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES ('CUS-001','FreshMart Supermarket','Abena Mensah','orders@freshmart.com','0201234567',NULL,'Retailer','Active','2026-07-21 15:33:12'),('CUS-002','HealthPlus Pharmacy','Yaw Boateng','buy@healthplus.com','0202345678',NULL,'Distributor','Active','2026-07-21 15:33:12'),('CUS-003','Accra Fresh Juice Bar','Esi Ampofo','info@accrafjb.com','0203456789',NULL,'Restaurant','Active','2026-07-21 15:33:12'),('CUS-004','Kumasi Market Traders','Kwesi Appiah','traders@kumasi.com','0204567890',NULL,'Wholesaler','Active','2026-07-21 15:33:12'),('CUS-005','Hotel & Catering Services','Adwoa Foli','procurement@hotel.com','0205678901',NULL,'Hotel','Active','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `DocID` varchar(50) NOT NULL,
  `Title` varchar(200) NOT NULL,
  `DocType` varchar(100) NOT NULL,
  `Version` varchar(20) DEFAULT '1.0',
  `FilePath` varchar(500) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `EffectiveDate` date DEFAULT NULL,
  `ReviewDate` date DEFAULT NULL,
  `Status` enum('Draft','Under Review','Approved','Obsolete') DEFAULT 'Draft',
  `ApprovedBy` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`DocID`),
  KEY `ApprovedBy` (`ApprovedBy`),
  KEY `idx_doc_type` (`DocType`),
  KEY `idx_doc_status` (`Status`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`ApprovedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES ('DOC-001','Factory Quality Manual','Procedure','3.0',NULL,'Comprehensive quality management procedures','Quality Assurance','2025-01-01','2025-07-01','Approved','USR-002','2026-07-21 15:33:12'),('DOC-002','Production Floor Layout v2','Drawing','2.0',NULL,'Updated floor plan with new equipment locations','Production','2025-03-15','2025-09-15','Approved','USR-002','2026-07-21 15:33:12'),('DOC-003','Emergency Evacuation Plan','Plan','1.0',NULL,'Fire and chemical spill evacuation routes','Safety','2025-04-01','2025-10-01','Under Review','USR-005','2026-07-21 15:33:12'),('DOC-004','Supplier Quality Requirements','Specification','2.5',NULL,'Quality standards for all supplied materials','Quality Assurance','2025-02-01','2025-08-01','Approved','USR-005','2026-07-21 15:33:12'),('DOC-005','Energy Management Policy','Policy','1.0',NULL,'Company policy on energy efficiency and monitoring','Management','2025-05-01','2025-11-01','Draft',NULL,'2026-07-21 15:33:12'),('DOC-20260722-7DFAF','Drinks','PDF','1.0','&quot;C:\\Users\\user\\Downloads\\dashboard.pdf&quot;','','Supply','2026-07-22','2026-07-23','Approved','USR-001','2026-07-22 12:18:46');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emergency_drills`
--

DROP TABLE IF EXISTS `emergency_drills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emergency_drills` (
  `DrillID` varchar(50) NOT NULL,
  `DrillDate` date NOT NULL,
  `DrillType` varchar(100) NOT NULL,
  `Location` varchar(150) DEFAULT NULL,
  `ParticipantsCount` int(11) DEFAULT 0,
  `DurationMinutes` int(11) DEFAULT 0,
  `Outcome` text DEFAULT NULL,
  `IssuesFound` text DEFAULT NULL,
  `CorrectiveAction` text DEFAULT NULL,
  `ConductedBy` varchar(50) DEFAULT NULL,
  `Status` enum('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`DrillID`),
  KEY `ConductedBy` (`ConductedBy`),
  KEY `idx_ed_date` (`DrillDate`),
  CONSTRAINT `emergency_drills_ibfk_1` FOREIGN KEY (`ConductedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emergency_drills`
--

LOCK TABLES `emergency_drills` WRITE;
/*!40000 ALTER TABLE `emergency_drills` DISABLE KEYS */;
INSERT INTO `emergency_drills` VALUES ('DRL-001','2025-06-20','Fire Drill','Entire Factory',35,15,'Satisfactory - 15 min evacuation','1 person did not hear alarm due to noisy area','Install strobe light in high-noise zones','USR-002','Completed','2026-07-21 15:33:12'),('DRL-002','2025-07-05','Chemical Spill','Mixing Area',10,20,'Adequate - contained quickly, some confusion on protocol','Response team unclear on neutralization procedure','Schedule chemical spill response training','USR-005','Completed','2026-07-21 15:33:12'),('DRL-003','2025-07-18','First Aid Emergency','Packaging Area',15,30,'Scheduled','','','USR-002','Scheduled','2026-07-21 15:33:12'),('DRL-004','2025-08-01','Fire Drill','Entire Factory',40,0,'Scheduled','','','USR-002','Scheduled','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `emergency_drills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fat_records`
--

DROP TABLE IF EXISTS `fat_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fat_records` (
  `FAT_ID` varchar(50) NOT NULL,
  `MachineID` varchar(50) DEFAULT NULL,
  `TestDate` date NOT NULL,
  `TestType` varchar(100) NOT NULL,
  `ExpectedResult` text DEFAULT NULL,
  `ActualResult` text DEFAULT NULL,
  `Result` enum('Pending','Pass','Fail','Conditional') DEFAULT 'Pending',
  `DefectsFound` text DEFAULT NULL,
  `TestedBy` varchar(50) DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `Status` enum('Pending','In Progress','Completed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`FAT_ID`),
  KEY `MachineID` (`MachineID`),
  KEY `TestedBy` (`TestedBy`),
  KEY `idx_fat_date` (`TestDate`),
  KEY `idx_fat_result` (`Result`),
  CONSTRAINT `fat_records_ibfk_1` FOREIGN KEY (`MachineID`) REFERENCES `machines` (`MachineID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fat_records_ibfk_2` FOREIGN KEY (`TestedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fat_records`
--

LOCK TABLES `fat_records` WRITE;
/*!40000 ALTER TABLE `fat_records` DISABLE KEYS */;
INSERT INTO `fat_records` VALUES ('FAT-001','MCH-002','2023-05-15','Performance','Throughput 500L/hr','510L/hr achieved','Pass',NULL,'USR-008',NULL,'Completed','2026-07-21 15:33:12'),('FAT-002','MCH-004','2023-05-20','Performance','Temperature accuracy +/-0.5C','Accuracy +/-0.3C','Pass',NULL,'USR-008',NULL,'Completed','2026-07-21 15:33:12'),('FAT-003','MCH-006','2023-06-01','Installation','Bottling line installation per specs','All spec items verified','Pass',NULL,'USR-008',NULL,'Completed','2026-07-21 15:33:12'),('FAT-004','MCH-005','2025-07-10','Safety','Emergency stop within 2 seconds','E-stop activated in 1.5 seconds','Pass',NULL,'USR-008',NULL,'Completed','2026-07-21 15:33:12'),('FAT-005','MCH-010','2025-07-12','Performance','Cooling to 4C within 30 min','Reached 4C in 25 min','Pass',NULL,'USR-008',NULL,'Completed','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `fat_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finished_goods`
--

DROP TABLE IF EXISTS `finished_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finished_goods` (
  `FG_ID` varchar(50) NOT NULL,
  `BatchID` varchar(50) DEFAULT NULL,
  `Flavour` varchar(100) NOT NULL,
  `ExpiryDate` date NOT NULL,
  `QuantityAvailable` decimal(10,2) DEFAULT 0.00,
  `Unit` varchar(30) DEFAULT 'bottles',
  `StorageLocation` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`FG_ID`),
  KEY `BatchID` (`BatchID`),
  KEY `idx_fg_expiry` (`ExpiryDate`),
  KEY `idx_fg_flavour` (`Flavour`),
  CONSTRAINT `finished_goods_ibfk_1` FOREIGN KEY (`BatchID`) REFERENCES `production_batches` (`BatchID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finished_goods`
--

LOCK TABLES `finished_goods` WRITE;
/*!40000 ALTER TABLE `finished_goods` DISABLE KEYS */;
INSERT INTO `finished_goods` VALUES ('FG-001','BAT-001','Mango','2025-12-31',480.00,'bottles','Cold Storage A - Shelf 1','2026-07-21 15:33:12'),('FG-002','BAT-002','Orange','2025-12-31',750.00,'bottles','Cold Storage A - Shelf 2','2026-07-21 15:33:12'),('FG-003','BAT-003','Pineapple','2026-01-15',290.00,'bottles','Cold Storage A - Shelf 3','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `finished_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `generator_log`
--

DROP TABLE IF EXISTS `generator_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `generator_log` (
  `LogID` varchar(50) NOT NULL,
  `Date` date NOT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL,
  `RuntimeHrs` decimal(6,2) DEFAULT 0.00,
  `FuelUsed` decimal(10,2) DEFAULT 0.00,
  `FuelUnit` varchar(30) DEFAULT 'litres',
  `Reason` varchar(200) DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`LogID`),
  KEY `idx_gl_date` (`Date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generator_log`
--

LOCK TABLES `generator_log` WRITE;
/*!40000 ALTER TABLE `generator_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `generator_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hazard_register`
--

DROP TABLE IF EXISTS `hazard_register`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hazard_register` (
  `HazardID` varchar(50) NOT NULL,
  `HazardDescription` text NOT NULL,
  `RiskCategory` varchar(100) DEFAULT NULL,
  `Likelihood` enum('Rare','Unlikely','Possible','Likely','Almost Certain') DEFAULT 'Possible',
  `Consequence` enum('Insignificant','Minor','Moderate','Major','Catastrophic') DEFAULT 'Moderate',
  `RiskRating` int(11) DEFAULT 0,
  `ControlMeasures` text DEFAULT NULL,
  `ResponsiblePerson` varchar(50) DEFAULT NULL,
  `ReviewDate` date DEFAULT NULL,
  `Status` enum('Active','Mitigated','Closed') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`HazardID`),
  KEY `idx_hr_rating` (`RiskRating`),
  KEY `idx_hr_status` (`Status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hazard_register`
--

LOCK TABLES `hazard_register` WRITE;
/*!40000 ALTER TABLE `hazard_register` DISABLE KEYS */;
INSERT INTO `hazard_register` VALUES ('HAZ-001','Juicer blade exposure during cleaning','Mechanical','Possible','Major',12,'Lock-out tag-out procedure, blade guard interlock','USR-008','2025-10-01','Active','2026-07-21 15:33:12'),('HAZ-002','Chemical splash during mixing','Chemical','Likely','Moderate',12,'Chemical handling SOP, PPE including face shield','USR-005','2025-09-01','Active','2026-07-21 15:33:12'),('HAZ-003','Slip hazard on wet production floor','Safety','Almost Certain','Minor',10,'Anti-slip mats, immediate spill cleanup protocol','USR-003','2025-08-01','Active','2026-07-21 15:33:12'),('HAZ-004','Electrical panel exposed in packaging area','Electrical','Unlikely','Catastrophic',10,'Locked panel, only authorized electricians','USR-008','2025-11-01','Active','2026-07-21 15:33:12'),('HAZ-005','Manual lifting of heavy fruit crates','Ergonomic','Possible','Moderate',9,'Provide lifting aids, team lifting training','USR-003','2025-09-01','Mitigated','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `hazard_register` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `improvement_initiatives`
--

DROP TABLE IF EXISTS `improvement_initiatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `improvement_initiatives` (
  `InitiativeID` varchar(50) NOT NULL,
  `Title` varchar(200) NOT NULL,
  `Category` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `RootCauseAnalysis` text DEFAULT NULL,
  `ActionPlan` text DEFAULT NULL,
  `TargetDate` date DEFAULT NULL,
  `ResponsiblePerson` varchar(50) DEFAULT NULL,
  `Status` enum('Proposed','Approved','In Progress','Completed','Cancelled') DEFAULT 'Proposed',
  `Effectiveness` text DEFAULT NULL,
  `CreatedBy` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`InitiativeID`),
  KEY `CreatedBy` (`CreatedBy`),
  KEY `idx_ii_status` (`Status`),
  KEY `idx_ii_target` (`TargetDate`),
  CONSTRAINT `improvement_initiatives_ibfk_1` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `improvement_initiatives`
--

LOCK TABLES `improvement_initiatives` WRITE;
/*!40000 ALTER TABLE `improvement_initiatives` DISABLE KEYS */;
INSERT INTO `improvement_initiatives` VALUES ('CAPA-001','Reduce bottle cap defect rate','Quality','Current cap defect rate is 2.3%, target <0.5%','Worn capping machine head','Replace capping head, calibrate torque settings, 100% inspection for 1 week','2025-07-30','USR-008','Approved',NULL,'USR-005','2026-07-21 15:33:12','2026-07-21 15:33:12'),('CAPA-002','Improve fruit washing efficiency','Process','Fruit washer using 20% more water than designed','Clogged spray nozzles, improper pressure settings','Clean all nozzles, calibrate pressure regulators, install flow meters','2025-07-25','USR-008','In Progress',NULL,'USR-003','2026-07-21 15:33:12','2026-07-21 15:33:12'),('CAPA-003','Reduce employee turnover in packaging','HR','30% turnover rate in packaging department','Lack of growth opportunities, inadequate training','Implement career path program, increase training budget, monthly reviews','2025-09-01','USR-002','Proposed',NULL,'USR-002','2026-07-21 15:33:12','2026-07-21 15:33:12'),('CAPA-004','Supplier delivery accuracy improvement','Supply Chain','SUP-003 delivery accuracy at 92% vs 98% target','Inconsistent communication and no quality checklist','Implement supplier scorecard, share quality requirements document, weekly check-in calls','2025-08-15','USR-004','Proposed',NULL,'USR-004','2026-07-21 15:33:12','2026-07-21 15:33:12'),('CAPA-005','Standardize batch documentation','Documentation','Batch records inconsistent across shifts','No standard template, different supervisors use different formats','Create standardized batch record template, train all supervisors, audit compliance','2025-07-20','USR-003','Completed',NULL,'USR-002','2026-07-21 15:33:12','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `improvement_initiatives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `InvoiceID` varchar(50) NOT NULL,
  `InvoiceDate` date NOT NULL,
  `Amount` decimal(15,2) DEFAULT 0.00,
  `Tax` decimal(15,2) DEFAULT 0.00,
  `TotalDue` decimal(15,2) DEFAULT 0.00,
  `PaymentStatus` enum('Unpaid','Partial','Paid','Overdue') DEFAULT 'Unpaid',
  `DueDate` date DEFAULT NULL,
  `OrderID` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`InvoiceID`),
  KEY `OrderID` (`OrderID`),
  KEY `idx_inv_status` (`PaymentStatus`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `sales_orders` (`OrderID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES ('INV-001','2025-07-05',12500.00,1875.00,14375.00,'Paid','2025-08-04','ORD-001','2026-07-21 15:33:12'),('INV-002','2025-07-07',18000.00,2700.00,20700.00,'Paid','2025-08-06','ORD-002','2026-07-21 15:33:12'),('INV-003','2025-07-09',5000.00,750.00,5750.00,'Unpaid','2025-08-08','ORD-003','2026-07-21 15:33:12'),('INV-004','2025-07-10',22500.00,3375.00,25875.00,'Partial','2025-08-09','ORD-004','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `machines`
--

DROP TABLE IF EXISTS `machines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `machines` (
  `MachineID` varchar(50) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Type` varchar(100) DEFAULT NULL,
  `Location` varchar(100) DEFAULT NULL,
  `Status` enum('Operational','Maintenance','Down','Decommissioned') DEFAULT 'Operational',
  `InstallDate` date DEFAULT NULL,
  `LastService` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`MachineID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `machines`
--

LOCK TABLES `machines` WRITE;
/*!40000 ALTER TABLE `machines` DISABLE KEYS */;
INSERT INTO `machines` VALUES ('MCH-001','Industrial Fruit Washer','Washer','Production Floor A','Operational','2023-06-15','2025-06-01','2026-07-21 15:33:12'),('MCH-002','Heavy Duty Juicer #1','Juicer','Production Floor A','Operational','2023-06-15','2025-06-15','2026-07-21 15:33:12'),('MCH-003','Heavy Duty Juicer #2','Juicer','Production Floor A','Operational','2023-08-01','2025-06-15','2026-07-21 15:33:12'),('MCH-004','Pasteurizer Unit','Pasteurizer','Production Floor B','Operational','2023-06-15','2025-05-20','2026-07-21 15:33:12'),('MCH-005','Bottling Line A','Bottling','Packaging Area','Operational','2023-07-01','2025-06-10','2026-07-21 15:33:12'),('MCH-006','Bottling Line B','Bottling','Packaging Area','Maintenance','2023-07-01','2025-05-15','2026-07-21 15:33:12'),('MCH-007','Capping Machine','Capping','Packaging Area','Operational','2023-07-01','2025-06-10','2026-07-21 15:33:12'),('MCH-008','Labeling Machine','Labeling','Packaging Area','Operational','2023-08-01','2025-06-12','2026-07-21 15:33:12'),('MCH-009','Shrink Wrapper','Wrapper','Packaging Area','Operational','2023-09-01','2025-06-12','2026-07-21 15:33:12'),('MCH-010','Cold Storage Unit #1','Storage','Warehouse','Operational','2023-06-15','2025-06-01','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `machines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_records`
--

DROP TABLE IF EXISTS `maintenance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maintenance_records` (
  `MaintenanceID` varchar(50) NOT NULL,
  `MaintenanceType` enum('Preventive','Corrective','Emergency') DEFAULT 'Preventive',
  `MaintenanceDate` date NOT NULL,
  `Downtime` decimal(10,2) DEFAULT 0.00,
  `Cost` decimal(15,2) DEFAULT 0.00,
  `MachineID` varchar(50) DEFAULT NULL,
  `TechnicianID` varchar(50) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `SpareParts` text DEFAULT NULL,
  `Status` enum('Scheduled','In Progress','Completed','Cancelled') DEFAULT 'Scheduled',
  `NextServiceDate` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`MaintenanceID`),
  KEY `MachineID` (`MachineID`),
  KEY `TechnicianID` (`TechnicianID`),
  KEY `idx_maint_date` (`MaintenanceDate`),
  CONSTRAINT `maintenance_records_ibfk_1` FOREIGN KEY (`MachineID`) REFERENCES `machines` (`MachineID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `maintenance_records_ibfk_2` FOREIGN KEY (`TechnicianID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_records`
--

LOCK TABLES `maintenance_records` WRITE;
/*!40000 ALTER TABLE `maintenance_records` DISABLE KEYS */;
INSERT INTO `maintenance_records` VALUES ('MNT-001','Preventive','2025-06-15',4.00,250.00,'MCH-002','USR-008','Routine oil change and belt inspection','Oil filter, Drive belt','Completed','2025-09-15','2026-07-21 15:33:12'),('MNT-002','Corrective','2025-06-20',8.00,850.00,'MCH-006','USR-008','Bottling line conveyor motor replacement','Motor unit, Bearings','Completed','2025-07-20','2026-07-21 15:33:12'),('MNT-003','Preventive','2025-07-01',2.00,120.00,'MCH-004','USR-008','Pasteurizer calibration','Calibration kit','Completed','2025-10-01','2026-07-21 15:33:12'),('MNT-004','Emergency','2025-07-08',6.00,1200.00,'MCH-006','USR-008','Hydraulic press failure repair','Hydraulic pump, Seals','In Progress','2025-07-15','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `maintenance_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packaging_materials`
--

DROP TABLE IF EXISTS `packaging_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packaging_materials` (
  `PackageID` varchar(50) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Unit` varchar(30) DEFAULT 'pcs',
  `CurrentStock` decimal(10,2) DEFAULT 0.00,
  `MinStock` decimal(10,2) DEFAULT 0.00,
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`PackageID`),
  KEY `idx_pm_type` (`Type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packaging_materials`
--

LOCK TABLES `packaging_materials` WRITE;
/*!40000 ALTER TABLE `packaging_materials` DISABLE KEYS */;
INSERT INTO `packaging_materials` VALUES ('PKG-001','500ml PET Bottle','Bottle','pcs',10000.00,2000.00,'Active','2026-07-21 15:33:12'),('PKG-002','1L PET Bottle','Bottle','pcs',8000.00,2000.00,'Active','2026-07-21 15:33:12'),('PKG-003','Bottle Cap (Standard)','Cap','pcs',20000.00,5000.00,'Active','2026-07-21 15:33:12'),('PKG-004','Bottle Cap (Sports)','Cap','pcs',5000.00,2000.00,'Active','2026-07-21 15:33:12'),('PKG-005','Product Label - Mango','Label','pcs',6000.00,2000.00,'Active','2026-07-21 15:33:12'),('PKG-006','Product Label - Orange','Label','pcs',8000.00,2000.00,'Active','2026-07-21 15:33:12'),('PKG-007','Product Label - Pineapple','Label','pcs',5000.00,2000.00,'Active','2026-07-21 15:33:12'),('PKG-008','Product Label - Strawberry','Label','pcs',3000.00,1000.00,'Active','2026-07-21 15:33:12'),('PKG-009','Shipping Carton','Carton','pcs',500.00,100.00,'Active','2026-07-21 15:33:12'),('PKG-010','PVC Shrink Wrap','Wrapper','roll',200.00,50.00,'Active','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `packaging_materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permits`
--

DROP TABLE IF EXISTS `permits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permits` (
  `PermitID` varchar(50) NOT NULL,
  `PermitName` varchar(200) NOT NULL,
  `PermitType` varchar(100) NOT NULL,
  `IssuingAuthority` varchar(200) DEFAULT NULL,
  `PermitNumber` varchar(100) DEFAULT NULL,
  `IssueDate` date NOT NULL,
  `ExpiryDate` date NOT NULL,
  `DocumentPath` varchar(500) DEFAULT NULL,
  `Status` enum('Active','Expired','Suspended','Pending Renewal') DEFAULT 'Active',
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`PermitID`),
  KEY `idx_perm_expiry` (`ExpiryDate`),
  KEY `idx_perm_type` (`PermitType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permits`
--

LOCK TABLES `permits` WRITE;
/*!40000 ALTER TABLE `permits` DISABLE KEYS */;
INSERT INTO `permits` VALUES ('PRM-001','Food Manufacturing License','Health Permit','Ghana FDA','FDA-FML-2024-001','2024-01-15','2026-01-15',NULL,'Active',NULL,'2026-07-21 15:33:12'),('PRM-002','Environmental Operating Permit','Environmental','EPA Ghana','EPA-EOP-2024-042','2024-03-01','2025-09-01',NULL,'Active',NULL,'2026-07-21 15:33:12'),('PRM-003','Building Safety Certificate','Safety','Ghana Fire Service','GFS-BSC-2024-008','2024-02-01','2025-08-01',NULL,'Active',NULL,'2026-07-21 15:33:12'),('PRM-004','Water Extraction License','Water','Water Resources Commission','WRC-WEL-2024-015','2024-01-01','2025-12-31',NULL,'Active',NULL,'2026-07-21 15:33:12'),('PRM-005','Waste Disposal License','Waste','EPA Ghana','EPA-WDL-2024-007','2024-06-01','2025-06-01',NULL,'Expired',NULL,'2026-07-21 15:33:12');
/*!40000 ALTER TABLE `permits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `power_usage`
--

DROP TABLE IF EXISTS `power_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `power_usage` (
  `PowerUsageID` varchar(50) NOT NULL,
  `Date` date NOT NULL,
  `Source` enum('Grid','Generator','Solar') DEFAULT 'Grid',
  `ConsumptionKWh` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Cost` decimal(15,2) DEFAULT 0.00,
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`PowerUsageID`),
  KEY `idx_pu_date` (`Date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `power_usage`
--

LOCK TABLES `power_usage` WRITE;
/*!40000 ALTER TABLE `power_usage` DISABLE KEYS */;
INSERT INTO `power_usage` VALUES ('PU-001','2025-07-01','Grid',450.00,675.00,'Normal production day','2026-07-21 15:33:12'),('PU-002','2025-07-02','Grid',520.00,780.00,'Higher usage - extra bottling run','2026-07-21 15:33:12'),('PU-003','2025-07-03','Grid',380.00,570.00,'Reduced production','2026-07-21 15:33:12'),('PU-004','2025-07-04','Generator',300.00,1200.00,'Grid outage 2hrs - generator used','2026-07-21 15:33:12'),('PU-005','2025-07-05','Grid',480.00,720.00,'Normal operations','2026-07-21 15:33:12'),('PU-006','2025-07-08','Grid',420.00,630.00,'Partial production','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `power_usage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppe_records`
--

DROP TABLE IF EXISTS `ppe_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ppe_records` (
  `PPE_ID` varchar(50) NOT NULL,
  `StaffID` varchar(50) NOT NULL,
  `PPESource` varchar(100) NOT NULL,
  `DateIssued` date NOT NULL,
  `ExpiryDate` date DEFAULT NULL,
  `Condition` enum('New','Good','Fair','Poor','Expired') DEFAULT 'New',
  `ReplacementNeeded` tinyint(1) DEFAULT 0,
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`PPE_ID`),
  KEY `idx_ppe_staff` (`StaffID`),
  CONSTRAINT `ppe_records_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`StaffID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppe_records`
--

LOCK TABLES `ppe_records` WRITE;
/*!40000 ALTER TABLE `ppe_records` DISABLE KEYS */;
INSERT INTO `ppe_records` VALUES ('PPE-001','STF-003','Safety Goggles','2025-01-15','2025-07-15','Fair',1,'Needs replacement soon','2026-07-21 15:33:12'),('PPE-002','STF-003','Steel Toe Boots','2025-01-15','2025-10-15','Good',0,'In good condition','2026-07-21 15:33:12'),('PPE-003','STF-005','Lab Coat','2025-02-01','2025-08-01','Poor',1,'Torn at sleeve, replace','2026-07-21 15:33:12'),('PPE-004','STF-004','Safety Gloves (Cut Resistant)','2025-03-01','2025-09-01','Good',0,'Still effective','2026-07-21 15:33:12'),('PPE-005','STF-003','Hearing Protection (Ear Muffs)','2025-01-15','2025-07-15','Expired',1,'Expired, replace immediately','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `ppe_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `production_batches`
--

DROP TABLE IF EXISTS `production_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_batches` (
  `BatchID` varchar(50) NOT NULL,
  `BatchNumber` varchar(50) NOT NULL,
  `ProductionDate` date NOT NULL,
  `Flavour` varchar(100) NOT NULL,
  `Quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Unit` varchar(30) DEFAULT 'litres',
  `Status` enum('Pending','In Progress','Completed','Rejected','Cancelled') DEFAULT 'Pending',
  `UserID` varchar(50) DEFAULT NULL,
  `RawMaterialID` varchar(50) DEFAULT NULL,
  `PackagingMaterialID` varchar(50) DEFAULT NULL,
  `MachineID` varchar(50) DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`BatchID`),
  UNIQUE KEY `BatchNumber` (`BatchNumber`),
  KEY `UserID` (`UserID`),
  KEY `RawMaterialID` (`RawMaterialID`),
  KEY `PackagingMaterialID` (`PackagingMaterialID`),
  KEY `MachineID` (`MachineID`),
  KEY `idx_pb_date` (`ProductionDate`),
  KEY `idx_pb_status` (`Status`),
  KEY `idx_pb_flavour` (`Flavour`),
  CONSTRAINT `production_batches_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `production_batches_ibfk_2` FOREIGN KEY (`RawMaterialID`) REFERENCES `raw_materials` (`MaterialID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `production_batches_ibfk_3` FOREIGN KEY (`PackagingMaterialID`) REFERENCES `packaging_materials` (`PackageID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `production_batches_ibfk_4` FOREIGN KEY (`MachineID`) REFERENCES `machines` (`MachineID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_batches`
--

LOCK TABLES `production_batches` WRITE;
/*!40000 ALTER TABLE `production_batches` DISABLE KEYS */;
INSERT INTO `production_batches` VALUES ('BAT-001','FJ-20250710-001','2025-07-01','Mango',500.00,'litres','Completed','USR-003','RM-001','PKG-001','MCH-002',NULL,'2026-07-21 15:33:12','2026-07-21 15:33:12'),('BAT-002','FJ-20250710-002','2025-07-03','Orange',800.00,'litres','Completed','USR-003','RM-002','PKG-002','MCH-003',NULL,'2026-07-21 15:33:12','2026-07-21 15:33:12'),('BAT-003','FJ-20250710-003','2025-07-05','Pineapple',300.00,'litres','Completed','USR-003','RM-003','PKG-001','MCH-002',NULL,'2026-07-21 15:33:12','2026-07-21 15:33:12'),('BAT-004','FJ-20250710-004','2025-07-08','Strawberry',200.00,'litres','In Progress','USR-003','RM-004','PKG-001','MCH-002',NULL,'2026-07-21 15:33:12','2026-07-21 15:33:12'),('BAT-005','FJ-20250710-005','2025-07-10','Mango',600.00,'litres','Pending','USR-003','RM-001','PKG-002','MCH-003',NULL,'2026-07-21 15:33:12','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `production_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `production_efficiency`
--

DROP TABLE IF EXISTS `production_efficiency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_efficiency` (
  `EfficiencyID` varchar(50) NOT NULL,
  `Date` date NOT NULL,
  `Shift` varchar(50) DEFAULT NULL,
  `MachineID` varchar(50) DEFAULT NULL,
  `PlannedRunTime` decimal(10,2) DEFAULT 0.00,
  `ActualRunTime` decimal(10,2) DEFAULT 0.00,
  `DowntimeMinutes` decimal(10,2) DEFAULT 0.00,
  `TotalProduced` int(11) DEFAULT 0,
  `GoodProduced` int(11) DEFAULT 0,
  `DefectCount` int(11) DEFAULT 0,
  `AvailabilityRate` decimal(5,2) DEFAULT 0.00,
  `PerformanceRate` decimal(5,2) DEFAULT 0.00,
  `QualityRate` decimal(5,2) DEFAULT 0.00,
  `OEE` decimal(5,2) DEFAULT 0.00,
  `Notes` text DEFAULT NULL,
  `recordedBy` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`EfficiencyID`),
  KEY `MachineID` (`MachineID`),
  KEY `recordedBy` (`recordedBy`),
  KEY `idx_pe_date` (`Date`),
  CONSTRAINT `production_efficiency_ibfk_1` FOREIGN KEY (`MachineID`) REFERENCES `machines` (`MachineID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `production_efficiency_ibfk_2` FOREIGN KEY (`recordedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_efficiency`
--

LOCK TABLES `production_efficiency` WRITE;
/*!40000 ALTER TABLE `production_efficiency` DISABLE KEYS */;
INSERT INTO `production_efficiency` VALUES ('EFF-001','2025-07-01','Morning','MCH-002',480.00,450.00,30.00,12000,11760,240,93.75,97.78,98.00,89.70,NULL,'USR-003','2026-07-21 15:33:12'),('EFF-002','2025-07-01','Morning','MCH-004',480.00,460.00,20.00,15000,14850,150,95.83,100.00,99.00,94.87,NULL,'USR-003','2026-07-21 15:33:12'),('EFF-003','2025-07-02','Morning','MCH-002',480.00,440.00,40.00,11000,10780,220,91.67,95.65,98.00,85.97,NULL,'USR-003','2026-07-21 15:33:12'),('EFF-004','2025-07-03','Morning','MCH-003',480.00,470.00,10.00,16000,15840,160,97.92,100.00,99.00,96.94,NULL,'USR-003','2026-07-21 15:33:12'),('EFF-005','2025-07-08','Morning','MCH-002',480.00,400.00,80.00,10000,9500,500,83.33,95.24,95.00,75.40,NULL,'USR-003','2026-07-21 15:33:12'),('EFF-006','2025-07-08','Afternoon','MCH-004',480.00,420.00,60.00,13500,13095,405,87.50,98.57,97.00,83.63,NULL,'USR-003','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `production_efficiency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quality_inspections`
--

DROP TABLE IF EXISTS `quality_inspections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quality_inspections` (
  `InspectionID` varchar(50) NOT NULL,
  `InspectionType` enum('Incoming','In-Process','Finished') NOT NULL,
  `BatchID` varchar(50) DEFAULT NULL,
  `InspectionDate` date NOT NULL,
  `Result` enum('Pass','Fail','Pending') DEFAULT 'Pending',
  `DefectsFound` text DEFAULT NULL,
  `TestResults` text DEFAULT NULL,
  `CAPA` text DEFAULT NULL,
  `InspectorID` varchar(50) DEFAULT NULL,
  `Status` enum('Open','In Progress','Closed') DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`InspectionID`),
  KEY `InspectorID` (`InspectorID`),
  KEY `idx_qi_type` (`InspectionType`),
  KEY `idx_qi_result` (`Result`),
  KEY `idx_qi_batch` (`BatchID`),
  CONSTRAINT `quality_inspections_ibfk_1` FOREIGN KEY (`BatchID`) REFERENCES `production_batches` (`BatchID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `quality_inspections_ibfk_2` FOREIGN KEY (`InspectorID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quality_inspections`
--

LOCK TABLES `quality_inspections` WRITE;
/*!40000 ALTER TABLE `quality_inspections` DISABLE KEYS */;
INSERT INTO `quality_inspections` VALUES ('QI-001','Incoming','BAT-001','2025-07-01','Pass',NULL,'pH: 3.8, Brix: 12.5','None required','USR-005','Closed','2026-07-21 15:33:12'),('QI-002','In-Process','BAT-001','2025-07-01','Pass',NULL,'Temperature: 4┬░C, Brix: 13.0','None required','USR-005','Closed','2026-07-21 15:33:12'),('QI-003','Finished','BAT-001','2025-07-02','Pass',NULL,'pH: 3.9, Brix: 12.8, Micro: PASS','None required','USR-005','Closed','2026-07-21 15:33:12'),('QI-004','Incoming','BAT-002','2025-07-03','Pass',NULL,'pH: 3.5, Brix: 11.8','None required','USR-005','Closed','2026-07-21 15:33:12'),('QI-005','Finished','BAT-002','2025-07-04','Pass',NULL,'pH: 3.6, Brix: 12.0, Micro: PASS','None required','USR-005','Closed','2026-07-21 15:33:12'),('QI-006','Incoming','BAT-003','2025-07-05','Pass',NULL,'pH: 3.2, Brix: 13.5','None required','USR-005','Closed','2026-07-21 15:33:12'),('QI-007','Finished','BAT-003','2025-07-06','Pass',NULL,'pH: 3.3, Brix: 13.2, Micro: PASS','None required','USR-005','Closed','2026-07-21 15:33:12'),('QI-008','Incoming','BAT-004','2025-07-08','Fail','Mold detected in 2 samples','Micro: FAIL','Supplier notified, batch quarantined','USR-005','Open','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `quality_inspections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `raw_materials`
--

DROP TABLE IF EXISTS `raw_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raw_materials` (
  `MaterialID` varchar(50) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Unit` varchar(30) DEFAULT 'kg',
  `CurrentStock` decimal(10,2) DEFAULT 0.00,
  `MinStock` decimal(10,2) DEFAULT 0.00,
  `SupplierID` varchar(50) DEFAULT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`MaterialID`),
  KEY `SupplierID` (`SupplierID`),
  KEY `idx_rm_type` (`Type`),
  CONSTRAINT `raw_materials_ibfk_1` FOREIGN KEY (`SupplierID`) REFERENCES `suppliers` (`SupplierID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `raw_materials`
--

LOCK TABLES `raw_materials` WRITE;
/*!40000 ALTER TABLE `raw_materials` DISABLE KEYS */;
INSERT INTO `raw_materials` VALUES ('RM-001','Fresh Mangoes','Fruit','kg',1200.00,200.00,'SUP-001','Active','2026-07-21 15:33:12'),('RM-002','Fresh Oranges','Fruit','kg',1500.00,200.00,'SUP-001','Active','2026-07-21 15:33:12'),('RM-003','Organic Pineapples','Fruit','kg',800.00,150.00,'SUP-005','Active','2026-07-21 15:33:12'),('RM-004','Fresh Strawberries','Fruit','kg',400.00,100.00,'SUP-001','Active','2026-07-21 15:33:12'),('RM-005','Cane Sugar','Sweetener','kg',600.00,100.00,'SUP-002','Active','2026-07-21 15:33:12'),('RM-006','Citric Acid','Additive','kg',50.00,10.00,'SUP-002','Active','2026-07-21 15:33:12'),('RM-007','Ascorbic Acid (Vitamin C)','Additive','kg',30.00,5.00,'SUP-002','Active','2026-07-21 15:33:12'),('RM-008','Natural Flavour Enhancer','Additive','kg',20.00,5.00,'SUP-002','Active','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `raw_materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `RoleID` varchar(50) NOT NULL,
  `RoleName` varchar(50) NOT NULL,
  PRIMARY KEY (`RoleID`),
  UNIQUE KEY `RoleName` (`RoleName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES ('ROLE-007','Accountant'),('ROLE-002','Factory Manager'),('ROLE-004','Inventory Officer'),('ROLE-008','Maintenance Engineer'),('ROLE-003','Production Supervisor'),('ROLE-005','QA/QC Officer'),('ROLE-006','Sales Officer'),('ROLE-001','System Administrator');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `safety_inspections`
--

DROP TABLE IF EXISTS `safety_inspections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `safety_inspections` (
  `SafetyID` varchar(50) NOT NULL,
  `InspectionDate` date NOT NULL,
  `InspectionType` varchar(100) NOT NULL,
  `Area` varchar(150) NOT NULL,
  `Findings` text DEFAULT NULL,
  `HazardLevel` enum('Low','Medium','High','Critical') DEFAULT 'Low',
  `CorrectiveAction` text DEFAULT NULL,
  `ResponsiblePerson` varchar(50) DEFAULT NULL,
  `TargetDate` date DEFAULT NULL,
  `Status` enum('Open','In Progress','Closed') DEFAULT 'Open',
  `InspectorID` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`SafetyID`),
  KEY `InspectorID` (`InspectorID`),
  KEY `idx_si_date` (`InspectionDate`),
  KEY `idx_si_level` (`HazardLevel`),
  CONSTRAINT `safety_inspections_ibfk_1` FOREIGN KEY (`InspectorID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `safety_inspections`
--

LOCK TABLES `safety_inspections` WRITE;
/*!40000 ALTER TABLE `safety_inspections` DISABLE KEYS */;
INSERT INTO `safety_inspections` VALUES ('SAF-001','2025-07-01','Machine Guarding','Production Floor A','All guards in place, 2 loose bolts on Juicer #1','Medium','Tighten bolts and log','USR-008','2025-07-05','Closed','USR-005','2026-07-21 15:33:12'),('SAF-002','2025-07-03','PPE Compliance','Packaging Area','3 workers without safety goggles on labeling line','High','Immediate PPE enforcement, retraining scheduled','USR-002','2025-07-10','In Progress','USR-005','2026-07-21 15:33:12'),('SAF-003','2025-07-05','Emergency Systems','Warehouse','Fire extinguisher in Cold Storage missing inspection tag','Low','Replace extinguisher and tag','USR-008','2025-07-12','Open','USR-005','2026-07-21 15:33:12'),('SAF-004','2025-07-08','Chemical Handling','Mixing Area','Improper storage of cleaning chemicals','Critical','Reorganize chemical storage with proper segregation','USR-002','2025-07-09','Open','USR-005','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `safety_inspections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_orders`
--

DROP TABLE IF EXISTS `sales_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales_orders` (
  `OrderID` varchar(50) NOT NULL,
  `OrderDate` date NOT NULL,
  `TotalAmount` decimal(15,2) DEFAULT 0.00,
  `Quantity` decimal(10,2) DEFAULT 0.00,
  `Status` enum('Pending','Processing','Completed','Cancelled') DEFAULT 'Pending',
  `CustomerID` varchar(50) DEFAULT NULL,
  `FG_ID` varchar(50) DEFAULT NULL,
  `CreatedBy` varchar(50) DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`OrderID`),
  KEY `CustomerID` (`CustomerID`),
  KEY `FG_ID` (`FG_ID`),
  KEY `CreatedBy` (`CreatedBy`),
  KEY `idx_so_date` (`OrderDate`),
  KEY `idx_so_status` (`Status`),
  CONSTRAINT `sales_orders_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`CustomerID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sales_orders_ibfk_2` FOREIGN KEY (`FG_ID`) REFERENCES `finished_goods` (`FG_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sales_orders_ibfk_3` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_orders`
--

LOCK TABLES `sales_orders` WRITE;
/*!40000 ALTER TABLE `sales_orders` DISABLE KEYS */;
INSERT INTO `sales_orders` VALUES ('ORD-001','2025-07-05',12500.00,200.00,'Completed','CUS-001','FG-001','USR-006',NULL,'2026-07-21 15:33:12'),('ORD-002','2025-07-07',18000.00,300.00,'Completed','CUS-002','FG-002','USR-006',NULL,'2026-07-21 15:33:12'),('ORD-003','2025-07-09',5000.00,100.00,'Completed','CUS-003','FG-001','USR-006','','2026-07-21 15:33:12'),('ORD-004','2025-07-10',22500.00,500.00,'Completed','CUS-004','FG-002','USR-006','','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `sales_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shifts` (
  `ShiftID` varchar(50) NOT NULL,
  `ShiftName` varchar(50) NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `Description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ShiftID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES ('SHF-001','Morning','06:00:00','14:00:00','Morning production shift'),('SHF-002','Afternoon','14:00:00','22:00:00','Afternoon production shift'),('SHF-003','Night','22:00:00','06:00:00','Night production shift');
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sop_checklists`
--

DROP TABLE IF EXISTS `sop_checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sop_checklists` (
  `ChecklistID` varchar(50) NOT NULL,
  `SOP_ID` varchar(50) NOT NULL,
  `BatchID` varchar(50) DEFAULT NULL,
  `Date` date NOT NULL,
  `ChecklistItems` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ChecklistItems`)),
  `CompletedItems` int(11) DEFAULT 0,
  `TotalItems` int(11) DEFAULT 0,
  `SupervisorID` varchar(50) DEFAULT NULL,
  `ApprovalStatus` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `ApprovedAt` datetime DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ChecklistID`),
  KEY `SOP_ID` (`SOP_ID`),
  KEY `BatchID` (`BatchID`),
  KEY `SupervisorID` (`SupervisorID`),
  KEY `idx_sc_date` (`Date`),
  CONSTRAINT `sop_checklists_ibfk_1` FOREIGN KEY (`SOP_ID`) REFERENCES `sop_templates` (`SOP_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sop_checklists_ibfk_2` FOREIGN KEY (`BatchID`) REFERENCES `production_batches` (`BatchID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sop_checklists_ibfk_3` FOREIGN KEY (`SupervisorID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sop_checklists`
--

LOCK TABLES `sop_checklists` WRITE;
/*!40000 ALTER TABLE `sop_checklists` DISABLE KEYS */;
INSERT INTO `sop_checklists` VALUES ('SC-001','SOP-002','BAT-001','2025-07-01','{\"sanitized\":\"yes\",\"calibrated\":\"yes\",\"first_check\":\"pass\",\"temp_monitored\":\"yes\",\"docs_complete\":\"yes\"}',5,5,'USR-003','Approved',NULL,'All steps completed successfully','2026-07-21 15:33:12'),('SC-002','SOP-003','BAT-001','2025-07-02','{\"bottle_clean\":\"yes\",\"filled\":\"yes\",\"capped\":\"yes\",\"labeled\":\"yes\",\"coded\":\"yes\",\"shrinkwrap\":\"yes\",\"carton\":\"yes\",\"spot_check\":\"pass\"}',8,8,'USR-003','Approved',NULL,'Perfect execution','2026-07-21 15:33:12'),('SC-003','SOP-002','BAT-002','2025-07-03','{\"sanitized\":\"yes\",\"calibrated\":\"yes\",\"first_check\":\"pass\",\"temp_monitored\":\"yes\",\"docs_complete\":\"yes\"}',5,5,'USR-003','Approved',NULL,'Completed per SOP','2026-07-21 15:33:12'),('SC-004','SOP-001','BAT-004','2025-07-08','{\"delivery_note\":\"yes\",\"visual_inspection\":\"fail\",\"temp_check\":\"pass\",\"weight\":\"pass\",\"sampling\":\"yes\",\"decision\":\"reject\"}',5,6,'USR-003','Approved',NULL,'Visual inspection failed - mold on strawberries','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `sop_checklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sop_templates`
--

DROP TABLE IF EXISTS `sop_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sop_templates` (
  `SOP_ID` varchar(50) NOT NULL,
  `Title` varchar(200) NOT NULL,
  `Department` varchar(50) DEFAULT NULL,
  `Version` varchar(20) DEFAULT '1.0',
  `Content` text DEFAULT NULL,
  `EffectiveDate` date DEFAULT NULL,
  `ReviewDate` date DEFAULT NULL,
  `Status` enum('Active','Under Review','Archived') DEFAULT 'Active',
  `CreatedBy` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`SOP_ID`),
  KEY `CreatedBy` (`CreatedBy`),
  CONSTRAINT `sop_templates_ibfk_1` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sop_templates`
--

LOCK TABLES `sop_templates` WRITE;
/*!40000 ALTER TABLE `sop_templates` DISABLE KEYS */;
INSERT INTO `sop_templates` VALUES ('SOP-001','Fruit Receiving & Inspection SOP','Quality Assurance','2.0','1. Verify delivery note\n2. Visual inspection of fruit quality\n3. Temperature check (must be <8┬░C for berries)\n4. Weight verification\n5. Sample collection for lab testing\n6. Accept or reject with documentation','2024-06-01','2025-06-01','Active','USR-005','2026-07-21 15:33:12'),('SOP-002','Juice Production Line SOP','Production','3.0','1. Sanitize all equipment\n2. Calibrate juicer settings\n3. First batch quality check\n4. Monitor Brix levels throughout\n5. Record temperatures every 30 mins\n6. Batch completion documentation','2024-06-01','2025-06-01','Active','USR-003','2026-07-21 15:33:12'),('SOP-003','Bottling & Packaging SOP','Production','2.0','1. Clean bottles with UV\n2. Fill to specified volume\n3. Cap and seal verification\n4. Label application check\n5. Code date printing\n6. Shrink wrap application\n7. Carton packing\n8. Quality spot check','2024-07-01','2025-07-01','Active','USR-003','2026-07-21 15:33:12'),('SOP-004','Cleaning & Sanitation SOP','Quality Assurance','1.5','1. Pre-rinse all surfaces\n2. Apply food-grade sanitizer\n3. Contact time: 15 minutes\n4. Final rinse with potable water\n5. Visual and ATP verification\n6. Log completion','2024-06-01','2025-06-01','Active','USR-005','2026-07-21 15:33:12'),('SOP-005','Emergency Recall SOP','Management','1.0','1. Stop production immediately\n2. Quarantine all affected products\n3. Notify management team\n4. Contact regulatory authorities\n5. Initiate customer notifications\n6. Document all actions\n7. Root cause analysis','2024-01-01','2025-01-01','Active','USR-002','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `sop_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff` (
  `StaffID` varchar(50) NOT NULL,
  `UserID` varchar(50) DEFAULT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Phone` varchar(30) DEFAULT NULL,
  `Department` varchar(50) DEFAULT NULL,
  `Position` varchar(100) DEFAULT NULL,
  `DateHired` date DEFAULT NULL,
  `Status` enum('Active','On Leave','Terminated') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`StaffID`),
  KEY `UserID` (`UserID`),
  KEY `idx_staff_dept` (`Department`),
  CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES ('STF-001','USR-001','Kwame','Admin','kwame@freshjuice.com','0241234567','Administration','System Admin','2024-01-15','Active','2026-07-21 15:33:12'),('STF-002','USR-002','Ama','Manager','ama@freshjuice.com','0242345678','Management','Factory Manager','2024-01-15','Active','2026-07-21 15:33:12'),('STF-003','USR-003','Kofi','Production','kofi@freshjuice.com','0243456789','Production','Production Supervisor','2024-02-01','Active','2026-07-21 15:33:12'),('STF-004','USR-004','Akosua','Inventory','akosua@freshjuice.com','0244567890','Inventory','Inventory Officer','2024-02-01','Active','2026-07-21 15:33:12'),('STF-005','USR-005','Yaw','QA','yaw@freshjuice.com','0245678901','Quality Assurance','QA/QC Officer','2024-02-15','Active','2026-07-21 15:33:12'),('STF-006','USR-006','Esi','Sales','esi@freshjuice.com','0246789012','Sales','Sales Officer','2024-03-01','Active','2026-07-21 15:33:12'),('STF-007','USR-007','Kojo','Accountant','kojo@freshjuice.com','0247890123','Finance','Accountant','2024-03-01','Active','2026-07-21 15:33:12'),('STF-008','USR-008','Nana','Maintenance','nana@freshjuice.com','0248901234','Maintenance','Maintenance Engineer','2024-03-15','Active','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_deliveries`
--

DROP TABLE IF EXISTS `supplier_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_deliveries` (
  `DeliveryID` varchar(50) NOT NULL,
  `SupplierID` varchar(50) NOT NULL,
  `DeliveryDate` date NOT NULL,
  `ItemName` varchar(150) NOT NULL,
  `Quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Unit` varchar(30) DEFAULT 'kg',
  `QualityGrade` varchar(50) DEFAULT 'Grade A',
  `ReceivedBy` varchar(50) DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `Status` enum('Received','Pending','Rejected') DEFAULT 'Received',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`DeliveryID`),
  KEY `SupplierID` (`SupplierID`),
  KEY `ReceivedBy` (`ReceivedBy`),
  KEY `idx_del_date` (`DeliveryDate`),
  CONSTRAINT `supplier_deliveries_ibfk_1` FOREIGN KEY (`SupplierID`) REFERENCES `suppliers` (`SupplierID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `supplier_deliveries_ibfk_2` FOREIGN KEY (`ReceivedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_deliveries`
--

LOCK TABLES `supplier_deliveries` WRITE;
/*!40000 ALTER TABLE `supplier_deliveries` DISABLE KEYS */;
INSERT INTO `supplier_deliveries` VALUES ('DLV-001','SUP-001','2025-07-01','Fresh Mangoes',500.00,'kg','Grade A','USR-004',NULL,'Received','2026-07-21 15:33:12'),('DLV-002','SUP-001','2025-07-03','Fresh Oranges',800.00,'kg','Grade A','USR-004',NULL,'Received','2026-07-21 15:33:12'),('DLV-003','SUP-002','2025-07-05','Cane Sugar',200.00,'kg','Premium','USR-004',NULL,'Received','2026-07-21 15:33:12'),('DLV-004','SUP-005','2025-07-07','Organic Pineapples',300.00,'kg','Organic','USR-004',NULL,'Received','2026-07-21 15:33:12'),('DLV-005','SUP-001','2025-07-09','Fresh Strawberries',150.00,'kg','Grade B','USR-004',NULL,'Received','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `supplier_deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_evaluations`
--

DROP TABLE IF EXISTS `supplier_evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_evaluations` (
  `EvaluationID` varchar(50) NOT NULL,
  `SupplierID` varchar(50) NOT NULL,
  `EvaluationDate` date NOT NULL,
  `QualityScore` decimal(3,1) DEFAULT 0.0,
  `DeliveryScore` decimal(3,1) DEFAULT 0.0,
  `PriceScore` decimal(3,1) DEFAULT 0.0,
  `OverallScore` decimal(3,1) DEFAULT 0.0,
  `Strengths` text DEFAULT NULL,
  `Weaknesses` text DEFAULT NULL,
  `Recommendations` text DEFAULT NULL,
  `EvaluatedBy` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`EvaluationID`),
  KEY `EvaluatedBy` (`EvaluatedBy`),
  KEY `idx_se_supplier` (`SupplierID`),
  CONSTRAINT `supplier_evaluations_ibfk_1` FOREIGN KEY (`SupplierID`) REFERENCES `suppliers` (`SupplierID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `supplier_evaluations_ibfk_2` FOREIGN KEY (`EvaluatedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_evaluations`
--

LOCK TABLES `supplier_evaluations` WRITE;
/*!40000 ALTER TABLE `supplier_evaluations` DISABLE KEYS */;
INSERT INTO `supplier_evaluations` VALUES ('SEV-001','SUP-001','2025-06-30',4.5,4.0,3.5,4.0,'Consistently high quality fruit, good communication','Premium pricing, occasional delayed shipments','Negotiate volume discount, improve delivery scheduling','USR-004','2026-07-21 15:33:12'),('SEV-002','SUP-002','2025-06-30',5.0,5.0,4.0,4.7,'Excellent quality sugar, always on time','Higher than market average price','Consider long-term contract for better pricing','USR-004','2026-07-21 15:33:12'),('SEV-003','SUP-003','2025-06-30',3.5,3.0,4.0,3.5,'Competitive pricing, wide product range','Inconsistent delivery, packaging quality varies','Establish clear quality checklist, penalize late deliveries','USR-004','2026-07-21 15:33:12'),('SEV-004','SUP-005','2025-06-30',5.0,4.5,3.0,4.2,'Premium organic quality, good traceability','Expensive, limited seasonal availability','Plan seasonal contracts, explore partial substitutions','USR-004','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `supplier_evaluations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `SupplierID` varchar(50) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Contact` varchar(100) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Phone` varchar(30) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `Type` varchar(50) DEFAULT 'Fruit Supplier',
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`SupplierID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES ('SUP-001','Tropical Fruits Ltd','John Doe','info@tropicalfruits.com','0301234567','','Fruit Supplier','Active','2026-07-21 15:33:12'),('SUP-002','Sweet Sugar Corp','Jane Smith','sales@sweetsugar.com','0302345678',NULL,'Ingredient Supplier','Active','2026-07-21 15:33:12'),('SUP-003','ClearPack Solutions','Mike Brown','orders@clearpack.com','0303456789',NULL,'Packaging Supplier','Active','2026-07-21 15:33:12'),('SUP-004','AquaPure Water','Sara Wilson','contact@aquapure.com','0304567890',NULL,'Water Supplier','Active','2026-07-21 15:33:12'),('SUP-005','GreenLeaf Organics','Tom Green','hello@greenleaf.com','0305678901',NULL,'Organic Fruit Supplier','Active','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_records`
--

DROP TABLE IF EXISTS `training_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_records` (
  `TrainingID` varchar(50) NOT NULL,
  `StaffID` varchar(50) NOT NULL,
  `TrainingType` varchar(150) NOT NULL,
  `TrainingDate` date NOT NULL,
  `Duration` varchar(50) DEFAULT NULL,
  `Trainer` varchar(150) DEFAULT NULL,
  `CertificateNo` varchar(100) DEFAULT NULL,
  `ExpiryDate` date DEFAULT NULL,
  `Status` enum('Scheduled','Completed','Failed','Cancelled') DEFAULT 'Scheduled',
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`TrainingID`),
  KEY `idx_tr_date` (`TrainingDate`),
  KEY `idx_tr_staff` (`StaffID`),
  CONSTRAINT `training_records_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`StaffID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_records`
--

LOCK TABLES `training_records` WRITE;
/*!40000 ALTER TABLE `training_records` DISABLE KEYS */;
INSERT INTO `training_records` VALUES ('TRN-001','STF-003','Food Safety & Hygiene','2025-06-15','2 days','Dr. Mensah','FSH-2025-003','2026-06-15','Completed',NULL,'2026-07-21 15:33:12'),('TRN-002','STF-004','Inventory Management','2025-06-20','1 day','Mr. Osei','IM-2025-012','2025-12-20','Completed',NULL,'2026-07-21 15:33:12'),('TRN-003','STF-005','HACCP Refresher','2025-06-25','1 day','Dr. Mensah','HACCP-2025-008','2026-06-25','Completed',NULL,'2026-07-21 15:33:12'),('TRN-004','STF-003','Machine Operation - Juicer','2025-07-05','3 days','MCH-002 Manual','MOJ-2025-001','2026-01-05','Completed',NULL,'2026-07-21 15:33:12'),('TRN-005','STF-006','Customer Service','2025-07-15','1 day','External Trainer',NULL,NULL,'Scheduled',NULL,'2026-07-21 15:33:12'),('TRN-006','STF-003','Emergency Response','2025-07-20','0.5 day','Fire Service',NULL,NULL,'Scheduled',NULL,'2026-07-21 15:33:12');
/*!40000 ALTER TABLE `training_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `UserID` varchar(50) NOT NULL,
  `RoleID` varchar(50) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  KEY `RoleID` (`RoleID`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`RoleID`) REFERENCES `roles` (`RoleID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('USR-001','ROLE-001','Kwame Admin','$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK','Active','2026-07-24 15:50:00','2026-07-24 15:50:23',NULL,NULL),('USR-002','ROLE-002','Ama Manager','$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK','Active','2026-07-24 15:50:00','2026-07-24 15:50:23',NULL,NULL),('USR-003','ROLE-003','Kofi Production','$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK','Active','2026-07-24 15:50:00','2026-07-24 15:50:23',NULL,NULL),('USR-004','ROLE-004','Akosua Inventory','$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK','Active','2026-07-24 15:50:00','2026-07-24 15:50:23',NULL,NULL),('USR-005','ROLE-005','Yaw QA','$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK','Active','2026-07-24 15:50:00','2026-07-24 15:50:23',NULL,NULL),('USR-006','ROLE-006','Esi Sales','$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK','Active','2026-07-24 15:50:00','2026-07-24 15:50:23',NULL,NULL),('USR-007','ROLE-007','Kojo Accountant','$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK','Active','2026-07-24 15:50:00','2026-07-24 15:50:23',NULL,NULL),('USR-008','ROLE-008','Nana Maintenance','$2y$10$nEizFLqRNolHOmidlgMOV.UOUrREJxxiaKpXgIrboR1MPG78oasPK','Active','2026-07-24 15:50:00','2026-07-24 15:50:23',NULL,NULL),('USR-222','ROLE-006','Collin Apau','$2y$10$n9K9RUe.9mbZEbl5EQTVWe1CemlXxcIJ.GnhAhDZuEk3BAB6Oy8r6','Active','2026-08-02 16:32:33','2026-08-02 16:32:33',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `waste_records`
--

DROP TABLE IF EXISTS `waste_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `waste_records` (
  `WasteID` varchar(50) NOT NULL,
  `Date` date NOT NULL,
  `WasteType` varchar(50) DEFAULT 'Production',
  `Quantity` decimal(10,2) DEFAULT 0.00,
  `Unit` varchar(30) DEFAULT 'kg',
  `DisposalMethod` varchar(100) DEFAULT 'Landfill',
  `BatchID` varchar(50) DEFAULT NULL,
  `EnvironmentalImpact` text DEFAULT NULL,
  `RecordedBy` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`WasteID`),
  KEY `BatchID` (`BatchID`),
  KEY `RecordedBy` (`RecordedBy`),
  KEY `idx_waste_date` (`Date`),
  CONSTRAINT `waste_records_ibfk_1` FOREIGN KEY (`BatchID`) REFERENCES `production_batches` (`BatchID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `waste_records_ibfk_2` FOREIGN KEY (`RecordedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `waste_records`
--

LOCK TABLES `waste_records` WRITE;
/*!40000 ALTER TABLE `waste_records` DISABLE KEYS */;
INSERT INTO `waste_records` VALUES ('WST-001','2025-07-01','Production',25.50,'kg','Composting','BAT-001','Minimal - organic waste composted','USR-003','2026-07-21 15:33:12'),('WST-002','2025-07-01','Packaging',5.00,'kg','Recycling','BAT-001','None - materials recycled','USR-003','2026-07-21 15:33:12'),('WST-003','2025-07-03','Production',30.00,'kg','Composting','BAT-002','Minimal - organic waste composted','USR-003','2026-07-21 15:33:12'),('WST-004','2025-07-05','Spoilage',45.00,'kg','Licensed Disposal','BAT-003','Moderate - disposed per EPA guidelines','USR-004','2026-07-21 15:33:12'),('WST-005','2025-07-08','Production',15.00,'kg','Composting','BAT-004','Minimal','USR-003','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `waste_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `water_quality_tests`
--

DROP TABLE IF EXISTS `water_quality_tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `water_quality_tests` (
  `WaterTestID` varchar(50) NOT NULL,
  `TestDate` date NOT NULL,
  `TestType` varchar(100) NOT NULL,
  `pH_Level` decimal(4,2) DEFAULT NULL,
  `Turbidity` decimal(8,2) DEFAULT NULL,
  `TDS` decimal(8,2) DEFAULT NULL,
  `Chlorine` decimal(6,2) DEFAULT NULL,
  `BacteriaCount` decimal(10,2) DEFAULT NULL,
  `Result` enum('Pass','Fail','Pending') DEFAULT 'Pending',
  `Notes` text DEFAULT NULL,
  `TestedBy` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`WaterTestID`),
  KEY `TestedBy` (`TestedBy`),
  KEY `idx_wqt_date` (`TestDate`),
  CONSTRAINT `water_quality_tests_ibfk_1` FOREIGN KEY (`TestedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `water_quality_tests`
--

LOCK TABLES `water_quality_tests` WRITE;
/*!40000 ALTER TABLE `water_quality_tests` DISABLE KEYS */;
INSERT INTO `water_quality_tests` VALUES ('WQT-001','2025-07-01','Daily Check',7.20,0.50,120.00,0.50,0.00,'Pass','Normal readings','USR-005','2026-07-21 15:33:12'),('WQT-002','2025-07-03','Daily Check',7.15,0.45,118.00,0.48,0.00,'Pass','Normal readings','USR-005','2026-07-21 15:33:12'),('WQT-003','2025-07-05','Weekly Full',7.10,0.30,115.00,0.50,0.00,'Pass','Full panel test - all within limits','USR-005','2026-07-21 15:33:12'),('WQT-004','2025-07-08','Daily Check',7.25,0.55,125.00,0.52,0.00,'Pass','Slight increase in TDS, monitor','USR-005','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `water_quality_tests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `water_usage`
--

DROP TABLE IF EXISTS `water_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `water_usage` (
  `WaterUsageID` varchar(50) NOT NULL,
  `Date` date NOT NULL,
  `UsageType` varchar(50) NOT NULL,
  `Quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Unit` varchar(30) DEFAULT 'litres',
  `Purpose` varchar(150) DEFAULT NULL,
  `RecordedBy` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`WaterUsageID`),
  KEY `RecordedBy` (`RecordedBy`),
  KEY `idx_wu_date` (`Date`),
  CONSTRAINT `water_usage_ibfk_1` FOREIGN KEY (`RecordedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `water_usage`
--

LOCK TABLES `water_usage` WRITE;
/*!40000 ALTER TABLE `water_usage` DISABLE KEYS */;
INSERT INTO `water_usage` VALUES ('WU-001','2025-07-01','Washing',2000.00,'litres','Fruit washing and preparation','USR-003','2026-07-21 15:33:12'),('WU-002','2025-07-01','Mixing',1500.00,'litres','Juice mixing and dilution','USR-003','2026-07-21 15:33:12'),('WU-003','2025-07-01','Cleaning',3000.00,'litres','Equipment and floor cleaning','USR-003','2026-07-21 15:33:12'),('WU-004','2025-07-02','Washing',1800.00,'litres','Fruit washing','USR-003','2026-07-21 15:33:12'),('WU-005','2025-07-02','Mixing',2000.00,'litres','Juice production','USR-003','2026-07-21 15:33:12'),('WU-006','2025-07-03','Washing',2200.00,'litres','Fruit washing','USR-003','2026-07-21 15:33:12'),('WU-007','2025-07-03','Cleaning',4000.00,'litres','Deep clean day','USR-003','2026-07-21 15:33:12'),('WU-008','2025-07-08','Washing',1500.00,'litres','Fruit washing','USR-003','2026-07-21 15:33:12');
/*!40000 ALTER TABLE `water_usage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'freshjuice'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-24 10:37:55
