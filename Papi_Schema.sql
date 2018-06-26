-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: w5t-live.co8qgy9qwqed.us-east-2.rds.amazonaws.com    Database: w5tdb
-- ------------------------------------------------------
-- Server version	5.6.29-log

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

CREATE DATABASE papidb;
USE papidb;

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
-- Table structure for table `Session`
--

DROP TABLE IF EXISTS `Session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Session` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `expiresAt` int(10) unsigned NOT NULL,
  `r_User` int(10) unsigned NOT NULL,
  `session_token` tinytext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` int(10) unsigned NOT NULL,
  `username` text NOT NULL,
  `password` tinytext NOT NULL,
  `email` text NOT NULL,
  `emailVerified` int(10) unsigned NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `code` text NOT NULL,
  `su` int(10) unsigned NOT NULL,
  `admin` int(10) unsigned NOT NULL,
  `company` text NOT NULL,
  `forgotKey` text NOT NULL,
  `forgotExpires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Dump completed on 2017-04-10 15:39:37
