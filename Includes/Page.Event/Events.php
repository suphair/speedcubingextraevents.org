<?php includePage('Navigator'); ?>
<?php

$request= getRequest();
if(isset($request[1])){
    $type_filter=$request[1];
}else{
    $type_filter='none';
}
$competitor= GetCompetitorData(); 
if($competitor){
    DataBaseClass::Query("Select Ct.Name,Ct.Code from Country C"
             . " join Continent Ct on Ct.Code=C.Continent where C.ISO2='".$competitor->country_iso2."'");
    $res=DataBaseClass::getRow();
    if(isset($res['Name'])){ 
        $Continent=$res['Name'];
        $Continent_Code=$res['Code'];
    }else{
        $Continent='';
        $Continent_Code='';
    }

    $Countries_code=[];
    DataBaseClass::Query("Select ISO2,Continent from Country where Continent<>''");
    foreach(DataBaseClass::getRows() as $row){
        if($row['Continent']==$Continent_Code){
            $Countries_code[]=$row['ISO2'];
        }
    }
}
$extraWhere="";
?>


<h2><a href="<?=PageIndex()?>Events"><?= ml('Events.Title'); ?></a>

<?php  
if($type_filter=='team') echo '/ '.ml('Events.Team.Title');
if($type_filter=='puzzles') echo '/ '.ml('Events.Puzzles.Title');
if($type_filter=='wcapuzzle') echo '/ '.ml('Events.WCAPuzzle.Title');
if($type_filter=='nonwcapuzzle') echo '/ '.ml('Events.nonWCAPuzzle.Title');
if($type_filter=='simple') echo '/ '.ml('Events.Simple.Title');
if($type_filter=='nonsimple') echo '/ '.ml('Events.nonSimple.Title');
 ?>

</h2>
<nobr><a class="<?= $type_filter=='team'?'select':'' ?>" href="<?= PageIndex() ?>Events/Team"><?= ml('Events.Team.Title'); ?></a></nobr>
▪ 
<nobr><a class="<?= $type_filter=='puzzles'?'select':'' ?>" href="<?= PageIndex() ?>Events/Puzzles"><?= ml('Events.Puzzles.Title'); ?></a></nobr>
▪ 
<nobr><a class="<?= $type_filter=='wcapuzzle'?'select':'' ?>" href="<?= PageIndex() ?>Events/WCAPuzzle"><?= ml('Events.WCAPuzzle.Title'); ?></a></nobr>
▪ 
<nobr><a class="<?= $type_filter=='nonwcapuzzle'?'select':'' ?>" href="<?= PageIndex() ?>Events/nonWCAPuzzle"><?= ml('Events.nonWCAPuzzle.Title'); ?></a></nobr>
▪ 
<nobr><a class="<?= $type_filter=='simple'?'select':'' ?>" href="<?= PageIndex() ?>Events/Simple"><?= ml('Events.Simple.Title'); ?></a></nobr>
▪ 
<nobr><a class="<?= $type_filter=='nonsimple'?'select':'' ?>" href="<?= PageIndex() ?>Events/nonSimple"><?= ml('Events.nonSimple.Title'); ?></a></nobr>

<?php $Competitor=GetCompetitorData();

if($type_filter=='simple'){
    $extraWhere=" and Simple=1";
}

if($type_filter=='nonsimple'){
    $extraWhere=" and Simple=0";
}

if($type_filter=='team'){
    $extraWhere=" and D.Competitors>1";
}
 
if($type_filter=='puzzles'){
    $extraWhere=" and coalesce(D.TNoodles,'') <>'' ";
}

if($type_filter=='wcapuzzle'){
    $extraWhere=" and coalesce(GlueScrambles,0)=1 ";
}

