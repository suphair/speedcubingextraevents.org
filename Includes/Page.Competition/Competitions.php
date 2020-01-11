<?php includePage('Navigator'); ?>
<?php includePage('News.Announce'); ?>

<?php     
    $Competitor=GetCompetitorData(); 
    $competitor_competitions=array(-1);
    if($Competitor){
        DataBaseClass::Query(
                "Select distinct(E.Competition) Competition from Competitor C"
                ." join CommandCompetitor CC on CC.Competitor=C.ID"
                ." join Command Com on Com.ID=CC.Command"
                ." join Event E on E.ID=Com.Event "
                ." where C.WID=".$Competitor->id.""
                );

        foreach(DataBaseClass::GetRows() as $row){
            $competitor_competitions[]=$row['Competition'];
        }
    }
    
    
$My=0;
$country_filter='0';
$request=request();

if(isset($request[1])){
    if($request[1]=='my'){
        $My=1;
    }else{
        $country_filter= DataBaseClass::Escape($request[1]);
    }
}  

if($My==1 and  !GetCompetitorData()){
    $My=0;
}
    DataBaseClass::Query("Select Cn.Country, count(*) count from Competition Cn "
            .(CheckAccess('Competitions.Hidden')?"": " where Cn.Status=1 and Cn.WCA not like 't.%'")
            . "group by Cn.Country order by 2 desc ");
    
    $competitions_countries=DataBaseClass::getRows();
    $competitions_countries_all=0;
    foreach($competitions_countries as $competitions_country){
        $competitions_countries_all+=$competitions_country['count'];
    }      
    
    
    DataBaseClass::Query("
    select t.*,
    case 
    when t.WCA like 't.%' then 2
    when Status=0 then -1
    when countDisiplines=0 then -1
    when (ResultExists and nonResultExist) or (current_date>=StartDate and nonResultExist) or (current_date>=StartDate and current_date<=EndDate) then 0
    when ResultExists and !nonResultExist then 1
    when ( !countCompetitors or (!ResultExists and nonResultExist)) and current_date<StartDate then 0.5
    end UpcomingStatus

        from(    

    select Cn.*, count(distinct C.ID) countCompetitors, count(distinct E.DisciplineFormat) countDisiplines,
    case when max(coalesce(Com.Place,0))>0 then 1 else 0 end ResultExists,
    case when sum(case when A.ID is null and Com.Decline!=1 then 1 else 0 end)>0 then 1 else 0 end nonResultExist
    from `Competition` Cn
    left outer join `Event` E on E.Competition=Cn.ID
    left outer join `Command` Com on Com.Event=E.ID  and Com.Decline!=1
    left outer join `Attempt` A on A.Command=Com.ID and A.Attempt=1
    left outer join `CommandCompetitor` CC on CC.Command=Com.ID 
    left outer join `Competitor` C on C.ID=CC.Competitor
    where ".(CheckAccess('Competitions.Hidden')?"1=1": "Cn.Status=1") ." and ".(CheckAccess('Competitions.Secret')?"1=1": "Cn.WCA not like 't.%'") ."
    and ('$country_filter'='0' or '$country_filter'=Cn.Country) 
    and ($My=0 or Cn.id in (".implode(",",$competitor_competitions)."))   
    group by Cn.ID 
    
    )t
    order by UpcomingStatus, t.StartDate desc"); 
    $results= DataBaseClass::getRows();
    ?>
    <h2> 
        <img src='<?= PageIndex()?>Image/Icons/competitions.png' width='20px'>
        <?php if($My){ ?>
            <?= ml('Competitions.My') ?>
            
        <?php }elseif($country_filter=='0'){ ?>
            <?= ml('Competitions.All') ?>
        <?php }else{ ?>
            <?= ImageCountry($country_filter, 50)?>
            <?= ml('Competitions.Title.Country',CountryName($country_filter)); ?>
        <?php } ?>
            
        <select onchange="document.location='<?= PageIndex()?>Competitions/' + this.value ">
            <option <?= ($country_filter=='0' and $My==0)?'selected':''?> value=""><?= ml('Competitions.Select.All') ?>: <?= $competitions_countries_all ?></option>
            <?php if(GetCompetitorData()){ ?><option <?= $My=='1'?'selected':''?> value="My"><?= ml('Competitions.Select.My') ?><?php if(sizeof($competitor_competitions)-1>0){ ?>: <?= sizeof($competitor_competitions)-1 ?> <?php } ?></option><?php } ?>
            <option disabled>------</option>

            <?php foreach($competitions_countries as $competitions_country)if($competitions_country['Country']){ ?>
                    <option <?= $country_filter==strtolower($competitions_country['Country'])?'selected':''?> value="<?= $competitions_country['Country']?>">        
                        <?= CountryName($competitions_country['Country']) ?> [<?= $competitions_country['Country'] ?>]: <?= $competitions_country['count'] ?>
                    </option> 
            <?php } ?>      

        </select>
    </h2>
    <table class="Competitions">
    <?php 
    $comp_statuses=[];
    foreach($results as $i=>$r){
        if(!isset($comp_statuses[$r['UpcomingStatus']])){
            $comp_statuses[$r['UpcomingStatus']]=0;
        }
        $comp_statuses[$r['UpcomingStatus']]++;
    }
    
    $comp_status='-2';
    foreach( $results as $i=>$r){ ?>
        <?php 
            if($r['UpcomingStatus']!=$comp_status){
            $comp_status = $r['UpcomingStatus']; ?>
            <?php if($i){ ?>
                <tr class="no_border"><td></td></tr>
            <?php } ?>    
            <tr class="no_border tr_title">
                <td colspan="3">
                    <?php if($comp_status==1){ ?>
                        <?= ml('Competitons.Past') ?>
                    <?php }elseif($comp_status==0){ ?>    
                        <?= ml('Competitons.Progress') ?>
                    <?php }elseif($comp_status==0.5){ ?>
                        <?= ml('Competitons.Upcoming') ?>
                    <?php }elseif($comp_status==-1){ ?>
                        <?= ml('Competitons.Hidden') ?>
                    <?php }else{ ?>
                        <?= ml('Competitons.Secret') ?>
                    <?php } ?>
                    <span class="badge"><?= $comp_statuses[$comp_status]?></span>
                </td>
                <td align="center">
                    <img height="15px"src="<?= PageIndex()?>Image/Icons/persons.png">
                </td>
                <td>
                    <?= ml('Competitions.Table.Events')?>
                </td>
            </tr>
        <?php 
        } ?>
    <tr valign="bottom">
        <td>
            <b><?= date_range($r['StartDate'],$r['EndDate']); ?></b>
        </td>   
        <td>
            <?php if($comp_status==0.5){ ?>
                <?php if($r['Registration']){ ?>
                    <?= svg_green(10,ml('Competition.Registration.True',false)) ?>
                <?php }else{ ?>
                    <?= svg_red(10,ml('Competition.Registration.False',false)) ?>
                <?php } ?>
            <?php } ?>    
            <?php if($comp_status==0){ ?>
                <?php if($r['Onsite']){ ?>
                    <?= svg_green(10,ml('Competition.Onsite.True',false)) ?>
                <?php }else{ ?>
                    <?= svg_red(10,ml('Competition.Onsite.False',false)) ?>
                <?php } ?>
            <?php } ?>    
            <a href="<?= LinkCompetition($r['WCA']) ?>">
                <span class="<?= ($r['Unofficial'] and $comp_status!=-1)?'unofficial':'' ?>"><?= $r['Name'] ?></span>
            </a>
        </td>
        <td>
            <?= ImageCountry($r['Country'],20) ?>
            <b><?= CountryName($r['Country']) ?></b>, <?= CountryName($r['City']) ?>
        </td>
        <td class="attempt">
            <?= $r['countCompetitors']?$r['countCompetitors']:'' ?>
        </td>
        <td style="padding:0px">
            <?php 
                    DataBaseClass::Query(
                            "Select E.ScrambleSalt, E.Round, E.ID Event, D.Name,D.Code,D.CodeScript  "
                            . " from Discipline D"
                            . " join DisciplineFormat DF on DF.Discipline=D.ID "
                            . " join Event E on E.DisciplineFormat=DF.ID "
                            . " left outer join Command Com on Com.Event=E.ID and Com.Decline!=1 "
                            . " left outer join Attempt A on A.Command=Com.ID and A.Attempt=1"
                            . ($My?(" join CommandCompetitor CC on CC.Command=Com.ID "
                            . " join Competitor C on C.ID=CC.Competitor and C.WID=".$Competitor->id):"")
                            . " where E.Competition=".$r['ID']
                            . " group by E.ID, D.Code, D.Name "
                            ." order by D.Name");
            
            
                  $j=0; 
                  
                  $diciplines=DataBaseClass::getRows();
                  
                  foreach($diciplines as $discipline){ 
                      if($discipline['Round']==1){ ?> 
                        <a href="<?= LinkEvent($discipline['Event']) ?>"><?= ImageEvent($discipline['CodeScript'],25,$discipline['Name']);?></a>
                        <?php $j++;
                        if($j==6){
                            $j=0;
                        echo "<br>";
                         }
                      }
                  } ?>
        </td>
    </tr>
<?php } ?>
    <?php if (!sizeof($results)){?>
        <h3><?= ml('Competitions.NotFound') ?></h3>
    <?php } ?>
    
    </table>


<?= mlb('Competitions.NotFound'); ?>

