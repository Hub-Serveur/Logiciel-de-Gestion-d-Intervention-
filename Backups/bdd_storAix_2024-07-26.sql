/*!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.8-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: bdd_storAix
-- ------------------------------------------------------
-- Server version	10.11.8-MariaDB-0ubuntu0.23.10.1

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
-- Table structure for table `Chantier`
--

DROP TABLE IF EXISTS `Chantier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Chantier` (
  `ID_Chantier` int(11) NOT NULL AUTO_INCREMENT,
  `Titre` varchar(255) DEFAULT NULL,
  `Adresse` varchar(255) DEFAULT NULL,
  `Ville` varchar(255) DEFAULT NULL,
  `CodePostal` varchar(10) DEFAULT NULL,
  `Zone` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `NombreColis` varchar(255) DEFAULT NULL,
  `NoteInterventionClient` text DEFAULT NULL,
  `NoteInterventionEquipe` text DEFAULT NULL,
  `StatutIntervention` varchar(255) DEFAULT NULL,
  `StatutSAV` varchar(255) DEFAULT NULL,
  `ID_Client` int(11) DEFAULT NULL,
  `ID_Equipe` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID_Chantier`),
  KEY `ID_Client` (`ID_Client`),
  KEY `ID_Equipe` (`ID_Equipe`),
  CONSTRAINT `Chantier_ibfk_1` FOREIGN KEY (`ID_Client`) REFERENCES `Client` (`ID_Client`) ON DELETE SET NULL,
  CONSTRAINT `Chantier_ibfk_2` FOREIGN KEY (`ID_Equipe`) REFERENCES `Equipe` (`ID_Equipe`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Chantier`
--

LOCK TABLES `Chantier` WRITE;
/*!40000 ALTER TABLE `Chantier` DISABLE KEYS */;
INSERT INTO `Chantier` VALUES
(1,'Projet Saint-Michel','789 boulevard Saint-Michel','Paris','75006','Île-de-France','Rénovation complète de l\'édifice','30','L\'intervention s\'est bien déroulée, les délais ont été respectés et les équipes ont fait preuve d\'un grand professionnalisme.','Le chantier s\'est déroulé sans encombre. Les matériaux étaient de bonne qualité et les tâches ont été exécutées de manière très professionnelle.','En cours','Non Requis',1,1),
(2,'Projet Rivoli','1011 rue de Rivoli','Paris','75001','Île-de-France','Construction d\'un nouveau bâtiment','100','Très satisfait du travail réalisé, bien que quelques retards aient été notés. Le résultat final est conforme à nos attentes.','L\'équipe a bien coordonné les différentes phases du chantier. Quelques ajustements ont été nécessaires en cours de route, mais tout s\'est bien terminé.','Cloturé','Non Requis',2,3),
(3,'Aménagement Parc','132 avenue de Wagram','Paris','75017','Île-de-France','Aménagement d\'un parc public','50','L\'aménagement du parc a été réalisé avec soin. Les espaces verts sont magnifiques et les structures installées sont de qualité.','Malgré quelques retards, l\'équipe a su gérer efficacement les imprévus et a livré un parc très agréable.','En cours','Requis',3,1),
(4,'Rénovation Musée','58 rue de Rivoli','Paris','75001','Île-de-France','Mise à jour des installations du musée','20','Excellente intervention, les nouvelles installations du musée répondent parfaitement à nos besoins et attentes.','L\'équipe a fait un travail remarquable en respectant le patrimoine historique tout en modernisant les installations.','Cloturé','Non Requis',4,2),
(5,'Construction École','202 rue Saint-Martin','Paris','75003','Île-de-France','Construction d\'une nouvelle école primaire','80','Bon travail réalisé par l\'équipe. L\'école est fonctionnelle et les enfants pourront en profiter dès la rentrée prochaine.','Les travaux ont été effectués avec rigueur et professionnalisme, malgré quelques difficultés rencontrées en cours de projet.','Facturé','Requis',5,4),
(6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'En cours',NULL,NULL,1),
(7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'En cours',NULL,NULL,2),
(8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'En cours',NULL,NULL,5);
/*!40000 ALTER TABLE `Chantier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Client`
--

DROP TABLE IF EXISTS `Client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Client` (
  `ID_Client` int(11) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(255) DEFAULT NULL,
  `Prenom` varchar(255) DEFAULT NULL,
  `Adresse` varchar(255) DEFAULT NULL,
  `Ville` varchar(255) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `TelephoneFixe` varchar(15) DEFAULT NULL,
  `TelephoneMobile` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`ID_Client`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Client`
--

LOCK TABLES `Client` WRITE;
/*!40000 ALTER TABLE `Client` DISABLE KEYS */;
INSERT INTO `Client` VALUES
(1,'Leroy','Michel','123 rue de la Paix','Paris','michel.leroy@example.com','0123456789','0123456789'),
(2,'Moreau','Sophie','456 avenue des Champs-Elysées','Paris','sophie.moreau@example.com','98 76 54 32 10','98 76 54 32 10'),
(3,'Girard','Nicolas','789 rue de la République','Lyon','nicolas.girard@example.com','02 34 56 78 91','02 34 56 78 91'),
(4,'Lopez','Isabelle','101 rue Saint-Lazare','Marseille','isabelle.lopez@example.com','03 45 67 89 02','03 45 67 89 02'),
(5,'Bernier','Emilie','202 avenue Victor Hugo','Bordeaux','emilie.bernier@example.com','0456789012','0456789012');
/*!40000 ALTER TABLE `Client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Equipe`
--

DROP TABLE IF EXISTS `Equipe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Equipe` (
  `ID_Equipe` int(11) NOT NULL AUTO_INCREMENT,
  `Nom_Equipe` varchar(255) DEFAULT NULL,
  `Couleur` varchar(7) DEFAULT NULL,
  `Telephone` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`ID_Equipe`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Equipe`
--

LOCK TABLES `Equipe` WRITE;
/*!40000 ALTER TABLE `Equipe` DISABLE KEYS */;
INSERT INTO `Equipe` VALUES
(1,'Equipe Alpha','#ff5733','0601010101'),
(2,'Equipe Beta','#4CAF50','0602020202'),
(3,'Equipe Gamma','#2196F3','0603030303'),
(4,'Equipe Delta','#FFC107','0604040404'),
(5,'Equipe Epsilon','#673AB7','0605050505'),
(6,'Equipe bg','#51a0c8',NULL);
/*!40000 ALTER TABLE `Equipe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Equipe_Chantier`
--

DROP TABLE IF EXISTS `Equipe_Chantier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Equipe_Chantier` (
  `ID_Equipe` int(11) NOT NULL,
  `ID_Chantier` int(11) NOT NULL,
  PRIMARY KEY (`ID_Equipe`,`ID_Chantier`),
  KEY `ID_Chantier` (`ID_Chantier`),
  CONSTRAINT `Equipe_Chantier_ibfk_1` FOREIGN KEY (`ID_Equipe`) REFERENCES `Equipe` (`ID_Equipe`) ON DELETE CASCADE,
  CONSTRAINT `Equipe_Chantier_ibfk_2` FOREIGN KEY (`ID_Chantier`) REFERENCES `Chantier` (`ID_Chantier`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Equipe_Chantier`
--

LOCK TABLES `Equipe_Chantier` WRITE;
/*!40000 ALTER TABLE `Equipe_Chantier` DISABLE KEYS */;
/*!40000 ALTER TABLE `Equipe_Chantier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Horaire_Chantier`
--

DROP TABLE IF EXISTS `Horaire_Chantier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Horaire_Chantier` (
  `Unique_Id` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Chantier` int(11) DEFAULT NULL,
  `ID_Equipe` int(11) DEFAULT NULL,
  `Date_Travail` date DEFAULT NULL,
  `Heure_Debut` time DEFAULT NULL,
  `Heure_Fin` time DEFAULT NULL,
  PRIMARY KEY (`Unique_Id`),
  KEY `ID_Chantier` (`ID_Chantier`),
  KEY `ID_Equipe` (`ID_Equipe`),
  CONSTRAINT `Horaire_Chantier_ibfk_1` FOREIGN KEY (`ID_Chantier`) REFERENCES `Chantier` (`ID_Chantier`) ON DELETE CASCADE,
  CONSTRAINT `Horaire_Chantier_ibfk_2` FOREIGN KEY (`ID_Equipe`) REFERENCES `Equipe` (`ID_Equipe`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Horaire_Chantier`
--

LOCK TABLES `Horaire_Chantier` WRITE;
/*!40000 ALTER TABLE `Horaire_Chantier` DISABLE KEYS */;
INSERT INTO `Horaire_Chantier` VALUES
(1,1,1,'2024-05-27','09:30:00','12:00:00'),
(2,1,1,'2024-05-27','13:15:00','16:00:00'),
(3,2,3,'2024-05-28','09:45:00','12:00:00'),
(4,2,4,'2024-05-28','13:00:00','16:30:00'),
(5,2,3,'2024-05-29','09:45:00','12:00:00'),
(6,2,4,'2024-05-29','13:00:00','16:30:00'),
(7,3,1,'2024-05-29','09:00:00','12:45:00'),
(8,3,5,'2024-05-29','13:00:00','16:15:00'),
(9,1,5,'2024-05-30','13:00:00','16:15:00'),
(10,4,2,'2024-05-30','09:00:00','12:00:00'),
(11,4,3,'2024-05-30','13:00:00','16:00:00'),
(12,5,4,'2024-05-31','09:00:00','12:00:00'),
(13,5,5,'2024-05-31','13:00:00','16:00:00'),
(14,1,1,'2024-06-01','08:00:00','11:00:00'),
(15,1,2,'2024-06-01','12:00:00','15:00:00'),
(16,2,3,'2024-06-02','08:00:00','11:00:00'),
(17,2,4,'2024-06-02','12:00:00','15:00:00'),
(18,3,1,'2024-06-03','08:00:00','11:00:00'),
(19,3,5,'2024-06-03','12:00:00','15:00:00'),
(20,4,2,'2024-06-04','09:00:00','12:00:00'),
(21,4,3,'2024-06-04','13:00:00','16:00:00'),
(22,5,4,'2024-06-05','09:00:00','12:00:00'),
(23,5,5,'2024-06-05','13:00:00','16:00:00'),
(24,1,1,'2024-06-06','09:00:00','12:00:00'),
(25,1,2,'2024-06-06','13:00:00','16:00:00'),
(26,2,3,'2024-06-07','09:00:00','12:00:00'),
(27,2,4,'2024-06-07','13:00:00','16:00:00'),
(28,3,1,'2024-06-08','09:00:00','12:00:00'),
(29,3,5,'2024-06-08','13:00:00','16:00:00'),
(30,4,2,'2024-06-09','08:00:00','11:00:00'),
(31,4,3,'2024-06-09','12:00:00','15:00:00'),
(32,5,4,'2024-06-10','08:00:00','11:00:00'),
(33,5,5,'2024-06-10','12:00:00','15:00:00'),
(34,1,1,'2024-06-11','09:00:00','12:00:00'),
(35,1,2,'2024-06-11','13:00:00','16:00:00'),
(36,2,3,'2024-06-12','09:00:00','12:00:00'),
(37,2,4,'2024-06-12','13:00:00','16:00:00'),
(38,3,1,'2024-06-13','09:00:00','12:00:00'),
(39,3,5,'2024-06-13','13:00:00','16:00:00'),
(40,4,2,'2024-06-14','09:00:00','12:00:00'),
(41,4,3,'2024-06-14','13:00:00','16:00:00'),
(42,5,4,'2024-06-15','09:00:00','12:00:00'),
(43,5,5,'2024-06-15','13:00:00','16:00:00'),
(44,1,1,'2024-06-16','08:00:00','11:00:00'),
(45,1,2,'2024-06-16','12:00:00','15:00:00'),
(46,2,3,'2024-06-17','08:00:00','11:00:00'),
(47,2,4,'2024-06-17','12:00:00','15:00:00'),
(48,3,1,'2024-06-18','08:00:00','11:00:00'),
(49,3,5,'2024-06-18','12:00:00','15:00:00'),
(50,4,2,'2024-06-19','09:00:00','12:00:00'),
(51,4,3,'2024-06-19','13:00:00','16:00:00'),
(52,5,4,'2024-06-20','09:00:00','12:00:00'),
(53,5,5,'2024-06-20','13:00:00','16:00:00'),
(54,1,1,'2024-06-21','07:00:00','10:00:00'),
(55,1,2,'2024-06-21','10:00:00','13:00:00'),
(56,2,3,'2024-06-22','13:00:00','16:00:00'),
(57,2,4,'2024-06-22','16:00:00','19:00:00'),
(58,3,1,'2024-06-23','07:00:00','10:00:00'),
(59,3,5,'2024-06-23','10:00:00','13:00:00'),
(60,4,2,'2024-06-24','07:00:00','10:00:00'),
(61,4,3,'2024-06-24','10:00:00','13:00:00'),
(62,5,4,'2024-06-25','13:00:00','16:00:00'),
(63,5,5,'2024-06-25','16:00:00','19:00:00'),
(64,1,1,'2024-06-26','07:00:00','10:00:00'),
(65,1,2,'2024-06-26','10:00:00','19:00:00'),
(66,2,3,'2024-06-27','13:00:00','19:00:00'),
(67,1,4,'2024-06-27','07:00:00','14:00:00'),
(68,3,1,'2024-06-28','07:00:00','10:00:00'),
(69,3,5,'2024-06-28','10:00:00','13:00:00'),
(70,4,2,'2024-06-29','07:00:00','10:00:00'),
(71,4,3,'2024-06-29','10:00:00','19:00:00'),
(72,5,4,'2024-06-30','13:00:00','16:00:00'),
(73,5,5,'2024-06-30','16:00:00','19:00:00'),
(77,1,6,'2024-07-31','19:47:00','20:47:00');
/*!40000 ALTER TABLE `Horaire_Chantier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Images_Chantier`
--

DROP TABLE IF EXISTS `Images_Chantier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Images_Chantier` (
  `ID_Image` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Chantier` int(11) DEFAULT NULL,
  `Image_Path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_Image`),
  KEY `ID_Chantier` (`ID_Chantier`),
  CONSTRAINT `Images_Chantier_ibfk_1` FOREIGN KEY (`ID_Chantier`) REFERENCES `Chantier` (`ID_Chantier`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Images_Chantier`
--

LOCK TABLES `Images_Chantier` WRITE;
/*!40000 ALTER TABLE `Images_Chantier` DISABLE KEYS */;
INSERT INTO `Images_Chantier` VALUES
(2,3,'uploads/ai-image-generator-two.webp');
/*!40000 ALTER TABLE `Images_Chantier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Intervenant`
--

DROP TABLE IF EXISTS `Intervenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Intervenant` (
  `ID_Intervenant` int(11) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(255) DEFAULT NULL,
  `Prenom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_Intervenant`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Intervenant`
--

LOCK TABLES `Intervenant` WRITE;
/*!40000 ALTER TABLE `Intervenant` DISABLE KEYS */;
INSERT INTO `Intervenant` VALUES
(1,'Dupont','Jean'),
(2,'Martin','Alice'),
(3,'Bernard','Claire'),
(4,'Thomas','Alexandre'),
(5,'Petit','Sophie'),
(6,'Moreau','Paul'),
(7,'Durand','Marie'),
(8,'Roux','Julien'),
(9,'Lemoine','Chantal'),
(10,'Blanc','Sylvie'),
(11,'DAMASSE','Nathan');
/*!40000 ALTER TABLE `Intervenant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Intervenant_Equipe`
--

DROP TABLE IF EXISTS `Intervenant_Equipe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Intervenant_Equipe` (
  `ID_Intervenant` int(11) NOT NULL,
  `ID_Equipe` int(11) NOT NULL,
  PRIMARY KEY (`ID_Intervenant`,`ID_Equipe`),
  KEY `ID_Equipe` (`ID_Equipe`),
  CONSTRAINT `Intervenant_Equipe_ibfk_1` FOREIGN KEY (`ID_Intervenant`) REFERENCES `Intervenant` (`ID_Intervenant`) ON DELETE CASCADE,
  CONSTRAINT `Intervenant_Equipe_ibfk_2` FOREIGN KEY (`ID_Equipe`) REFERENCES `Equipe` (`ID_Equipe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Intervenant_Equipe`
--

LOCK TABLES `Intervenant_Equipe` WRITE;
/*!40000 ALTER TABLE `Intervenant_Equipe` DISABLE KEYS */;
INSERT INTO `Intervenant_Equipe` VALUES
(2,2),
(3,3),
(3,6),
(4,4),
(5,5),
(7,2),
(8,1),
(8,3),
(9,1),
(9,4),
(10,5),
(11,1);
/*!40000 ALTER TABLE `Intervenant_Equipe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Locataire`
--

DROP TABLE IF EXISTS `Locataire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Locataire` (
  `ID_Locataire` int(11) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(255) DEFAULT NULL,
  `Prenom` varchar(255) DEFAULT NULL,
  `TelephoneFixe` varchar(15) DEFAULT NULL,
  `TelephoneMobile` varchar(15) DEFAULT NULL,
  `ID_Client` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID_Locataire`),
  KEY `ID_Client` (`ID_Client`),
  CONSTRAINT `Locataire_ibfk_1` FOREIGN KEY (`ID_Client`) REFERENCES `Client` (`ID_Client`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Locataire`
--

LOCK TABLES `Locataire` WRITE;
/*!40000 ALTER TABLE `Locataire` DISABLE KEYS */;
INSERT INTO `Locataire` VALUES
(1,'Duval','Marie','0412345678','0612345678',1),
(2,'Garcia','Pierre','0423456789','0623456789',1),
(3,'Lemoine','Isabelle','04 34 56 78 90','06 34 56 78 90',2),
(4,'Martinez','Julie','04 45 67 89 01','06 45 67 89 01',3),
(5,'Fernandez','Luc','04 56 78 90 12','06 56 78 90 12',4);
/*!40000 ALTER TABLE `Locataire` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-07-26 16:41:46
