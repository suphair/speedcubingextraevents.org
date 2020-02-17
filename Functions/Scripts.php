<?php
function Script_exportData(){
    
    
    
    DataBaseClass::Query("
    select * ,
    case 
            when final and not Cutoff then 'f' 
            when final and Cutoff then 'c' 	
            when not final and Round=1 and not Cutoff then '1'
            when not final and Round=1 and Cutoff then 'd' 
            when not final and Round=2 and not Cutoff then '2'
            when not final and Round=2 and Cutoff then 'e' 
            when not final and Round=3 and not Cutoff then '3'
            when not final and Round=3 and Cutoff then 'g' 
    end RoundType
    from
    (
    select E.ID Event, E.Competition, DF.Discipline,Round, CutoffMinute+CutoffSecond>0 Cutoff,
    Final.Final is not null final
    from Event E
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat

    left outer join
    (select Competition, DF.Discipline,Max(Round) Final 
    from Event E
    join DisciplineFormat DF on DF.ID=E.DisciplineFormat
    group by Competition,Discipline)Final on Final.Competition=E.Competition and Final.Discipline=DF.Discipline and Final.Final=Round
    )t
    order by 1,2,3 ");
    foreach(DataBaseClass::getRows() as $row){
        DataBaseClass::Query("Update Event set RoundType='{$row['RoundType']}' where ID={$row['Event']} ");
    }
                
    
    DataBaseClassExport::Query("Delete from Countries");
    DataBaseClassWCA::Query("Select * from Countries");
    foreach(DataBaseClassWCA::getRows() as $row){
        DataBaseClassExport::Query("Insert into Countries (id,name,continentId,iso2) "
                . "values ('{$row['id']}','".DataBaseClass::Escape($row['name'])."','{$row['continentId']}','{$row['iso2']}')");
    }
    
    DataBaseClassExport::Query("Delete from Continents");
    DataBaseClassWCA::Query("Select * from Continents");
    foreach(DataBaseClassWCA::getRows() as $row){
        DataBaseClassExport::Query("Insert into Continents (id,name,recordName) "
                . "values ('{$row['id']}','".DataBaseClass::Escape($row['name'])."','{$row['recordName']}')");
    }
    
    DataBaseClass::Query("Update Command set exportId=null,exportName=null,exportCountryId=null");
    DataBaseClass::Query("
        Select Com.ID Command,
        coalesce(Country.Name,'') countryId,
        GROUP_CONCAT(C.Name order by C.Name separator ', ') name,
        GROUP_CONCAT(case when C.WCAID<>'' then C.WCAID else CONCAT('NAN0',C.ID) end order by C.Name separator ', ') id 
        from Command Com
        join Event E on E.ID=Com.Event
        join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0 and Cn.WCA not like 't.%'
        join Attempt A on A.Command=Com.ID and A.Attempt=1
        join CommandCompetitor CC on CC.Command=Com.ID
        join Competitor C on C.ID=CC.Competitor
        left join Country on Country.ISO2=Com.vCountry
        group by Com.ID,countryId
    ");
    foreach(DataBaseClass::getRows() as $row){
        DataBaseClass::Query("Update Command set exportId='{$row['id']}',exportName='".DataBaseClass::Escape($row['name'])."',exportCountryId='{$row['countryId']}' where ID={$row['Command']} ");
    }
    
    
 
    DataBaseClassExport::Query("Delete from Persons");
    
    DataBaseClass::Query("Select distinct exportCountryId,exportName,exportId
                        from Command Com
                        join Event E on E.ID=Com.Event
                        join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0 and Cn.WCA not like 't.%'
                        join Attempt A on A.Command=Com.ID and A.Attempt=1");
    foreach(DataBaseClass::getRows() as $row){
        DataBaseClassExport::Query("Insert into Persons (id,name,countryId) "
                . "values ('{$row['exportId']}','".DataBaseClass::Escape($row['exportName'])."','{$row['exportCountryId']}')");
    }

    DataBaseClassExport::Query("Delete from Events");
    DataBaseClass::Query("
    select id,name,
    case when Status='Active' then (@i:=@i+1)*10 else 1000-(@j:=@j+1) end rank,
    format
    from(
    select Code id,D.Name name, 
    case FR.Format 
    when 'T' then 'time'
    when 'A T' then 'multi'
    when 'A' then 'number'
    end format,
    Status 
    from Discipline D join FormatResult FR on FR.ID=D.FormatResult
    order by Status, Code)
    events, (select @i:=0,@j:=0)X order by rank");
    
    foreach(DataBaseClass::getRows() as $row){
        DataBaseClassExport::Query("Insert into Events "
        . "values ('{$row['id']}','{$row['name']}','{$row['rank']}','{$row['format']}')");   
    }
    
    
    DataBaseClass::Query("Select * from Competition where Unofficial=0 and WCA not like 't.%'");
    DataBaseClassExport::Query("Delete from Competitions");
    
    foreach(DataBaseClass::getRows() as $row){
        DataBaseClassWCA::Query("Select * from Competitions where id='".$row['WCA']."'");
        $c=DataBaseClassWCA::getRow();
        if(isset($c['id'])){
            DataBaseClass::Query("Select group_concat(distinct D.Code order by D.Code separator ' ') Codes from Event E "
                    . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat "
                    . " join Discipline D on D.ID=DF.Discipline "
                    . "  where E.Competition=".$row['ID'].""
                    . " order by D.Code");
            $codes=DataBaseClass::getRow()['Codes'];
            foreach($c as $f=>$v){
                $c[$f]=DataBaseClass::Escape($v);
            }
            
            $Delegates=$c['wcaDelegate'];

            DataBaseClass::Query("Select C.Name,C.Email,C.WCAID from CompetitionDelegate CD "
                    . " join Delegate D on D.ID=CD.Delegate"
                    . " join Competitor C on C.WCAID=D.WCA_ID"
                    . " where CD.Competition=".$row['ID']."");

            foreach(DataBaseClass::getRows() as $d){
                if($d['Email']){
                    $Delegates.="[{".$d['Name']."}{mailto:".$d['Email']."}{SEE Delegate}] ";
                }else{
                    $Delegates.="[{".$d['Name']."}{}{SEE Delegate}] ";
                }
            }
            
            
            DataBaseClassExport::Query("INSERT INTO `Competitions` VALUES 
                ('{$c['id']}', '{$c['name']}', '{$c['cityName']}', '{$c['countryId']}',"
                . "'{$row['Comment']}', "
                . "'{$c['year']}', '{$c['month']}', '{$c['day']}', '{$c['endMonth']}', '{$c['endDay']}',"
                . " '$codes', '{$Delegates}',"
                . " '{$c['organiser']}')");
        }
    }
    
  DataBaseClass::Query("      
        update Attempt join(
            select case 
                when A.IsDNF then -1
                when A.IsDNS then -2
                when FR.Format='T' then
                        A.Minute*60*100+A.Second*100+A.Milisecond
                when FR.Format='A T' then
                        (100-A.Amount)*100000+ A.Minute*60*100+A.Second*100+A.Milisecond
            end exportValue,
            A.ID
            from Command Com
            join Event E on E.ID=Com.Event
            join DisciplineFormat DF on DF.ID=E.DisciplineFormat
            join Discipline D on D.ID=DF.Discipline
            join FormatResult FR on FR.ID=D.FormatResult
            join Attempt A on A.Command=Com.ID
            where Com.exportId is not null
        )T on Attempt.ID=T.ID
        set Attempt.exportValue=T.exportValue
    ");
    
    
    DataBaseClassExport::Query("Delete from Results");
    DataBaseClass::Query("
          select distinct 
          Cn.WCA competitionId,
          D.Code eventId,
          E.RoundType roundTypeId,
          Com.Place pos,
          coalesce(Abest.exportValue,0) best,
          coalesce(Aaverage.exportValue,0) average,
          coalesce(A1.exportValue,0) value1,
          coalesce(A2.exportValue,0) value2,
          coalesce(A3.exportValue,0) value3,
          coalesce(A4.exportValue,0) value4,
          coalesce(A5.exportValue,0) value5,
          coalesce(A6.exportValue,0) value6,
          coalesce(A7.exportValue,0) value7,
          coalesce(A8.exportValue,0) value8,
          coalesce(A9.exportValue,0) value9,
          coalesce(A10.exportValue,0) value10,
          coalesce(A10.exportValue,0) value11,
          F.FormatID formatId,
          Com.exportId personID,
          Com.exportName personName,
          Com.exportCountryId personCountryId

          from Command Com
          join Event E on E.ID=Com.Event
          join DisciplineFormat DF on DF.ID=E.DisciplineFormat
          join Format F on F.ID=DF.Format
          join Discipline D on D.ID=DF.Discipline
          join Competition Cn on Cn.ID=E.Competition
          left join Attempt Abest on Abest.Command=Com.ID and Abest.Special in('Best','Sum')
          left join Attempt Aaverage on Aaverage.Command=Com.ID and Aaverage.Special in('Average','Mean')
          left join Attempt A1 on A1.Command=Com.ID and A1.Attempt=1
          left join Attempt A2 on A2.Command=Com.ID and A2.Attempt=2
          left join Attempt A3 on A3.Command=Com.ID and A3.Attempt=3
          left join Attempt A4 on A4.Command=Com.ID and A4.Attempt=4
          left join Attempt A5 on A5.Command=Com.ID and A5.Attempt=5
          left join Attempt A6 on A6.Command=Com.ID and A6.Attempt=6
          left join Attempt A7 on A7.Command=Com.ID and A7.Attempt=7
          left join Attempt A8 on A8.Command=Com.ID and A8.Attempt=8
          left join Attempt A9 on A9.Command=Com.ID and A9.Attempt=9
          left join Attempt A10 on A10.Command=Com.ID and A10.Attempt=10
          left join Attempt A11 on A11.Command=Com.ID and A11.Attempt=11
          where Com.exportId is not null 
          ");
    
    foreach(DataBaseClass::getRows() as $row){
        DataBaseClassExport::Query("Insert into Results"
        . "(competitionId,eventId,roundTypeId,pos,best,average,"
                . "personID,personName,personCountryId,formatId,"
                . "value1,value2,value3,value4,value5,value6,value7,value8,value9,value10,value11) "
        . "values ('{$row['competitionId']}','{$row['eventId']}','{$row['roundTypeId']}','{$row['pos']}','{$row['best']}','{$row['average']}',"
        . "'{$row['personID']}','".DataBaseClass::Escape($row['personName'])."','{$row['personCountryId']}','{$row['formatId']}',"
        . "'{$row['value1']}','{$row['value2']}','{$row['value3']}','{$row['value4']}','{$row['value5']}','{$row['value6']}','{$row['value7']}','{$row['value8']}','{$row['value9']}','{$row['value10']}','{$row['value11']}')");   
    }
  
  
  
}



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