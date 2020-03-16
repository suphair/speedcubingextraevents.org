<?php $Delegate=ObjectClass::getObject('PageDelegate'); 
 DataBaseClass::FromTable('Competitor','WID='.$Delegate['Delegate_WID']);
 $competitor=DataBaseClass::QueryGenerate(false);
?>


<h1>
    <?php if($competitor['Competitor_Country']){ ?><?= ImageCountry($competitor['Competitor_Country'])?><?php } ?>  
    <?= $competitor['Competitor_Name'] ?>
</h1>  
    <table class="table_info">
    <?php if(CheckAccess('Delegate.Settings')){ ?>
    <tr>
        <td><i class="fas fa-cog"></i></td>
        <td><a href="<?= LinkDelegate($Delegate['Delegate_WCA_ID']) ?>/Settings"> Settings</a></td>
    </tr>   
    <?php } ?>
    <td><?= ml('Delegate.Delegate'); ?></td>        
    <td><?= ml('Delegate.'.$Delegate['Delegate_Status']) ?></td>
    <tr>
        <?php if($competitor['Competitor_Country']){ ?>
            <td><?= ml('Delegate.Country'); ?></td>        
            <td><?= CountryName($competitor['Competitor_Country']) ?></td>
        <?php } ?>
    </tr>
    <?php if ($competitor['Competitor_WCAID']){ ?>    
    <tr>
        <td>WCA ID</td>
        <td><a target="_blank" href="https://www.worldcubeassociation.org/persons/<?= $competitor['Competitor_WCAID'] ?>"><?= $competitor['Competitor_WCAID'] ?> <i class="fas fa-external-link-alt"></i></a></td>
    </tr>
    <?php } ?>  
    <tr>
        <td><?= ml('Delegate.Competitor'); ?></td>        
        <td><a href="<?= LinkCompetitor($competitor['Competitor_ID'])?>"><?= ml('Delegate.CompetitorLink'); ?></a></td>    
    </tr>
    <?php if($Delegate['Delegate_Contact']){ ?>
    <tr>
        <td><?= ml('Delegate.Contacts'); ?></td>        
        <td><?= Parsedown($Delegate['Delegate_Contact']) ?></td>
    <tr>
    <?php } ?>    
</table>
<?php
$CheckHidden=CheckAccess('Competitions.Hidden');

    DataBaseClass::FromTable('Delegate',"ID='".$Delegate['Delegate_ID']."'");
    DataBaseClass::Join_current('CompetitionDelegate');
    DataBaseClass::Join_current('Competition');
    if(!$CheckHidden){
        DataBaseClass::Where_current('Status=1');
    }
    DataBaseClass::OrderClear('Competition', 'StartDate desc');
    $competitions=DataBaseClass::QueryGenerate(); 
    ?>
<?php if(sizeof($competitions)){ ?>
<br>
<h2><?= ml('Delegate.Competitions'); ?></h2>    
    <table class="table_new">
        <thead>
        <tr>
            <td><?= ml('Delegate.Table.Date')?></td>
            <td><?= ml('Delegate.Table.Competition')?></td>
            <td><?= ml('Delegate.Table.Country')?></td>
            <td><?= ml('Delegate.Table.Events')?></td>
         </tr>   
         </thead>
         <tbody>
            <?php foreach($competitions as $competition){ ?>
            <tr>
                <td class="table_new_bold">
                   <?= date_range($competition['Competition_StartDate'],$competition['Competition_EndDate']); ?>
                </td>   
                <td>
                    <a href="<?= LinkCompetition($competition['Competition_WCA']) ?>">
                        <?= ImageCountry($competition['Competition_Country'], 20)?> <span class="<?= $competition['Competition_Unofficial']?"unofficial":"" ?>"><?= $competition['Competition_Name'] ?></span>
                    </a>
                </td>
                <td>
                    <?= CountryName($competition['Competition_Country']) ?>, <?= CountryName($competition['Competition_City']) ?>
                <td>
                <?php DataBaseClass::FromTable("Event","Competition=".$competition['Competition_ID']);
                      DataBaseClass::Join_current("DisciplineFormat");
                      DataBaseClass::Join_current("Discipline");
                      DataBaseClass::OrderClear("Discipline", "Name");
                      DataBaseClass::Select("distinct D.*");

                      foreach(DataBaseClass::QueryGenerate() as $discipline){ ?>
                            <?= ImageEvent($discipline['CodeScript'],1,$discipline['Name']);?>
                      <?php } ?>
                </td>
            </tr>    
            <?php } ?>
        </tbody>
    </table>
<?php } ?>




    
<?= mlb('Delegate.Archive') ?>
<?= mlb('Delegate.Senior') ?>
<?= mlb('Delegate.Junior') ?>
<?= mlb('Delegate.Middle') ?>
