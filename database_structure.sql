/*
 Navicat MySQL Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 50723
 Source Host           : localhost
 Source Database       : suphair_see

 Target Server Type    : MySQL
 Target Server Version : 50723
 File Encoding         : utf-8

 Date: 02/19/2020 08:40:58 AM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `Attempt`
-- ----------------------------
DROP TABLE IF EXISTS `Attempt`;
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
) ENGINE=InnoDB AUTO_INCREMENT=59897 DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `BlockText`
-- ----------------------------
DROP TABLE IF EXISTS `BlockText`;
CREATE TABLE `BlockText` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Value` text,
  `Country` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`,`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `Command`
-- ----------------------------
DROP TABLE IF EXISTS `Command`;
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
  PRIMARY KEY (`ID`),
  KEY `Event` (`Event`),
  CONSTRAINT `competitorevent_ibfk_3` FOREIGN KEY (`Event`) REFERENCES `Event` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11748 DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `CommandCompetitor`
-- ----------------------------
DROP TABLE IF EXISTS `CommandCompetitor`;
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
) ENGINE=InnoDB AUTO_INCREMENT=17719 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `Competition`
-- ----------------------------
DROP TABLE IF EXISTS `Competition`;
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
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `CompetitionDelegate`
-- ----------------------------
DROP TABLE IF EXISTS `CompetitionDelegate`;
CREATE TABLE `CompetitionDelegate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competition` int(11) DEFAULT NULL,
  `Delegate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `CompetitionDelegate` (`Competition`,`Delegate`) USING BTREE,
  KEY `Competition` (`Competition`),
  KEY `Delegate` (`Delegate`),
  CONSTRAINT `competitiondelegate_ibfk_1` FOREIGN KEY (`Competition`) REFERENCES `Competition` (`ID`),
  CONSTRAINT `competitiondelegate_ibfk_2` FOREIGN KEY (`Delegate`) REFERENCES `Delegate` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=282 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `CompetitionReport`
-- ----------------------------
DROP TABLE IF EXISTS `CompetitionReport`;
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
  KEY `Delegate` (`Delegate`),
  KEY `Competition` (`Competition`,`Delegate`) USING BTREE,
  CONSTRAINT `CompetitionReport_ibfk_1` FOREIGN KEY (`Competition`) REFERENCES `Competition` (`ID`),
  CONSTRAINT `CompetitionReport_ibfk_2` FOREIGN KEY (`Delegate`) REFERENCES `Delegate` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `CompetitionReportComment`
-- ----------------------------
DROP TABLE IF EXISTS `CompetitionReportComment`;
CREATE TABLE `CompetitionReportComment` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Delegate` int(11) DEFAULT NULL,
  `CommentDelegate` int(11) DEFAULT NULL,
  `Comment` text,
  `Competition` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `Competitor`
-- ----------------------------
DROP TABLE IF EXISTS `Competitor`;
CREATE TABLE `Competitor` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `WCAID` varchar(255) DEFAULT '',
  `Country` varchar(255) DEFAULT NULL,
  `WID` bigint(11) DEFAULT NULL,
  `Language` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Avatar` varchar(255) DEFAULT NULL,
  `UpdateTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`,`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=8200 DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `Continent`
-- ----------------------------
DROP TABLE IF EXISTS `Continent`;
CREATE TABLE `Continent` (
  `Code` varchar(255) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  UNIQUE KEY `Code` (`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `Country`
-- ----------------------------
DROP TABLE IF EXISTS `Country`;
CREATE TABLE `Country` (
  `ISO2` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Continent` varchar(255) DEFAULT NULL,
  UNIQUE KEY `ISO2` (`ISO2`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `Delegate`
-- ----------------------------
DROP TABLE IF EXISTS `Delegate`;
CREATE TABLE `Delegate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Status` varchar(255) DEFAULT NULL,
  `Site` varchar(255) DEFAULT NULL,
  `WCA_ID` varchar(11) NOT NULL,
  `WID` int(11) DEFAULT NULL,
  `Contact` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Secret` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `DelegateChange`
-- ----------------------------
DROP TABLE IF EXISTS `DelegateChange`;
CREATE TABLE `DelegateChange` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Delegate` int(11) DEFAULT NULL,
  `Senior` int(11) DEFAULT NULL,
  `Status` varchar(255) DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `Discipline`
-- ----------------------------
DROP TABLE IF EXISTS `Discipline`;
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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `DisciplineFormat`
-- ----------------------------
DROP TABLE IF EXISTS `DisciplineFormat`;
CREATE TABLE `DisciplineFormat` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Discipline` int(11) DEFAULT NULL,
  `Format` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Discipline` (`Discipline`),
  KEY `Format` (`Format`),
  CONSTRAINT `disciplineformat_ibfk_1` FOREIGN KEY (`Discipline`) REFERENCES `Discipline` (`ID`),
  CONSTRAINT `disciplineformat_ibfk_2` FOREIGN KEY (`Format`) REFERENCES `Format` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `Event`
-- ----------------------------
DROP TABLE IF EXISTS `Event`;
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
  PRIMARY KEY (`ID`),
  KEY `Competition` (`Competition`),
  KEY `Discipline` (`DisciplineFormat`),
  CONSTRAINT `event_ibfk_1` FOREIGN KEY (`Competition`) REFERENCES `Competition` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=419 DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `Format`
-- ----------------------------
DROP TABLE IF EXISTS `Format`;
CREATE TABLE `Format` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Result` varchar(255) DEFAULT NULL,
  `Attemption` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `ExtResult` varchar(255) DEFAULT NULL,
  `FormatID` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`,`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `FormatResult`
-- ----------------------------
DROP TABLE IF EXISTS `FormatResult`;
CREATE TABLE `FormatResult` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Format` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `GrandAccess`
-- ----------------------------
DROP TABLE IF EXISTS `GrandAccess`;
CREATE TABLE `GrandAccess` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` varchar(255) DEFAULT NULL,
  `Level` int(11) DEFAULT NULL,
  `Competition` int(11) DEFAULT '0',
  `Group` int(11) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `GrandGroup`
-- ----------------------------
DROP TABLE IF EXISTS `GrandGroup`;
CREATE TABLE `GrandGroup` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `GrandGroupMember`
-- ----------------------------
DROP TABLE IF EXISTS `GrandGroupMember`;
CREATE TABLE `GrandGroupMember` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Group` int(11) DEFAULT NULL,
  `Delegate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `GrandRole`
-- ----------------------------
DROP TABLE IF EXISTS `GrandRole`;
CREATE TABLE `GrandRole` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Level` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `LogMail`
-- ----------------------------
DROP TABLE IF EXISTS `LogMail`;
CREATE TABLE `LogMail` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `To` text,
  `Subject` text,
  `Body` text,
  `DateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Result` text,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `Logs`
-- ----------------------------
DROP TABLE IF EXISTS `Logs`;
CREATE TABLE `Logs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `DateTime` datetime DEFAULT CURRENT_TIMESTAMP,
  `Object` varchar(255) DEFAULT NULL,
  `Action` varchar(255) DEFAULT NULL,
  `Details` text,
  `IP` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8782 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `LogsPost`
-- ----------------------------
DROP TABLE IF EXISTS `LogsPost`;
CREATE TABLE `LogsPost` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Post` text,
  `Competitor` varchar(255) DEFAULT NULL,
  `Request` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5185 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `LogsRegistration`
-- ----------------------------
DROP TABLE IF EXISTS `LogsRegistration`;
CREATE TABLE `LogsRegistration` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) DEFAULT NULL,
  `Action` varchar(12) DEFAULT NULL,
  `Doing` varchar(255) DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Details` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `MultiLanguage`
-- ----------------------------
DROP TABLE IF EXISTS `MultiLanguage`;
CREATE TABLE `MultiLanguage` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Value` text,
  `Language` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Name` (`Name`,`Language`)
) ENGINE=InnoDB AUTO_INCREMENT=46223 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `News`
-- ----------------------------
DROP TABLE IF EXISTS `News`;
CREATE TABLE `News` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date DEFAULT NULL,
  `Text` text,
  `Delegate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `Registration`
-- ----------------------------
DROP TABLE IF EXISTS `Registration`;
CREATE TABLE `Registration` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Competition` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `CompetitorCompetition` (`Competitor`,`Competition`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=28849 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `Regulation`
-- ----------------------------
DROP TABLE IF EXISTS `Regulation`;
CREATE TABLE `Regulation` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) DEFAULT NULL,
  `Language` varchar(255) DEFAULT NULL,
  `Text` text,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `EventLanguage` (`Event`,`Language`),
  KEY `Discipline` (`Event`),
  CONSTRAINT `regulation_ibfk_1` FOREIGN KEY (`Event`) REFERENCES `Discipline` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=416 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `RequestCandidate`
-- ----------------------------
DROP TABLE IF EXISTS `RequestCandidate`;
CREATE TABLE `RequestCandidate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `WID` (`Competitor`),
  KEY `Competitor` (`Competitor`),
  CONSTRAINT `requestcandidate_ibfk_1` FOREIGN KEY (`Competitor`) REFERENCES `Competitor` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `RequestCandidateField`
-- ----------------------------
DROP TABLE IF EXISTS `RequestCandidateField`;
CREATE TABLE `RequestCandidateField` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Field` varchar(255) DEFAULT NULL,
  `Value` text,
  `RequestCandidate` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `RequestCandidate` (`RequestCandidate`),
  CONSTRAINT `requestcandidatefield_ibfk_1` FOREIGN KEY (`RequestCandidate`) REFERENCES `RequestCandidate` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=344 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `RequestCandidateTemplate`
-- ----------------------------
DROP TABLE IF EXISTS `RequestCandidateTemplate`;
CREATE TABLE `RequestCandidateTemplate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(1024) DEFAULT NULL,
  `Type` varchar(255) DEFAULT NULL,
  `Language` varchar(255) DEFAULT 'RU',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `RequestCandidateVote`
-- ----------------------------
DROP TABLE IF EXISTS `RequestCandidateVote`;
CREATE TABLE `RequestCandidateVote` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Status` int(11) DEFAULT '0',
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Delegate` int(11) DEFAULT NULL,
  `Reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `Scramble`
-- ----------------------------
DROP TABLE IF EXISTS `Scramble`;
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
) ENGINE=InnoDB AUTO_INCREMENT=1326 DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `ScramblePdf`
-- ----------------------------
DROP TABLE IF EXISTS `ScramblePdf`;
CREATE TABLE `ScramblePdf` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event` int(11) NOT NULL,
  `Secret` varchar(255) NOT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Delegate` int(11) DEFAULT NULL,
  `Action` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=766 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `Value`
-- ----------------------------
DROP TABLE IF EXISTS `Value`;
CREATE TABLE `Value` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Value` text,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3329 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `Visit`
-- ----------------------------
DROP TABLE IF EXISTS `Visit`;
CREATE TABLE `Visit` (
  `IP` varchar(255) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `User_Agent` varchar(255) DEFAULT NULL,
  `Hidden` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `WCAauth`
-- ----------------------------
DROP TABLE IF EXISTS `WCAauth`;
CREATE TABLE `WCAauth` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `WID` bigint(20) DEFAULT NULL,
  `Object` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5659 DEFAULT CHARSET=cp1251;

SET FOREIGN_KEY_CHECKS = 1;
