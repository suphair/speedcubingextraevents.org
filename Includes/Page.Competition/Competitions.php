<?php 
includePage('News.Announce');
$competitor = getCompetitor(); 
$filterMine = false;
$filterCountry = false;
$filterYear = false;
    
$request=request();

if(isset($request[1])){
    $filter = $request[1];
    if($filter == 'mine'){
        $filterMine = $competitor!=false;
    }else{
        if(is_numeric($filter)){
            $filterYear = $filter;
        }else{
            if(DataBaseClass::exists("select ISO2 from Country where ISO2='$filter'")){
                $filterCountry = $filter;
            }
        }
    }
}
$filter = ($filterMine?'Mine':'') . $filterYear . $filterCountry;

if($competitor){
    $competitorCompetitions = DataBaseClass::getColumn(
        "Select distinct(E.Competition) from Competitor C"
        ." join CommandCompetitor CC on CC.Competitor=C.ID"
        ." join Command Com on Com.ID=CC.Command"
        ." join Event E on E.ID=Com.Event "
        ." where C.WID=".$competitor->id.""
    );
}
    
$whereCountry = $filterCountry ? "and '$filterCountry'=Cn.Country":"";
$whereYear = $filterYear ? "and $filterYear=year(StartDate)":"";
$whereMine = $filterMine ? "and Cn.id in (".implode(",",array_merge($competitorCompetitions,[-1])).")":"";
$whereHidden = !CheckAccess('Competitions.Hidden')?" and Cn.Status=1":""; 
$whereSecret = !CheckAccess('Competitions.Secret')?" and Cn.WCA not like 't.%'":"";

$countryCountCompetitions = DataBaseClass::getRowsAssoc(
    "Select lower(Cn.Country) Country, Country.Name CountryName, count(*) count from Competition Cn "
    . " join Country on Country.ISO2=Cn.Country "
    . " where 1=1 "
    . " $whereHidden "
    . " $whereSecret "
    . " group by Cn.Country,Country.Name "
    . " order by Country.Name"
 );
$countCompetitions=array_sum(array_column($countryCountCompetitions,'count'));;
    
$competitions = DataBaseClass::getRowsAssoc("
    select Cn.*,
    coalesce(Country.Name,'') CountryName,
    case
        when Cn.Status=0 then -1
        when current_date < Cn.StartDate then 0
        when current_date >= Cn.StartDate and current_date <= Cn.EndDate then 1
        when current_date > Cn.EndDate then 2
    end UpcomingStatus
    from `Competition` Cn
    left outer join Country on Country.ISO2=Cn.Country
    where 1=1
    $whereHidden
    $whereSecret
    $whereCountry
    $whereYear
    $whereMine
    order by Cn.StartDate desc, Cn.EndDate desc"
); 
 
$competitionsID=array_column($competitions,'ID');

$eventsCompetition=[];
$eventsPanel=[];

$events = DataBaseClass::getRowsAssoc("
    select D.Name, D.Code, D.CodeScript, D.Status, Cn.ID
        from `Competition` Cn
        join `Event` E on E.Competition=Cn.ID
        join `DisciplineFormat` DF on DF.ID=E.DisciplineFormat and E.Round=1
        join `Discipline` D on D.ID=DF.Discipline
        where Cn.ID in(".implode(',',array_merge($competitionsID,[-1])).")
        order by D.Code"
); 

foreach($events as $event){
    $eventsPanel[$event['Code']]=$event;
    $eventsCompetition[$event['ID']][$event['Code']]=$event;
}

$eventsImage=[];
foreach($eventsPanel as $event){ 
    if($event['Status']=='Active'){
        $eventsImage[]=ImageEvent($event['CodeScript'],1,$event['Name']);
    }    
}

$yearCountCompetitions = DataBaseClass::getColumnAssoc("
    select year(StartDate) year, count(*) count 
    from `Competition` C where C.Status=1 and C.WCA not like 't.%' 
    group by year(StartDate)
    order by 1"
);

$title=$filterMine?ml('Competitions.My'):ml('Competitions.Title');

$accessCompetitionAdd=CheckAccess('Competition.Add');

$iconCompetitionStatus=[];
$iconCompetitionStatus[-1]=<<<OUT
    <span style="color:var(--red)"><i class="fas fa-eye-slash"></i></span>
OUT;
$iconCompetitionStatus[0]=<<<OUT
    <span style="color:var(--light_gray)"><i class="fas fa-hourglass-start"></i></span>
OUT;
$iconCompetitionStatus[1]=<<<OUT
    <span style="color:var(--green)"><i class="fas fa-hourglass-half"></i></span>
OUT;
$iconCompetitionStatus[2]=<<<OUT
    <span style="color:var(--black)"><i class="fas fa-hourglass-end"></i></span>
