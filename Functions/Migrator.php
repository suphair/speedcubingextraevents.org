<?php
function checkColumn($table,$column){
    DataBaseClass::Query("select count(*) count from information_schema.`COLUMNS` where `TABLE_SCHEMA`='".getIni('DB','schema')."' and `TABLE_NAME`='$table' and `COLUMN_NAME`='$column'");
    return DataBaseClass::getrow()['count'];
}
function checkTable($table){
    DataBaseClass::Query("select count(*) count from information_schema.`TABLES` where `TABLE_SCHEMA`='".getIni('DB','schema')."' and `TABLE_NAME`='$table'");
    return DataBaseClass::getrow()['count'];
}

function checkConstraint($table,$constraint){
    DataBaseClass::Query("select count(*) count from information_schema.`TABLE_CONSTRAINTS` where `CONSTRAINT_SCHEMA`='".getIni('DB','schema')."' and `TABLE_NAME`='$table' and `CONSTRAINT_NAME`='$constraint'");
    return DataBaseClass::getrow()['count'];
}

function checkIndex($table,$index){
    DataBaseClass::Query("select count(*) count from information_schema.`STATISTICS` where `TABLE_SCHEMA`='".getIni('DB','schema')."' and `TABLE_NAME`='$table' and `INDEX_NAME`='$index'");
    return DataBaseClass::getrow()['count'];
}





