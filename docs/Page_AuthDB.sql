-- MySQL dump 10.13  Distrib 5.5.44, for debian-linux-gnu (x86_64)
--
-- Host: pm-rds.cnhdkpq25xo5.us-east-1.rds.amazonaws.com    Database: pm_catalog
-- ------------------------------------------------------
-- Server version	5.6.23-log

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
-- Table structure for table `AppSettings`
--

DROP TABLE IF EXISTS `AppSettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AppSettings` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `JSON` longtext NOT NULL COMMENT 'JSON array of the settings',
  `Created` int(10) unsigned NOT NULL COMMENT 'When it was created',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='History of the web application settings and its current (largest ID)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `Auth`
--

DROP TABLE IF EXISTS `Auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Auth` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL COMMENT 'User login name',
  `password` text NOT NULL COMMENT 'User encrypted password',
  `password_expiry` text NOT NULL COMMENT 'When current password expires next',
  `r_Profile` text NOT NULL COMMENT 'Link to associated profile',
  `FacebookID` text NOT NULL COMMENT 'Used for Facebook Auth',
  `birthdate` text NOT NULL COMMENT 'Used to authenticate',
  `acl` text NOT NULL COMMENT 'Contains tags that unlock certain features',
  `flags` int(10) unsigned NOT NULL COMMENT 'Bitflags for this user',
  `securityq1` text NOT NULL COMMENT 'Security Question 1',
  `securityq2` text NOT NULL COMMENT 'Security Question 2',
  `securityq3` text NOT NULL COMMENT 'Security Question 3',
  `securitya1` text NOT NULL COMMENT 'Security Answer 1',
  `securitya2` text NOT NULL COMMENT 'Security Answer 2',
  `securitya3` text NOT NULL COMMENT 'Security Answer 3',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=445 DEFAULT CHARSET=latin1 COMMENT='Main Auth table for handling user logins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Auth`
--

LOCK TABLES `Auth` WRITE;
/*!40000 ALTER TABLE `Auth` DISABLE KEYS */;
INSERT INTO `Auth` VALUES (1,'admin','','1477635633','1','','','su,admin',0,'','','','','','');
/*!40000 ALTER TABLE `Auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AutoRowLock`
--

DROP TABLE IF EXISTS `AutoRowLock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AutoRowLock` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T` varchar(45) NOT NULL,
  `I` int(10) unsigned NOT NULL,
  `Timestamp` int(10) unsigned NOT NULL,
  `r_Auth` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4246 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `File`
--

DROP TABLE IF EXISTS `File`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `File` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL COMMENT 'Original name of the file',
  `Uploaded` int(10) unsigned NOT NULL COMMENT 'Timestamp of upload time',
  `Uploader` int(10) unsigned NOT NULL COMMENT 'Reference to Auth of the uploader',
  `Size` int(10) unsigned NOT NULL COMMENT 'File size in bytes',
  `Extension` varchar(45) NOT NULL COMMENT 'Lower case extension of the file',
  `filemtime` int(10) unsigned NOT NULL,
  `Type` text NOT NULL COMMENT 'Mime type',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=19355 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileFLAC`
--

DROP TABLE IF EXISTS `FileFLAC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileFLAC` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Length` decimal(10,8) unsigned NOT NULL,
  `r_File` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`,`Length`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `FileFLAC`
--


--
-- Table structure for table `FileImage`
--

DROP TABLE IF EXISTS `FileImage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileImage` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Width` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Size in pixels',
  `Height` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Size in pixels',
  `Format` int(10) unsigned NOT NULL COMMENT '0=JPG, 1=PNG',
  `r_File` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9663 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `FileWAV`
--

DROP TABLE IF EXISTS `FileWAV`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileWAV` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Length` decimal(10,0) NOT NULL COMMENT 'Audio length in seconds',
  `r_File` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=261 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `Modification`
--

DROP TABLE IF EXISTS `Modification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Modification` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `r_Auth` int(10) unsigned NOT NULL COMMENT 'Who made the modification',
  `What` longtext NOT NULL COMMENT 'JSON in the form of array(tables)=>array(fields)=>array(IDs)',
  `Message` longtext NOT NULL COMMENT 'Any extra data provided by the application as to what was changed',
  `Timestamp` int(10) unsigned NOT NULL COMMENT 'When this modification was made for history screens',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11212 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Modification`
--

LOCK TABLES `Modification` WRITE;
/*!40000 ALTER TABLE `Modification` DISABLE KEYS */;
/*!40000 ALTER TABLE `Modification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Profile`
--

DROP TABLE IF EXISTS `Profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Profile` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` tinytext NOT NULL,
  `first_name` tinytext NOT NULL,
  `last_name` tinytext NOT NULL,
  `flags` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=313 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Profile`
--

LOCK TABLES `Profile` WRITE;
/*!40000 ALTER TABLE `Profile` DISABLE KEYS */;
INSERT INTO `Profile` VALUES (1,'info@piecemaker.com','Captain','Automatic',0);
/*!40000 ALTER TABLE `Profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `RowLock`
--

DROP TABLE IF EXISTS `RowLock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RowLock` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `T` varchar(45) NOT NULL,
  `I` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `RowLock`
--

LOCK TABLES `RowLock` WRITE;
/*!40000 ALTER TABLE `RowLock` DISABLE KEYS */;
/*!40000 ALTER TABLE `RowLock` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `Session`
--

DROP TABLE IF EXISTS `Session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Session` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `r_Auth` int(10) unsigned NOT NULL COMMENT 'Reference to an Auth',
  `requests` int(10) unsigned NOT NULL COMMENT '# of requests made by this session',
  `last_url` text NOT NULL,
  `flags` int(10) unsigned NOT NULL COMMENT 'Any special flags',
  `login` text NOT NULL COMMENT 'Login time',
  `logout` text NOT NULL COMMENT 'Logout time',
  `status` int(11) NOT NULL COMMENT 'Connected status',
  `IP` text NOT NULL COMMENT 'IP Address',
  `ip_info` text NOT NULL COMMENT 'IP information gathered from GeoIP',
  `HOST` text NOT NULL COMMENT 'Hostname',
  `REFERRER` text NOT NULL COMMENT 'Referring URL',
  `BROWSER` text NOT NULL COMMENT 'Browser information',
  `refreshed` int(10) unsigned NOT NULL COMMENT 'Last refresh',
  `last_refreshed` int(10) unsigned NOT NULL COMMENT 'Last refresh before that',
  `timeout` text NOT NULL COMMENT 'How long until we time out',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2124 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-01-31  6:01:22
