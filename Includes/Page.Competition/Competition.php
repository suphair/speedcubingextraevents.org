<?php 
$Competition=ObjectClass::getObject('PageCompetition');
$CompetitionEvents=ObjectClass::getObject('PageCompetitionEvents');
$CompetitionEvent=ObjectClass::getObject('PageCompetitionEvent');
$CompetitionDelegates=ObjectClass::getObject('PageCompetitionDelegates');

if(!$CompetitionEvent and isset($CompetitionEvents[0])){
    ObjectClass::setObjects('PageCompetitionEvent',$CompetitionEvents[0]);
    $CompetitionEvent=$CompetitionEvents[0];        
}

DataBaseClass::Query("select Cn.ID, count( distinct C.ID) count,count(A.ID) Attempts, now()>=Cm.StartDate Start from `Competition` Cn  
join `Event` E on E.Competition=Cn.ID
join `Competition` Cm on Cm.ID=E.Competition
left outer join `Command` Com on Com.Event=E.ID and Com.Decline!=1
left outer join `CommandCompetitor` CC on CC.Command=Com.ID 
left outer join `Competitor` C on CC.Competitor=C.ID
left outer join Attempt A on A.Command=Com.ID
where E.Competition='".$Competition['Competition_ID']."'
group by Cn.ID");
$data=DataBaseClass::getRow();

$count_competitors=$data['count']+0;
$attempts_exists=($data['Attempts']>0 or $data['Start']);
    
?>
<?php if(CheckAccess('Competition.Settings',$Competition['Competition_ID'])){ ?>
    <a class='Settings' href="<?= LinkCompetition($Competition['Competition_WCA'])?>/Settings"><img style="vertical-align: middle" width="20px" src="<?= PageIndex()?>Image/Icons/settings.png"> <?= ml('Competition.Settings') ?></a>
<?php } ?> 
<?php if(strtotime($Competition['Competition_StartDate'])<=strtotime(date('Y-m-d'))){
    if(CheckAccess('Competition.Report.Create',$Competition['Competition_ID'])){ ?>    
        <a class='Settings' href="<?= LinkCompetition($Competition['Competition_WCA'])?>/Report"><img style="vertical-align: middle" width="20px" src="<?= PageIndex()?>Image/Icons/report.png"> <?= ml('Competition.Report.Create') ?></a>
    <?php }elseif(CheckAccess('Competition.Report',$Competition['Competition_ID']) and sizeof(DataBaseClass::SelectTableRows("CompetitionReport","Competition=".$Competition['Competition_ID']))){ ?>
        <a class='Settings' href="<?= LinkCompetition($Competition['Competition_WCA'])?>/Report"><img style="vertical-align: middle" width="20px" src="<?= PageIndex()?>Image/Icons/report.png"> <?= ml('Competition.Report.View') ?></a>
    <?php } 
} ?>      
 <table><tr class="no_border"><td>
    <?php $imgCompetition=ImageCompetition($Competition['Competition_WCA'],100); ?>
    <?php if($imgCompetition){ ?>
        <?= $imgCompetition ?>
    <?php }else{ ?>
        <?= ImageCountry($Competition['Competition_Country'],75) ?> 
    <?php } ?>
    </td><td>
    <h1 class="competition_name">      
        <nobr><a href="<?= LinkCompetition($Competition['Competition_WCA'])?>"><?= $Competition['Competition_Name'] ?></a>
        <span class="badge"><?= $count_competitors ?></span></nobr>
    </h1>            
    <h2 class="competition_details">
        <?= $imgCompetition?ImageCountry($Competition['Competition_Country'],50):'' ?>
        <?=  date_range($Competition['Competition_StartDate'],$Competition['Competition_EndDate']); ?>
        &#9642; <?= CountryName($Competition['Competition_Country']); ?>, <?= $Competition['Competition_City'] ?>
        &#9642; <a href="https://www.worldcubeassociation.org/competitions/<?= $Competition['Competition_WCA'] ?>">WCA</a>
    </h2>    
    </td></tr></table>
    
    
<?php $delegates_out=[];

$comment="";    
    $delegate_str="";
    if($Competition['Competition_DelegateWCAOn']){
        $CompetitionDelegatesWCA=explode(',',$Competition['Competition_DelegateWCA']);
        foreach($CompetitionDelegatesWCA as $d=>$delegate){
            if(trim($delegate)){
                DataBaseClass::FromTable('Competitor',"WCAID='".trim($delegate)."'");
                $row=DataBaseClass::QueryGenerate(false);
                if(isset($row['Competitor_Name'])){
                    $CompetitionDelegatesWCA[$d]=$row;
                    $delegates_out[]="<a href='mailto:".$row['Competitor_Email']."'><img valign='middle' src='". PageIndex()."Image/Icons/mail.png' width='15px'>".$row['Competitor_Name']."</a>";   
                }
            }
        }
        
        if(sizeof($CompetitionDelegatesWCA)>0){
            if(sizeof($CompetitionDelegatesWCA)==1){
                $delegate_str=ml('Competiion.DelegateWCA')." ";
            } 
            if(sizeof($CompetitionDelegatesWCA)>1){
                $delegate_str=ml('Competiion.DelegatesWCA').": "; 
            }

            $comment.="<p>".$delegate_str."<b>".implode(", ",$delegates_out)."</b></p>";
        }
    }else{
        $delegate_str="";
        $delegates_out=[];
        if(sizeof($CompetitionDelegates)==1){
            $delegate_str=ml('Competiion.Delegate')." ";
        } 
        if(sizeof($CompetitionDelegates)>1){
            $delegate_str=ml('Competiion.Delegates').": "; 
        }
        foreach($CompetitionDelegates as $delegate){ 
            ob_start(); 
            if($delegate['Delegate_Status']!='Archive'){
                ?><a href="<?= LinkDelegate($delegate['Delegate_WCA_ID'])?>"><?= Short_Name($delegate['Delegate_Name'])?></a><?php 
            }else{
                ?><?= Short_Name($delegate['Delegate_Name'])?><?php
            }        
            $delegates_out[]= ob_get_contents();
            ob_end_clean();
       }
       $comment.="<p>".$delegate_str."<b>".implode(", ",$delegates_out)."</b></p>";           
    }  ?>  

<?php if(strtotime($Competition['Competition_StartDate'])<=strtotime(date('Y-m-d'))){ ?>    
    <?php if( $Competition['Competition_Unofficial']){ 
           if(!$Competition['Competition_DelegateWCAOn']){ ?>
                <?php $comment.= "<p>".svg_red(10)." ".ml('Competition.Unofficial.True')."</p>"?>
        <?php }else{ ?>
                <?php $comment.= "<p>".svg_red(10)." ".ml('Competition.Unofficial.TrueTemp')."</p>"?>
        <?php } ?>                    
    <?php } ?>    
<?php } ?>        

<?php if(strtotime($Competition['Competition_EndDate'])>=strtotime(date('Y-m-d'))){ ?>    
    <?php if(!$attempts_exists){?>
        <?php if($Competition['Competition_Registration']){ ?>
            <?php $comment.= "<p>".svg_green(10)." ".ml('Competition.Registration.True')."</p>"?>
        <?php }else{ ?>    
            <?php $comment.= "<p>".svg_red(10)." ".ml('Competition.Registration.False')."</p>"?>
        <?php } ?>
    <?php } ?>
    <?php if( $Competition['Competition_Onsite']){ ?>
        <?php $comment.= "<p>".svg_green(10)." ".ml('Competition.Onsite.True')."</p>"?>
    <?php }else{ ?>    
        <?php $comment.= "<p>".svg_red(10)." ".ml('Competition.Onsite.False')."</p>"?>
    <?php } ?>
<?php } ?>    
    
<?php if($Competition['Competition_Comment']){ ?>
    <?php $comment.="<hr>".Parsedown(ml_json($Competition['Competition_Comment']),false); ?>
<?php } ?>                  
    
<?php if($comment){ ?>
   <div class="block_comment"> 
       <?= $comment?>
   </div> 
    <br>
<?php } ?>
    
<?php 
if(sizeof($CompetitionEvents)){
    include 'Competition.Event.php';
}
?>
<?= mlb('Competiion.Delegate') ?>
<?= mlb('Competiion.Delegates') ?>
<?= mlb('Competition.Registration.True') ?>
<?= mlb('Competition.Registration.False') ?>
<?= mlb('Competition.Onsite.True') ?>
<?= mlb('Competition.Onsite.False') ?>
<?= mlb('Competition.Unofficial.True') ?>
<?= mlb('Competition.Settings') ?>
<?= mlb('Competiion.DelegateWCA') ?>
<?= mlb('Competiion.DelegatesWCA') ?>
<?= mlb('Competition.Report.Create') ?>
<?= mlb('Competition.Report.View') ?>