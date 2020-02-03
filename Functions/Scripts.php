<?php
function Script_phpinfo(){
    phpinfo();
}

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

function Script_Load333FT(){
    $hash=[];
    $Comment='{"EN":"This information is based on competition results owned and maintained by the World Cube Assocation, published at https://worldcubeassociation.org/results as of January 9, 2020."}';
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

        DataBaseClass::Query("Select E.ID from Event E "
        . " where E.Competition ='".$CompetitionID."' "
        . " and E.DisciplineFormat=0");
        
        foreach(DataBaseClass::getrows() as $row){
            DataBaseClass::Query("Delete from Attempt where Command in(select ID from Command where Event=".$row['ID'].")");
            DataBaseClass::Query("Delete from CommandCompetitor where Command in(select ID from Command where Event=".$row['ID'].")");
            DataBaseClass::Query("Delete from Command where Event=".$row['ID']."");
            DataBaseClass::Query("Delete from Event where ID=".$row['ID']."");
        }
        
        if($competition['Result']=='Average'){
            $Add='and (value4<>0 or (value2<>0 and value3=0))';
        }else{
            $Add='and (value4=0 and (value2=0 or value3<>0))';
        }
             
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
            
            if($row['personId']=='2012GOOD02'){
               $row['personId']='UNKN01';
               $row['personName']='Unknown01';
            }
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

function Script_UpdateCountry(){
    DataBaseClassWCA::Query("select C.name,C.iso2,Ct.recordName from `Countries` C join `Continents` Ct on Ct.id=C.continentid");
    foreach(DataBaseClassWCA::getRows() as $row){
        DataBaseClass::Query("REPLACE into Country (ISO2,Name,Continent) values ('".$row['iso2']."','".DataBaseClass::Escape($row['name'])."','".$row['recordName']."')");
    }

    DataBaseClassWCA::Query("select Ct.recordName,Ct.`name` from `Continents`Ct ");
    foreach(DataBaseClassWCA::getRows() as $row){
        DataBaseClass::Query("REPLACE into Continent (Code,Name) values ('".$row['recordName']."','".DataBaseClass::Escape($row['name'])."')");
    }
    
}