function Migrator(){
    
    #Competitor
    if(!checkColumn('Competitor','Language')){DataBaseClass::Query("ALTER TABLE `Competitor` ADD COLUMN `Language` varchar(255)");}
    if(!checkColumn('Competitor','Email')){DataBaseClass::Query("ALTER TABLE `Competitor` ADD COLUMN `Email` varchar(255)");}
    if(!checkColumn('Competitor','Avatar')){DataBaseClass::Query("ALTER TABLE `Competitor` ADD COLUMN `Avatar` varchar(255)");}
    
    #WCAauth
    if(checkColumn('WCAauth','Name')){DataBaseClass::Query("ALTER TABLE `WCAauth` DROP COLUMN `Name`");}
    if(checkColumn('WCAauth','WCAID')){DataBaseClass::Query("ALTER TABLE `WCAauth` DROP COLUMN `WCAID`");}
    if(checkColumn('WCAauth','Country')){DataBaseClass::Query("ALTER TABLE `WCAauth` DROP COLUMN `Country`");}
    if(!checkColumn('WCAauth','Object')){DataBaseClass::Query("ALTER TABLE `WCAauth`ADD COLUMN `Object` varchar(1024)");    }
    
    #DROP TABLE
    foreach([ 'AchievementGoal','Achievement','AchievementGroup','AchievementEvent','Friend','GoalResults','Goal','GoalCompetition','GoalDiscipline','MailUpcomingCompetitions','MeetingOrganizer','MeetingCompetitorDiscipline','MeetingCompetitor','MeetingDiscipline','Meeting','MeetingDisciplineList','MeetingFormat','MosaicBuildingImage','MosaicBuilding'] as $table){
        if(checkTable($table)){
            DataBaseClass::Query("DROP TABLE $table");        
        }
    }
    
    #DELETE FROM
    foreach([ 'WCAauth','Scramble','LogMail','Logs','Visit','News'] as $table){
        DataBaseClass::Query("DELETE FROM $table");        
    }

    #Registration
    if(checkConstraint('Registration','Competitor')){DataBaseClass::Query("ALTER TABLE `Registration` DROP INDEX `Competitor`");}
    if(!checkConstraint('Registration','CompetitorCompetition')){
        DataBaseClass::Query("create table `tmp` (ID int) select min(ID) ID from `Registration` group by Competitor,Competition");
        DataBaseClass::Query("DELETE FROM `Registration` where ID not in(select ID from tmp)");
        DataBaseClass::Query("DROP TABLE `tmp`");
        DataBaseClass::Query("ALTER TABLE `Registration` ADD UNIQUE `CompetitorCompetition` USING BTREE (`Competitor`, `Competition`)");
        
    }

    #Event
    if(!checkColumn('Event','ScrambleSalt')){DataBaseClass::Query("ALTER TABLE `Event` ADD COLUMN `ScrambleSalt` varchar(255)");}
    if(!checkColumn('Event','ScrambleSalt')){DataBaseClass::Query("ALTER TABLE `Event` ADD COLUMN `ScramblePublic` varchar(255)");}
    DataBaseClass::Query("ALTER TABLE `Event` CHANGE COLUMN `LimitMinute` `LimitMinute` int(11) DEFAULT 10"); 
    DataBaseClass::Query("ALTER TABLE `Event` CHANGE COLUMN `LimitSecond` `LimitSecond` int(11) DEFAULT 0"); 
    DataBaseClass::Query("ALTER TABLE `Event` CHANGE COLUMN `CutoffMinute` `CutoffMinute` int(11) DEFAULT 0"); 
    DataBaseClass::Query("ALTER TABLE `Event` CHANGE COLUMN `CutoffSecond` `CutoffSecond` int(11) DEFAULT 0"); 
    DataBaseClass::Query("ALTER TABLE `Event` CHANGE COLUMN `Competitors` `Competitors` int(11) DEFAULT 500;");
    DataBaseClass::Query("ALTER TABLE `Event` CHANGE COLUMN `Round` `Round` int(11) DEFAULT 1;");
    
    #BlockText
    DataBaseClass::Query("DELETE FROM `BlockText` where Name='Comment'");
    DataBaseClass::Query("DELETE FROM `BlockText` where Name='Regulation'");
    
    #Country
    if(!checkConstraint('Country','ISO2')){DataBaseClass::Query("ALTER TABLE `Country` ADD UNIQUE `ISO2` USING BTREE (`ISO2`)" );}

    #Competition 
    if(checkConstraint('Competition','competition_ibfk_1')){DataBaseClass::Query("ALTER TABLE `Competition` DROP FOREIGN KEY `competition_ibfk_1`");}
    if(checkIndex('Competition','Delegate')){DataBaseClass::Query("ALTER TABLE `Competition` DROP INDEX `Delegate`");}
    foreach([ 'Delegate_','Report_','EventPicture'] as $column){
        if(checkColumn('Competition',$column)){DataBaseClass::Query("ALTER TABLE `Competition` DROP COLUMN `$column`");}
    }
    if(!checkColumn('Competition','Unofficial')){DataBaseClass::Query("ALTER TABLE `Competition` ADD COLUMN `Unofficial` int DEFAULT 0 ");}
    if(!checkColumn('Competition','DelegateWCA')){DataBaseClass::Query("ALTER TABLE `Competition` ADD COLUMN `DelegateWCA` varchar(255)");}
    if(!checkColumn('Competition','DelegateWCAOn')){DataBaseClass::Query(" ALTER TABLE `Competition` ADD COLUMN `DelegateWCAOn` tinyint DEFAULT 0");}
    DataBaseClass::Query("ALTER TABLE `Competition` CHANGE COLUMN `Status` `Status` varchar(255) NOT NULL DEFAULT '0';");
    
    #News
    if(!checkTable('News')){DataBaseClass::Query("CREATE TABLE `News` (`ID` int(11) NOT NULL AUTO_INCREMENT,`Date` date DEFAULT NULL,`Text` text,`Delegate` int(11) DEFAULT NULL,PRIMARY KEY (`ID`))");}
       
    #Delegate
    if(checkColumn('Delegate','Admin') and checkColumn('Delegate','Candidate')){
        DataBaseClass::Query("update Delegate set Status=(case when Status='Archive' then 'Archive' when Admin=1 then 'Senior' when Candidate=1 then 'Junior' else 'Middle' end)");
        DataBaseClass::Query("ALTER TABLE `Delegate` DROP COLUMN `Admin`");
        DataBaseClass::Query("ALTER TABLE `Delegate` DROP COLUMN `Candidate`");
    }
    if(checkColumn('Delegate','Admin')){DataBaseClass::Query("ALTER TABLE  `Delegate` DROP COLUMN `OrderLine`");}
    
    #Competitor
    DataBaseClass::Query("update Competitor set Name=trim(SUBSTRING_INDEX(Name, '(', 1))"); 
    DataBaseClass::Query("Update Competitor set Country=null where WCAID='' and WID is null");
    
    #Command
    if(checkColumn('Command','vName')){DataBaseClass::Query("ALTER TABLE `Command` DROP COLUMN `vName`;");}
    if(checkColumn('Command','vCompetitorIDs')){DataBaseClass::Query("ALTER TABLE `Command` DROP COLUMN `vCompetitorIDs`;");}
    
    #CommandCompetitor
    if(!checkConstraint('CommandCompetitor','CommandCompetitor')){DataBaseClass::Query("ALTER TABLE `CommandCompetitor` ADD UNIQUE `CommandCompetitor` USING BTREE (`Command`, `Competitor`);");}
    
    #Discipline
    if(!checkColumn('Discipline','Simple')){DataBaseClass::Query("ALTER TABLE `Discipline` ADD COLUMN `Simple` int DEFAULT 0");}
    if(!checkColumn('Discipline','Inspection')){DataBaseClass::Query("ALTER TABLE `Discipline` ADD COLUMN `Inspection` int DEFAULT 15");}    
    DataBaseClass::Query("UPDATE `Discipline` set `Inspection`= 20 where ID in(21,35,39,44,58) ");
    DataBaseClass::Query("UPDATE `Discipline` set Status='Archive' where ID in(31,33,37)"); 
    
    #LogsPost
    if(!checkTable('LogsPost')){DataBaseClass::Query("CREATE TABLE `LogsPost` (`ID` int NOT NULL AUTO_INCREMENT,`Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,`Post` text,`Competitor` varchar(255),`Request` varchar(255),PRIMARY KEY (`ID`))");}
    
    #Regulation
    if(checkColumn('Regulation','Discipline') and !checkColumn('Regulation','Event')){
        DataBaseClass::Query("ALTER TABLE `Regulation` CHANGE COLUMN `Discipline` `Event` int(11) DEFAULT NULL");
    }
    if(checkColumn('Regulation','Country') and !checkColumn('Regulation','Language')){
        DataBaseClass::Query("update `Regulation` set Country='EN' where Country='GB'");
        DataBaseClass::Query("ALTER TABLE `Regulation` CHANGE COLUMN `Country` `Language` varchar(255) DEFAULT NULL");
    }
    if(!checkConstraint('Regulation','EventLanguage')){DataBaseClass::Query("ALTER TABLE `Regulation` ADD UNIQUE `EventLanguage` USING BTREE (`Event`, `Language`)");}
        
    #CompetitionDelegate
    if(checkConstraint('CompetitionDelegate','Competition_2')){DataBaseClass::Query("ALTER TABLE `CompetitionDelegate` DROP INDEX `Competition_2`");}
    if(!checkConstraint('CompetitionDelegate','CompetitionDelegate'))DataBaseClass::Query("ALTER TABLE `CompetitionDelegate`  ADD UNIQUE `CompetitionDelegate` USING BTREE (`Competition`, `Delegate`);");
    
    #ScramblePdf
    if(!checkTable('ScramblePdf')){DataBaseClass::Query("CREATE TABLE `ScramblePdf` (`ID` int(11) NOT NULL AUTO_INCREMENT,`Event` int(11) NOT NULL,`Secret` varchar(255) NOT NULL,`Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,`Delegate` int(11) DEFAULT NULL,PRIMARY KEY (`ID`)) ");}
          
    #LogsRegistration
    if(!checkTable('LogsRegistration')){DataBaseClass::Query("CREATE TABLE `LogsRegistration` (`ID` int(11) NOT NULL AUTO_INCREMENT,`Event` int(11) DEFAULT NULL,`Action` varchar(12) DEFAULT NULL,`Details` varchar(255) DEFAULT NULL,`Doing` varchar(255) DEFAULT NULL,`Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`ID`))");}
    
    #MultiLanguage
    if(!checkTable('MultiLanguage')){
            DataBaseClass::Query("CREATE TABLE `MultiLanguage` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `Name` varchar(255) DEFAULT NULL,
            `Value` text,
            `Language` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`ID`),
            UNIQUE KEY `Name` (`Name`,`Language`) USING BTREE)");
        } 
       
    DataBaseClass::Query("update `RequestCandidateTemplate` set Language='EN' where Language='US'");    
    DataBaseClass::Query("Delete from `CompetitionDelegate` where Competition=110");
    DataBaseClass::Query("Delete from `Competition` where ID=110");

    CommandUpdate(); 
    UpdateCountry();
    echo 'MIGRATION IS COMPLETED';
    exit();
}

