<?php includePage('Navigator'); ?>
<h1><?= ml('Delegates.Settings.Title') ?></h1>
<?php 
$Delegate= CashDelegate();
DataBaseClass::Query("Select * from Delegate where status='Senior' order by Name");
$Seniors=DataBaseClass::getRows(); 

$DelegateChange=[];
$DelegateChangeTime=[];
DataBaseClass::Query("Select * from DelegateChange");
foreach(DataBaseClass::getRows()  as $dc){
    $DelegateChange[$dc['Senior']][$dc['Delegate']]=$dc['Status'];
    $DelegateChangeTime[$dc['Senior']]=$dc['Timestamp'];;
}

$Statuses=[];
$Statuses['Senior']=4;
$Statuses['Middle']=3;
$Statuses['Junior']=2;
$Statuses['Trainee']=1;
$Statuses['Archive']=0;

?>
<form method="POST" action="<?= PageAction('Delegates.Change.Delete') ?>">
    <input class="delete" type="submit" value="<?= ml('*.Delete',false)?>">
</form>
<form method="POST" action="<?= PageAction('Delegates.Change.Edit') ?>">
<table>
<tr class="tr_title">
    <td>Name</td>
    <td>Status</td>
    <td>New</td>
    <?php foreach($Seniors as $senior)if($senior['ID']==$Delegate['Delegate_ID']){ ?>
        <td align="center">
            <b><?= $senior['Name'] ?></b><br>
        <?= isset($DelegateChangeTime[$senior['ID']])?($DelegateChangeTime[$senior['ID']]):svg_red(10) ?>
        </td>
    <?php } ?>
    <?php foreach($Seniors as $senior)if($senior['ID']!=$Delegate['Delegate_ID']){ ?>
        <td align="center">
        <?= $senior['Name'] ?><br>
        <?= isset($DelegateChangeTime[$senior['ID']])?($DelegateChangeTime[$senior['ID']]):svg_red(10) ?>
        </td>
    <?php } ?>
    <td><b>Results</b></td>   
</tr>    
<?php
DataBaseClass::Query("Select D.*,C.Country from Delegate D "
        . " join Competitor C on C.WID=D.WID "
        . "where D.Status!='Archive' and D.Status!='Senior' order by D.Name");
foreach(DataBaseClass::getRows() as $delegate){ 
    
if(isset($DelegateChange[$Delegate['Delegate_ID']][$delegate['ID']])){
    $status_new=$DelegateChange[$Delegate['Delegate_ID']][$delegate['ID']];
}else{
    $status_new=false;
} 
$status_news=[]; ?>
<tr>
    <td>
        <?= ImageCountry($delegate['Country'], 20)?>
        <a href="<?= LinkDelegate($delegate['WCA_ID'])?>" target="_blank"><?= short_name($delegate['Name']) ?></a></td>
    <td><?= $delegate['Status']?></td>
    <td>
        <select name="Delegate[<?= $delegate['ID'] ?>]" style="width: 80px">
            <?php foreach($Statuses as $status=>$n){ ?>
            <option  <?= $status==($status_new?$status_new:$delegate['Status'])?'Selected':'' ?> value="<?= $status ?>"><?= $status ?></option>    
            <?php } ?>
        </select>
    </td>
    <?php foreach($Seniors as $senior)if($senior['ID']==$Delegate['Delegate_ID']){ ?>
        <td class="border-right-solid" align="center">
            <?php if(isset($DelegateChange[$senior['ID']][$delegate['ID']])){
                $status_new=$DelegateChange[$senior['ID']][$delegate['ID']];
                $status_news[]=$status_new; ?> 
            <span class="<?= $Statuses[$status_new]!=$Statuses[$delegate['Status']]?($Statuses[$status_new]>$Statuses[$delegate['Status']]?'message':'error'):'' ?>"><?= $status_new ?></span>
            <?php }else{ ?>
                *
            <?php } ?>            
        </td>
    <?php } ?>
    <?php foreach($Seniors as $senior)if($senior['ID']!=$Delegate['Delegate_ID']){ ?>
        <td class="border-right-dotted" align="center">
            <?php if(isset($DelegateChange[$senior['ID']][$delegate['ID']])){
                $status_new=$DelegateChange[$senior['ID']][$delegate['ID']];
                $status_news[]=$status_new; ?> 
            <span class="<?= $Statuses[$status_new]!=$Statuses[$delegate['Status']]?($Statuses[$status_new]>$Statuses[$delegate['Status']]?'message':'error'):'' ?>"><?= $status_new ?></span>
            <?php }else{ ?>
                *
            <?php } ?>            
        </td>
    <?php } ?>
        <td class="border-left-solid tr_title" align="center">
            <?php $status_news_unique=array_unique($status_news);
            if(sizeof($status_news_unique)==1 and sizeof($status_news)==sizeof($Seniors)){ ?>
                <span class="<?= $Statuses[$status_news[0]]!=$Statuses[$delegate['Status']]?($Statuses[$status_news[0]]>$Statuses[$delegate['Status']]?'message':'error'):'' ?>"><?= $status_news[0] ?></span>
            <?php }else{ ?>
                *
            <?php } ?> 
        </td>
</tr>
<?php } ?>
</table>
    <input type="submit" value="<?= ml('*.Save',false)?>">
</form>
    
