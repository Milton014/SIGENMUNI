-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sigenmuni4
-- ------------------------------------------------------
-- Server version	8.0.41

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

--
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sueldo_basico` decimal(12,2) DEFAULT '0.00',
  `dedicacion_funcional` decimal(12,2) DEFAULT '0.00',
  `suplemento_especial` decimal(12,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categoria_codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` VALUES (1,'18','Categoría 18',540000.00,540000.00,90000.00),(2,'19','Categoría 19',500000.00,500000.00,80000.00),(3,'20','Categoría 20',460000.00,460000.00,70000.00),(4,'21','Categoría 21',440000.00,440000.00,65000.00),(5,'22','Categoría 22',420000.00,420000.00,60000.00),(6,'23','Categoría 23',400000.00,400000.00,55000.00),(7,'24','Categoría 24',380000.00,380000.00,50000.00),(15,'1','Categoría 1',1000000.00,1000000.00,200000.00),(16,'2','Categoría 2',950000.00,950000.00,180000.00),(17,'3','Categoría 3',900000.00,900000.00,170000.00),(18,'4','Categoría 4',850000.00,850000.00,160000.00),(19,'5','Categoría 5',800000.00,800000.00,150000.00),(20,'6','Categoría 6',750000.00,750000.00,140000.00),(21,'7','Categoría 7',700000.00,700000.00,130000.00),(22,'8','Categoría 8',650000.00,650000.00,120000.00);
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `concepto`
--

