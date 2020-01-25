<?php includePage('Navigator'); ?>
<?php $Delegate=ObjectClass::getObject('PageDelegate'); 
 DataBaseClass::FromTable('Competitor','WID='.$Delegate['Delegate_WID']);
 $competitor=DataBaseClass::QueryGenerate(false);
?>

<?php if(CheckAccess('Delegate.Settings')){ ?>
    <a class='Settings' href="<?= LinkDelegate($Delegate['Delegate_WCA_ID']) ?>/Settings"><img style="vertical-align: middle" width="20px" src="<?= PageIndex()?>Image/Icons/settings.png"> <?= ml('*.Settings') ?></a>
<?php } ?>

<table class="no_border">
    <tr>
        <td>
            <?php if($competitor['Competitor_Avatar']){?>
                <img style="border-radius: 20px" src="<?= $competitor['Competitor_Avatar'] ?>" valign=top>
            <?php } ?>
        </td>
        <td>
<h1><?= $competitor['Competitor_Name'] ?></h1>  
<h3>
<?php if($competitor['Competitor_Country']){ ?>
    <?= ImageCountry($competitor['Competitor_Country'], 20)?> <?= CountryName($competitor['Competitor_Country']) ?>
<?php } ?>  
<?php if ($competitor['Competitor_WCAID']){ ?>    
    &#9642; <a href="https://www.worldcubeassociation.org/persons/<?= $competitor['Competitor_WCAID'] ?>"><?= $competitor['Competitor_WCAID'] ?></a>
<?php } ?>  
    &#9642; <a href="<?= LinkCompetitor($Delegate['Delegate_WCA_ID']) ?>"><?= ml('Delegate.Results') ?></a>
</h3>    
<h3><?= ml('Delegate.'.$Delegate['Delegate_Status']) ?></h3>

        </td>
    </tr>
</table>    
    
<?php if($Delegate['Delegate_Contact']){ ?>
    <div class="form"><?= Parsedown($Delegate['Delegate_Contact']) ?></div>
<?php } ?>
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
    <table class="Competitions">
        <tr class="tr_title">
            <td><?= ml('Delegate.Table.Date')?></td>
            <td><?= ml('Delegate.Table.Competition')?></td>
            <td><?= ml('Delegate.Table.Country')?></td>
            <td><?= ml('Delegate.Table.Events')?></td>
         </tr>   
            <?php foreach($competitions as $competition){ ?>
            <tr>
                <td>
                    <span class="<?= $competition['Competition_Status']!='1'?"error":""; ?>">
                        <b><?= date_range($competition['Competition_StartDate'],$competition['Competition_EndDate']); ?></b>
                    </span>
                </td>   
                <td>
                    <a href="<?= LinkCompetition($competition['Competition_WCA']) ?>">
                        <?= ImageCountry($competition['Competition_Country'], 20)?> <span class="<?= $competition['Competition_Unofficial']?"unofficial":"" ?>"><?= $competition['Competition_Name'] ?></span>
                    </a>
                </td>
                <td>
                    <b><?= CountryName($competition['Competition_Country']) ?></b>, <?= CountryName($competition['Competition_City']) ?>
                <td>
                <?php DataBaseClass::FromTable("Event","Competition=".$competition['Competition_ID']);
                      DataBaseClass::Join_current("DisciplineFormat");
                      DataBaseClass::Join_current("Discipline");
                      DataBaseClass::OrderClear("Discipline", "Name");
                      DataBaseClass::Select("distinct D.*");

                      foreach(DataBaseClass::QueryGenerate() as $discipline){ ?>
                            <a href="<?= LinkDiscipline($discipline['Code']) ?>"><?= ImageEvent($discipline['CodeScript'],25,$discipline['Name']);?></a>
                      <?php } ?>
                </td>
            </tr>    
            <?php } ?>
        
    </table>
<?php } ?>




    
<?= mlb('Delegate.Archive') ?>
<?= mlb('Delegate.Senior') ?>
<?= mlb('Delegate.Junior') ?>
<?= mlb('Delegate.Middle') ?>
