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
-- Dumping data for table `client_links`
--

LOCK TABLES `client_links` WRITE;
/*!40000 ALTER TABLE `client_links` DISABLE KEYS */;
INSERT INTO `client_links` (`id`, `name`, `last_name`, `mother_last_name`, `email`, `phone`, `nickname`, `address`, `rfc`, `profession`, `degree`, `active`, `user_id`, `client_id`, `created_at`, `updated_at`) VALUES (1,'omar','cruz','martinez','info@cruzysantizo.com','2222324472','lic. omar cruz','calle 31 poniente número 3318, interior 402, col. santa cruz los Ángeles, c. p. 72400 , puebla, pue.','cumo810811tm7','licenciado en derecho','licenciatura',0,12,15,'2024-04-18 22:03:34','2024-04-18 22:03:34'),(2,'jesus','reyes','mellado','juridico.housing@gmail.com','2221496020','lic. jesus','calle 13poniente número 214, col. ampliación granada, c.p. 72160, puebla, pue.','remj890805rs2','licenciado en derecho','licenciatura',0,12,2,'2024-04-19 17:16:40','2024-04-19 17:16:40'),(3,'carolina','roa','huerta','carolina.roa@tipmexico','5550937200','lic. carolina roa','','hurc0000000','','',0,12,26,'2024-04-24 18:28:53','2024-04-24 18:28:53');
/*!40000 ALTER TABLE `client_links` ENABLE KEYS */;
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

-- Dump completed on 2024-06-02 19:21:43
