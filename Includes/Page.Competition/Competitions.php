<?php includePage('News.Announce'); ?>

<?php     
    $Competitor=getCompetitor(); 
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
$year_filter='0';

$request=request();

if(isset($request[1])){
    if($request[1]=='my'){
        $My=1;
    }else{
        if(is_numeric($request[1])){
            $year_filter=$request[1];
        }else{
            $country_filter_tmp=DataBaseClass::Escape($request[1]);
            $country_filter='0';
            DataBaseClass::Query('select * from Country');
            foreach(DataBaseClass::getRows() as $row){
                if($row['ISO2']== strtoupper($country_filter_tmp)){
                    $country_filter=$country_filter_tmp;
                }
            }
        }
    }
}  
if($My==1 and  !getCompetitor()){
    $My=0;
}
    DataBaseClass::Query("Select Cn.Country,Country.Name CountryName, count(*) count from Competition Cn "
            . " left outer join Country on Country.ISO2=Cn.Country "
            .(CheckAccess('Competitions.Hidden')?"": " where Cn.Status=1 and Cn.WCA not like 't.%'")
            . " group by Cn.Country,Country.Name order by 2 ");
    
    $competitions_countries=DataBaseClass::getRows();
    $competitions_countries_all=0;
    foreach($competitions_countries as $competitions_country){
        $competitions_countries_all+=$competitions_country['count'];
    }      
    
    
    DataBaseClass::Query("
    select t.*,
    case when t.WCA like 't.%' then 1 else 0 end Secret,
    case 
    when current_date < StartDate then 0
    when current_date >= StartDate and current_date <= EndDate then 1
    when current_date > EndDate then 2
    end UpcomingStatus

        from(    

    select Cn.*, coalesce(Country.Name,'') CountryName,
    GROUP_CONCAT(DISTINCT Concat(D.Name,';',D.Code,';',D.CodeScript,';',D.Status) order by D.Code SEPARATOR '#') events,
    count(distinct C.ID) countCompetitors, count(distinct E.DisciplineFormat) countDisiplines,
    case when max(coalesce(Com.Place,0))>0 then 1 else 0 end ResultExists,
    case when sum(case when A.ID is null and Com.Decline!=1 then 1 else 0 end)>0 then 1 else 0 end nonResultExist
    from `Competition` Cn
    left outer join `Event` E on E.Competition=Cn.ID
    left outer join `DisciplineFormat` DF on DF.ID=E.DisciplineFormat and E.Round=1
    left outer join `Discipline` D on D.ID=DF.Discipline

    left outer join `Command` Com on Com.Event=E.ID  and Com.Decline!=1
    left outer join `Attempt` A on A.Command=Com.ID and A.Attempt=1
    left outer join `CommandCompetitor` CC on CC.Command=Com.ID 
    left outer join `Competitor` C on C.ID=CC.Competitor
    left outer join Country on Country.ISO2=Cn.Country
    where ".(CheckAccess('Competitions.Hidden')?"1=1": "Cn.Status=1") ." and ".(CheckAccess('Competitions.Secret')?"1=1": "Cn.WCA not like 't.%'") ."
    and ('$country_filter'='0' or '$country_filter'=Cn.Country) 
    and( not $year_filter or $year_filter=year(StartDate))    
    and ($My=0 or Cn.id in (".implode(",",$competitor_competitions)."))   
    group by Cn.ID,Country.Name
    
    )t
    order by UpcomingStatus,t.StartDate desc, t.EndDate desc"); 
    $results= DataBaseClass::getRows();
    $events=[];
    foreach($results as $result){
        foreach(explode('#',$result['events']) as $event){
            if($event){
                $events[]=$event;
            }
        }
    }
    $events= array_unique($events);
    sort($events);
    
    DataBaseClass::FromTable("Competition");
    DataBaseClass::Where_current("Status=1");
    DataBaseClass::Where_current("WCA not like 't.%'");
    DataBaseClass::OrderClear("Competition","StartDate");
    $years=[];
    foreach(DataBaseClass::QueryGenerate() as $row){
        $yearStart=date("Y",strtotime($row['Competition_StartDate']));
        if(!isset($years[$yearStart])){
            $years[$yearStart]=0;
        }
        $years[$yearStart]++;
    }?>

    <h1> 
        <?php if($My){ ?>
            <?= ml('Competitions.My') ?>
        <?php }else{ ?>
            <?= ml('Competitions.Title') ?>
        <?php } ?>
    </h1>

<table width="100%"><tr><td>
<table class="table_info">
<?php if(CheckAccess('Competition.Add')){?>
    <tr>
        <td><i class="fas fa-plus-square"></i></td>
        <td><a href='<?= PageIndex()?>Competition/Add'>Add Competition</a></td>
    </tr>    
<?php } ?>
    <tr>
        <td><?= ml('Competitions.Filter') ?></td>
        <td>
            <select onchange="document.location='<?= PageIndex()?>Competitions/' + this.value ">
                <option <?= ($country_filter=='0' and $My==0)?'selected':''?> value=""><?= ml('Competitions.All') ?> (<?= $competitions_countries_all ?>)</option>
                <?php if(getCompetitor()){ ?><option <?= $My=='1'?'selected':''?> value="My"><?= ml('Competitions.My') ?><?php if(sizeof($competitor_competitions)-1>0){ ?> (<?= sizeof($competitor_competitions)-1 ?>) <?php } ?></option><?php } ?>
                <option disabled>------</option>
                <?php foreach($years as $year=>$count){ ?>
                <option value="<?= $year ?>" <?= $year_filter==$year?'selected':'' ?> ><?= $year ?> (<?= $count ?>)</option>    
                <?php } ?>
                <option disabled>------</option>
                <?php foreach($competitions_countries as $competitions_country)if($competitions_country['Country']){ ?>
                        <option <?= $country_filter==strtolower($competitions_country['Country'])?'selected':''?> value="<?= $competitions_country['Country']?>">        
                            <?= $competitions_country['CountryName'] ?> (<?= $competitions_country['count'] ?>)
                        </option> 
                <?php } ?>      
            </select>
        </td>
    </tr>
</table>    
</td><td>
    <table class="table_info">
        <tr>
            <td><?= ml('Competitions.Events');?></td>
            <td>
                <i title="Show all events" class=" competitions_events_panel_all fas fa-star"></i>
                <span class="competitions_events_panel">
                <?php foreach($events as $event){ 
                    $event_data=explode(';',$event);
                    if( isset($event_data[3]) and $event_data[3]=='Active'){ ?>
                        <?= ImageEvent($event_data[2],1,$event_data[0]); ?>
                    <?php } ?>    
                <?php } ?>
                </span>
                <i title="Clear filter" class=" competitions_events_panel_none fas fa-ban"></i>
            </td>
        </tr>
    </table>
</td></tr></table>            
    <table class="table_new competitions" width="80%">
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
        <?php if($r['UpcomingStatus']!=$comp_status and $comp_status!=-2){ ?>
            
        <?php } 
        $comp_status = $r['UpcomingStatus']; 
        $events_row=[];
        foreach(explode('#',$r['events']) as $e=>$event){
            $event_data=explode(';',$event); 
            if(isset($event_data[2])){
                $events_row[]="event_".$event_data[2]; 
            }
        }?>
    <tr valign="bottom" class="competition <?= implode(" ",$events_row); ?>">
        <td>
            <?php if($r['Status']==0){ ?>
                <i class="fas fa-eye-slash"></i>
            <?php }else{ ?>
                <?php if($comp_status==0){ ?>
                    <span style="color:var(--light_gray)"><i class="fas fa-hourglass-start"></i></span>
                <?php }?>
                <?php if($comp_status==1){ ?>
                    <span style="color:var(--green)"><i class="fas fa-hourglass-half"></i></span>
                <?php }?>
                <?php if($comp_status==2){ ?>
                    <span style="color:var(--black)"><i class="fas fa-hourglass-end"></i></span>
                <?php }?>
            <?php } ?>
        </td>            
        <td>            
            <b><?= date_range($r['StartDate'],$r['EndDate']); ?></b>    
        </td>   
        <td width="1px">
            <?= ImageCountry($r['Country']) ?>
        </td>
        <td>
            <a href="<?= LinkCompetition($r['WCA']) ?>">
                <span class="<?= ($r['Unofficial'] and $comp_status!=-1)?'unofficial':'' ?>"><?= $r['Name'] ?></span>
            </a>
        </td>
        <td>
            <b><?= $r['CountryName'] ?></b>, <?= $r['City'] ?>
        </td>
        <td>
            <?php foreach(explode('#',$r['events']) as $e=>$event){
                if($e<10){
                    $event_data=explode(';',$event); 
                    if(isset($event_data[2])){?>
                        <?= ImageEvent($event_data[2],1.3,$event_data[0]);?>
                    <?php } 
                }
            } ?>
            <?php if(empty($r['events'])){?>
                <i class="fas fa-ban"></i>
            <?php } ?>
        </td>
    </tr>
<?php } ?>
    <?php if (!sizeof($results)){?>
        <h3><?= ml('Competitions.NotFound') ?></h3>
    <?php } ?>
    
    </table>


