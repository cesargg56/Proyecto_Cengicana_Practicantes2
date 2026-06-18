CREATE DATABASE  IF NOT EXISTS "usuarios_menu" /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `usuarios_menu`;
-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: mysql-3baa13e1-cengicana1.e.aivencloud.com    Database: usuarios_menu
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '6f6a5ecc-0f3f-11f1-b9f5-42df523ca1e2:1-17,
a4708222-011a-11f1-84f4-4e55e3d6998d:1-29,
c1b97603-32ac-11f1-ac44-7e533e067a6e:1-1222';

--
-- Table structure for table `ingenios`
--

DROP TABLE IF EXISTS `ingenios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingenios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_ingenio` varchar(150) NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  `creado` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingenios`
--

LOCK TABLES `ingenios` WRITE;
/*!40000 ALTER TABLE `ingenios` DISABLE KEYS */;
INSERT INTO `ingenios` VALUES (1,'Cengicaña',1,'2026-05-14 20:57:06'),(2,'Pantaleon',1,'2026-05-14 20:57:06'),(3,'Madre Tierra',1,'2026-05-14 20:57:06'),(4,'La Union',1,'2026-05-14 20:57:06'),(5,'Santa Ana',1,'2026-05-14 20:57:06');
/*!40000 ALTER TABLE `ingenios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulos`
--

DROP TABLE IF EXISTS `modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
INSERT INTO `modulos` VALUES (5,'Cursos'),(4,'Ensayos'),(3,'Laboratorio'),(7,'Pago'),(2,'Servicio técnico'),(1,'Solicitud de visitas');
/*!40000 ALTER TABLE `modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_permiso` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_permiso` (`nombre_permiso`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (1,'ver_dashboard','Permite ver el dashboard general'),(2,'gestionar_solicitudes','Permite aprobar/rechazar solicitudes'),(3,'ver_solicitudes','Permite ver el listado de solicitudes'),(4,'gestionar_pagos','Permite marcar solicitudes como pagadas'),(5,'ver_pagos','Permite ver el dashboard de pagos'),(6,'gestionar_usuarios','Permite crear y editar usuarios'),(7,'gestionar_roles','Permite editar roles y sus permisos'),(8,'gestionar_modulos','Permite gestionar los módulos del sistema'),(9,'gestionar_ingenios','Permite gestionar los ingenios'),(12,'ver_solicitudes_aprobadas','Ver solo solicitudes aprobadas'),(14,'gestionar_areas','Crear, editar y eliminar areas'),(15,'ocultar_solicitudes','Ocultar solicitudes del dashboard'),(16,'enviar_correos','Enviar correos de solicitudes');
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol_permiso`
--

DROP TABLE IF EXISTS `rol_permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol_permiso` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rol_id` int NOT NULL,
  `permiso_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rol_id` (`rol_id`),
  KEY `permiso_id` (`permiso_id`),
  CONSTRAINT `rol_permiso_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rol_permiso_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol_permiso`
--

LOCK TABLES `rol_permiso` WRITE;
/*!40000 ALTER TABLE `rol_permiso` DISABLE KEYS */;
INSERT INTO `rol_permiso` VALUES (54,7,16),(55,7,2),(56,7,15),(57,7,1),(58,7,3),(59,2,16),(60,2,2),(61,2,15),(62,2,1),(63,2,3),(64,2,12),(65,2,4),(66,2,5),(67,2,14),(68,2,9),(69,2,8),(70,2,7),(71,2,6),(72,1,16),(73,1,14),(74,1,2),(75,1,15),(76,1,3),(77,1,12),(78,1,9),(79,1,4),(80,1,5),(81,1,8),(82,1,7),(83,1,6),(84,1,1);
/*!40000 ALTER TABLE `rol_permiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_rol` (`nombre_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (2,'Administrador'),(9,'Administrador Pagos'),(4,'Analista'),(7,'Coordinador'),(8,'Director'),(11,'Estudiante'),(12,'Gestor'),(6,'Instructor'),(5,'Recepcionista'),(1,'Superadmin'),(3,'Técnico');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_modulo`
--

DROP TABLE IF EXISTS `usuario_modulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario_modulo` (
  `usuario_id` int NOT NULL,
  `modulo_id` int NOT NULL,
  PRIMARY KEY (`usuario_id`,`modulo_id`),
  KEY `modulo_id` (`modulo_id`),
  CONSTRAINT `usuario_modulo_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usuario_modulo_ibfk_2` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_modulo`
--

LOCK TABLES `usuario_modulo` WRITE;
/*!40000 ALTER TABLE `usuario_modulo` DISABLE KEYS */;
INSERT INTO `usuario_modulo` VALUES (2,1),(28,1),(3,2),(4,3),(35,3),(36,3),(37,3),(5,4),(6,5),(32,7);
/*!40000 ALTER TABLE `usuario_modulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol_id` int NOT NULL,
  `es_superadmin` tinyint(1) DEFAULT '0',
  `ingenio_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`),
  KEY `rol_id` (`rol_id`),
  KEY `fk_usuario_ingenio` (`ingenio_id`),
  CONSTRAINT `fk_usuario_ingenio` FOREIGN KEY (`ingenio_id`) REFERENCES `ingenios` (`id`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (2,'Admin Visitas','admin.visitas@prueba.com','$2y$10$C0qM.MS8H.qdwjfDIJsYDOR5iWYo.CKlkvnj0XWzGxBidOB4rPey2',2,0,1),(3,'Admin Servicio','admin.servicio@prueba.com','hash',2,0,NULL),(4,'Admin Laboratorio','admin.lab@prueba.com','$2y$10$UB.KD4mD3ovXcJ0xcY85POtLs.gVnDA/v9ex9A0wEPTGBdyuAJb0u',2,0,NULL),(5,'Admin Ensayos','admin.ensayos@prueba.com','hash',2,0,NULL),(6,'Admin Cursos','admin.cursos@prueba.com','$2y$10$wqb7gIUQPWPQ6U1nz1d9/uDOrlYZKfZNo4k6jPg6uvHoznlUNemXq',2,0,NULL),(24,'Super Admin','superadmin@prueba.com','$2y$10$cmvJUjx.Rw3iP.LhpQ9mqOr7tMDOvFx1w4cMy27NCyP3YLqf3iS0i',1,1,NULL),(28,'Usuario Visitas','cesaranthony759@gmail.com','$2y$10$UvJDZH7mT9WXBp/uTd2sdu9UT5YN3ueXLLAPs2hoycRc4SyafD/S2',7,0,NULL),(32,'lilian','pagos@gmail.com','$2y$10$2IRwyjXvu/Cn1IWXQAFtkuWDGCw1zPqKiqCD6bDFJUJfllMn1N7Y6',9,0,NULL),(35,'Tecnico','tecnico@gmail.com','$2y$10$siv00bmiKg8YiUh5FW0dz.KZH.EPz5wsP.vYMhcM6XvlOmkpjdYMq',3,0,1),(36,'Analista','analista@gmail.com','$2y$10$NO8oXgbHo4FF8zbqvSeoq.IaMCkHZ3JRA7h6bfdKDgcapFeDkafcG',4,0,1),(37,'Recepcionista','recepcionista@gmail.com','$2y$10$UoFFVkGxDWOrCMQRhJAqpObnpw2h7vtUbXtOV0CMoEjJCZX5oGLG2',5,0,1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-10 15:37:22
