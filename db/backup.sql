-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: db_cllean
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.24.04.1

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
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `address` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `service_type` varchar(50) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  `status` varchar(50) DEFAULT 'Новая заявка',
  `cancel_reason` text,
  `cancel_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'shved','Shved','89639078683','2025-06-06','12:00:00','Услуга1','Карта','2025-06-05 07:26:00',0,'Новая заявка',NULL,NULL),(2,'ddd','sg','1','2025-06-06','12:00:00','Услуга1','Карта','2025-06-05 07:26:02',0,'Новая заявка',NULL,NULL),(3,'ыы','ыы','ыы','2025-05-30','12:00:00','Услуга2','Карта','2025-06-05 07:37:11',0,'Новая заявка',NULL,NULL),(4,'ыы','ыы','ыы','2025-05-30','12:00:00','Услуга2','Карта','2025-06-05 07:37:53',0,'Новая заявка',NULL,NULL),(5,'ыы','ыы','ыы','2025-05-30','12:00:00','Услуга2','Карта','2025-06-05 07:39:02',0,'Новая заявка',NULL,NULL),(7,'Уфа','Алексей','89639078683','2025-08-03','12:00:00','Генеральная уборка','Наличные','2025-06-08 10:05:17',1,'Отменено',NULL,'Не ответил на звонок'),(8,'Улица пушкина','Дамир','89639078688','2025-08-03','15:00:00','Химчистка ковров и мебели','Карта','2025-06-08 12:59:31',1,'Принято',NULL,NULL),(9,'Уфа, улица Бакалейная, дом 65/2, кв 55','Игнат','89656557585','2025-06-13','11:00:00','Генеральная уборка','Наличные','2025-06-08 13:03:29',3,'Новая заявка',NULL,NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'shved','$2y$10$lSA090GW7HRQT0X2rO9dAeh91Q4NtQ0nag5zlhLbTB1L0./yCtwfi','Швуд Дмитрий Сергеевич','89639078683','Dead_inside_26@mail.ru',NULL),(2,'adminka','$2y$10$qmSsTxuFgsSBGYlO1hzPfO4mNDLxZ1cgc9kRrejMAR0J84kj4fP5C','Администратор','0000000000','admin@example.com','admin'),(3,'comm','$2y$10$0pN/gtF2WFuuPSBie6lyFOlr/lHBCVMDbkhgEzzUU/zIcIQ5lkXFu','Курзяков Игнат Финадович','89656557585','Finad@mail.ru',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-09  8:07:34
