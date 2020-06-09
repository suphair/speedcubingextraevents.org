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

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Attempt` (
  `ID` bigint(11) NOT NULL AUTO_INCREMENT,
  `Command` int(11) NOT NULL,
  `Attempt` int(11) DEFAULT NULL,
  `IsDNF` smallint(6) DEFAULT '0',
  `IsDNS` smallint(6) DEFAULT '0',
  `Minute` int(11) DEFAULT NULL,
  `Second` int(11) DEFAULT NULL,
  `Milisecond` int(11) DEFAULT NULL,
  `Except` smallint(6) DEFAULT '0',
  `Special` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `vOrder` bigint(20) DEFAULT NULL,
  `vOut` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `Amount` double(3,1) DEFAULT '0.0',
  `exportValue` int(11) DEFAULT NULL,
  `worldRecord` bit(1) DEFAULT NULL,
  `countryRecord` bit(1) DEFAULT NULL,
  `continentRecord` bit(1) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Command` (`Command`),
  KEY `vOrder` (`vOrder`),
  KEY `ID` (`ID`),
  KEY `Special` (`Special`(191)),
  CONSTRAINT `Attempt_ibfk_1` FOREIGN KEY (`Command`) REFERENCES `Command` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BlockText` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `Value` text CHARACTER SET utf8,
  `Country` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`,`Name`),
  KEY `ID` (`ID`),
  KEY `Name` (`Name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CandidateCode` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Delegate` varchar(255) DEFAULT NULL,
  `Candidate` varchar(255) DEFAULT NULL,
  `TimeStamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Command` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) NOT NULL,
  `Place` int(11) DEFAULT NULL,
  `CardID` int(11) DEFAULT NULL,
  `Decline` smallint(6) NOT NULL DEFAULT '0',
  `Group` int(11) NOT NULL DEFAULT '-1',
  `Secret` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `vCompetitors` int(11) DEFAULT NULL,
  `vCountry` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Warnings` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Onsite` smallint(6) DEFAULT '0',
  `DateCreated` datetime DEFAULT CURRENT_TIMESTAMP,
  `Video` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Name` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Sum333` int(11) DEFAULT NULL,
  `exportId` varchar(40) CHARACTER SET cp1251 DEFAULT NULL,
  `exportName` varchar(160) COLLATE utf8mb4_bin DEFAULT NULL,
  `exportCountryId` varchar(50) CHARACTER SET cp1251 DEFAULT NULL,
  `inCup` int(11) DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `Event` (`Event`),
  KEY `ID` (`ID`),
  KEY `Secret` (`Secret`),
  CONSTRAINT `competitorevent_ibfk_3` FOREIGN KEY (`Event`) REFERENCES `Event` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CommandCompetitor` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Command` int(11) DEFAULT NULL,
  `Competitor` int(11) DEFAULT NULL,
  `CheckStatus` int(11) DEFAULT '1',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `CommandCompetitor` (`Command`,`Competitor`) USING BTREE,
  KEY `Command` (`Command`),
  KEY `Competitor` (`Competitor`),
  KEY `CommandCompetitor_2` (`Command`,`Competitor`) USING BTREE,
  CONSTRAINT `commandcompetitor_ibfk_1` FOREIGN KEY (`Command`) REFERENCES `Command` (`ID`),
  CONSTRAINT `commandcompetitor_ibfk_2` FOREIGN KEY (`Competitor`) REFERENCES `Competitor` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Competition` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `WCA` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `City` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Registration` smallint(6) DEFAULT '0',
  `Country` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Status` varchar(255) CHARACTER SET cp1251 NOT NULL DEFAULT '0',
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `WebSite` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `MaxCardID` int(11) DEFAULT NULL,
  `CheckDateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Onsite` smallint(6) DEFAULT '0',
  `LoadDateTime` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Unofficial` int(11) DEFAULT '0',
  `DelegateWCA` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `DelegateWCAOn` tinyint(4) DEFAULT '0',
  `Cubingchina` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `WCA` (`WCA`),
  KEY `EndDate` (`EndDate`),
  KEY `Unofficial` (`Unofficial`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CompetitionDelegate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competition` int(11) DEFAULT NULL,
  `Delegate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `CompetitionDelegate` (`Competition`,`Delegate`) USING BTREE,
  KEY `Competition` (`Competition`),
  KEY `Delegate` (`Delegate`),
  KEY `CompetitionDelegate_2` (`Competition`,`Delegate`) USING BTREE,
  CONSTRAINT `competitiondelegate_ibfk_1` FOREIGN KEY (`Competition`) REFERENCES `Competition` (`ID`),
  CONSTRAINT `competitiondelegate_ibfk_2` FOREIGN KEY (`Delegate`) REFERENCES `Delegate` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CompetitionReport` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competition` int(11) DEFAULT NULL,
  `Delegate` int(11) DEFAULT NULL,
  `Report` text CHARACTER SET utf8,
  `CreateTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdateTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DelegateWCA` int(11) DEFAULT NULL,
  `Parsedown` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Competition` (`Competition`,`Delegate`),
  KEY `Delegate` (`Delegate`),
  KEY `DelegateWCA` (`DelegateWCA`),
  CONSTRAINT `CompetitionReport_ibfk_1` FOREIGN KEY (`Competition`) REFERENCES `Competition` (`ID`),
  CONSTRAINT `CompetitionReport_ibfk_2` FOREIGN KEY (`Delegate`) REFERENCES `Delegate` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CompetitionReportComment` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Delegate` int(11) DEFAULT NULL,
  `CommentDelegate` int(11) DEFAULT NULL,
  `Comment` text CHARACTER SET utf8,
  `Competition` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Delegate` (`Delegate`),
  KEY `Competition` (`Competition`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Competitor` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(191) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `WCAID` varchar(255) CHARACTER SET cp1251 DEFAULT '',
  `Country` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `WID` int(11) DEFAULT NULL,
  `Language` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Email` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `UpdateTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `WCAID` (`WCAID`),
  KEY `WID` (`WID`),
  KEY `Country` (`Country`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Continent` (
  `Code` varchar(255) CHARACTER SET utf8 NOT NULL,
  `Name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  UNIQUE KEY `Code` (`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Country` (
  `ISO2` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Continent` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Code` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  UNIQUE KEY `ISO2` (`ISO2`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CupCell` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) DEFAULT NULL,
  `CommandWin` int(11) DEFAULT NULL,
  `Command1` int(11) DEFAULT NULL,
  `Command2` int(11) DEFAULT NULL,
  `Round` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `CupCell1` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `CupCell2` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Number` int(11) DEFAULT NULL,
  `Status` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Command2` (`Command2`),
  KEY `CommandWin` (`CommandWin`),
  KEY `Command_1_2` (`Command1`,`Command2`) USING BTREE,
  KEY `Command1` (`Command1`) USING BTREE,
  KEY `Event` (`Event`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CupValue` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CupCell` int(11) DEFAULT NULL,
  `Value1_1` int(11) DEFAULT NULL,
  `Value2_1` int(11) DEFAULT NULL,
  `Value3_1` int(11) DEFAULT NULL,
  `Sum1` int(11) DEFAULT NULL,
  `Attempt` int(11) DEFAULT NULL,
  `Value1_2` int(11) DEFAULT NULL,
  `Value2_2` int(11) DEFAULT NULL,
  `Value3_2` int(11) DEFAULT NULL,
  `Sum2` int(11) DEFAULT NULL,
  `Point1` int(11) DEFAULT NULL,
  `Point2` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `CupCell` (`CupCell`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Delegate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Status` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Site` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `WCA_ID` varchar(11) CHARACTER SET cp1251 NOT NULL,
  `OrderLine` int(11) DEFAULT '99',
  `WID` int(11) DEFAULT NULL,
  `Contact` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Secret` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `WCA_ID` (`WCA_ID`),
  KEY `WID` (`WID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DelegateChange` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Delegate` int(11) DEFAULT NULL,
  `Senior` int(11) DEFAULT NULL,
  `Status` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `Delegate` (`Delegate`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Discipline` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) CHARACTER SET cp1251 NOT NULL,
  `Status` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Code` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Competitors` int(11) NOT NULL DEFAULT '1',
  `GlueScrambles` tinyint(4) DEFAULT NULL,
  `FormatResult` int(11) DEFAULT '1',
  `TNoodle` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `TNoodles` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `TNoodlesMult` int(11) DEFAULT '1',
  `CutScrambles` tinyint(4) DEFAULT '0',
  `Simple` int(11) DEFAULT '0',
  `Inspection` int(11) DEFAULT '15',
  `CodeScript` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Comment` text CHARACTER SET cp1251,
  `ScrambleComment` text CHARACTER SET cp1251,
  `Codes` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `Code` (`Code`),
  KEY `CodeScript` (`CodeScript`),
  KEY `FormatResult` (`FormatResult`),
  CONSTRAINT `Discipline_ibfk_1` FOREIGN KEY (`FormatResult`) REFERENCES `FormatResult` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DisciplineFormat` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Discipline` int(11) DEFAULT NULL,
  `Format` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Discipline` (`Discipline`),
  KEY `Format` (`Format`),
  KEY `ID` (`ID`),
  CONSTRAINT `disciplineformat_ibfk_1` FOREIGN KEY (`Discipline`) REFERENCES `Discipline` (`ID`),
  CONSTRAINT `disciplineformat_ibfk_2` FOREIGN KEY (`Format`) REFERENCES `Format` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Event` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DisciplineFormat` int(11) NOT NULL,
  `Competition` int(11) NOT NULL,
  `CutoffMinute` int(11) DEFAULT '0',
  `CutoffSecond` int(11) DEFAULT '0',
  `Secret` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `MaxCardID` int(11) DEFAULT '0',
  `LimitMinute` int(11) DEFAULT '10',
  `LimitSecond` int(11) DEFAULT '0',
  `Competitors` int(11) DEFAULT '500',
  `Groups` int(11) NOT NULL DEFAULT '2',
  `LocalID` int(11) DEFAULT NULL,
  `Round` int(11) DEFAULT '1',
  `RoundType` varchar(1) CHARACTER SET cp1251 DEFAULT NULL,
  `vRound` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Cumulative` smallint(6) DEFAULT '0',
  `Comment` text CHARACTER SET cp1251,
  `ScrambleSalt` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `ScramblePublic` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `CommandsCup` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Competition` (`Competition`),
  KEY `Discipline` (`DisciplineFormat`),
  KEY `ID` (`ID`),
  KEY `Secret` (`Secret`),
  CONSTRAINT `event_ibfk_1` FOREIGN KEY (`Competition`) REFERENCES `Competition` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Format` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Result` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `Attemption` int(11) DEFAULT NULL,
  `Name` varchar(255) CHARACTER SET cp1251 NOT NULL,
  `ExtResult` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  `FormatID` varchar(255) CHARACTER SET cp1251 DEFAULT NULL,
  PRIMARY KEY (`ID`,`Name`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormatResult` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Format` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GrandAccess` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Level` int(11) DEFAULT NULL,
  `Competition` int(11) DEFAULT '0',
  `Group` int(11) DEFAULT NULL,
  `Description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `Type` (`Type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GrandGroup` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GrandGroupMember` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Group` int(11) DEFAULT NULL,
  `Delegate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `Group` (`Group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GrandRole` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Level` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LogMail` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `To` text CHARACTER SET cp1251,
  `Subject` text COLLATE utf8mb4_bin,
  `Body` text CHARACTER SET cp1251,
  `DateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Result` text CHARACTER SET cp1251,
  PRIMARY KEY (`ID`),
  KEY `DateTime` (`DateTime`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LogWcaApi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `request` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `response` text CHARACTER SET utf8,
  `method` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `context` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Logs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `DateTime` datetime DEFAULT CURRENT_TIMESTAMP,
  `Object` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Action` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Details` text CHARACTER SET utf8,
  `IP` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `DateTime` (`DateTime`),
  KEY `Action` (`Action`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LogsPost` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Post` text CHARACTER SET utf8,
  `Competitor` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Request` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Timestamp` (`Timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LogsRegistration` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) DEFAULT NULL,
  `Action` varchar(12) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Details` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Doing` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `Timestamp` (`Timestamp`),
  KEY `Event` (`Event`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MultiLanguage` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Value` text,
  `Language` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Name`,`Language`),
  KEY `Name_2` (`Name`),
  KEY `Language` (`Language`),
  KEY `Name_3` (`Name`,`Language`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `News` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date DEFAULT NULL,
  `Text` text CHARACTER SET utf8,
  `Delegate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Date` (`Date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Registration` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Competition` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `CompetitorCompetition` (`Competitor`,`Competition`) USING BTREE,
  KEY `Competitor` (`Competitor`,`Competition`) USING BTREE,
  KEY `Competition` (`Competition`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Regulation` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) DEFAULT NULL,
  `Language` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Text` text CHARACTER SET utf8,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `EventLanguage` (`Event`,`Language`) USING BTREE,
  KEY `Discipline` (`Event`),
  CONSTRAINT `regulation_ibfk_1` FOREIGN KEY (`Event`) REFERENCES `Discipline` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RequestCandidate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `WID` (`Competitor`),
  KEY `Competitor` (`Competitor`),
  CONSTRAINT `requestcandidate_ibfk_1` FOREIGN KEY (`Competitor`) REFERENCES `Competitor` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RequestCandidateField` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Field` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Value` text CHARACTER SET utf8,
  `RequestCandidate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `RequestCandidate` (`RequestCandidate`),
  CONSTRAINT `requestcandidatefield_ibfk_1` FOREIGN KEY (`RequestCandidate`) REFERENCES `RequestCandidate` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RequestCandidateTemplate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
  `Type` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Language` varchar(255) CHARACTER SET utf8 DEFAULT 'RU',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RequestCandidateVote` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Status` int(11) DEFAULT '0',
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Delegate` int(11) DEFAULT NULL,
  `Reason` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Competitor` (`Competitor`),
  KEY `Delegate` (`Delegate`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Scramble` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) NOT NULL,
  `Scramble` varchar(1024) CHARACTER SET cp1251 DEFAULT NULL,
  `Group` int(11) DEFAULT NULL,
  `Timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `Attempt` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `EventID` (`Event`),
  KEY `ID` (`ID`),
  CONSTRAINT `scramble_ibfk_1` FOREIGN KEY (`Event`) REFERENCES `Event` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ScramblePdf` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) NOT NULL,
  `Secret` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Delegate` int(11) DEFAULT NULL,
  `Action` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Event` (`Event`),
  KEY `Secret` (`Secret`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Value` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Value` text CHARACTER SET utf8,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `Name` (`Name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Visitor` (
  `IP` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `User_Agent` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Hidden` tinyint(4) DEFAULT '0',
  KEY `IP` (`IP`),
  KEY `Date` (`Date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron_config` (
  `name` varchar(255) NOT NULL,
  `command` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `last` datetime DEFAULT NULL,
  `next` datetime DEFAULT NULL,
  `period` int(11) DEFAULT NULL COMMENT 'in minutes',
  `schedule` time DEFAULT NULL,
  `argument` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `begin` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `details` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smtp_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `from` varchar(255) DEFAULT NULL,
  `result` varchar(255) DEFAULT NULL,
  `version` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wca_oauth_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `me_id` int(11) DEFAULT NULL,
  `me_name` varchar(255) DEFAULT NULL,
  `me_wcaid` varchar(10) DEFAULT NULL,
  `me_countryiso2` varchar(2) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `version` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