<?= mlb('Competitions.NotFound'); ?>
<script>    
    $('.competitions_events_panel i').on("click",function(){
        if($(this).hasClass('competitions_events_select')){
            $(this).removeClass('competitions_events_select');
        }else{
            $(this).addClass('competitions_events_select');
        }
        reload_competitions();
    });
    
    $('.competitions_events_panel_none').on("click",function(){
        $('.competitions_events_panel i').removeClass('competitions_events_select');
        reload_competitions();
    });
    
    $('.competitions_events_panel_all').on("click",function(){
        $('.competitions_events_panel i').addClass('competitions_events_select');
        reload_competitions();
    });
    
    function reload_competitions(){
        var events = [];
        $('.competitions_events_panel i.competitions_events_select').each(function( ) {
                $(this).attr('class').split(' ').forEach(
                    (element) => {
                        var tmp=element.replace('ee-','');
                        if(tmp!==element){
                            events.push('event_' + tmp);
                        }
                });
        
            });
            
            
        $('.competition').hide();    
        $('.competition').removeClass('competition_show');
        var i=1;
        $('.competition').each(function() {
            var show=false;
            events.forEach(
                (element) => {
                if($(this).hasClass(element)){
                    show=true;
                }
            });
            if(show){
                $(this).show();
                if(i%2!==0){
                    $(this).addClass('odd');
                    $(this).removeClass('even');
                }else{
                    $(this).addClass('even');
                    $(this).removeClass('odd');
                }
                i=i+1;  
            }
        });
    }
    $('.competitions_events_panel i').addClass('competitions_events_select');
</script>    