DROP TABLE IF EXISTS `concepto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `concepto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` int NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` enum('REMUNERATIVO','NO_REMUNERATIVO','ASIGNACION_FAMILIAR','DESCUENTO','APORTE_PATRONAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `forma_calculo` enum('FIJO','TABLA_CATEGORIA','PORCENTAJE','MANUAL','FORMULA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FIJO',
  `porcentaje` decimal(10,4) DEFAULT '0.0000',
  `monto_fijo` decimal(12,2) DEFAULT '0.00',
  `requiere_manual` tinyint(1) DEFAULT '0',
  `base_calculo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden_calculo` int NOT NULL DEFAULT '0',
  `aplica_sac` tinyint(1) NOT NULL DEFAULT '0',
  `visible_recibo` tinyint(1) NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_desde` date DEFAULT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `concepto`
--

LOCK TABLES `concepto` WRITE;
/*!40000 ALTER TABLE `concepto` DISABLE KEYS */;
INSERT INTO `concepto` VALUES (1,101,'Sueldo Basico','REMUNERATIVO','TABLA_CATEGORIA',0.0000,0.00,1,NULL,1,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 16:46:02','2026-03-26 23:17:13'),(2,102,'Dedicacion Funcional','REMUNERATIVO','TABLA_CATEGORIA',0.0000,0.00,1,NULL,2,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 16:57:05','2026-03-26 23:17:13'),(3,103,'Adicional por Funcion Jerarquica','REMUNERATIVO','PORCENTAJE',10.0000,0.00,0,'150000',3,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 17:00:41','2026-03-23 17:00:41'),(4,104,'Suplemento Especial','REMUNERATIVO','TABLA_CATEGORIA',0.0000,0.00,1,NULL,4,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 17:03:16','2026-03-26 23:17:13'),(5,105,'Responsabilidad Profesional','REMUNERATIVO','FIJO',0.0000,0.00,1,NULL,5,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 17:38:17','2026-03-23 17:38:31'),(6,106,'Adicional por Reestructura','REMUNERATIVO','FIJO',0.0000,0.00,1,NULL,6,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 17:39:55','2026-03-23 17:39:55'),(7,107,'Horas Extras','REMUNERATIVO','FIJO',0.0000,0.00,1,NULL,0,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 20:49:09','2026-03-23 20:49:09'),(8,108,'Antiguedad','REMUNERATIVO','PORCENTAJE',0.0000,0.00,0,NULL,8,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 20:50:17','2026-03-23 20:50:17'),(9,109,'Presentismo','REMUNERATIVO','PORCENTAJE',0.0000,0.00,0,NULL,9,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 20:55:38','2026-03-23 20:55:38'),(10,110,'Titulo','REMUNERATIVO','PORCENTAJE',0.0000,0.00,0,NULL,10,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 20:56:26','2026-03-23 20:56:26'),(11,111,'Otro Remunerativo','REMUNERATIVO','FIJO',0.0000,0.00,1,NULL,0,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 20:56:59','2026-03-23 20:56:59'),(12,112,'No Remunerativo','NO_REMUNERATIVO','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 20:57:39','2026-03-23 20:57:39'),(13,201,'Asignacion por hijo','ASIGNACION_FAMILIAR','FIJO',0.0000,5000.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 20:58:22','2026-03-26 22:39:10'),(14,202,'Asignacion por hijo con Discapacidad','ASIGNACION_FAMILIAR','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 20:59:12','2026-03-23 20:59:12'),(15,203,'Prenatal','ASIGNACION_FAMILIAR','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 20:59:53','2026-03-23 20:59:53'),(16,204,'Ayuda Escolar','ASIGNACION_FAMILIAR','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:00:22','2026-03-23 21:00:22'),(17,205,'Ayuda Escolar para hijo con Discap.','ASIGNACION_FAMILIAR','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:01:22','2026-03-23 21:01:22'),(18,206,'Nacimiento','ASIGNACION_FAMILIAR','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:02:01','2026-03-23 21:02:01'),(19,207,'Adopcion','ASIGNACION_FAMILIAR','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:02:31','2026-03-23 21:02:31'),(20,208,'Matrimonio','ASIGNACION_FAMILIAR','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:03:17','2026-03-23 21:03:17'),(21,209,'Otra Asignacion','ASIGNACION_FAMILIAR','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:03:34','2026-03-23 21:03:34'),(22,301,'Caja de Prevision Social','DESCUENTO','PORCENTAJE',11.0000,0.00,0,NULL,0,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:07:30','2026-04-13 01:04:16'),(23,302,'IASEP Obra Social','DESCUENTO','PORCENTAJE',5.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:09:28','2026-04-13 01:12:00'),(24,303,'IASEP Sepelio','DESCUENTO','PORCENTAJE',1.0000,0.00,0,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:10:23','2026-04-13 01:12:00'),(25,304,'IASEP Voluntario','DESCUENTO','PORCENTAJE',8.0000,0.00,0,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:11:51','2026-04-13 01:12:00'),(26,305,'IASEP Credito Asistencial','DESCUENTO','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:13:37','2026-04-13 01:13:05'),(27,306,'IPS 1','DESCUENTO','PORCENTAJE',1.0000,0.00,0,NULL,0,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:14:35','2026-04-13 01:04:16'),(28,307,'IPS 2','DESCUENTO','PORCENTAJE',2.0000,0.00,0,NULL,0,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:15:07','2026-04-13 01:04:16'),(29,308,'Gremios','DESCUENTO','PORCENTAJE',2.0000,0.00,0,'TOTAL_REMUNERATIVO',0,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:16:23','2026-04-12 22:32:02'),(30,309,'Instituto Provincial Seguro','DESCUENTO','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:17:19','2026-03-23 21:17:19'),(31,310,'Embargo Judicial','DESCUENTO','MANUAL',0.0000,0.00,1,'REMUNERATIVO_MENOS_APORTES',0,1,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:18:01','2026-04-12 23:56:50'),(32,311,'Otro Descuento','DESCUENTO','FIJO',0.0000,0.00,1,NULL,0,0,1,1,NULL,'2026-03-23',NULL,'2026-03-23 21:18:25','2026-03-23 21:18:25'),(33,150,'Sueldo Anual Complementario','REMUNERATIVO','FORMULA',0.0000,0.00,0,'TOTAL_REMUNERATIVO',150,0,1,1,'Aguinaldo - 50% de conceptos remunerativos',NULL,NULL,'2026-04-13 00:29:19','2026-04-13 00:29:19');
/*!40000 ALTER TABLE `concepto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `concepto_formula`
--

DROP TABLE IF EXISTS `concepto_formula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `concepto_formula` (
  `id` int NOT NULL AUTO_INCREMENT,
  `concepto_id` int NOT NULL,
  `formula` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_concepto_formula_concepto` (`concepto_id`),
  CONSTRAINT `fk_concepto_formula_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `concepto` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `concepto_formula`
--

LOCK TABLES `concepto_formula` WRITE;
/*!40000 ALTER TABLE `concepto_formula` DISABLE KEYS */;
/*!40000 ALTER TABLE `concepto_formula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `concepto_valor`
--

DROP TABLE IF EXISTS `concepto_valor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `concepto_valor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `concepto_id` int NOT NULL,
  `categoria_id` int DEFAULT NULL,
  `escalafon_id` int DEFAULT NULL,
  `monto` decimal(12,2) DEFAULT '0.00',
  `porcentaje` decimal(10,2) DEFAULT '0.00',
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_concepto_valor_concepto` (`concepto_id`),
  CONSTRAINT `fk_concepto_valor_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `concepto` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `concepto_valor`
--

LOCK TABLES `concepto_valor` WRITE;
/*!40000 ALTER TABLE `concepto_valor` DISABLE KEYS */;
/*!40000 ALTER TABLE `concepto_valor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleado`
--

DROP TABLE IF EXISTS `empleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institucion_id` int NOT NULL,
  `oficina_id` int NOT NULL,
  `situacion_id` int NOT NULL,
  `escalafon_id` int NOT NULL,
  `categoria_id` int NOT NULL,
  `nro_legajo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuil` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_alta` date NOT NULL,
  `fecha_baja` date DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domicilio` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nro_legajo` (`nro_legajo`),
  UNIQUE KEY `dni` (`dni`),
  UNIQUE KEY `cuil` (`cuil`),
  UNIQUE KEY `uq_empleado_legajo` (`nro_legajo`),
  UNIQUE KEY `uq_empleado_dni` (`dni`),
  UNIQUE KEY `uq_empleado_cuil` (`cuil`),
  UNIQUE KEY `uq_empleado_email` (`email`),
  KEY `institucion_id` (`institucion_id`),
  KEY `oficina_id` (`oficina_id`),
  KEY `situacion_id` (`situacion_id`),
  KEY `escalafon_id` (`escalafon_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `empleado_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `institucion` (`id`),
  CONSTRAINT `empleado_ibfk_2` FOREIGN KEY (`oficina_id`) REFERENCES `oficina` (`id`),
  CONSTRAINT `empleado_ibfk_3` FOREIGN KEY (`situacion_id`) REFERENCES `situacion` (`id`),
  CONSTRAINT `empleado_ibfk_4` FOREIGN KEY (`escalafon_id`) REFERENCES `escalafon` (`id`),
  CONSTRAINT `empleado_ibfk_5` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleado`
--

LOCK TABLES `empleado` WRITE;
/*!40000 ALTER TABLE `empleado` DISABLE KEYS */;
INSERT INTO `empleado` VALUES (1,1,1,1,8,2,'1','Chavez','Milton','29775014','20297750144','2026-03-22',NULL,'3704304781','chavezmilton082@gmail.com','Jose Maria Uriburu 3345','',1),(2,1,1,1,9,1,'2','Villalba','Hector','22900749','20229007492','2026-03-01',NULL,'','villalbahector900@gmail.com','Moreno 850','',1),(3,1,1,1,1,15,'3','Carabajal','Antonio','08075112','20080751129','2026-03-01',NULL,'','antoniocarabjal@gmail.com','San Pedro 789','',1),(4,1,1,1,8,2,'4','Garcia','Pedro','16715494','20167154949','2026-03-01',NULL,'','garciapedro987@gmail.com','Alberti 775','',1),(12,1,1,2,9,3,'5','Medina','Jesus','25217351','20252173510','2026-03-01',NULL,'','jesusmedina@gmail.com','Francia 456','',1),(13,1,1,2,8,4,'6','PEREZ','MIRIAN','25749498','27257494980','2026-03-01',NULL,NULL,'perezmirian654@gmail.com',NULL,NULL,1),(14,1,1,2,10,4,'7','ORTEGA','MARTA','41013626','27410136266','2026-03-01',NULL,'','martaortega@gmail.com','','',1),(15,1,1,2,9,6,'8','RAMIREZ','SANDRA','31977108','27319771080','2026-03-01',NULL,'','sandraramirez@gmail.com','','',1),(16,1,1,2,9,7,'9','GONZALEZ','NILDA','28551554','27285515543','2026-03-01',NULL,'','nildagonzalez@gmail.com','','',1),(17,1,1,2,10,6,'10','RUIZ','ELSA','38577830','27385778304','2026-03-01',NULL,'','elsaruiz@gmail.com','','',1),(18,1,1,2,8,5,'11','Arias','Roberto','44756321','20447563212','2026-04-01',NULL,'','robertoarias@gmail.com','Castelli 568','',1);
/*!40000 ALTER TABLE `empleado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleado_asignacion_familiar`
--

DROP TABLE IF EXISTS `empleado_asignacion_familiar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado_asignacion_familiar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empleado_id` int NOT NULL,
  `concepto_id` int NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `monto_unitario` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_asig_emp_empleado` (`empleado_id`),
  KEY `fk_asig_emp_concepto` (`concepto_id`),
  CONSTRAINT `fk_asig_emp_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `concepto` (`id`),
  CONSTRAINT `fk_asig_emp_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleado_asignacion_familiar`
--

LOCK TABLES `empleado_asignacion_familiar` WRITE;
/*!40000 ALTER TABLE `empleado_asignacion_familiar` DISABLE KEYS */;
/*!40000 ALTER TABLE `empleado_asignacion_familiar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleado_concepto`
--

DROP TABLE IF EXISTS `empleado_concepto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado_concepto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empleado_id` int NOT NULL,
  `concepto_id` int NOT NULL,
  `monto_manual` decimal(12,2) DEFAULT '0.00',
  `porcentaje_manual` decimal(10,2) DEFAULT '0.00',
  `cantidad` decimal(10,2) DEFAULT '1.00',
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_empleado_concepto_empleado` (`empleado_id`),
  KEY `fk_empleado_concepto_concepto` (`concepto_id`),
  CONSTRAINT `fk_empleado_concepto_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `concepto` (`id`),
  CONSTRAINT `fk_empleado_concepto_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleado_concepto`
--

LOCK TABLES `empleado_concepto` WRITE;
/*!40000 ALTER TABLE `empleado_concepto` DISABLE KEYS */;
INSERT INTO `empleado_concepto` VALUES (1,3,13,45000.00,0.00,1.00,'2026-03-01',NULL,1,'','2026-04-06 21:31:42','2026-04-06 21:31:42'),(2,3,29,0.00,0.00,1.00,'2026-03-01',NULL,1,'','2026-04-12 14:31:30','2026-04-12 22:53:30'),(3,3,13,45000.00,0.00,1.00,'2026-03-01',NULL,1,'','2026-04-12 14:49:07','2026-04-12 14:49:07'),(4,3,31,0.00,15.00,1.00,'2026-03-01',NULL,1,'','2026-04-12 23:28:17','2026-04-13 00:07:12'),(5,3,33,0.00,0.00,1.00,'2026-03-01',NULL,1,'','2026-04-13 00:57:01','2026-04-13 00:57:01');
/*!40000 ALTER TABLE `empleado_concepto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleado_familiar`
--

DROP TABLE IF EXISTS `empleado_familiar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado_familiar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empleado_id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parentesco` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discapacidad` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `empleado_familiar_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleado_familiar`
--

LOCK TABLES `empleado_familiar` WRITE;
/*!40000 ALTER TABLE `empleado_familiar` DISABLE KEYS */;
/*!40000 ALTER TABLE `empleado_familiar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `escala_salarial`
--

DROP TABLE IF EXISTS `escala_salarial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `escala_salarial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `anio` int NOT NULL,
  `categoria_id` int NOT NULL,
  `sueldo_basico` decimal(12,2) NOT NULL DEFAULT '0.00',
  `suplemento_especial` decimal(12,2) NOT NULL DEFAULT '0.00',
  `titulo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `dedicacion_funcional` decimal(12,2) NOT NULL DEFAULT '0.00',
  `no_remunerativo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `asignacion_hijo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `asignacion_hijo_discapacitado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `prenatal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `ayuda_escolar` decimal(12,2) NOT NULL DEFAULT '0.00',
  `ayuda_escolar_discapacidad` decimal(12,2) NOT NULL DEFAULT '0.00',
  `nacimiento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `adopcion` decimal(12,2) NOT NULL DEFAULT '0.00',
  `matrimonio` decimal(12,2) NOT NULL DEFAULT '0.00',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_escala_categoria` (`categoria_id`),
  CONSTRAINT `fk_escala_salarial_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `escala_salarial`
--

LOCK TABLES `escala_salarial` WRITE;
/*!40000 ALTER TABLE `escala_salarial` DISABLE KEYS */;
/*!40000 ALTER TABLE `escala_salarial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `escalafon`
--

DROP TABLE IF EXISTS `escalafon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `escalafon` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `escalafon`
--

LOCK TABLES `escalafon` WRITE;
/*!40000 ALTER TABLE `escalafon` DISABLE KEYS */;
INSERT INTO `escalafon` VALUES (1,'Intendente'),(2,'Presidente HCD'),(3,'Concejal'),(4,'Jefe Contable'),(5,'Tesorero'),(6,'Secretario'),(7,'Subsecretario'),(8,'Administrativo y Técnico'),(9,'Obrero y Maestranza'),(10,'Jornalizado'),(11,'Asesor');
/*!40000 ALTER TABLE `escalafon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institucion`
--

DROP TABLE IF EXISTS `institucion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `institucion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domicilio` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `escudo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institucion`
--

LOCK TABLES `institucion` WRITE;
/*!40000 ALTER TABLE `institucion` DISABLE KEYS */;
INSERT INTO `institucion` VALUES (1,'Municipalidad de Fortín Lugones',NULL,NULL,NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `institucion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liquidacion`
--

DROP TABLE IF EXISTS `liquidacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `liquidacion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo_liquidacion` varchar(50) NOT NULL,
  `periodo` varchar(7) NOT NULL,
  `fecha_liquidacion` date NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `estado` enum('BORRADOR','CERRADA','ANULADA') NOT NULL DEFAULT 'BORRADOR',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liquidacion`
--

LOCK TABLES `liquidacion` WRITE;
/*!40000 ALTER TABLE `liquidacion` DISABLE KEYS */;
INSERT INTO `liquidacion` VALUES (1,'MENSUAL','2026-02','2026-02-28','','CERRADA','2026-03-24 15:51:20'),(2,'MENSUAL','2026-03','2026-03-24','','ANULADA','2026-03-24 15:59:26'),(3,'MENSUAL','2026-03','2026-04-06','','CERRADA','2026-04-06 20:40:56'),(4,'AGUINALDO','2026-03','2026-04-12','','CERRADA','2026-04-12 14:40:07'),(5,'AGUINALDO','2026-03','2026-04-13','','CERRADA','2026-04-13 00:54:29');
/*!40000 ALTER TABLE `liquidacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liquidacion_detalle`
--

DROP TABLE IF EXISTS `liquidacion_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `liquidacion_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `liquidacion_id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `concepto_id` int NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT '1.00',
  `porcentaje_aplicado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `monto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `es_manual` tinyint(1) NOT NULL DEFAULT '0',
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_liq_det_liquidacion` (`liquidacion_id`),
  KEY `idx_liq_det_empleado` (`empleado_id`),
  KEY `idx_liq_det_concepto` (`concepto_id`),
  CONSTRAINT `fk_liquidacion_detalle_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `concepto` (`id`),
  CONSTRAINT `fk_liquidacion_detalle_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`),
  CONSTRAINT `fk_liquidacion_detalle_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidacion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2011 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liquidacion_detalle`
--

LOCK TABLES `liquidacion_detalle` WRITE;
/*!40000 ALTER TABLE `liquidacion_detalle` DISABLE KEYS */;
INSERT INTO `liquidacion_detalle` VALUES (13,1,1,1,1.00,0.00,500000.00,0,'Sueldo básico','2026-03-24 22:07:20'),(14,1,1,2,1.00,0.00,500000.00,0,'Dedicación funcional','2026-03-24 22:07:20'),(15,1,1,3,1.00,10.00,100000.00,0,'10% sobre básico + dedicación funcional','2026-03-24 22:07:20'),(16,1,1,4,1.00,0.00,80000.00,0,'Suplemento especial','2026-03-24 22:07:20'),(17,1,1,9,1.00,15.00,162000.00,0,'15% sobre básico + dedicación + suplemento','2026-03-24 22:07:20'),(18,1,1,22,1.00,11.00,147620.00,0,'11% Caja de Previsión Social','2026-03-24 22:07:20'),(19,1,1,23,1.00,5.00,67100.00,0,'5% IASEP Obra Social','2026-03-24 22:07:20'),(20,1,1,24,1.00,1.00,13420.00,0,'1% IASEP Sepelio','2026-03-24 22:07:20'),(21,1,1,25,1.00,8.00,107360.00,0,'8% IASEP Voluntario','2026-03-24 22:07:20'),(22,1,1,27,1.00,1.00,13420.00,0,'1% IPS para categoría 21 o inferior','2026-03-24 22:07:20'),(23,1,1,28,1.00,2.00,26840.00,0,'2% IPS para categoría 22 o inferior','2026-03-24 22:07:20'),(24,1,1,29,1.00,0.00,6000.00,1,'','2026-03-24 22:57:01'),(989,4,3,1,1.00,0.00,1000000.00,0,'Sueldo básico','2026-04-12 14:40:18'),(990,4,3,2,1.00,0.00,1000000.00,0,'Dedicación funcional','2026-04-12 14:40:18'),(991,4,3,3,1.00,10.00,200000.00,0,'10% sobre básico + dedicación funcional','2026-04-12 14:40:18'),(992,4,3,4,1.00,0.00,200000.00,0,'Suplemento especial','2026-04-12 14:40:18'),(993,4,3,9,1.00,15.00,330000.00,0,'15% sobre básico + dedicación + suplemento','2026-04-12 14:40:18'),(994,4,3,13,1.00,0.00,45000.00,1,'Concepto asignado al empleado','2026-04-12 14:40:18'),(995,4,3,29,1.00,0.00,56000.00,1,'Concepto asignado al empleado','2026-04-12 14:40:18'),(996,4,3,22,1.00,11.00,300300.00,0,'11% Caja de Previsión Social','2026-04-12 14:40:18'),(997,4,3,23,1.00,5.00,136500.00,0,'5% IASEP Obra Social','2026-04-12 14:40:18'),(998,4,3,24,1.00,1.00,27300.00,0,'1% IASEP Sepelio','2026-04-12 14:40:18'),(999,4,3,25,1.00,8.00,218400.00,0,'8% IASEP Voluntario','2026-04-12 14:40:18'),(1000,4,3,27,1.00,1.00,27300.00,0,'1% IPS para categoría 21 o inferior','2026-04-12 14:40:18'),(1001,4,3,28,1.00,2.00,54600.00,0,'2% IPS para categoría 22 o inferior','2026-04-12 14:40:18'),(1002,4,1,1,1.00,0.00,500000.00,0,'Sueldo básico','2026-04-12 14:40:18'),(1003,4,1,2,1.00,0.00,500000.00,0,'Dedicación funcional','2026-04-12 14:40:18'),(1004,4,1,4,1.00,0.00,80000.00,0,'Suplemento especial','2026-04-12 14:40:18'),(1005,4,1,9,1.00,15.00,162000.00,0,'15% sobre básico + dedicación + suplemento','2026-04-12 14:40:18'),(1006,4,1,22,1.00,11.00,136620.00,0,'11% Caja de Previsión Social','2026-04-12 14:40:18'),(1007,4,1,23,1.00,5.00,62100.00,0,'5% IASEP Obra Social','2026-04-12 14:40:18'),(1008,4,1,24,1.00,1.00,12420.00,0,'1% IASEP Sepelio','2026-04-12 14:40:18'),(1009,4,1,25,1.00,8.00,99360.00,0,'8% IASEP Voluntario','2026-04-12 14:40:18'),(1010,4,1,27,1.00,1.00,12420.00,0,'1% IPS para categoría 21 o inferior','2026-04-12 14:40:18'),(1011,4,1,28,1.00,2.00,24840.00,0,'2% IPS para categoría 22 o inferior','2026-04-12 14:40:18'),(1012,4,4,1,1.00,0.00,500000.00,0,'Sueldo básico','2026-04-12 14:40:18'),(1013,4,4,2,1.00,0.00,500000.00,0,'Dedicación funcional','2026-04-12 14:40:18'),(1014,4,4,4,1.00,0.00,80000.00,0,'Suplemento especial','2026-04-12 14:40:18'),(1015,4,4,9,1.00,15.00,162000.00,0,'15% sobre básico + dedicación + suplemento','2026-04-12 14:40:18'),(1016,4,4,22,1.00,11.00,136620.00,0,'11% Caja de Previsión Social','2026-04-12 14:40:18'),(1017,4,4,23,1.00,5.00,62100.00,0,'5% IASEP Obra Social','2026-04-12 14:40:18'),(1018,4,4,24,1.00,1.00,12420.00,0,'1% IASEP Sepelio','2026-04-12 14:40:18'),(1019,4,4,25,1.00,8.00,99360.00,0,'8% IASEP Voluntario','2026-04-12 14:40:18'),(1020,4,4,27,1.00,1.00,12420.00,0,'1% IPS para categoría 21 o inferior','2026-04-12 14:40:18'),(1021,4,4,28,1.00,2.00,24840.00,0,'2% IPS para categoría 22 o inferior','2026-04-12 14:40:18'),(1022,4,16,1,1.00,0.00,380000.00,0,'Sueldo básico','2026-04-12 14:40:18'),(1023,4,16,2,1.00,0.00,380000.00,0,'Dedicación funcional','2026-04-12 14:40:18'),(1024,4,16,4,1.00,0.00,50000.00,0,'Suplemento especial','2026-04-12 14:40:18'),(1025,4,16,9,1.00,15.00,121500.00,0,'15% sobre básico + dedicación + suplemento','2026-04-12 14:40:18'),(1026,4,16,22,1.00,11.00,102465.00,0,'11% Caja de Previsión Social','2026-04-12 14:40:18'),(1027,4,16,23,1.00,5.00,46575.00,0,'5% IASEP Obra Social','2026-04-12 14:40:18'),(1028,4,16,24,1.00,1.00,9315.00,0,'1% IASEP Sepelio','2026-04-12 14:40:18'),(1029,4,16,25,1.00,8.00,74520.00,0,'8% IASEP Voluntario','2026-04-12 14:40:18'),(1030,4,12,1,1.00,0.00,460000.00,0,'Sueldo básico','2026-04-12 14:40:18'),(1031,4,12,2,1.00,0.00,460000.00,0,'Dedicación funcional','2026-04-12 14:40:18'),(1032,4,12,4,1.00,0.00,70000.00,0,'Suplemento especial','2026-04-12 14:40:18'),(1033,4,12,9,1.00,15.00,148500.00,0,'15% sobre básico + dedicación + suplemento','2026-04-12 14:40:18'),(1034,4,12,22,1.00,11.00,125235.00,0,'11% Caja de Previsión Social','2026-04-12 14:40:18'),(1035,4,12,23,1.00,5.00,56925.00,0,'5% IASEP Obra Social','2026-04-12 14:40:18'),(1036,4,12,24,1.00,1.00,11385.00,0,'1% IASEP Sepelio','2026-04-12 14:40:18'),(1037,4,12,25,1.00,8.00,91080.00,0,'8% IASEP Voluntario','2026-04-12 14:40:18'),(1038,4,12,27,1.00,1.00,11385.00,0,'1% IPS para categoría 21 o inferior','2026-04-12 14:40:18'),(1039,4,12,28,1.00,2.00,22770.00,0,'2% IPS para categoría 22 o inferior','2026-04-12 14:40:18'),(1040,4,14,1,1.00,0.00,440000.00,0,'Sueldo básico','2026-04-12 14:40:18'),(1041,4,14,2,1.00,0.00,440000.00,0,'Dedicación funcional','2026-04-12 14:40:18'),(1042,4,14,4,1.00,0.00,65000.00,0,'Suplemento especial','2026-04-12 14:40:18'),(1043,4,14,9,1.00,15.00,141750.00,0,'15% sobre básico + dedicación + suplemento','2026-04-12 14:40:18'),(1044,4,14,22,1.00,11.00,119542.50,0,'11% Caja de Previsión Social','2026-04-12 14:40:18'),(1045,4,14,23,1.00,5.00,54337.50,0,'5% IASEP Obra Social','2026-04-12 14:40:18'),(1046,4,14,24,1.00,1.00,10867.50,0,'1% IASEP Sepelio','2026-04-12 14:40:18'),(1047,4,14,25,1.00,8.00,86940.00,0,'8% IASEP Voluntario','2026-04-12 14:40:18'),(1048,4,14,27,1.00,1.00,10867.50,0,'1% IPS para categoría 21 o inferior','2026-04-12 14:40:18'),(1049,4,14,28,1.00,2.00,21735.00,0,'2% IPS para categoría 22 o inferior','2026-04-12 14:40:18'),(1050,4,13,1,1.00,0.00,440000.00,0,'Sueldo básico','2026-04-12 14:40:18'),(1051,4,13,2,1.00,0.00,440000.00,0,'Dedicación funcional','2026-04-12 14:40:18'),(1052,4,13,4,1.00,0.00,65000.00,0,'Suplemento especial','2026-04-12 14:40:18'),(1053,4,13,9,1.00,15.00,141750.00,0,'15% sobre básico + dedicación + suplemento','2026-04-12 14:40:18'),(1054,4,13,22,1.00,11.00,119542.50,0,'11% Caja de Previsión Social','2026-04-12 14:40:19'),(1055,4,13,23,1.00,5.00,54337.50,0,'5% IASEP Obra Social','2026-04-12 14:40:19'),(1056,4,13,24,1.00,1.00,10867.50,0,'1% IASEP Sepelio','2026-04-12 14:40:19'),(1057,4,13,25,1.00,8.00,86940.00,0,'8% IASEP Voluntario','2026-04-12 14:40:19'),(1058,4,13,27,1.00,1.00,10867.50,0,'1% IPS para categoría 21 o inferior','2026-04-12 14:40:19'),(1059,4,13,28,1.00,2.00,21735.00,0,'2% IPS para categoría 22 o inferior','2026-04-12 14:40:19'),(1060,4,15,1,1.00,0.00,400000.00,0,'Sueldo básico','2026-04-12 14:40:19'),(1061,4,15,2,1.00,0.00,400000.00,0,'Dedicación funcional','2026-04-12 14:40:19'),(1062,4,15,4,1.00,0.00,55000.00,0,'Suplemento especial','2026-04-12 14:40:19'),(1063,4,15,9,1.00,15.00,128250.00,0,'15% sobre básico + dedicación + suplemento','2026-04-12 14:40:19'),(1064,4,15,22,1.00,11.00,108157.50,0,'11% Caja de Previsión Social','2026-04-12 14:40:19'),(1065,4,15,23,1.00,5.00,49162.50,0,'5% IASEP Obra Social','2026-04-12 14:40:19'),(1066,4,15,24,1.00,1.00,9832.50,0,'1% IASEP Sepelio','2026-04-12 14:40:19'),(1067,4,15,25,1.00,8.00,78660.00,0,'8% IASEP Voluntario','2026-04-12 14:40:19'),(1068,4,17,1,1.00,0.00,400000.00,0,'Sueldo básico','2026-04-12 14:40:19'),(1069,4,17,2,1.00,0.00,400000.00,0,'Dedicación funcional','2026-04-12 14:40:19'),(1070,4,17,4,1.00,0.00,55000.00,0,'Suplemento especial','2026-04-12 14:40:19'),(1071,4,17,9,1.00,15.00,128250.00,0,'15% sobre básico + dedicación + suplemento','2026-04-12 14:40:19'),(1072,4,17,22,1.00,11.00,108157.50,0,'11% Caja de Previsión Social','2026-04-12 14:40:19'),(1073,4,17,23,1.00,5.00,49162.50,0,'5% IASEP Obra Social','2026-04-12 14:40:19'),(1074,4,17,24,1.00,1.00,9832.50,0,'1% IASEP Sepelio','2026-04-12 14:40:19'),(1075,4,17,25,1.00,8.00,78660.00,0,'8% IASEP Voluntario','2026-04-12 14:40:19'),(1076,4,2,1,1.00,0.00,540000.00,0,'Sueldo básico','2026-04-12 14:40:19'),(1077,4,2,2,1.00,0.00,540000.00,0,'Dedicación funcional','2026-04-12 14:40:19'),(1078,4,2,4,1.00,0.00,90000.00,0,'Suplemento especial','2026-04-12 14:40:19'),(1079,4,2,9,1.00,15.00,175500.00,0,'15% sobre básico + dedicación + suplemento','2026-04-12 14:40:19'),(1080,4,2,22,1.00,11.00,148005.00,0,'11% Caja de Previsión Social','2026-04-12 14:40:19'),(1081,4,2,23,1.00,5.00,67275.00,0,'5% IASEP Obra Social','2026-04-12 14:40:19'),(1082,4,2,24,1.00,1.00,13455.00,0,'1% IASEP Sepelio','2026-04-12 14:40:19'),(1083,4,2,25,1.00,8.00,107640.00,0,'8% IASEP Voluntario','2026-04-12 14:40:19'),(1084,4,2,27,1.00,1.00,13455.00,0,'1% IPS para categoría 21 o inferior','2026-04-12 14:40:19'),(1085,4,2,28,1.00,2.00,26910.00,0,'2% IPS para categoría 22 o inferior','2026-04-12 14:40:19'),(1488,3,3,1,1.00,0.00,1000000.00,0,'Sueldo básico','2026-04-13 00:07:31'),(1489,3,3,2,1.00,0.00,1000000.00,0,'Dedicación funcional','2026-04-13 00:07:31'),(1490,3,3,3,1.00,10.00,200000.00,0,'10% sobre básico + dedicación funcional','2026-04-13 00:07:31'),(1491,3,3,4,1.00,0.00,200000.00,0,'Suplemento especial','2026-04-13 00:07:31'),(1492,3,3,9,1.00,15.00,330000.00,0,'15% sobre básico + dedicación + suplemento','2026-04-13 00:07:31'),(1493,3,3,13,1.00,0.00,45000.00,1,'Concepto asignado al empleado','2026-04-13 00:07:31'),(1494,3,3,13,1.00,0.00,45000.00,1,'Concepto asignado al empleado','2026-04-13 00:07:31'),(1495,3,3,22,1.00,11.00,300300.00,0,'11% Caja de Previsión Social','2026-04-13 00:07:31'),(1496,3,3,23,1.00,5.00,136500.00,0,'5% IASEP Obra Social','2026-04-13 00:07:31'),(1497,3,3,24,1.00,1.00,27300.00,0,'1% IASEP Sepelio','2026-04-13 00:07:31'),(1498,3,3,25,1.00,8.00,218400.00,0,'8% IASEP Voluntario','2026-04-13 00:07:31'),(1499,3,3,27,1.00,1.00,27300.00,0,'1% IPS para categoría 21 o inferior','2026-04-13 00:07:31'),(1500,3,3,28,1.00,2.00,54600.00,0,'2% IPS para categoría 22 o inferior','2026-04-13 00:07:31'),(1501,3,3,29,1.00,2.00,54600.00,0,'Descuento gremial sobre total remunerativo','2026-04-13 00:07:31'),(1502,3,3,31,1.00,15.00,294840.00,0,'Embargo judicial sobre remunerativo menos aportes 301 al 307','2026-04-13 00:07:31'),(1503,3,1,1,1.00,0.00,500000.00,0,'Sueldo básico','2026-04-13 00:07:31'),(1504,3,1,2,1.00,0.00,500000.00,0,'Dedicación funcional','2026-04-13 00:07:31'),(1505,3,1,4,1.00,0.00,80000.00,0,'Suplemento especial','2026-04-13 00:07:31'),(1506,3,1,9,1.00,15.00,162000.00,0,'15% sobre básico + dedicación + suplemento','2026-04-13 00:07:31'),(1507,3,1,22,1.00,11.00,136620.00,0,'11% Caja de Previsión Social','2026-04-13 00:07:31'),(1508,3,1,23,1.00,5.00,62100.00,0,'5% IASEP Obra Social','2026-04-13 00:07:31'),(1509,3,1,24,1.00,1.00,12420.00,0,'1% IASEP Sepelio','2026-04-13 00:07:31'),(1510,3,1,25,1.00,8.00,99360.00,0,'8% IASEP Voluntario','2026-04-13 00:07:31'),(1511,3,1,27,1.00,1.00,12420.00,0,'1% IPS para categoría 21 o inferior','2026-04-13 00:07:31'),(1512,3,1,28,1.00,2.00,24840.00,0,'2% IPS para categoría 22 o inferior','2026-04-13 00:07:31'),(1513,3,4,1,1.00,0.00,500000.00,0,'Sueldo básico','2026-04-13 00:07:31'),(1514,3,4,2,1.00,0.00,500000.00,0,'Dedicación funcional','2026-04-13 00:07:31'),(1515,3,4,4,1.00,0.00,80000.00,0,'Suplemento especial','2026-04-13 00:07:31'),(1516,3,4,9,1.00,15.00,162000.00,0,'15% sobre básico + dedicación + suplemento','2026-04-13 00:07:31'),(1517,3,4,22,1.00,11.00,136620.00,0,'11% Caja de Previsión Social','2026-04-13 00:07:31'),(1518,3,4,23,1.00,5.00,62100.00,0,'5% IASEP Obra Social','2026-04-13 00:07:31'),(1519,3,4,24,1.00,1.00,12420.00,0,'1% IASEP Sepelio','2026-04-13 00:07:31'),(1520,3,4,25,1.00,8.00,99360.00,0,'8% IASEP Voluntario','2026-04-13 00:07:31'),(1521,3,4,27,1.00,1.00,12420.00,0,'1% IPS para categoría 21 o inferior','2026-04-13 00:07:31'),(1522,3,4,28,1.00,2.00,24840.00,0,'2% IPS para categoría 22 o inferior','2026-04-13 00:07:31'),(1523,3,16,1,1.00,0.00,380000.00,0,'Sueldo básico','2026-04-13 00:07:31'),(1524,3,16,2,1.00,0.00,380000.00,0,'Dedicación funcional','2026-04-13 00:07:31'),(1525,3,16,4,1.00,0.00,50000.00,0,'Suplemento especial','2026-04-13 00:07:31'),(1526,3,16,9,1.00,15.00,121500.00,0,'15% sobre básico + dedicación + suplemento','2026-04-13 00:07:31'),(1527,3,16,22,1.00,11.00,102465.00,0,'11% Caja de Previsión Social','2026-04-13 00:07:31'),(1528,3,16,23,1.00,5.00,46575.00,0,'5% IASEP Obra Social','2026-04-13 00:07:31'),(1529,3,16,24,1.00,1.00,9315.00,0,'1% IASEP Sepelio','2026-04-13 00:07:31'),(1530,3,16,25,1.00,8.00,74520.00,0,'8% IASEP Voluntario','2026-04-13 00:07:31'),(1531,3,12,1,1.00,0.00,460000.00,0,'Sueldo básico','2026-04-13 00:07:31'),(1532,3,12,2,1.00,0.00,460000.00,0,'Dedicación funcional','2026-04-13 00:07:31'),(1533,3,12,4,1.00,0.00,70000.00,0,'Suplemento especial','2026-04-13 00:07:31'),(1534,3,12,9,1.00,15.00,148500.00,0,'15% sobre básico + dedicación + suplemento','2026-04-13 00:07:31'),(1535,3,12,22,1.00,11.00,125235.00,0,'11% Caja de Previsión Social','2026-04-13 00:07:31'),(1536,3,12,23,1.00,5.00,56925.00,0,'5% IASEP Obra Social','2026-04-13 00:07:31'),(1537,3,12,24,1.00,1.00,11385.00,0,'1% IASEP Sepelio','2026-04-13 00:07:31'),(1538,3,12,25,1.00,8.00,91080.00,0,'8% IASEP Voluntario','2026-04-13 00:07:31'),(1539,3,12,27,1.00,1.00,11385.00,0,'1% IPS para categoría 21 o inferior','2026-04-13 00:07:31'),(1540,3,12,28,1.00,2.00,22770.00,0,'2% IPS para categoría 22 o inferior','2026-04-13 00:07:31'),(1541,3,14,1,1.00,0.00,440000.00,0,'Sueldo básico','2026-04-13 00:07:31'),(1542,3,14,2,1.00,0.00,440000.00,0,'Dedicación funcional','2026-04-13 00:07:31'),(1543,3,14,4,1.00,0.00,65000.00,0,'Suplemento especial','2026-04-13 00:07:31'),(1544,3,14,9,1.00,15.00,141750.00,0,'15% sobre básico + dedicación + suplemento','2026-04-13 00:07:31'),(1545,3,14,22,1.00,11.00,119542.50,0,'11% Caja de Previsión Social','2026-04-13 00:07:31'),(1546,3,14,23,1.00,5.00,54337.50,0,'5% IASEP Obra Social','2026-04-13 00:07:31'),(1547,3,14,24,1.00,1.00,10867.50,0,'1% IASEP Sepelio','2026-04-13 00:07:31'),(1548,3,14,25,1.00,8.00,86940.00,0,'8% IASEP Voluntario','2026-04-13 00:07:31'),(1549,3,14,27,1.00,1.00,10867.50,0,'1% IPS para categoría 21 o inferior','2026-04-13 00:07:31'),(1550,3,14,28,1.00,2.00,21735.00,0,'2% IPS para categoría 22 o inferior','2026-04-13 00:07:31'),(1551,3,13,1,1.00,0.00,440000.00,0,'Sueldo básico','2026-04-13 00:07:31'),(1552,3,13,2,1.00,0.00,440000.00,0,'Dedicación funcional','2026-04-13 00:07:31'),(1553,3,13,4,1.00,0.00,65000.00,0,'Suplemento especial','2026-04-13 00:07:31'),(1554,3,13,9,1.00,15.00,141750.00,0,'15% sobre básico + dedicación + suplemento','2026-04-13 00:07:31'),(1555,3,13,22,1.00,11.00,119542.50,0,'11% Caja de Previsión Social','2026-04-13 00:07:31'),(1556,3,13,23,1.00,5.00,54337.50,0,'5% IASEP Obra Social','2026-04-13 00:07:31'),(1557,3,13,24,1.00,1.00,10867.50,0,'1% IASEP Sepelio','2026-04-13 00:07:31'),(1558,3,13,25,1.00,8.00,86940.00,0,'8% IASEP Voluntario','2026-04-13 00:07:31'),(1559,3,13,27,1.00,1.00,10867.50,0,'1% IPS para categoría 21 o inferior','2026-04-13 00:07:31'),(1560,3,13,28,1.00,2.00,21735.00,0,'2% IPS para categoría 22 o inferior','2026-04-13 00:07:31'),(1561,3,15,1,1.00,0.00,400000.00,0,'Sueldo básico','2026-04-13 00:07:31'),(1562,3,15,2,1.00,0.00,400000.00,0,'Dedicación funcional','2026-04-13 00:07:31'),(1563,3,15,4,1.00,0.00,55000.00,0,'Suplemento especial','2026-04-13 00:07:31'),(1564,3,15,9,1.00,15.00,128250.00,0,'15% sobre básico + dedicación + suplemento','2026-04-13 00:07:31'),(1565,3,15,22,1.00,11.00,108157.50,0,'11% Caja de Previsión Social','2026-04-13 00:07:31'),(1566,3,15,23,1.00,5.00,49162.50,0,'5% IASEP Obra Social','2026-04-13 00:07:31'),(1567,3,15,24,1.00,1.00,9832.50,0,'1% IASEP Sepelio','2026-04-13 00:07:31'),(1568,3,15,25,1.00,8.00,78660.00,0,'8% IASEP Voluntario','2026-04-13 00:07:31'),(1569,3,17,1,1.00,0.00,400000.00,0,'Sueldo básico','2026-04-13 00:07:31'),(1570,3,17,2,1.00,0.00,400000.00,0,'Dedicación funcional','2026-04-13 00:07:31'),(1571,3,17,4,1.00,0.00,55000.00,0,'Suplemento especial','2026-04-13 00:07:31'),(1572,3,17,9,1.00,15.00,128250.00,0,'15% sobre básico + dedicación + suplemento','2026-04-13 00:07:31'),(1573,3,17,22,1.00,11.00,108157.50,0,'11% Caja de Previsión Social','2026-04-13 00:07:31'),(1574,3,17,23,1.00,5.00,49162.50,0,'5% IASEP Obra Social','2026-04-13 00:07:31'),(1575,3,17,24,1.00,1.00,9832.50,0,'1% IASEP Sepelio','2026-04-13 00:07:31'),(1576,3,17,25,1.00,8.00,78660.00,0,'8% IASEP Voluntario','2026-04-13 00:07:31'),(1577,3,2,1,1.00,0.00,540000.00,0,'Sueldo básico','2026-04-13 00:07:31'),(1578,3,2,2,1.00,0.00,540000.00,0,'Dedicación funcional','2026-04-13 00:07:31'),(1579,3,2,4,1.00,0.00,90000.00,0,'Suplemento especial','2026-04-13 00:07:31'),(1580,3,2,9,1.00,15.00,175500.00,0,'15% sobre básico + dedicación + suplemento','2026-04-13 00:07:31'),(1581,3,2,22,1.00,11.00,148005.00,0,'11% Caja de Previsión Social','2026-04-13 00:07:31'),(1582,3,2,23,1.00,5.00,67275.00,0,'5% IASEP Obra Social','2026-04-13 00:07:31'),(1583,3,2,24,1.00,1.00,13455.00,0,'1% IASEP Sepelio','2026-04-13 00:07:31'),(1584,3,2,25,1.00,8.00,107640.00,0,'8% IASEP Voluntario','2026-04-13 00:07:31'),(1585,3,2,27,1.00,1.00,13455.00,0,'1% IPS para categoría 21 o inferior','2026-04-13 00:07:31'),(1586,3,2,28,1.00,2.00,26910.00,0,'2% IPS para categoría 22 o inferior','2026-04-13 00:07:31'),(1975,5,3,33,1.00,50.00,1365000.00,0,'Sueldo Anual Complementario - 50% de conceptos remunerativos','2026-04-13 01:45:33'),(1976,5,3,22,1.00,11.00,150150.00,0,'11% Caja de Previsión Social sobre aguinaldo','2026-04-13 01:45:33'),(1977,5,3,27,1.00,1.00,13650.00,0,'1% IPS sobre aguinaldo','2026-04-13 01:45:33'),(1978,5,3,28,1.00,2.00,27300.00,0,'2% IPS sobre aguinaldo','2026-04-13 01:45:33'),(1979,5,3,29,1.00,2.00,27300.00,0,'Descuento gremial sobre aguinaldo','2026-04-13 01:45:33'),(1980,5,3,31,1.00,15.00,176085.00,0,'Embargo judicial sobre aguinaldo menos aportes','2026-04-13 01:45:33'),(1981,5,1,33,1.00,50.00,621000.00,0,'Sueldo Anual Complementario - 50% de conceptos remunerativos','2026-04-13 01:45:33'),(1982,5,1,22,1.00,11.00,68310.00,0,'11% Caja de Previsión Social sobre aguinaldo','2026-04-13 01:45:33'),(1983,5,1,27,1.00,1.00,6210.00,0,'1% IPS sobre aguinaldo','2026-04-13 01:45:33'),(1984,5,1,28,1.00,2.00,12420.00,0,'2% IPS sobre aguinaldo','2026-04-13 01:45:33'),(1985,5,4,33,1.00,50.00,621000.00,0,'Sueldo Anual Complementario - 50% de conceptos remunerativos','2026-04-13 01:45:33'),(1986,5,4,22,1.00,11.00,68310.00,0,'11% Caja de Previsión Social sobre aguinaldo','2026-04-13 01:45:33'),(1987,5,4,27,1.00,1.00,6210.00,0,'1% IPS sobre aguinaldo','2026-04-13 01:45:33'),(1988,5,4,28,1.00,2.00,12420.00,0,'2% IPS sobre aguinaldo','2026-04-13 01:45:33'),(1989,5,16,33,1.00,50.00,465750.00,0,'Sueldo Anual Complementario - 50% de conceptos remunerativos','2026-04-13 01:45:33'),(1990,5,16,22,1.00,11.00,51232.50,0,'11% Caja de Previsión Social sobre aguinaldo','2026-04-13 01:45:33'),(1991,5,12,33,1.00,50.00,569250.00,0,'Sueldo Anual Complementario - 50% de conceptos remunerativos','2026-04-13 01:45:33'),(1992,5,12,22,1.00,11.00,62617.50,0,'11% Caja de Previsión Social sobre aguinaldo','2026-04-13 01:45:33'),(1993,5,12,27,1.00,1.00,5692.50,0,'1% IPS sobre aguinaldo','2026-04-13 01:45:33'),(1994,5,12,28,1.00,2.00,11385.00,0,'2% IPS sobre aguinaldo','2026-04-13 01:45:33'),(1995,5,14,33,1.00,50.00,543375.00,0,'Sueldo Anual Complementario - 50% de conceptos remunerativos','2026-04-13 01:45:33'),(1996,5,14,22,1.00,11.00,59771.25,0,'11% Caja de Previsión Social sobre aguinaldo','2026-04-13 01:45:33'),(1997,5,14,27,1.00,1.00,5433.75,0,'1% IPS sobre aguinaldo','2026-04-13 01:45:33'),(1998,5,14,28,1.00,2.00,10867.50,0,'2% IPS sobre aguinaldo','2026-04-13 01:45:33'),(1999,5,13,33,1.00,50.00,543375.00,0,'Sueldo Anual Complementario - 50% de conceptos remunerativos','2026-04-13 01:45:33'),(2000,5,13,22,1.00,11.00,59771.25,0,'11% Caja de Previsión Social sobre aguinaldo','2026-04-13 01:45:33'),(2001,5,13,27,1.00,1.00,5433.75,0,'1% IPS sobre aguinaldo','2026-04-13 01:45:33'),(2002,5,13,28,1.00,2.00,10867.50,0,'2% IPS sobre aguinaldo','2026-04-13 01:45:33'),(2003,5,15,33,1.00,50.00,491625.00,0,'Sueldo Anual Complementario - 50% de conceptos remunerativos','2026-04-13 01:45:33'),(2004,5,15,22,1.00,11.00,54078.75,0,'11% Caja de Previsión Social sobre aguinaldo','2026-04-13 01:45:33'),(2005,5,17,33,1.00,50.00,491625.00,0,'Sueldo Anual Complementario - 50% de conceptos remunerativos','2026-04-13 01:45:33'),(2006,5,17,22,1.00,11.00,54078.75,0,'11% Caja de Previsión Social sobre aguinaldo','2026-04-13 01:45:33'),(2007,5,2,33,1.00,50.00,672750.00,0,'Sueldo Anual Complementario - 50% de conceptos remunerativos','2026-04-13 01:45:33'),(2008,5,2,22,1.00,11.00,74002.50,0,'11% Caja de Previsión Social sobre aguinaldo','2026-04-13 01:45:33'),(2009,5,2,27,1.00,1.00,6727.50,0,'1% IPS sobre aguinaldo','2026-04-13 01:45:33'),(2010,5,2,28,1.00,2.00,13455.00,0,'2% IPS sobre aguinaldo','2026-04-13 01:45:33');
/*!40000 ALTER TABLE `liquidacion_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liquidacion_empleado`
--

DROP TABLE IF EXISTS `liquidacion_empleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `liquidacion_empleado` (
  `id` int NOT NULL AUTO_INCREMENT,
  `liquidacion_id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `total_remunerativo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_descuentos` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_no_remunerativo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_asignaciones` decimal(12,2) NOT NULL DEFAULT '0.00',
  `neto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_liq_emp_liquidacion` (`liquidacion_id`),
  KEY `idx_liq_emp_empleado` (`empleado_id`),
  CONSTRAINT `fk_liquidacion_empleado_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`),
  CONSTRAINT `fk_liquidacion_empleado_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidacion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liquidacion_empleado`
--

LOCK TABLES `liquidacion_empleado` WRITE;
/*!40000 ALTER TABLE `liquidacion_empleado` DISABLE KEYS */;
INSERT INTO `liquidacion_empleado` VALUES (2,2,1,0.00,0.00,0.00,0.00,0.00,'2026-03-24 16:00:43'),(4,1,1,1342000.00,381760.00,0.00,0.00,960240.00,'2026-03-24 22:07:20'),(105,4,3,2730000.00,820400.00,0.00,45000.00,1954600.00,'2026-04-12 14:40:18'),(106,4,1,1242000.00,347760.00,0.00,0.00,894240.00,'2026-04-12 14:40:18'),(107,4,4,1242000.00,347760.00,0.00,0.00,894240.00,'2026-04-12 14:40:18'),(108,4,16,931500.00,232875.00,0.00,0.00,698625.00,'2026-04-12 14:40:18'),(109,4,12,1138500.00,318780.00,0.00,0.00,819720.00,'2026-04-12 14:40:18'),(110,4,14,1086750.00,304290.00,0.00,0.00,782460.00,'2026-04-12 14:40:18'),(111,4,13,1086750.00,304290.00,0.00,0.00,782460.00,'2026-04-12 14:40:19'),(112,4,15,983250.00,245812.50,0.00,0.00,737437.50,'2026-04-12 14:40:19'),(113,4,17,983250.00,245812.50,0.00,0.00,737437.50,'2026-04-12 14:40:19'),(114,4,2,1345500.00,376740.00,0.00,0.00,968760.00,'2026-04-12 14:40:19'),(155,3,3,2730000.00,1113840.00,0.00,90000.00,1706160.00,'2026-04-13 00:07:31'),(156,3,1,1242000.00,347760.00,0.00,0.00,894240.00,'2026-04-13 00:07:31'),(157,3,4,1242000.00,347760.00,0.00,0.00,894240.00,'2026-04-13 00:07:31'),(158,3,16,931500.00,232875.00,0.00,0.00,698625.00,'2026-04-13 00:07:31'),(159,3,12,1138500.00,318780.00,0.00,0.00,819720.00,'2026-04-13 00:07:31'),(160,3,14,1086750.00,304290.00,0.00,0.00,782460.00,'2026-04-13 00:07:31'),(161,3,13,1086750.00,304290.00,0.00,0.00,782460.00,'2026-04-13 00:07:31'),(162,3,15,983250.00,245812.50,0.00,0.00,737437.50,'2026-04-13 00:07:31'),(163,3,17,983250.00,245812.50,0.00,0.00,737437.50,'2026-04-13 00:07:31'),(164,3,2,1345500.00,376740.00,0.00,0.00,968760.00,'2026-04-13 00:07:31'),(205,5,3,1365000.00,394485.00,0.00,0.00,970515.00,'2026-04-13 01:45:33'),(206,5,1,621000.00,86940.00,0.00,0.00,534060.00,'2026-04-13 01:45:33'),(207,5,4,621000.00,86940.00,0.00,0.00,534060.00,'2026-04-13 01:45:33'),(208,5,16,465750.00,51232.50,0.00,0.00,414517.50,'2026-04-13 01:45:33'),(209,5,12,569250.00,79695.00,0.00,0.00,489555.00,'2026-04-13 01:45:33'),(210,5,14,543375.00,76072.50,0.00,0.00,467302.50,'2026-04-13 01:45:33'),(211,5,13,543375.00,76072.50,0.00,0.00,467302.50,'2026-04-13 01:45:33'),(212,5,15,491625.00,54078.75,0.00,0.00,437546.25,'2026-04-13 01:45:33'),(213,5,17,491625.00,54078.75,0.00,0.00,437546.25,'2026-04-13 01:45:33'),(214,5,2,672750.00,94185.00,0.00,0.00,578565.00,'2026-04-13 01:45:33');
/*!40000 ALTER TABLE `liquidacion_empleado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oficina`
--

DROP TABLE IF EXISTS `oficina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oficina` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institucion_id` int NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `institucion_id` (`institucion_id`),
  CONSTRAINT `oficina_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `institucion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oficina`
--

LOCK TABLES `oficina` WRITE;
/*!40000 ALTER TABLE `oficina` DISABLE KEYS */;
INSERT INTO `oficina` VALUES (1,1,'Poder Ejecutivo'),(2,1,'Honorable Concejo Deliberante');
/*!40000 ALTER TABLE `oficina` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `situacion`
--

DROP TABLE IF EXISTS `situacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `situacion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `situacion`
--

LOCK TABLES `situacion` WRITE;
/*!40000 ALTER TABLE `situacion` DISABLE KEYS */;
INSERT INTO `situacion` VALUES (1,'Planta Permanente'),(2,'Planta Temporaria');
/*!40000 ALTER TABLE `situacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clave` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('ADMIN','OPERADOR') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OPERADOR',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `primer_ingreso` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario_nombre_usuario` (`nombre_usuario`),
  UNIQUE KEY `uq_usuario_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'milton','chavez','MILCHAV','chavezmilton082@gmail.com','$2y$10$GvjJPnhtPsxp99Ekn2mqMuWPbYQc9o0YmThPs5zlHVaR4JsXcQHSK','ADMIN',1,0,'2026-03-22 22:42:10','2026-03-27 21:02:26'),(2,'Juan','Perez','JPEREZ','miltonchavez82@hotmail.com','$2y$10$DQn3mBSvYHHqKucYrjYilebG55fi/84FVIzvsKUnXiJZGnqoh9Xwu','OPERADOR',1,0,'2026-04-30 22:18:14','2026-04-30 22:26:45');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-08 22:06:43
