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
    <h1><?= ml('Competitors.Competitors' )?></h1>
<table width="100%"><tr><td>    
    <table class="table_info">    
        <tr>
            <td><i class="fas fa-filter"></i> <?= ml('Competitors.CitizenOf' )?></td>
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
            <td><i class="fas fa-sort-amount-down"></i> <?= ml('Competitors.Sort' )?></td>
            <td>
                <select onchange="document.location='<?= PageIndex()?>/Competitors/<?= $country_filter?$country_filter:'' ?>?Sort='+ this.value">
                    <option value="Name" <?= $sort_name=="Name"?'Selected':'' ?>><?= ml('Competitors.Table.Competitor') ?></option>
                    <option value="Competitions" <?= $sort_name=="Competitions"?'Selected':'' ?>><?= ml('Competitors.Table.Competitions') ?></option>
                    <option value="Events" <?= $sort_name=="Events"?'Selected':'' ?>><?= ml('Competitors.Table.Events') ?></option>
                    <option value="Medals" <?= $sort_name=="Medals"?'Selected':'' ?>><?= ml('Competitors.Table.Medals') ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><i class="fas fa-search"></i> <?= ml('Competitors.Find') ?></td>
            <td><input ID="competitor-find"/></td>
        </tr>
    </table>        
</td></tr></table>

    <table class="table_new" data-competitors>
        <thead>
            <tr>
                <td><?= ml('Competitors.Table.Competitor') ?></td>
                <td><?= ml('Competitors.Table.WCAID') ?></td>
                <td><?= ml('Competitors.Table.Country') ?></td>
                <td class="table_new_center"><?= ml('Competitors.Table.Competitions') ?></td>
                <td class="table_new_center"><?= ml('Competitors.Table.Events') ?></td>
                <td class="table_new_center"><?= ml('Competitors.Table.Medals') ?></td>
            </tr> 
        </thead>
    <tbody>    
    <?php 
    foreach($competitors_medals as $competitors_medal){ ?>
            <tr data-key="<?= Short_Name($competitors_medal['Name']) ?> <?= $competitors_medal['WCAID'] ?>">
                <td >
                    <a href="<?= PageIndex() ?>Competitor/<?= $competitors_medal['WCAID']?$competitors_medal['WCAID']:$competitors_medal['ID'] ?>">            
                        <?= Short_Name($competitors_medal['Name']) ?> 
                    </a>
                </td>
                <td><?= $competitors_medal['WCAID'] ?></td>
                <td><?= $competitors_medal['CountryName'] ?></td>
                <td class="table_new_center"><?= $competitors_medal['Competitions']; ?></td>
                <td class="table_new_center"><?= $competitors_medal['Events']; ?></td>
                <td class="table_new_center"><?= $competitors_medal['Medals']?$competitors_medal['Medals']:"" ?></td>
            </tr>    
    <?php } ?>
        </tbody>
    </table>

<script>

$('#competitor-find').on("input",function(){
    var find=$(this).val().toLowerCase();
    reload(find);
});


function reload(find){
    var i=1;
    $('table[data-competitors] tbody tr').hide();
    $('table[data-competitors] tbody tr').each(function() {
        var key=$(this).data('key').toLowerCase();
        if(key.indexOf(find)!== -1){
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

</script>