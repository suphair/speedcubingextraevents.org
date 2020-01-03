<?php includePage('Navigator'); ?>
<?php $Delegate=ObjectClass::getObject('PageDelegate'); ?>

<?php if(CheckAccess('Delegate.Settings')){ ?>
    <a class='Settings' href="<?= LinkDelegate($Delegate['Delegate_WCA_ID']) ?>/Settings"><img style="vertical-align: middle" width="20px" src="<?= PageIndex()?>Image/Icons/settings.png"> <?= ml('*.Settings') ?></a>
<?php } ?>

<h1><?= short_Name($Delegate['Delegate_Name']) ?></h1>    
<h2>       
    <span class='<?= $Delegate['Delegate_Status']=='Archive'?'archive':'' ?>
                <?= $Delegate['Delegate_Status']=='Trainee'?'error':'' ?>
                <?= $Delegate['Delegate_Status']=='Senior'?'message':'' ?>'>
                    <?= ml('Delegate.'.$Delegate['Delegate_Status']) ?></span>
  &#9642;  <a href="https://www.worldcubeassociation.org/persons/<?= $Delegate['Delegate_WCA_ID'] ?>"><?= $Delegate['Delegate_WCA_ID'] ?></a> 
  &#9642;  <a href="<?= LinkCompetitor($Delegate['Delegate_WCA_ID']) ?>"><?= ml('Delegate.Results') ?></a> 
</h2> 
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
            <?php foreach($competitions as $competition){ ?>
            <tr>
                <td>
                    <a class="<?= $competition['Competition_Status']!='1'?"archive":""; ?> "  href="<?= LinkCompetition($competition['Competition_WCA']) ?>">
                        <span class="<?= $competition['Competition_Unofficial']?"unofficial":"" ?>"><?= $competition['Competition_Name'] ?></span>
                    </a>
                </td>
                <td>
                    <?= date_range($competition['Competition_StartDate'],$competition['Competition_EndDate']); ?>
                </td>   
                <td>
                    <?= ImageCountry($competition['Competition_Country'], 30)?>
                    <?= CountryName($competition['Competition_Country']) ?>, <?= CountryName($competition['Competition_City']) ?>
                <td>
                <?php DataBaseClass::FromTable("Event","Competition=".$competition['Competition_ID']);
                      DataBaseClass::Join_current("DisciplineFormat");
                      DataBaseClass::Join_current("Discipline");
                      DataBaseClass::OrderClear("Discipline", "Name");
                      DataBaseClass::Select("distinct D.*");

                      $j=0; 
                      foreach(DataBaseClass::QueryGenerate() as $discipline){ ?>
                            <a href="<?= LinkDiscipline($discipline['Code']) ?>"><?= ImageEvent($discipline['CodeScript'],30,$discipline['Name']);?></a>
                            <?php $j++;
                            if($j==8){
                                $j=0;
                            echo "<br>";
                        }
                      } ?>
                </td>
            </tr>    
            <?php } ?>
        
    </table>
<?php } ?>




    
<?= mlb('Delegate.Archive') ?>
<?= mlb('Delegate.Senior') ?>
<?= mlb('Delegate.Junior') ?>
<?= mlb('Delegate.Middle') ?>
