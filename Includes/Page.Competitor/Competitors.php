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
    
    DataBaseClass::Query("Select C.Country, Country.Name CountryName,  count(distinct C.ID) count from Competitor C "
            . " join CommandCompetitor CC on CC.Competitor=C.ID "
            . " join Command Com on CC.Command=Com.ID "
            . " left outer join Country on Country.ISO2=C.Country "
            . " join Event E on E.ID=Com.Event "
            . " join Competition Cm on Cm.ID=E.Competition and Cm.WCA not like 't.%'"
            . " where Com.Decline!=1  "
            . " and (C.WCAID<>'' or WID is not null)"
            . "group by C.Country,Country.Name order by 2 ");
    
    $competitors_countries=DataBaseClass::getRows();
    $competitors_countries_all=0;
    foreach($competitors_countries as $competitors_country){
        $competitors_countries_all+=$competitors_country['count'];
    }      
    
    
    DataBaseClass::Query("Select distinct C.*, Country.Name CountryName from Competitor C "
         . " join CommandCompetitor CC on CC.Competitor=C.ID "
         . " join Command Com on CC.Command=Com.ID "
         . " left outer join Country on Country.ISO2=C.Country "
         . " join Event E on E.ID=Com.Event "
         . " join Competition Cm on Cm.ID=E.Competition and Cm.WCA not like 't.%'"
         . " where Com.Decline!=1  "
         . " and (C.WCAID<>'' or WID is not null)"
         .($country_filter?(" and C.Country='".strtoupper($country_filter)."'"):"")  
         . "Order by C.Name "); 
    
    $competitors=DataBaseClass::getRows();
    
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
Select C.WCAID WCAID, C.Name, C.Country, Country.Name CountryName, C.ID, 
sum(case when Com.Place=1 and Emax.Round=E.Round then 1 else 0 end) Gold, 
sum(case when Com.Place=2 and Emax.Round=E.Round then 1 else 0 end) Silver, 
sum(case when Com.Place=3 and Emax.Round=E.Round then 1 else 0 end) Bronze, 
sum(case when Com.Place in (1,2,3) and Emax.Round=E.Round  then 1 else 0 end) Medals, 
count(distinct Cm.ID) Competitions, count(distinct D.ID) Events
 from Competitor C 
join CommandCompetitor CC on CC.Competitor=C.ID 
join Country on Country.ISO2=C.Country
join Command Com on Com.ID=CC.Command and Com.Decline!=1 
join Event E on E.ID=Com.Event 
join DisciplineFormat DF on E.DisciplineFormat=DF.ID 
join Discipline D on D.ID=DF.Discipline 
join Competition Cm on Cm.ID=E.Competition and Cm.WCA not like 't.%' 
left outer join (
select max(E2.Round) Round , E2.Competition,DF2.Discipline
from Event E2 
join DisciplineFormat DF2 on E2.DisciplineFormat=DF2.ID
group by E2.Competition,DF2.Discipline
) Emax on  Emax.Competition=E.Competition and Emax.Discipline=DF.Discipline
where ('$country_filter'='0' or '".strtoupper($country_filter)."'=C.Country)
and (C.WCAID<>'' or WID is not null) 
group by C.WCAID, C.Name, C.Country,C.ID , Country.Name order by $sort C.Name, WCAID, Competitions desc, Events desc, Medals desc, Gold desc, Silver desc, Bronze desc        
");

    $competitors_medals=DataBaseClass::getRows(); ?>
    <h2><?= ml('Competitors.Competitors' )?></h2>
    
