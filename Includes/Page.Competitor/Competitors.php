<?php includePage('Navigator'); ?>
    <?php
    $request= getRequest();
    if(isset($request[1])){
        $country_filter=DataBaseClass::Escape($request[1]);
    }else{
        $country_filter='0';    
    } 
    
    DataBaseClass::Query("Select * from Country where ISO2='". strtoupper($country_filter)."'");
    if(sizeof(DataBaseClass::getRows())!==1){
        $country_filter='0'; 
    } 
    
    DataBaseClass::Query("Select C.Country, count(distinct C.ID) count from Competitor C "
            . " join CommandCompetitor CC on CC.Competitor=C.ID "
            . " join Command Com on CC.Command=Com.ID "
            . " join Event E on E.ID=Com.Event "
            . " join Competition Cm on Cm.ID=E.Competition and Cm.WCA not like 't.%'"
            . " where Com.Decline!=1 and E.Round = (select max(E2.Round) from Event E2 where E2.Competition=E.Competition) "
            . " and (C.WCAID<>'' or WID is not null)"
            . "group by C.Country order by 2 desc ");
    
    $competitors_countries=DataBaseClass::getRows();
    $competitors_countries_all=0;
    foreach($competitors_countries as $competitors_country){
        $competitors_countries_all+=$competitors_country['count'];
    }      
    DataBaseClass::FromTable("Competitor");
    DataBaseClass::Join_current("CommandCompetitor");
    DataBaseClass::Join_current("Command");
    DataBaseClass::Join_current("Event");
    DataBaseClass::Join_current("Competition");
    DataBaseClass::Where_current("WCA not like 't.%'");
    DataBaseClass::Where("Command","Decline!=1");
    DataBaseClass::OrderClear("Competitor","Name");
    if($country_filter){
        DataBaseClass::Where("Competitor","Country='".strtoupper($country_filter)."'");
    }
    DataBaseClass::Select("Distinct Cm.*");
    
    $competitors=DataBaseClass::QueryGenerate();
    
    $sort='';
    $sort_name='Name';
    if(isset($_GET['Sort']) and in_array($_GET['Sort'],
        array('Name','WCAID','Country','Competitions','Events','Medals','Gold','Silver','Bronze'))){
        if(in_array($_GET['Sort'],array('Name','WCAID'))){
            $sort=$_GET['Sort'].',';
        }else{
            $sort=$_GET['Sort'].' desc,';
        }
        $sort_name=$_GET['Sort'];
    }
    
    DataBaseClass::Query("
        Select C.WCAID WCAID, C.Name, C.Country,C.ID,
        sum(case when Com.Place=1 then 1 else 0 end) Gold,
        sum(case when Com.Place=2 then 1 else 0 end) Silver,
        sum(case when Com.Place=3 then 1 else 0 end) Bronze,
        sum(case when Com.Place in (1,2,3) then 1 else 0 end) Medals,
        count(distinct Cm.ID) Competitions,
        count(distinct D.ID) Events
        from Competitor C join CommandCompetitor CC on CC.Competitor=C.ID 
        join Command Com on Com.ID=CC.Command and Com.Decline!=1
        join Event E on E.ID=Com.Event
        join DisciplineFormat DF on E.DisciplineFormat=DF.ID
        join Discipline D on D.ID=DF.Discipline
        join Competition Cm on Cm.ID=E.Competition and Cm.WCA not like 't.%'
        where ('$country_filter'='0' or '".strtoupper($country_filter)."'=C.Country)
        and E.Round = (select max(E2.Round) from Event E2 where E2.Competition=E.Competition)
        and (C.WCAID<>'' or WID is not null)
        group by C.WCAID, C.Name, C.Country,C.ID 
        order by 
        $sort
        C.Name,
        WCAID,
        Competitions desc,
        Events desc,
        Medals desc,
        Gold desc,
        Silver desc,
        Bronze desc
            ");

    $competitors_medals=DataBaseClass::getRows(); ?>
    <h2><img src='<?= PageIndex()?>Image/Icons/persons.png' width='20px'>
        <?php if($country_filter!='0'){ ?>
            <?= ImageCountry($country_filter, 50)?> <?= ml('Competitors.Title.Country',CountryName($country_filter))?>
        <?php }else{ ?>
            <?= ml('Competitors.Title.All' )?>
        <?php } ?>
        <select onchange="document.location='<?= PageIndex()?>/' + this.value ">
            <option <?= ($country_filter=='0')?'selected':''?> value="Competitors"><?= ml('Competitors.Select.All' )?>: <?= $competitors_countries_all ?></option>
            <option disabled>------</option>
            <?php foreach($competitors_countries as $competitors_country){ ?>
                    <option <?= strtoupper($country_filter)==$competitors_country['Country']?'selected':''?> value="Competitors/<?= $competitors_country['Country']?>">        
                        <?= CountryName($competitors_country['Country']) ?> [<?= $competitors_country['Country']?>]: <?= $competitors_country['count'] ?>
                    </option> 
            <?php } ?>      
        </select>
    </h2>
    
    <select hidden tabindex="1" ID="SelectCompetitors" style="width: 800px"
                         data-placeholder="Find competitor by Name or WCAID" 
                         class="chosen-select" multiple onchange="if(this.value){
                location.href = '<?= PageIndex()?>Competitor/' + this.value;
            }">
            <option value=""></option>
            <?php 
            foreach($competitors as $competitor){ ?>
                <option value="<?= $competitor['ID'] ?>"> <?= $competitor['WCAID'] ?> &#9642; <?= $competitor['Name'] ?> &#9642; <?= CountryName($competitor['Country']) ?>  </option>    
            <?php } ?>
    </select>
    
