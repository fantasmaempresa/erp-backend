-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: erp_backup
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` (`id`, `name`, `description`, `quote`, `config`, `created_at`, `updated_at`) VALUES (34,'tabla de subdiviciÓn anexo a','tabla de subdiviciÓn anexo a',0.00,NULL,'2024-04-08 15:24:14','2024-04-08 15:24:14'),(35,'extracto de defuncion','extracto de defuncion',0.00,NULL,'2024-04-12 17:29:59','2024-04-12 17:29:59'),(36,'boleta de registro','boleta de registro',0.00,NULL,'2024-04-12 18:40:24','2024-04-12 18:40:24'),(37,'contrato de promesa de compraventa','contrato de promesa de compraventa',0.00,NULL,'2024-04-16 22:59:34','2024-04-16 22:59:34'),(38,'acta de asamblea','acta de asamblea',0.00,NULL,'2024-04-16 23:36:29','2024-04-16 23:36:29'),(39,'proyecto final acta notarial de concubinato','acta notarial de concubinato',0.00,NULL,'2024-04-24 18:14:33','2024-04-24 18:14:33'),(40,'estatutos','estatutos',0.00,NULL,'2024-04-25 23:08:16','2024-04-25 23:08:16'),(41,'aviso de uso','aviso de uso',0.00,NULL,'2024-04-25 23:08:49','2024-04-25 23:08:49'),(42,'uipe','uipe',0.00,NULL,'2024-04-25 23:10:42','2024-04-25 23:10:42'),(43,'registro siger','registro siger',0.00,NULL,'2024-04-25 23:11:28','2024-04-25 23:11:28'),(44,'documento a ratificar','ratificacion',0.00,NULL,'2024-04-26 23:36:37','2024-04-26 23:36:37');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'erp_backup'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-06-02 19:47:22