function Migrator_Load333FT(){
    $hash=[];
    $Comment='{"EN":"Results were downloaded from WCA","RU":"Данные загружены с WCA"}';
    foreach([
        ['Name'=>'WCA 3x3x3 FT A5',     'Code'=>'t.Transfer333ftAverage5'   ,'Result' =>'Average',  'Attemption' => '5', 'format'=>'average'],
        ['Name'=>'WCA 3x3x3 FT M3',      'Code'=>'t.Transfer333ftMean3'      ,'Result' =>'Mean',     'Attemption' => '3', 'format'=>'average'],
        ['Name'=>'WCA 3x3x3 FT B5',      'Code'=>'t.Transfer333ftBest5'     ,'Result' =>'Average',  'Attemption' => '5', 'format'=>'best'],        
        ['Name'=>'WCA 3x3x3 FT B3',      'Code'=>'t.Transfer333ftBest3'      ,'Result' =>'Mean',     'Attemption' => '3', 'format'=>'best'], 
        ] as $competition){
        
        DataBaseClass::Query("Select ID from Competition where WCA ='".$competition['Code']."'");
        $row=DataBaseClass::getRow();
        if(isset($row['ID'])){
            $CompetitionID=$row['ID'];
            DataBaseClass::Query("Update Competition set Name='".$competition['Name']."', Comment='".$Comment."' where ID ='".$CompetitionID."'");    
            
        }else{
            DataBaseClass::Query("Insert into Competition (Name,WCA,Comment,StartDate,EndDate) "
                    . "values ('".$competition['Name']."','".$competition['Code']."','".$Comment."','2019-12-31','2019-12-31')");    
            $CompetitionID=DataBaseClass::getID();
        }
        
        
        DataBaseClass::Query("Select DF.ID from  DisciplineFormat DF "
                . " join Discipline D on D.ID=DF.Discipline "
                . " join Format F on F.ID = DF.Format"
                . " where F.Result='".$competition['Result']."' "
                . " and F.Attemption='".$competition['Attemption']."'"
                . " and D.Code='333ft'");
        $DisciplineFormat_ID=DataBaseClass::getrow()['ID'];
        
        DataBaseClass::Query("Select E.ID from Event E "
                . " where E.Competition ='".$CompetitionID."' "
                . " and E.DisciplineFormat='".$DisciplineFormat_ID."' ");
        
        $row=DataBaseClass::getrow();
        if(isset($row['ID'])){
            $EventID=$row['ID'];    
        }else{
            DataBaseClass::Query("Insert into Event (DisciplineFormat,Competition) "
                    . "values ('$DisciplineFormat_ID','$CompetitionID')");    
            $EventID=DataBaseClass::getID();
        }

        $Format=$competition['format'];
        
        DataBaseClass::Query("Delete from Attempt where Command in(select ID from Command where Event=$EventID)");
        DataBaseClass::Query("Delete from CommandCompetitor where Command in(select ID from Command where Event=$EventID)");
        DataBaseClass::Query("Delete from Command where Event=$EventID");

        if($competition['Result']=='Average'){
            $Add='and (value4<>0 or (value2<>0 and value3=0))';
        }else{
            $Add='and (value4=0 and (value2=0 or value3<>0))';
        }
/*
        DataBaseClassWCA::Query("
                select Concat(R.competitionId,personID,roundTypeId) hash,C.iso2, R.value1,R.value2,R.value3,R.value4,R.value5,R.personName,R.`personId`
                    from `Results` R
                    join (
                        select min($Format) $Format ,personCountryId from `Results`
                        where eventid='333ft' and $Format>0
                        group by personCountryId
                    ) R1 on R1.personCountryId=R.personCountryId and R1.$Format=R.$Format
                    join `Countries` C 
                        on C.id=R.personCountryId
                    where R.eventid='333ft' and R.$Format>0 ".$Add."
                    order by R.$Format");
*/        
        
        DataBaseClassWCA::Query("
                select Concat(R.competitionId,R.personID,R.roundTypeId) hash,C.iso2, R.value1,R.value2,R.value3,R.value4,R.value5,R.personName,R.`personId`
                    from `Results` R
                    join (
                        select min($Format) $Format ,personID from `Results`
                        where eventid='333ft' and $Format>0
                        group by personID
                    ) R1 on R1.personID=R.personID and R1.$Format=R.$Format
                    join `Countries` C 
                        on C.id=R.personCountryId
                    where R.eventid='333ft' and R.$Format>0 ".$Add."
                    order by R.$Format");

        foreach (DataBaseClassWCA::getRows() as $row)
            if(!in_array($row['hash'],$hash)){
            $hash[]=$row['hash'];
            DataBaseClass::FromTable("Competitor","WCAID='".$row['personId']."'");
            $Competitor=DataBaseClass::QueryGenerate(false);
            if(!$Competitor){
                DataBaseClass::Query("Insert Into Competitor"
                        . "  (Country,Name,WCAID) "
                        . " values ('".$row['iso2']."','".Short_Name(DataBaseClass::Escape($row['personName']))."','".$row['personId']."')");    
                $CompetitorID=DataBaseClass::getID();
            }else{
                $CompetitorID=$Competitor['Competitor_ID'];
                DataBaseClass::Query("Update Competitor "
                        . " set Country='".$row['iso2']."',Name='".Short_Name(DataBaseClass::Escape($row['personName']))."' "
                        . " where ID=".$CompetitorID);    
            }

            DataBaseClass::FromTable("Competitor","ID='$CompetitorID'");
            DataBaseClass::Join_current("CommandCompetitor");
            DataBaseClass::Join_current("Command");
            DataBaseClass::Where_current("Event=".$EventID);

            $Command=DataBaseClass::QueryGenerate(false);
            if(!$Command){
                $CommandID=CommandAdd(0,$EventID,$CompetitorID);
            }else{
                $CommandID=$Command['Command_ID'];
            }
            $amounts=[];
            $values=[];
            for($i=1;$i<=$competition['Attemption'];$i++){
                if($row['value'.$i]>0){
                    $values[$i]=getTimeStrFromValue($row['value'.$i]);
                }else{
                    $values[$i]=str_replace([-1,-2],['DNF','DNS'],$row['value'.$i]);
                }
                $amounts[$i]=0;
            }

            if($competition['Attemption']==5){
                SetValueAttempts($CommandID,sizeof($amounts),'Average','Best',$values,$amounts);    
            }else{
                SetValueAttempts($CommandID,sizeof($amounts),'Mean','Best',$values,$amounts);
            }   
        }
    }
}

function UpdateCountry(){
    DataBaseClassWCA::Query("select C.name,C.iso2,Ct.recordName from `Countries` C join `Continents` Ct on Ct.id=C.continentid");
    foreach(DataBaseClassWCA::getRows() as $row){
        DataBaseClass::Query("REPLACE into Country (ISO2,Name,Continent) values ('".$row['iso2']."','".DataBaseClass::Escape($row['name'])."','".$row['recordName']."')");
    }

    DataBaseClassWCA::Query("select Ct.recordName,Ct.`name` from `Continents`Ct ");
    foreach(DataBaseClassWCA::getRows() as $row){
        DataBaseClass::Query("REPLACE into Continent (Code,Name) values ('".$row['recordName']."','".DataBaseClass::Escape($row['name'])."')");
    }
    
    /*
    DataBaseClassWCA::Query("select P.id,C.iso2,P.name from Persons P join Countries C on C.id=P.countryId");
    foreach(DataBaseClassWCA::getRows() as $row){
        DataBaseClass::Query("Update Competitor set Country='".$row['iso2']."', Name='".Short_Name(DataBaseClass::Escape($row['name']))."' where WCAID='".$row['id']."'");
    }*/
}