<table class="table_info">    
        <tr>
            <td><?= ml('Competitors.CitizenOf' )?></td>
            <td>
                <select onchange="document.location='<?= PageIndex()?>/' + this.value ">
                    <option <?= ($country_filter=='0')?'selected':''?> value="Competitors"><?= ml('Competitors.Select.All' )?>: <?= $competitors_countries_all ?></option>
                    <option disabled>------</option>
                    <?php foreach($competitors_countries as $competitors_country){ ?>
                            <option <?= strtoupper($country_filter)==$competitors_country['Country']?'selected':''?> value="Competitors/<?= $competitors_country['Country']?>">        
                                <?= $competitors_country['CountryName'] ?>: <?= $competitors_country['count'] ?>
                            </option> 
                    <?php } ?>      
                </select>               
            </td>
        </tr>    
        <tr>
            <td><?= ml('Competitors.FindAndGo' )?></td>
            <td>
                <select hidden tabindex="1" ID="SelectCompetitors" style="width: 800px"
                                data-placeholder="<?= ml('Competitors.FindPlaceholder',false )?>" 
                                class="chosen-select" multiple onchange="if(this.value){
                       location.href = '<?= PageIndex()?>Competitor/' + this.value;
                   }">
                    <?= mlb('Competitors.FindPlaceholder' )?>
                   <option value=""></option>
                   <?php 
                   foreach($competitors as $competitor){ ?>
                       <option value="<?= $competitor['ID'] ?>"> <?= $competitor['WCAID'] ?> &#9642; <?= $competitor['Name'] ?> &#9642; <?= $competitor['CountryName'] ?>  </option>    
                   <?php } ?>
                </select>
            </td>
         </tr>   
    </table>

    <table class="table_new" width="80%">
    <?php 
    foreach($competitors_medals as $i=>$competitors_medal){ ?>
            <?php if(ceil($i/10)*10==$i){ ?>
                <thead>
                <?php if($i==0){ 
                        $urlSort=PageIndex()."Competitors/?".($country_filter?"&Country=".$country_filter:""); ?>
                        <tr>
                        <td/>
                        <td><a href="<?= $urlSort ?>Name" class="<?= $sort_name!='Name'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Competitor') ?></a></td>
                        <td><?= ml('Competitors.Table.WCAID') ?> <i class="fas fa-external-link-alt"></i></td>
                        <td><?= ml('Competitors.Table.Country') ?></td>
                        <td class="table_new_center"><a href="<?= $urlSort ?>&Sort=Competitions" class="<?= $sort_name!='Competitions'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Competitions') ?></a></td>
                        <td class="table_new_center"><a href="<?= $urlSort ?>&Sort=Events" class="<?= $sort_name!='Events'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Events') ?></a></td>
                        <td class="table_new_center"><a href="<?= $urlSort ?>&Sort=Medals" class="<?= $sort_name!='Medals'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Medals') ?></a></td>
                        <td class="table_new_center"><a href="<?= $urlSort ?>&Sort=Gold" class="<?= $sort_name!='Gold'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Gold') ?></a></td>
                        <td class="table_new_center"><a href="<?= $urlSort ?>&Sort=Silver" class="<?= $sort_name!='Silver'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Silver') ?></a></td> 
                        <td class="table_new_center"><a href="<?= $urlSort ?>&Sort=Bronze" class="<?= $sort_name!='Bronze'?'local_link':'select_link'?>"><?= ml('Competitors.Table.Bronze') ?></a></td>
                    </tr> 
                    </thead>
                    <tbody>
                <?php }else{ ?>
                    </tbody>
                    <thead>
                    <tr>
                       <td/>
                       <td><?= ml('Competitors.Table.Competitor') ?></td>
                       <td><?= ml('Competitors.Table.WCAID') ?></td>
                       <td><?= ml('Competitors.Table.Country') ?></td>
                       <td class="table_new_center"><?= ml('Competitors.Table.Competitions') ?></td>
                       <td class="table_new_center"><?= ml('Competitors.Table.Events') ?></td>
                       <td class="table_new_center"><?= ml('Competitors.Table.Medals') ?></td>
                       <td class="table_new_center"><?= ml('Competitors.Table.Gold') ?></td>
                       <td class="table_new_center"><?= ml('Competitors.Table.Silver') ?></td> 
                       <td class="table_new_center"><?= ml('Competitors.Table.Bronze') ?></td>
                   </tr> 
                   </thead>
                   <tbody>
                <?php } ?>
                </thead>   
            <?php } ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td>
                    <a href="<?= PageIndex() ?>Competitor/<?= $competitors_medal['WCAID']?$competitors_medal['WCAID']:$competitors_medal['ID'] ?>">            
                        <?= trim(explode("(",$competitors_medal['Name'])[0]) ?> 
                    </a>
                </td>
                <td>
                    <?php if($competitors_medal['WCAID']){ ?>
                        <a target="_blank" href="https://www.worldcubeassociation.org/persons/<?= $competitors_medal['WCAID']; ?>"><?= $competitors_medal['WCAID']; ?></a>
                    <?php } ?>
                </td>
                <td>
                    <?= ImageCountry($competitors_medal['Country'], 15)?>
                    <?= $competitors_medal['CountryName'] ?>
                </td>
                <td class="table_new_center"><?= $competitors_medal['Competitions']; ?></td>
                <td class="table_new_center"><?= $competitors_medal['Events']; ?></td>
                <td class="table_new_center table_new_bold"><?= $competitors_medal['Medals']?$competitors_medal['Medals']:"" ?></td>
                <td class="table_new_center"><?= $competitors_medal['Gold']?$competitors_medal['Gold']:"" ?></td>
                <td class="table_new_center"><?= $competitors_medal['Silver']?$competitors_medal['Silver']:"" ?></td>
                <td class="table_new_center"><?= $competitors_medal['Bronze']?$competitors_medal['Bronze']:"" ?></td>
            </tr>    
    <?php } ?>
        </tbody>
    </table>

<script>
$("#SelectCompetitors").show();
</script>
<script src="<?= PageLocal()?>jQuery/chosen_v1/docsupport/init.js" type="text/javascript" charset="utf-8"></script>

<?= mlb('*.Reload')?>