OUT;
?>
<!--------OUTPUT--------->
<h1><?= $title ?></h1>
<table width="100%">
    <tr>
        <td>
            <table class="table_info">
            <?php if($accessCompetitionAdd){?>
                <tr>
                    <td><i class="fas fa-plus-square"></i></td>
                    <td><a href='<?= PageIndex()?>Competition/Add'>Add Competition</a></td>
                </tr>    
            <?php } ?>
                <tr>
                    <td><?= ml('Competitions.Filter') ?></td>
                    <td>
                        <select ID="filter">
                            <option <?= !$filter?'selected':''?> value="">
                                <?= ml('Competitions.All') ?> (<?= $countCompetitions ?>)
                            </option>
                        <?php if($competitor){ ?>
                            <option value="Mine">
                                <?= ml('Competitions.My') ?> (<?= sizeof($competitorCompetitions) ?>)
                            </option>
                        <?php } ?>
                            <option disabled>------</option>
                        <?php foreach($yearCountCompetitions as $year=>$count){ ?>
                            <option value="<?= $year ?>">
                                <?= $year ?> (<?= $count ?>)
                            </option>    
                        <?php } ?>
                            <option disabled>------</option>
                        <?php foreach($countryCountCompetitions as $competitions_country){ ?>
                            <option  value="<?= $competitions_country['Country'] ?>">        
                                <?= $competitions_country['CountryName'] ?> (<?= $competitions_country['count'] ?>)
                            </option> 
                        <?php } ?>      
                        </select>
                    </td>
                    <td>
                        <span class="competitions_events_panel">
                        <?php foreach($eventsImage as $eventImage){ ?>
                            <?= $eventImage ?>
                        <?php } ?>
                        </span>
                        <i title="Clear filter" class=" competitions_events_panel_none fas fa-ban"></i>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>            

<table class="table_new competitions" width="80%">
<?php foreach( $competitions as $competition){
        $classUnnoficial=($competition['Unofficial'] and $competition['UpcomingStatus']!=-1)?'unofficial':''; ?>
    <tr class="competition <?= empty($eventsCompetition[$competition['ID']])?'':implode(" ",array_column($eventsCompetition[$competition['ID']],'CodeScript')) ?>">
        <td class="table_new_center">
            <?= $iconCompetitionStatus[$competition['UpcomingStatus']]; ?>
        </td>            
        <td>            
            <b><?= date_range($competition['StartDate'],$competition['EndDate']); ?></b>    
        </td>   
        <td>
            <?= ImageCountry($competition['Country']) ?>
        </td>
        <td>
            <a href="<?= LinkCompetition($competition['WCA']) ?>">
                <span class="<?= $classUnnoficial ?>"><?= $competition['Name'] ?></span>
            </a>
        </td>
        <td>
            <b><?= $competition['CountryName'] ?></b>, <?= $competition['City'] ?>
        </td>
        <td>
            <?php if(empty($eventsCompetition[$competition['ID']])){?>
                <i class="fas fa-ban"></i>
            <?php }else{ ?>
                <?php $i=0;    
                foreach($eventsCompetition[$competition['ID']] as $event){ 
                    if($i++>9){
                        break;
                    }?>
                    <?= ImageEvent($event['CodeScript'],1.3,$event['Name']);?>
                <?php  } ?>
            <?php } ?>    
        </td>
    </tr>
<?php } ?>
</table>
<h3 ID='competitionsNotFound'><?= ml('Competitions.NotFound') ?></h3>
<!--------SCRIPT--------->
<script>    
    var ce_select='competitions_events_select';
    var ce_panel='competitions_events_panel';
    $('.' + ce_panel+ ' i').on("click",function(){
        if($(this).hasClass(ce_select)){
            $(this).removeClass(ce_select);
        }else{
            $(this).addClass(ce_select);
        }
        reload_competitions();
    });
    
    $('.' + ce_panel+ '_none').on("click",function(){
        $('.' + ce_panel+ ' i').removeClass(ce_select);
        reload_competitions();
    });
    
    function reload_competitions(){
        var events = [];
        $('.' + ce_panel+ ' i.' + ce_select).each(function(){
            $(this).attr('class').split(' ').forEach(
                (element) => {
                    var tmp=element.replace('ee-','');
                    if(tmp!==element){
                        events.push( tmp);
                    }
                }
            );
        });
        
        if(events.length>0){
            $('.competition').hide();    
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
            if(i===1){
                $('#competitionsNotFound').show();
            }else{
                $('#competitionsNotFound').hide();
            }
        }else{
            $('.competition').show();    
            $('.competition').removeClass('odd');
            $('.competition').removeClass('even');
        }
    }
    
    if($('.competition').length>0){
        $('#competitionsNotFound').hide();
    }

    $('#filter option[value="<?= $filter ?>"]').prop('selected', true);

    $('#filter').on("change",function(){
        document.location='<?= PageIndex()?>Competitions/' + $(this).val();
    });
     
</script>    