-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: localhost    Database: tthh
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `area` (
  `AreaId` int(11) NOT NULL AUTO_INCREMENT,
  `Area` varchar(120) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `CreadoEn` datetime NOT NULL DEFAULT current_timestamp(),
  `ActualizadoEn` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`AreaId`),
  UNIQUE KEY `uq_area` (`Area`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area`
--

/*!40000 ALTER TABLE `area` DISABLE KEYS */;
INSERT INTO `area` VALUES (2,'CAJAS',1,'2026-01-07 23:01:08','2026-01-07 23:02:27'),(3,'OPERATIVA',1,'2026-01-07 23:01:23',NULL),(4,'ATENCION AL USUARIO',1,'2026-01-07 23:01:37',NULL),(5,'RECEPCION',1,'2026-01-07 23:01:45',NULL),(6,'TESORERIA',1,'2026-01-07 23:01:50',NULL),(7,'CONTABILIDAD',1,'2026-01-07 23:01:56',NULL),(8,'INFORMATICA',1,'2026-01-07 23:02:03',NULL),(9,'ELECTRICIDAD',1,'2026-01-07 23:02:08',NULL),(10,'PLOMERIA',1,'2026-01-07 23:02:14',NULL);
/*!40000 ALTER TABLE `area` ENABLE KEYS */;

--
-- Table structure for table `cargo`
--

DROP TABLE IF EXISTS `cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cargo` (
  `CargoId` int(11) NOT NULL AUTO_INCREMENT,
  `Cargo` varchar(120) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `CreadoEn` datetime NOT NULL DEFAULT current_timestamp(),
  `ActualizadoEn` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`CargoId`),
  UNIQUE KEY `uq_cargo` (`Cargo`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargo`
--

/*!40000 ALTER TABLE `cargo` DISABLE KEYS */;
INSERT INTO `cargo` VALUES (3,'GERENTE GENERAL',1,'2026-01-07 22:23:47',NULL),(4,'JEFE OPERATIVO',1,'2026-01-07 22:31:07',NULL),(5,'TESORERO ADMINISTRATIVO',1,'2026-01-07 22:31:25',NULL),(6,'CONTADOR/A',1,'2026-01-07 22:31:49','2026-01-07 22:32:26'),(7,'JEFE FACTURACION',1,'2026-01-07 22:31:56',NULL),(8,'SECRETARIO/A ADMINISTRATIVO/A',1,'2026-01-07 22:32:13',NULL),(9,'CAJERO/A',1,'2026-01-07 22:41:44',NULL),(10,'AUXILIAR TESORERIA',1,'2026-01-07 22:41:56',NULL),(11,'ENC. DE TI',1,'2026-01-07 22:42:25',NULL),(12,'CALL CENTER',1,'2026-01-07 22:42:46',NULL),(13,'SERVICIOS GENERALES',1,'2026-01-07 22:42:58',NULL),(14,'PLOMERIA',1,'2026-01-07 22:43:46',NULL),(15,'ENC. ELECTRICISTA',1,'2026-01-07 22:44:58',NULL);
/*!40000 ALTER TABLE `cargo` ENABLE KEYS */;

--
-- Table structure for table `colaboradores`
--

DROP TABLE IF EXISTS `colaboradores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colaboradores` (
  `ColaboradorId` int(11) NOT NULL AUTO_INCREMENT,
  `Legajo` varchar(30) DEFAULT NULL,
  `Nombres` varchar(120) NOT NULL,
  `Apellidos` varchar(120) NOT NULL,
  `TipoDocumentoId` int(11) DEFAULT NULL,
  `NroDocumento` varchar(40) DEFAULT NULL,
  `CodigoReloj` varchar(30) DEFAULT NULL,
  `RUC` char(30) DEFAULT NULL,
  `EstadoCivilId` int(11) DEFAULT NULL,
  `FechaNacimiento` date DEFAULT NULL,
  `GrupoSanguineo` char(5) DEFAULT NULL,
  `VencimientoCI` date DEFAULT NULL,
  `SalarioBase` decimal(18,0) DEFAULT NULL,
  `PlusCargo` decimal(18,0) DEFAULT NULL,
  `NroAseguradoIPS` char(20) DEFAULT NULL,
  `Email` varchar(120) DEFAULT NULL,
  `Telefono` varchar(60) DEFAULT NULL,
  `TelefonoParticular` varchar(30) DEFAULT NULL,
  `BonificacionFamiliar` tinyint(1) NOT NULL DEFAULT 0,
  `Aguinaldo` tinyint(1) NOT NULL DEFAULT 1,
  `FotoSelfiePath` varchar(255) DEFAULT NULL,
  `FotoCIFrentePath` varchar(255) DEFAULT NULL,
  `FotoCIAtrasPath` varchar(255) DEFAULT NULL,
  `Observacion` mediumtext DEFAULT NULL,
  `Direccion` varchar(200) DEFAULT NULL,
  `PaisId` int(11) DEFAULT NULL,
  `DptoId` int(11) DEFAULT NULL,
  `DistritoId` int(11) DEFAULT NULL,
  `LocalidadId` int(11) DEFAULT NULL,
  `CargoId` int(11) DEFAULT NULL,
  `AreaId` int(11) DEFAULT NULL,
  `SectorId` int(11) DEFAULT NULL,
  `TurnoId` int(11) DEFAULT NULL,
  `TipoId` int(11) DEFAULT NULL,
  `FormaPagoId` int(11) DEFAULT NULL,
  `FechaIngreso` date DEFAULT NULL,
  `FechaEgreso` date DEFAULT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `CreadoEn` datetime NOT NULL DEFAULT current_timestamp(),
  `ActualizadoEn` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`ColaboradorId`),
  UNIQUE KEY `uk_colab_doc` (`TipoDocumentoId`,`NroDocumento`),
  UNIQUE KEY `uk_colab_codigo_reloj` (`CodigoReloj`),
  KEY `ix_colab_nombre` (`Apellidos`,`Nombres`),
  KEY `ix_colab_activo` (`Activo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colaboradores`
--

/*!40000 ALTER TABLE `colaboradores` DISABLE KEYS */;
INSERT INTO `colaboradores` VALUES (1,'PR001','PROBANDO','PRUEBA',1,'12345',NULL,'12345-0',1,'1996-01-01','0+','2030-03-01',1500000,500000,'123','asd@as.com','999111222','',1,1,NULL,NULL,NULL,'esto es una prueba','ASDFASDFASDF',1,12,170,170,12,4,8,2,2,3,'2000-01-01',NULL,1,'2026-01-09 00:05:24','2026-01-12 16:03:41');
/*!40000 ALTER TABLE `colaboradores` ENABLE KEYS */;

--
-- Table structure for table `departamento`
--

DROP TABLE IF EXISTS `departamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departamento` (
  `DptoId` int(11) NOT NULL,
  `DptoDes` varchar(120) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`DptoId`),
  UNIQUE KEY `uk_departamento_des` (`DptoDes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamento`
--

/*!40000 ALTER TABLE `departamento` DISABLE KEYS */;
INSERT INTO `departamento` VALUES (1,'CAPITAL',1),(2,'CONCEPCION',1),(3,'SAN PEDRO',1),(4,'CORDILLERA',1),(5,'GUAIRA',1),(6,'CAAGUAZU',1),(7,'CAAZAPA',1),(8,'ITAPUA',1),(9,'MISIONES',1),(10,'PARAGUARI',1),(11,'ALTO PARANA',1),(12,'CENTRAL',1),(13,'NEEMBUCU',1),(14,'AMAMBAY',1),(15,'PTE. HAYES',1),(16,'BOQUERON',1),(17,'ALTO PARAGUAY',1),(18,'CANINDEYU',1);
/*!40000 ALTER TABLE `departamento` ENABLE KEYS */;

--
-- Table structure for table `distrito`
--

DROP TABLE IF EXISTS `distrito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `distrito` (
  `DistritoId` int(11) NOT NULL,
  `DistritoDes` varchar(120) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`DistritoId`),
  UNIQUE KEY `uk_distrito_des` (`DistritoDes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distrito`
--

/*!40000 ALTER TABLE `distrito` DISABLE KEYS */;
INSERT INTO `distrito` VALUES (1,'ASUNCION (DISTRITO)',1),(2,'CONCEPCION (MUNICIPIO)',1),(3,'SAN LAZARO',1),(4,'SAN CARLOS',1),(5,'BELEN',1),(6,'LORETO',1),(7,'HORQUETA',1),(8,'SAN SALVADOR',1),(9,'YBY YA\'U',1),(10,'SAN PEDRO DE YCUAMANDYYU',1),(11,'ANTEQUERA',1),(12,'GRAL. E.AQUINO',1),(13,'ITACURUBI DEL ROSARIO',1),(14,'SAN ESTANISLAO (SANTANI)',1),(15,'LIMA',1),(16,'NUEVA GERMANIA',1),(17,'TACUATI',1),(18,'UNION',1),(19,'25 DE DICIEMBRE',1),(20,'VILLA DEL ROSARIO',1),(21,'YATAITY DEL NORTE',1),(22,'ISIDORO RESQUIN',1),(23,'CHORE',1),(24,'SAN PABLO',1),(25,'SAN JOSE DEL ROSARIO',1),(26,'CAACUPE',1),(27,'ALTOS',1),(28,'ARROYOS Y ESTEROS',1),(29,'ATYRA',1),(30,'CARAGUATAY',1),(31,'EMBOSCADA',1),(32,'EUSEBIO AYALA',1),(33,'ISLA PUCU',1),(34,'ITACURUBI DE LA CORDILLERA',1),(35,'JUAN DE MENA',1),(36,'NUEVA COLOMBIA',1),(37,'PIRIBEBUY',1),(38,'1RO.DE MARZO',1),(39,'SAN BERNARDINO',1),(40,'SANTA ELENA',1),(41,'TOBATI',1),(42,'VALENZUELA',1),(43,'LOMA GRANDE',1),(44,'SAN JOSE OBRERO',1),(45,'VILLARRICA',1),(47,'BORJA',1),(48,'INDEPENDENCIA (R.D.MELGAREJO)',1),(49,'GRAL.EUGENIO A. GARAY',1),(50,'CNEL. MARTINEZ',1),(51,'JOSE FASSARDI',1),(52,'FELIX PEREZ CARDOZO',1),(53,'MAURICIO JOSE TROCHE',1),(54,'ITAPE',1),(55,'ITURBE',1),(56,'MBOCAYATY',1),(57,'NATALICIO TALAVERA',1),(58,' UMI',1),(59,'YATAITY',1),(60,'DR. BOTREL',1),(61,'CNEL. OVIEDO',1),(62,'CAAGUAZU',1),(63,'CARAYAO',1),(64,'CECILIO BAEZ',1),(65,'NUEVA LONDRES',1),(66,'SAN JOAQUIN',1),(67,'SAN JOSE DE LOS ARROYOS',1),(68,'YHU',1),(69,'JUAN MANUEL FRUTOS',1),(70,'REPATRIACION',1),(71,'SANTA ROSA DEL MBUTUY',1),(72,'J. EULOGIO ESTIGARRIBIA',1),(73,'JOSE D. OCAMPOS',1),(74,'R.I.3 CORRALES',1),(75,'RAUL A. OVIEDO',1),(76,'MCAL.F.SOLANO LOPEZ',1),(77,'CAAZAPA',1),(78,'BUENA VISTA',1),(79,'GRAL. H. MORINIGO',1),(80,'MACIEL',1),(81,'MOISES BERTONI',1),(82,'SAN JUAN NEPOMUCENO',1),(83,'ABAI',1),(84,'TAVAI',1),(85,'YEGROS',1),(86,'YUTY',1),(87,'ENCARNACION',1),(88,'BELLA VISTA',1),(89,'CAMBYRETA',1),(90,'CAPITAN MEZA',1),(91,'CARMEN DEL PARANA',1),(92,'CAPITAN MIRANDA',1),(93,'CORONEL BOGADO',1),(94,'FRAM',1),(95,'GRAL. ARTIGAS',1),(96,'GRAL. DELGADO',1),(97,'HOHENAU',1),(98,'JESUS',1),(99,'OBLIGADO',1),(100,'SAN COSME Y DAMIAN',1),(101,'SAN PEDRO DEL PARANA',1),(102,'NUEVA ALBORADA',1),(103,'TRINIDAD',1),(104,'NATALIO',1),(105,'JOSE LEANDRO OVIEDO',1),(106,'SAN RAFAEL DEL PARANA',1),(107,'CARLOS A. LOPEZ',1),(108,'JULIO D. OTA O',1),(109,'EDELIRA',1),(110,'SAN JUAN DEL PARANA',1),(111,'LA PAZ',1),(112,'TOMAS R. PEREIRA',1),(113,'YATYTAY',1),(114,'HERIBERTA S.DE IGLESIAS',1),(115,'SAN JUAN BAUTISTA',1),(116,'AYOLAS',1),(117,'SAN IGNACIO',1),(118,'SAN MIGUEL',1),(119,'SAN PATRICIO',1),(120,'SANTIAGO',1),(121,'SANTA MARIA',1),(122,'SANTA ROSA',1),(123,'VILLA FLORIDA',1),(124,'YABEBYRY',1),(125,'PARAGUARI',1),(126,'ACAHAY',1),(127,'CAAPUCU',1),(128,'CABALLERO',1),(129,'CARAPEGUA',1),(130,'LA COLMENA',1),(131,'ESCOBAR',1),(132,'MBUYAPEY',1),(133,'PIRAYU',1),(134,'QUIINDY',1),(135,'ROQUE GONZALEZ',1),(136,'SAPUCAI',1),(137,'YBYCUI',1),(138,'QUYQUYHO',1),(139,'YBYTYMI',1),(140,'TEBICUARY MI',1),(141,'YAGUARON',1),(142,'HERNANDARIAS',1),(143,'DOMINGO MARTINEZ DE IRALA',1),(144,' ACUNDAY',1),(145,'CIUDAD DEL ESTE',1),(146,'JUAN LEON MALLORQUIN',1),(147,'ITAKYRY',1),(148,'JUAN E.O\'LEARY',1),(149,'PUERTO PTE.FRANCO',1),(150,'YGUAZU',1),(151,'SAN CRISTOBAL',1),(152,'AREGUA',1),(153,'CAPIATA',1),(154,'FERNANDO DE LA MORA',1),(155,'GUARAMBARE',1),(156,'ITA',1),(157,'ITAUGUA',1),(158,'LIMPIO',1),(159,'LUQUE',1),(160,'MARIANO ROQUE ALONSO',1),(161,' EMBY',1),(162,'NUEVA ITALIA',1),(163,'SAN ANTONIO',1),(164,'SAN LORENZO',1),(165,'VILLA ELISA',1),(166,'VILLETA',1),(167,'YPACARAI',1),(168,'YPANE',1),(169,'LAMBARE',1),(170,'J.AUGUSTO SALDIVAR',1),(171,'PILAR',1),(172,'ALBERDI',1),(173,'CERRITO',1),(174,'DESMOCHADOS',1),(175,'GUAZU CUA',1),(176,'HUMAITA',1),(177,'ISLA UMBU',1),(178,'LAURELES',1),(179,'PASO DE PATRIA',1),(180,'MAYOR J.D.MARTINEZ',1),(181,'SAN JUAN DE  EEMBUCU',1),(182,'TACUARAS',1),(183,'VILLA OLIVA',1),(184,'VILLALBIN',1),(185,'PEDRO JUAN CABALLERO',1),(187,'CAPITAN BADO',1),(188,'VILLA HAYES',1),(189,'BENJAMIN ACEVAL(MONTE SOCIEDAD',1),(190,'PTO.PINAZCO',1),(191,'NANAWA',1),(192,'MCAL.ESTIGARRIBIA',1),(193,'BOQUERON',1),(194,'PEDRO P.PE A',1),(195,'FUERTE OLIMPO',1),(196,'BAHIA NEGRA',1),(197,'SAN ISIDRO DEL CURUGUATY',1),(198,'VILLA YGATIMI',1),(199,'YPEJHU',1),(200,'CORPUS CHRISTI',1),(201,'ITANARA',1),(202,'FRANCISCO CABALLERO ALVAREZ',1),(203,'GRAL.JOSE EDUVIGIS DIAZ',1),(205,'VILLA FRANCA',1),(206,'POZO COLORADO',1),(207,'SALTO DEL GUAIRA',1),(209,'SANTA RITA',1),(210,'MINGA GUAZU',1),(211,'LOS CEDRALES',1),(212,'SAN ALBERTO',1),(213,'MINGA PORA',1),(214,'MBOCAYATY DEL YHAGUY',1),(215,'NARANJAL',1),(216,'SANTA ROSA DEL MONDAY',1),(217,'IRU A',1),(218,'PUERTO CASADO',1),(219,'MBARACAYU',1),(220,'GUAYAIBI',1),(221,'PIRAPO',1),(223,'PUERTO GUARANI',1),(225,'PASO YOBAY',1),(226,'CAPIIBARY',1),(227,'3 DE FEBRERO',1),(228,'SIMON BOLIVAR',1),(229,'LA PASTORA',1),(232,'ITAPUA POTY',1),(233,'MAYOR PABLO LAGERENZA',1),(234,'GRAL. EUGENIO A. GARAY',1),(235,'LA PALOMA',1),(236,'KATUETE',1),(237,'VAQUERIA',1),(239,'SANTA FE DEL PARANA',1),(240,'NUEVA ESPERANZA',1),(241,'YASY KA Y',1),(242,'JOSE FALCON',1),(243,'FEDERICO CHAVEZ',1),(244,'ESCULIES',1),(245,'YRYBUCUA',1),(246,'SIN EQUIVALENCIA',1),(259,'FILADELFIA',1),(265,'TEBICUARY',1),(270,'SARGENTO JOSE FELIX LOPEZ',1),(271,'YBYRAROBANA',1),(272,'3 DE MAYO',1),(274,'YBY PYTA',1),(275,'TEMBIAPORA',1),(277,'LOMA PYTA',1),(278,'KARAPAI',1);
/*!40000 ALTER TABLE `distrito` ENABLE KEYS */;

--
-- Table structure for table `empresa_parametros`
--

DROP TABLE IF EXISTS `empresa_parametros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_parametros` (
  `id` int(11) NOT NULL,
  `empresa` varchar(150) NOT NULL DEFAULT '',
  `ruc` varchar(50) NOT NULL DEFAULT '',
  `telefono` varchar(50) NOT NULL DEFAULT '',
  `direccion` varchar(200) NOT NULL DEFAULT '',
  `capital` varchar(120) NOT NULL DEFAULT '',
  `numero_patronal_ips` varchar(80) NOT NULL DEFAULT '',
  `cantidad_empleados` int(11) NOT NULL DEFAULT 0,
  `logo_path` varchar(255) DEFAULT NULL,
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_parametros`
--

/*!40000 ALTER TABLE `empresa_parametros` DISABLE KEYS */;
INSERT INTO `empresa_parametros` VALUES (1,'Mi Empresa S.A.','1234567890','0999999999','Av. Principal 123, Ciudad','1000000','NP-123456',50,'public/assets/img/logo_empresa_20260107_212723.png','2026-01-07 21:27:23');
/*!40000 ALTER TABLE `empresa_parametros` ENABLE KEYS */;

--
-- Table structure for table `estadocivil`
--

DROP TABLE IF EXISTS `estadocivil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estadocivil` (
  `EstadoCivilId` int(11) NOT NULL AUTO_INCREMENT,
  `EstadoCivilDes` varchar(80) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`EstadoCivilId`),
  UNIQUE KEY `uk_estadocivil_des` (`EstadoCivilDes`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estadocivil`
--

/*!40000 ALTER TABLE `estadocivil` DISABLE KEYS */;
INSERT INTO `estadocivil` VALUES (1,'SOLTERO/A',1),(2,'CASADO/A',1);
/*!40000 ALTER TABLE `estadocivil` ENABLE KEYS */;

--
-- Table structure for table `formapago`
--

DROP TABLE IF EXISTS `formapago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `formapago` (
  `FormaPagoId` int(11) NOT NULL AUTO_INCREMENT,
  `FormaPagoDes` varchar(120) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`FormaPagoId`),
  UNIQUE KEY `uk_formapago_des` (`FormaPagoDes`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formapago`
--

/*!40000 ALTER TABLE `formapago` DISABLE KEYS */;
INSERT INTO `formapago` VALUES (1,'EFECTIVO',1),(2,'TRANSFERENCIA',1),(3,'CUENTA BANCARIA',1);
/*!40000 ALTER TABLE `formapago` ENABLE KEYS */;

--
-- Table structure for table `localidad`
--

DROP TABLE IF EXISTS `localidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `localidad` (
  `LocalidadId` int(11) NOT NULL,
  `LocalidadDes` varchar(120) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`LocalidadId`),
  UNIQUE KEY `uk_localidad_des` (`LocalidadDes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `localidad`
--

/*!40000 ALTER TABLE `localidad` DISABLE KEYS */;
INSERT INTO `localidad` VALUES (1,'ASUNCION (DISTRITO)',1),(2,'CONCEPCION (MUNICIPIO)',1),(3,'SAN LAZARO',1),(4,'SAN CARLOS',1),(5,'BELEN',1),(6,'LORETO',1),(7,'HORQUETA',1),(8,'SAN SALVADOR',1),(9,'YBY YA\'U',1),(10,'SAN PEDRO DE YCUAMANDYYU',1),(11,'ANTEQUERA',1),(12,'GRAL. E.AQUINO',1),(13,'ITACURUBI DEL ROSARIO',1),(14,'SAN ESTANISLAO (SANTANI)',1),(15,'LIMA',1),(16,'NUEVA GERMANIA',1),(17,'TACUATI',1),(18,'UNION',1),(19,'25 DE DICIEMBRE',1),(20,'VILLA DEL ROSARIO',1),(21,'YATAITY DEL NORTE',1),(22,'ISIDORO RESQUIN',1),(23,'CHORE',1),(24,'SAN PABLO',1),(25,'SAN JOSE DEL ROSARIO',1),(26,'CAACUPE',1),(27,'ALTOS',1),(28,'ARROYOS Y ESTEROS',1),(29,'ATYRA',1),(30,'CARAGUATAY',1),(31,'EMBOSCADA',1),(32,'EUSEBIO AYALA',1),(33,'ISLA PUCU',1),(34,'ITACURUBI DE LA CORDILLERA',1),(35,'JUAN DE MENA',1),(36,'NUEVA COLOMBIA',1),(37,'PIRIBEBUY',1),(38,'1RO.DE MARZO',1),(39,'SAN BERNARDINO',1),(40,'SANTA ELENA',1),(41,'TOBATI',1),(42,'VALENZUELA',1),(43,'LOMA GRANDE',1),(44,'SAN JOSE OBRERO',1),(45,'VILLARRICA',1),(47,'BORJA',1),(48,'INDEPENDENCIA (R.D.MELGAREJO)',1),(49,'GRAL.EUGENIO A. GARAY',1),(50,'CNEL. MARTINEZ',1),(51,'JOSE FASSARDI',1),(52,'FELIX PEREZ CARDOZO',1),(53,'MAURICIO JOSE TROCHE',1),(54,'ITAPE',1),(55,'ITURBE',1),(56,'MBOCAYATY',1),(57,'NATALICIO TALAVERA',1),(58,' UMI',1),(59,'YATAITY',1),(60,'DR. BOTREL',1),(61,'CNEL. OVIEDO',1),(62,'CAAGUAZU',1),(63,'CARAYAO',1),(64,'CECILIO BAEZ',1),(65,'NUEVA LONDRES',1),(66,'SAN JOAQUIN',1),(67,'SAN JOSE DE LOS ARROYOS',1),(68,'YHU',1),(69,'JUAN MANUEL FRUTOS',1),(70,'REPATRIACION',1),(71,'SANTA ROSA DEL MBUTUY',1),(72,'J. EULOGIO ESTIGARRIBIA',1),(73,'JOSE D. OCAMPOS',1),(74,'R.I.3 CORRALES',1),(75,'RAUL A. OVIEDO',1),(76,'MCAL.F.SOLANO LOPEZ',1),(77,'CAAZAPA',1),(78,'BUENA VISTA',1),(79,'GRAL. H. MORINIGO',1),(80,'MACIEL',1),(81,'MOISES BERTONI',1),(82,'SAN JUAN NEPOMUCENO',1),(83,'ABAI',1),(84,'TAVAI',1),(85,'YEGROS',1),(86,'YUTY',1),(87,'ENCARNACION',1),(88,'BELLA VISTA',1),(89,'CAMBYRETA',1),(90,'CAPITAN MEZA',1),(91,'CARMEN DEL PARANA',1),(92,'CAPITAN MIRANDA',1),(93,'CORONEL BOGADO',1),(94,'FRAM',1),(95,'GRAL. ARTIGAS',1),(96,'GRAL. DELGADO',1),(97,'HOHENAU',1),(98,'JESUS',1),(99,'OBLIGADO',1),(100,'SAN COSME Y DAMIAN',1),(101,'SAN PEDRO DEL PARANA',1),(102,'NUEVA ALBORADA',1),(103,'TRINIDAD',1),(104,'NATALIO',1),(105,'JOSE LEANDRO OVIEDO',1),(106,'SAN RAFAEL DEL PARANA',1),(107,'CARLOS A. LOPEZ',1),(108,'JULIO D. OTA O',1),(109,'EDELIRA',1),(110,'SAN JUAN DEL PARANA',1),(111,'LA PAZ',1),(112,'TOMAS R. PEREIRA',1),(113,'YATYTAY',1),(114,'HERIBERTA S.DE IGLESIAS',1),(115,'SAN JUAN BAUTISTA',1),(116,'AYOLAS',1),(117,'SAN IGNACIO',1),(118,'SAN MIGUEL',1),(119,'SAN PATRICIO',1),(120,'SANTIAGO',1),(121,'SANTA MARIA',1),(122,'SANTA ROSA',1),(123,'VILLA FLORIDA',1),(124,'YABEBYRY',1),(125,'PARAGUARI',1),(126,'ACAHAY',1),(127,'CAAPUCU',1),(128,'CABALLERO',1),(129,'CARAPEGUA',1),(130,'LA COLMENA',1),(131,'ESCOBAR',1),(132,'MBUYAPEY',1),(133,'PIRAYU',1),(134,'QUIINDY',1),(135,'ROQUE GONZALEZ',1),(136,'SAPUCAI',1),(137,'YBYCUI',1),(138,'QUYQUYHO',1),(139,'YBYTYMI',1),(140,'TEBICUARY MI',1),(141,'YAGUARON',1),(142,'HERNANDARIAS',1),(143,'DOMINGO MARTINEZ DE IRALA',1),(144,' ACUNDAY',1),(145,'CIUDAD DEL ESTE',1),(146,'JUAN LEON MALLORQUIN',1),(147,'ITAKYRY',1),(148,'JUAN E.O\'LEARY',1),(149,'PUERTO PTE.FRANCO',1),(150,'YGUAZU',1),(151,'SAN CRISTOBAL',1),(152,'AREGUA',1),(153,'CAPIATA',1),(154,'FERNANDO DE LA MORA',1),(155,'GUARAMBARE',1),(156,'ITA',1),(157,'ITAUGUA',1),(158,'LIMPIO',1),(159,'LUQUE',1),(160,'MARIANO ROQUE ALONSO',1),(161,' EMBY',1),(162,'NUEVA ITALIA',1),(163,'SAN ANTONIO',1),(164,'SAN LORENZO',1),(165,'VILLA ELISA',1),(166,'VILLETA',1),(167,'YPACARAI',1),(168,'YPANE',1),(169,'LAMBARE',1),(170,'J.AUGUSTO SALDIVAR',1),(171,'PILAR',1),(172,'ALBERDI',1),(173,'CERRITO',1),(174,'DESMOCHADOS',1),(175,'GUAZU CUA',1),(176,'HUMAITA',1),(177,'ISLA UMBU',1),(178,'LAURELES',1),(179,'PASO DE PATRIA',1),(180,'MAYOR J.D.MARTINEZ',1),(181,'SAN JUAN DE  EEMBUCU',1),(182,'TACUARAS',1),(183,'VILLA OLIVA',1),(184,'VILLALBIN',1),(185,'PEDRO JUAN CABALLERO',1),(187,'CAPITAN BADO',1),(188,'VILLA HAYES',1),(189,'BENJAMIN ACEVAL(MONTE SOCIEDAD',1),(190,'PTO.PINAZCO',1),(191,'NANAWA',1),(192,'MCAL.ESTIGARRIBIA',1),(193,'BOQUERON',1),(194,'PEDRO P.PE A',1),(195,'FUERTE OLIMPO',1),(196,'BAHIA NEGRA',1),(197,'SAN ISIDRO DEL CURUGUATY',1),(198,'VILLA YGATIMI',1),(199,'YPEJHU',1),(200,'CORPUS CHRISTI',1),(201,'ITANARA',1),(202,'FRANCISCO CABALLERO ALVAREZ',1),(203,'GRAL.JOSE EDUVIGIS DIAZ',1),(205,'VILLA FRANCA',1),(206,'POZO COLORADO',1),(207,'SALTO DEL GUAIRA',1),(209,'SANTA RITA',1),(210,'MINGA GUAZU',1),(211,'LOS CEDRALES',1),(212,'SAN ALBERTO',1),(213,'MINGA PORA',1),(214,'MBOCAYATY DEL YHAGUY',1),(215,'NARANJAL',1),(216,'SANTA ROSA DEL MONDAY',1),(217,'IRU A',1),(218,'PUERTO CASADO',1),(219,'MBARACAYU',1),(220,'GUAYAIBI',1),(221,'PIRAPO',1),(223,'PUERTO GUARANI',1),(225,'PASO YOBAY',1),(226,'CAPIIBARY',1),(227,'3 DE FEBRERO',1),(228,'SIMON BOLIVAR',1),(229,'LA PASTORA',1),(232,'ITAPUA POTY',1),(233,'MAYOR PABLO LAGERENZA',1),(234,'GRAL. EUGENIO A. GARAY',1),(235,'LA PALOMA',1),(236,'KATUETE',1),(237,'VAQUERIA',1),(239,'SANTA FE DEL PARANA',1),(240,'NUEVA ESPERANZA',1),(241,'YASY KA Y',1),(242,'JOSE FALCON',1),(243,'FEDERICO CHAVEZ',1),(244,'ESCULIES',1),(245,'YRYBUCUA',1),(246,'SIN EQUIVALENCIA',1),(259,'FILADELFIA',1),(265,'TEBICUARY',1),(270,'SARGENTO JOSE FELIX LOPEZ',1),(271,'YBYRAROBANA',1),(272,'3 DE MAYO',1),(274,'YBY PYTA',1),(275,'TEMBIAPORA',1),(277,'LOMA PYTA',1),(278,'KARAPAI',1);
/*!40000 ALTER TABLE `localidad` ENABLE KEYS */;

--
-- Table structure for table `login_intentos`
--

DROP TABLE IF EXISTS `login_intentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_intentos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `ip` varchar(60) NOT NULL,
  `exitoso` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_usuario_fecha` (`usuario`,`creado_en`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_intentos`
--

/*!40000 ALTER TABLE `login_intentos` DISABLE KEYS */;
INSERT INTO `login_intentos` VALUES (6,'admin','127.0.0.1',0,'2026-01-07 21:13:48'),(7,'admin','127.0.0.1',0,'2026-01-07 21:14:00'),(8,'admin','127.0.0.1',0,'2026-01-07 21:21:13'),(9,'admin','127.0.0.1',0,'2026-01-07 21:21:26'),(10,'admin','127.0.0.1',1,'2026-01-07 21:26:49');
/*!40000 ALTER TABLE `login_intentos` ENABLE KEYS */;

--
-- Table structure for table `pais`
--

DROP TABLE IF EXISTS `pais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pais` (
  `PaisId` int(11) NOT NULL AUTO_INCREMENT,
  `PaisDes` varchar(120) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`PaisId`),
  UNIQUE KEY `uk_pais_des` (`PaisDes`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pais`
--

/*!40000 ALTER TABLE `pais` DISABLE KEYS */;
INSERT INTO `pais` VALUES (1,'PARAGUAY',1),(2,'ARGENTINA',1),(3,'BRASIL',1);
/*!40000 ALTER TABLE `pais` ENABLE KEYS */;

--
-- Table structure for table `reloj_movimientos`
--

DROP TABLE IF EXISTS `reloj_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reloj_movimientos` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `CodigoReloj` varchar(30) NOT NULL,
  `ColaboradorId` int(11) DEFAULT NULL,
  `FechaHora` datetime NOT NULL,
  `TipoEvento` varchar(30) DEFAULT NULL,
  `Dispositivo` varchar(80) DEFAULT NULL,
  `FuenteArchivo` varchar(120) DEFAULT NULL,
  `CreadoEn` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Id`),
  KEY `ix_reloj_cod` (`CodigoReloj`),
  KEY `ix_reloj_colab` (`ColaboradorId`),
  KEY `ix_reloj_fh` (`FechaHora`),
  CONSTRAINT `fk_reloj_colab` FOREIGN KEY (`ColaboradorId`) REFERENCES `colaboradores` (`ColaboradorId`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reloj_movimientos`
--

/*!40000 ALTER TABLE `reloj_movimientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `reloj_movimientos` ENABLE KEYS */;

--
-- Table structure for table `sector`
--

DROP TABLE IF EXISTS `sector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sector` (
  `SectorId` int(11) NOT NULL AUTO_INCREMENT,
  `Sector` varchar(120) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `CreadoEn` datetime NOT NULL DEFAULT current_timestamp(),
  `ActualizadoEn` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`SectorId`),
  UNIQUE KEY `uq_sector` (`Sector`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sector`
--

/*!40000 ALTER TABLE `sector` DISABLE KEYS */;
INSERT INTO `sector` VALUES (7,'TANQUE',1,'2026-01-07 22:49:37','2026-01-07 22:50:03'),(8,'CENTRAL',1,'2026-01-07 22:50:11',NULL);
/*!40000 ALTER TABLE `sector` ENABLE KEYS */;

--
-- Table structure for table `tipo`
--

DROP TABLE IF EXISTS `tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo` (
  `TipoId` int(11) NOT NULL AUTO_INCREMENT,
  `Tipo` varchar(120) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `CreadoEn` datetime NOT NULL DEFAULT current_timestamp(),
  `ActualizadoEn` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`TipoId`),
  UNIQUE KEY `uq_tipo` (`Tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo`
--

/*!40000 ALTER TABLE `tipo` DISABLE KEYS */;
INSERT INTO `tipo` VALUES (2,'FUNCIONARIO/A',1,'2026-01-07 23:02:44',NULL),(3,'DIRIGENTE',1,'2026-01-07 23:02:51',NULL),(4,'CONTRATADO/A',1,'2026-01-07 23:03:04',NULL),(5,'TERCERIZADO/A',1,'2026-01-07 23:03:12',NULL);
/*!40000 ALTER TABLE `tipo` ENABLE KEYS */;

--
-- Table structure for table `tipodocumento`
--

DROP TABLE IF EXISTS `tipodocumento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipodocumento` (
  `TipoDocumentoId` int(11) NOT NULL AUTO_INCREMENT,
  `TipoDocumentoDes` varchar(80) NOT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`TipoDocumentoId`),
  UNIQUE KEY `uk_tipodocumento_des` (`TipoDocumentoDes`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipodocumento`
--

/*!40000 ALTER TABLE `tipodocumento` DISABLE KEYS */;
INSERT INTO `tipodocumento` VALUES (1,'CI',1),(2,'PASAPORTE',1);
/*!40000 ALTER TABLE `tipodocumento` ENABLE KEYS */;

--
-- Table structure for table `turno`
--

DROP TABLE IF EXISTS `turno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `turno` (
  `TurnoId` int(11) NOT NULL AUTO_INCREMENT,
  `Turno` varchar(120) NOT NULL,
  `TurnoHoraEntrada` time DEFAULT NULL,
  `TurnoHoraSalida` time DEFAULT NULL,
  `TurnoHoraSaleAlmorzar` time DEFAULT NULL,
  `TurnoHoraEntraAlmorzar` time DEFAULT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `CreadoEn` datetime NOT NULL DEFAULT current_timestamp(),
  `ActualizadoEn` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`TurnoId`),
  UNIQUE KEY `uq_turno` (`Turno`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `turno`
--

/*!40000 ALTER TABLE `turno` DISABLE KEYS */;
INSERT INTO `turno` VALUES (2,'DIURNO NORMAL','07:00:00','17:00:00','12:00:00','13:00:00',1,'2026-01-07 23:03:44',NULL),(3,'DIURNO SABADOS','07:00:00','12:00:00','00:00:00','00:00:00',1,'2026-01-07 23:04:29',NULL),(4,'DIURNO DOMINGOS Y FERIADOS','07:00:00','12:00:00','00:00:00','00:00:00',1,'2026-01-07 23:05:04',NULL),(5,'NOCTURNO NORMAL','15:00:00','20:00:00','00:00:00','00:00:00',1,'2026-01-07 23:05:23',NULL),(6,'NOCTURNO SABADOS','12:00:00','20:00:00','00:00:00','00:00:00',1,'2026-01-07 23:05:39',NULL),(7,'NOCTURNO DOMINGOS Y FERIADOS','12:00:00','20:00:00','00:00:00','00:00:00',1,'2026-01-07 23:05:56',NULL);
/*!40000 ALTER TABLE `turno` ENABLE KEYS */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `pass_hash` varchar(255) NOT NULL,
  `rol` enum('ADMIN','RRHH','LECTURA') NOT NULL DEFAULT 'RRHH',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `intentos_fallidos` int(11) NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (2,'admin','Administrador','admin@local','$2y$10$B7i3t45R6lYfdv2bVYBijeVGvlcd5WG0r5mEmvuFkvSqB3sG.S1Q2','ADMIN',1,0,NULL,'2026-01-07 21:20:44','2026-01-07 21:26:35');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;

--
-- Dumping routines for database 'tthh'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-12 22:21:41