if($type_filter=='nonwcapuzzle'){
    $extraWhere=" and coalesce(GlueScrambles,0)<>1 ";
}

    DataBaseClass::Query("select D.Simple,D.ID,D.Code,D.CodeScript,D.Status,D.Name,D.Competitors,
    count(distinct C.ID) countCompetitors,
    count(distinct Cn.ID) countCompetitions,
    (sum(case when Com.Place>0 and Cn.ID is not null then 1 else 0 end)>0) AttemptExists
    from `Discipline` D
    left outer join `DisciplineFormat` DF on DF.Discipline=D.ID
    left outer join `Event` E on E.DisciplineFormat=DF.ID
    left outer join `Competition` Cn on Cn.ID=E.Competition And Cn.Unofficial=0  and Cn.WCA not like 't.%'
    left outer join `Command` Com on Com.Event=E.ID 
    left outer join `CommandCompetitor` CC on CC.Command=Com.ID  
    left outer join `Competitor` C on C.ID=CC.Competitor and Cn.WCA not like 't.%'
    where 1=1 ".(CheckAccess('Events.Arhive')?"": " and D.Status='Active' ") ." $extraWhere
    group by D.ID
    order by D.Status,(sum(case when Com.Place>0 and Cn.ID is not null then 1 else 0 end)>0)
    ,count(distinct C.ID) desc, count(distinct E.Competition) desc,  D.Name"); 
    $disciplines= DataBaseClass::getRows(); 
    ?>
    <table class="Disciplines">
    <?php $attempt_exists='-1';
    foreach( $disciplines as $d=>$discipline){ ?>
         <?php if($discipline['AttemptExists']!=$attempt_exists){
            $attempt_exists = $discipline['AttemptExists']; ?>
            <tr class="no_border">
                <td colspan="9"><b>
                        <?= $d?'<br>':'' ?>
                    <?php if($attempt_exists){ ?>
                        <?php # ml('Events.Old') ?>
                    <?php }else{ ?>    
                        <?= ml('Events.New') ?>
                    <?php } ?>
                    </b>
                </td>
            </tr>
            <tr class="tr_title">
                <td><?= ml('Events.Table.Name') ?></td>
                <td align="center"> <img height="15px"src="<?= PageIndex() ?>Image/Icons/persons.png"></td>
                <td align="center"> <img height="12px"src="<?= PageIndex() ?>Image/Icons/competitions.png"></td>
                <?php if($Competitor){?>
                    <td class="attempt" colspan="2"><b><?= ml('Events.Table.PersonalRecord') ?></b></td>
                    <td class="attempt" colspan="2">
                        <b><?= ml('Events.Table.NationalRecord') ?></b>
                        <?php if($attempt_exists){ ?>
                            <?= ImageCountry($Competitor->country_iso2, 25) ?> 
                        <?php } ?>
                    </td>
                    <td class="attempt" colspan="2">
                        <b><?= ml('Events.Table.ContinentRecord') ?></b>
                            <?php if($attempt_exists){ ?>
                                (<?= $Continent ?>)
                            <?php } ?>
                    </td>
                <?php } ?>
                
                <td class="attempt" colspan="2"><b><?= ml('Events.Table.WorldRecord') ?></b></td>
                <td></td>
                <td></td>
            </tr>
            <?php if($attempt_exists){?>
            <tr >
                <td></td>
                <td/>
                <td/>    
                
                <?php if($Competitor){?>
                    <td class="attempt"><?= ml('Events.Table.Single') ?></td>
                    <td class="attempt"><?= ml('Events.Table.Average') ?></td>
                    <td class="attempt"><?= ml('Events.Table.Single') ?></td>
                    <td class="attempt"><?= ml('Events.Table.Average') ?></td>
                    <td class="attempt"><?= ml('Events.Table.Single') ?></td>
                    <td class="attempt"><?= ml('Events.Table.Average') ?></td>
                <?php } ?>
                <td class="attempt"><?= ml('Events.Table.Single') ?></td>
                <td class="attempt"><?= ml('Events.Table.Average') ?></td>
            </tr>
            <?php } ?>
        <?php 
        } ?>
    <tr valign="bottom" >
    <td class="border-right-dotted" style="padding:0; margin:0">
        <?= ImageEvent($discipline['CodeScript'],25,$discipline['Name']) ?>  

        <a href="<?= LinkDiscipline($discipline['Code']) ?>">
                <?= $discipline['Name'] ?>
        </a>
        <?php if($discipline['Status']=='Archive'){ ?>
            <?= svg_red(10);?>
        <?php } ?>
    </td>
    <td class="border-right-dotted" align="right">
        <?= $discipline['countCompetitions']?$discipline['countCompetitors']:'0' ?> 
    </td>
    <td align="right">     
        <?= $discipline['countCompetitions']?$discipline['countCompetitions']:'0' ?>
    </td>
        <?php 
        $Record=array();
        $BaseSql="Select A.vOut,Com.vCountry,A.Special from Attempt A "
                . " join Command Com on Com.ID=A.Command and A.Special in ('@Special') @Country"
                . " join Event E on E.ID=Com.Event "
                . " join Competition Cn on Cn.ID=E.Competition and Cn.Unofficial=0 "
                . " join CommandCompetitor CC on CC.Command=Com.ID "
                . " join Competitor C on C.ID=CC.Competitor @Competitor" 
                . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat and DF.Discipline=".$discipline['ID'] 
                . " where A.Special in (select Result from Format F where F.ID=DF.Format union select ExtResult from Format F where F.ID=DF.Format)   "
                . " order by A.vOrder limit 1";
        
        $params=array("@Special","@Country","@Competitor");
        $values=array("","","");
        
        $values[0]="Best','Sum";
        DataBaseClass::Query(str_replace($params,$values,$BaseSql));
        $Record[0][0]=DataBaseClass::getRow();
        
        $values[0]="Average','Mean";
        DataBaseClass::Query(str_replace($params,$values,$BaseSql));
        $Record[0][1]=DataBaseClass::getRow();
        
        if($Competitor){
            $values[1]="and Com.vCountry='".$Competitor->country_iso2."'";
            
            $values[0]="Best','Sum";
            DataBaseClass::Query(str_replace($params,$values,$BaseSql));
            $Record[1][0]=DataBaseClass::getRow();

            $values[0]="Average','Mean";
            DataBaseClass::Query(str_replace($params,$values,$BaseSql));
            $Record[1][1]=DataBaseClass::getRow();
            
            $values[1]="and Com.vCountry in ('".implode("','",$Countries_code)."')";
            
            $values[0]="Best','Sum";
            DataBaseClass::Query(str_replace($params,$values,$BaseSql));
            $Record[3][0]=DataBaseClass::getRow();

            $values[0]="Average','Mean";
            DataBaseClass::Query(str_replace($params,$values,$BaseSql));
            $Record[3][1]=DataBaseClass::getRow();
            
        }
        
        
        
        if($Competitor){ 
            $values[1]="";
            $values[2]=" and C.wid='".$Competitor->id."'";

            $values[0]="Best','Sum";
            DataBaseClass::Query(str_replace($params,$values,$BaseSql));
            $Record[2][0]=DataBaseClass::getRow();

            $values[0]="Average','Mean";
            DataBaseClass::Query(str_replace($params,$values,$BaseSql));
            $Record[2][1]=DataBaseClass::getRow();
            ?>
            <td class="attempt border-left-dotted" style="text-align:right">
                <?php if(isset($Record[2][0]['vOut'])){
                    $r=$Record[2][0]; ?>
                    <?php if($r['vOut']==$Record[0][0]['vOut']){?>
                        <span class="message"><?= ml('*.WorldRecord') ?></span>
                    <?php }elseif($r['vOut']==$Record[1][0]['vOut']){?>
                        <span class="message"><?= ml('*.NationalRecord') ?></span>
                    <?php } ?>
                        <?= $r['vOut'] ?>
                <?php } ?>
            </td>
            <td class="attempt border-right-dotted" style="text-align:right">
                <?php if(isset($Record[2][1]['vOut'])){
                    $r=$Record[2][1]; ?>
                    <?php if($r['vOut']==$Record[0][1]['vOut']){?>
                        <span class="message"><?= ml('*.WorldRecord') ?></span>
                    <?php }elseif($r['vOut']==$Record[1][1]['vOut']){?>
                        <span class="message"><?= ml('*.NationalRecord') ?></span>
                    <?php } ?>
                        <?= $r['vOut'] ?>
                <?php } ?>
            </td>            
            <td class="attempt border-left-solid" style="text-align:right">
                <?php if(isset($Record[1][0]['vOut'])){
                    $r=$Record[1][0]; ?>
                    <a href='<?= PageIndex()?>Records/<?= $Competitor->country_iso2 ?>/<?= $discipline['Code'] ?>'><?= $r['vOut']?></a>
                <?php } ?>
            </td>
            <td class="attempt border-left-dotted" style="text-align:right">
                 <?php if(isset($Record[1][1]['vOut'])){ 
                     $r=$Record[1][1]; ?>
                     <a href='<?= PageIndex()?>Records/<?= $Competitor->country_iso2 ?>/<?= $discipline['Code'] ?>'><?= $r['vOut'] ?></a>
                <?php } ?>
            </td>
            <td class="attempt border-left-solid" style="text-align:right">
                <?php if(isset($Record[3][0]['vOut'])){
                    $r=$Record[3][0]; ?>
                    <a href='<?= PageIndex()?>Records/_<?= $Continent_Code ?>/<?= $discipline['Code'] ?>'><?= $r['vOut']?></a>
                    <?php if( $r['vCountry'] ){ ?>
                        <?= ImageCountry($r['vCountry'], 20)?>
                    <?php } ?>
                <?php } ?>
            </td>
            <td class="attempt border-left-dotted" style="text-align:right">
                 <?php if(isset($Record[3][1]['vOut'])){ 
                     $r=$Record[3][1]; ?>
                     <a href='<?= PageIndex()?>Records/_<?= $Continent_Code ?>/<?= $discipline['Code'] ?>'><?= $r['vOut'] ?></a>
                     <?php if( $r['vCountry'] ){ ?>
                        <?= ImageCountry($r['vCountry'], 20)?>
                    <?php } ?>
                <?php } ?>
            </td>
        <?php } ?>
        <td class="border-left-solid" style="text-align:right">
        <?php if(isset($Record[0][0]['vOut'])){
            $r=$Record[0][0]; ?>
                <a href='<?= PageIndex()?>Records/All/<?= $discipline['Code'] ?>'><?= $r['vOut'] ?></a>
                <?php if( $r['vCountry'] ){ ?>
                    <?= ImageCountry($r['vCountry'], 20)?>
                <?php } ?>
        <?php } ?>
        </td> 
        <td class="border-left-dotted" style="text-align:right">    
        <?php if(isset($Record[0][1]['vOut'])){ 
            $r=$Record[0][1]; ?>
                <a href='<?= PageIndex()?>Records/All/<?= $discipline['Code'] ?>'><?= $r['vOut'] ?></a>
                <?php if($r['vCountry']){ ?>
                    <?= ImageCountry($r['vCountry'], 20)?>
                <?php } ?>
        <?php } ?>
        </td>  
    </tr>
<?php } ?>
    </table>
    
    
<?= mlb('*.WorldRecord') ?>
<?= mlb('*.NationalRecord') ?>

