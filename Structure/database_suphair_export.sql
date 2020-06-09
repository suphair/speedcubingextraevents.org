-- mysqldump-php https://github.com/ifsnop/mysqldump-php
--
-- Host: suphair.mysql	Database: suphair_export
-- ------------------------------------------------------
-- Server version 	5.6.41-84.1
-- Date: Tue, 09 Jun 2020 12:28:55 +0300

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
-- Table structure for table `Competitions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Competitions` (
  `id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `cityName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `countryId` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `information` mediumtext COLLATE utf8mb4_unicode_ci,
  `year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `month` smallint(5) unsigned NOT NULL DEFAULT '0',
  `day` smallint(5) unsigned NOT NULL DEFAULT '0',
  `endMonth` smallint(5) unsigned NOT NULL DEFAULT '0',
  `endDay` smallint(5) unsigned NOT NULL DEFAULT '0',
  `eventSpecs` longtext COLLATE utf8mb4_unicode_ci,
  `wcaDelegate` mediumtext COLLATE utf8mb4_unicode_ci,
  `organiser` mediumtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Continents`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Continents` (
  `id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `recordName` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Countries`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Countries` (
  `id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `continentId` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `iso2` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Events`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Events` (
  `id` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(54) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `rank` int(11) NOT NULL DEFAULT '0',
  `format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Formats`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Formats` (
  `id` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_by_second` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expected_solve_count` int(11) NOT NULL,
  `trim_fastest_n` int(11) NOT NULL,
  `trim_slowest_n` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Persons`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Persons` (
  `id` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `countryId` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Results`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Results` (
  `competitionId` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `eventId` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `roundTypeId` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `pos` smallint(6) NOT NULL DEFAULT '0',
  `best` int(11) NOT NULL DEFAULT '0',
  `average` int(11) NOT NULL DEFAULT '0',
  `personName` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personId` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `personCountryId` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `formatId` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `value1` int(11) NOT NULL DEFAULT '0',
  `value2` int(11) NOT NULL DEFAULT '0',
  `value3` int(11) NOT NULL DEFAULT '0',
  `value4` int(11) NOT NULL DEFAULT '0',
  `value5` int(11) NOT NULL DEFAULT '0',
  `value6` int(11) NOT NULL,
  `value7` int(11) NOT NULL,
  `value8` int(11) NOT NULL,
  `value9` int(11) NOT NULL,
  `value10` int(11) NOT NULL,
  `value11` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `RoundTypes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RoundTypes` (
  `id` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `final` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on: Tue, 09 Jun 2020 12:28:55 +0300
