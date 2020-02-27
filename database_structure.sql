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
  `Special` varchar(255) DEFAULT NULL,
  `vOrder` bigint(20) DEFAULT NULL,
  `vOut` varchar(255) DEFAULT NULL,
  `Amount` double(3,1) DEFAULT '0.0',
  `exportValue` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Command` (`Command`),
  CONSTRAINT `Attempt_ibfk_1` FOREIGN KEY (`Command`) REFERENCES `Command` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BlockText` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Value` text,
  `Country` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`,`Name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
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
  `Secret` varchar(255) DEFAULT NULL,
  `vCompetitors` int(11) DEFAULT NULL,
  `vCountry` varchar(255) DEFAULT NULL,
  `Warnings` varchar(255) DEFAULT NULL,
  `Onsite` smallint(6) DEFAULT '0',
  `DateCreated` datetime DEFAULT CURRENT_TIMESTAMP,
  `Video` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Sum333` int(11) DEFAULT NULL,
  `exportId` varchar(40) DEFAULT NULL,
  `exportName` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `exportCountryId` varchar(50) DEFAULT NULL,
  `inCup` int(11) DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `Event` (`Event`),
  CONSTRAINT `competitorevent_ibfk_3` FOREIGN KEY (`Event`) REFERENCES `Event` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
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
  CONSTRAINT `commandcompetitor_ibfk_1` FOREIGN KEY (`Command`) REFERENCES `Command` (`ID`),
  CONSTRAINT `commandcompetitor_ibfk_2` FOREIGN KEY (`Competitor`) REFERENCES `Competitor` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Competition` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `WCA` varchar(255) DEFAULT NULL,
  `City` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Registration` smallint(6) DEFAULT '0',
  `Country` varchar(255) DEFAULT NULL,
  `Status` varchar(255) NOT NULL DEFAULT '0',
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `WebSite` varchar(255) DEFAULT NULL,
  `MaxCardID` int(11) DEFAULT NULL,
  `CheckDateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Onsite` smallint(6) DEFAULT '0',
  `LoadDateTime` varchar(255) DEFAULT NULL,
  `Unofficial` int(11) DEFAULT '0',
  `DelegateWCA` varchar(255) DEFAULT NULL,
  `DelegateWCAOn` tinyint(4) DEFAULT '0',
  `Cubingchina` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
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
  KEY `Competition_2` (`Competition`,`Delegate`),
  CONSTRAINT `competitiondelegate_ibfk_1` FOREIGN KEY (`Competition`) REFERENCES `Competition` (`ID`),
  CONSTRAINT `competitiondelegate_ibfk_2` FOREIGN KEY (`Delegate`) REFERENCES `Delegate` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CompetitionReport` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competition` int(11) DEFAULT NULL,
  `Delegate` int(11) DEFAULT NULL,
  `Report` text,
  `CreateTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdateTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DelegateWCA` int(11) DEFAULT NULL,
  `Parsedown` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Competition` (`Competition`,`Delegate`),
  KEY `Delegate` (`Delegate`),
  CONSTRAINT `CompetitionReport_ibfk_1` FOREIGN KEY (`Competition`) REFERENCES `Competition` (`ID`),
  CONSTRAINT `CompetitionReport_ibfk_2` FOREIGN KEY (`Delegate`) REFERENCES `Delegate` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CompetitionReportComment` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Delegate` int(11) DEFAULT NULL,
  `CommentDelegate` int(11) DEFAULT NULL,
  `Comment` text,
  `Competition` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Competitor` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `WCAID` varchar(255) DEFAULT '',
  `Country` varchar(255) DEFAULT NULL,
  `WID` int(11) DEFAULT NULL,
  `Language` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `UpdateTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Continent` (
  `Code` varchar(255) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  UNIQUE KEY `Code` (`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Country` (
  `ISO2` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Continent` varchar(255) DEFAULT NULL,
  UNIQUE KEY `ISO2` (`ISO2`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CupCell` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) DEFAULT NULL,
  `CommandWin` int(11) DEFAULT NULL,
  `Command1` int(11) DEFAULT NULL,
  `Command2` int(11) DEFAULT NULL,
  `Round` varchar(255) DEFAULT NULL,
  `CupCell1` varchar(255) DEFAULT NULL,
  `CupCell2` varchar(255) DEFAULT NULL,
  `Number` int(11) DEFAULT NULL,
  `Status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Command1` (`Command1`,`Command2`),
  KEY `Command1_2` (`Command1`),
  KEY `Command2` (`Command2`),
  KEY `CommandWin` (`CommandWin`),
  KEY `Command1_3` (`Command1`),
  KEY `Command2_2` (`Command2`),
  KEY `CommandWin_2` (`CommandWin`),
  KEY `Command1_4` (`Command1`),
  KEY `Command2_3` (`Command2`),
  KEY `CommandWin_3` (`CommandWin`),
  KEY `Event` (`Event`),
  KEY `Command1_5` (`Command1`),
  KEY `Command2_4` (`Command2`),
  KEY `CommandWin_4` (`CommandWin`),
  KEY `Event_2` (`Event`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
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
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Delegate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Status` varchar(255) DEFAULT NULL,
  `Site` varchar(255) DEFAULT NULL,
  `WCA_ID` varchar(11) NOT NULL,
  `OrderLine` int(11) DEFAULT '99',
  `WID` int(11) DEFAULT NULL,
  `Contact` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Secret` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DelegateChange` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Delegate` int(11) DEFAULT NULL,
  `Senior` int(11) DEFAULT NULL,
  `Status` varchar(255) DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Discipline` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Status` varchar(255) DEFAULT NULL,
  `Code` varchar(255) DEFAULT NULL,
  `Competitors` int(11) NOT NULL DEFAULT '1',
  `GlueScrambles` tinyint(4) DEFAULT NULL,
  `FormatResult` int(11) DEFAULT '1',
  `TNoodle` varchar(255) DEFAULT NULL,
  `TNoodles` varchar(255) DEFAULT NULL,
  `TNoodlesMult` int(11) DEFAULT '1',
  `CutScrambles` tinyint(4) DEFAULT '0',
  `Simple` int(11) DEFAULT '0',
  `Inspection` int(11) DEFAULT '15',
  `CodeScript` varchar(255) DEFAULT NULL,
  `Comment` text,
  `ScrambleComment` text,
  `Codes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FormatResult` (`FormatResult`),
  CONSTRAINT `Discipline_ibfk_1` FOREIGN KEY (`FormatResult`) REFERENCES `FormatResult` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
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
  CONSTRAINT `disciplineformat_ibfk_1` FOREIGN KEY (`Discipline`) REFERENCES `Discipline` (`ID`),
  CONSTRAINT `disciplineformat_ibfk_2` FOREIGN KEY (`Format`) REFERENCES `Format` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Event` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DisciplineFormat` int(11) NOT NULL,
  `Competition` int(11) NOT NULL,
  `CutoffMinute` int(11) DEFAULT '0',
  `CutoffSecond` int(11) DEFAULT '0',
  `Secret` varchar(255) DEFAULT NULL,
  `MaxCardID` int(11) DEFAULT '0',
  `LimitMinute` int(11) DEFAULT '10',
  `LimitSecond` int(11) DEFAULT '0',
  `Competitors` int(11) DEFAULT '500',
  `Groups` int(11) NOT NULL DEFAULT '2',
  `LocalID` int(11) DEFAULT NULL,
  `Round` int(11) DEFAULT '1',
  `RoundType` varchar(1) DEFAULT NULL,
  `vRound` varchar(255) DEFAULT NULL,
  `Cumulative` smallint(6) DEFAULT '0',
  `Comment` text,
  `ScrambleSalt` varchar(255) DEFAULT NULL,
  `ScramblePublic` varchar(255) DEFAULT NULL,
  `CommandsCup` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Competition` (`Competition`),
  KEY `Discipline` (`DisciplineFormat`),
  CONSTRAINT `event_ibfk_1` FOREIGN KEY (`Competition`) REFERENCES `Competition` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Format` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Result` varchar(255) DEFAULT NULL,
  `Attemption` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `ExtResult` varchar(255) DEFAULT NULL,
  `FormatID` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`,`Name`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormatResult` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Format` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GrandAccess` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` varchar(255) DEFAULT NULL,
  `Level` int(11) DEFAULT NULL,
  `Competition` int(11) DEFAULT '0',
  `Group` int(11) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GrandGroup` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GrandGroupMember` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Group` int(11) DEFAULT NULL,
  `Delegate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GrandRole` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Level` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LogMail` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `To` text,
  `Subject` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `Body` text,
  `DateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Result` text,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LogWcaApi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `request` varchar(255) DEFAULT NULL,
  `response` text,
  `method` varchar(255) DEFAULT NULL,
  `context` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Logs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `DateTime` datetime DEFAULT CURRENT_TIMESTAMP,
  `Object` varchar(255) DEFAULT NULL,
  `Action` varchar(255) DEFAULT NULL,
  `Details` text,
  `IP` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LogsPost` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Post` text,
  `Competitor` varchar(255) DEFAULT NULL,
  `Request` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LogsRegistration` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) DEFAULT NULL,
  `Action` varchar(12) COLLATE utf8_bin DEFAULT NULL,
  `Details` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Doing` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MultiLanguage` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Value` text,
  `Language` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Name`,`Language`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `News` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date DEFAULT NULL,
  `Text` text,
  `Delegate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Registration` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Competition` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `CompetitorCompetition` (`Competitor`,`Competition`) USING BTREE,
  KEY `Competitor` (`Competitor`,`Competition`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Regulation` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) DEFAULT NULL,
  `Language` varchar(255) DEFAULT NULL,
  `Text` text,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `EventLanguage` (`Event`,`Language`) USING BTREE,
  KEY `Discipline` (`Event`),
  CONSTRAINT `regulation_ibfk_1` FOREIGN KEY (`Event`) REFERENCES `Discipline` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RequestCandidate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `WID` (`Competitor`),
  KEY `Competitor` (`Competitor`),
  CONSTRAINT `requestcandidate_ibfk_1` FOREIGN KEY (`Competitor`) REFERENCES `Competitor` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RequestCandidateField` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Field` varchar(255) DEFAULT NULL,
  `Value` text,
  `RequestCandidate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `RequestCandidate` (`RequestCandidate`),
  CONSTRAINT `requestcandidatefield_ibfk_1` FOREIGN KEY (`RequestCandidate`) REFERENCES `RequestCandidate` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RequestCandidateTemplate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(1024) DEFAULT NULL,
  `Type` varchar(255) DEFAULT NULL,
  `Language` varchar(255) DEFAULT 'RU',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RequestCandidateVote` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Status` int(11) DEFAULT '0',
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Delegate` int(11) DEFAULT NULL,
  `Reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Scramble` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) NOT NULL,
  `Scramble` varchar(1024) DEFAULT NULL,
  `Group` int(11) DEFAULT NULL,
  `Timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `Attempt` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `EventID` (`Event`),
  CONSTRAINT `scramble_ibfk_1` FOREIGN KEY (`Event`) REFERENCES `Event` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ScramblePdf` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) NOT NULL,
  `Secret` varchar(255) COLLATE utf8_bin NOT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Delegate` int(11) DEFAULT NULL,
  `Action` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Value` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Value` text,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Visitor` (
  `IP` varchar(255) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `User_Agent` varchar(255) DEFAULT NULL,
  `Hidden` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WCAauth` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `WID` bigint(20) DEFAULT NULL,
  `Object` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