<?php if(CheckAccess('Competitors.Reload')){     
    DataBaseClass::Query("Select distinct C.WID from Competitor C  where  C.WID is not null and WCAID=''");         
    $WithoutWCAID=DataBaseClass::getRows();
    
    DataBaseClass::Query("Select * from Competitor C where  C.WID is null and WCAID!=''");
    $WithoutWID=DataBaseClass::getRows(); ?>

    <?php if(sizeof($WithoutWCAID) or sizeof($WithoutWID)){ ?>
        <div class="block_comment">
                <span class="error"><?= sizeof($WithoutWCAID) ?></span>\wca_id
                &#9642;
               <span class="error"><?= sizeof($WithoutWID) ?></span>\user_id
               ▪
               <?= GetValue('Competitors.Reload'); ?>
        </div>       
    <?php } ?>
               
<?php } ?>

    <table class="Competitors">
    <?php 
    foreach($competitors_medals as $i=>$competitors_medal){ ?>
            <?php if(ceil($i/10)*10==$i){ ?>
                <?php if($i==0){ 
                        $urlSort=PageIndex()."Competitors/?".($country_filter?"&Country=".$country_filter:""); ?>
                        <tr class="tr_title">
                        <td/>
                        <td><a href="<?= $urlSort ?>Name" class="<?= $sort_name!='Name'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Competitor') ?></a></td>
                        <td><a href="<?= $urlSort ?>WCAID" class="<?= $sort_name!='WCAID'?'local_link':'select_link'?>"><?= ml('Competitors.Table.WCAID') ?></a></td>
                        <?php if($country_filter=='0'){ ?><td><a href="<?= $urlSort ?>Country" class="<?= $sort_name!='Country'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Country') ?></a></td><?php } ?>    
                        <td align="center"><a href="<?= $urlSort ?>&Sort=Competitions" class="<?= $sort_name!='Competitions'?'local_link':'select_link'?>"><img height="15px"src="<?= PageIndex()?>Image/Icons/competitions.png"></a></td>
                        <td class="attempt"><a href="<?= $urlSort ?>&Sort=Events" class="<?= $sort_name!='Events'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Events') ?></a></td>
                        <td class="attempt"><a href="<?= $urlSort ?>&Sort=Medals" class="<?= $sort_name!='Medals'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Medals') ?></a></td>
                        <td class="attempt"><a href="<?= $urlSort ?>&Sort=Gold" class="<?= $sort_name!='Gold'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Gold') ?></a></td>
                        <td class="attempt"><a href="<?= $urlSort ?>&Sort=Silver" class="<?= $sort_name!='Silver'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Silver') ?></a></td> 
                        <td class="attempt"><a href="<?= $urlSort ?>&Sort=Bronze" class="<?= $sort_name!='Bronze'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Bronze') ?></a></td>
                    </tr> 
                <?php }else{ ?>
                    <tr class="tr_title">
                       <td/>
                       <td><?= ml('Competitors.Table.Competitor') ?></td>
                       <td><?= ml('Competitors.Table.WCAID') ?></td>
                       <?php if($country_filter=='0'){ ?><td><?= ml('Competitors.Table.Country') ?></td><?php } ?>    
                       <td align="center"><img height="15px" src="<?= PageIndex()?>Image/Icons/competitions.png"></td>
                       <td class="attempt"><?= ml('Competitors.Table.Events') ?></td>
                       <td class="attempt"><?= ml('Competitors.Table.Medals') ?></td>
                       <td class="attempt"><?= ml('Competitors.Table.Gold') ?></td>
                       <td class="attempt"><?= ml('Competitors.Table.Silver') ?></td> 
                       <td class="attempt"><?= ml('Competitors.Table.Bronze') ?></td>
                   </tr>   
                <?php } ?>
            <?php } ?>
        
    
            <tr class="<?= ceil(($i+1)/10)*10==($i+1)?'no_border':'' ?>">
                <td><?= $i+1 ?></td>
                <td class="border-left-solid">
                    <a href="<?= PageIndex() ?>Competitor/<?= $competitors_medal['WCAID']?$competitors_medal['WCAID']:$competitors_medal['ID'] ?>">            
                        <?= trim(explode("(",$competitors_medal['Name'])[0]) ?> 
                    </a>
                </td>
                <td>
                   <?= $competitors_medal['WCAID']; ?> 
                </td>
                <?php if($country_filter=='0'){ ?>
                    <td>
                        <?= ImageCountry($competitors_medal['Country'], 15)?>
                        <?= CountryName($competitors_medal['Country']) ?>
                    </td>
                <?php } ?>
                <td class="attempt border-left-solid"><?= $competitors_medal['Competitions']; ?></td>
                <td class="attempt"><?= $competitors_medal['Events']; ?></td>
                <td class="attempt border-left-solid" ><b><?= $competitors_medal['Medals']?$competitors_medal['Medals']:"" ?></b></td>
                <td class="attempt"><?= $competitors_medal['Gold']?$competitors_medal['Gold']:"" ?></td>
                <td class="attempt"><?= $competitors_medal['Silver']?$competitors_medal['Silver']:"" ?></td>
                <td class="attempt"><?= $competitors_medal['Bronze']?$competitors_medal['Bronze']:"" ?></td>
            </tr>    
    <?php } ?>
    </table>

<script>
$("#SelectCompetitors").show();
</script>

<?= mlb('*.Reload')?>