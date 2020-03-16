<h1>SEE Delegate / Changes</h1>

<?php if(CheckAccess('Delegate.Settings.Ext')){ ?>
<form method="POST" action="<?= PageAction('Delegates.Change.DeleteAll') ?>" onsubmit="return confirm('Confirm clear')">
<table class="table_info">
    <tr>
        <td>Сlear all votes</td>
        <td><button><i class="fas fa-crown"></i> Clear</button></td>
    </td>    
</table>
</form>
<?php } ?>

<?php 
$Delegate= getDelegate();
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
$Statuses['?']=-1;
?>
<?php foreach([1,-1] as $tr_check){ ?>
<?php if($tr_check==1){ ?>
    <h3>Trainee Delegates</h3>
<?php } ?>
<?php if($tr_check!=1){ ?>
    <h3>Other Delegates</h3>
<?php } ?>    

<form method="POST" action="<?= PageAction('Delegates.Change.Edit') ?>">
<table class="table_new">
<thead>    
<tr>
    <td>Name</td>
    <td>Status</td>
    <td>Your votes</td>
    <?php foreach($Seniors as $senior)if($senior['ID']==$Delegate['Delegate_ID']){ ?>
        <td>
            <?= $senior['Name'] ?><br>
            <?= isset($DelegateChangeTime[$senior['ID']])?($DelegateChangeTime[$senior['ID']]):'<i class="fas fa-ellipsis-h"></i>' ?>
        </td>
    <?php } ?>
    <?php foreach($Seniors as $senior)if($senior['ID']!=$Delegate['Delegate_ID']){ ?>
        <td>
            <?= $senior['Name'] ?><br>
            <?= isset($DelegateChangeTime[$senior['ID']])?($DelegateChangeTime[$senior['ID']]):'<i class="fas fa-ellipsis-h"></i>' ?>
        </td>
    <?php } ?>
    <td>Total votes</td>   
</tr>   
</thead>
<tbody>
<?php DataBaseClass::Query("Select D.*,C.Country from Delegate D "
        . " join Competitor C on C.WID=D.WID "
        . "where D.Status!='Archive' and D.Status!='Senior' order by C.Country, D.Name");
$delegates=DataBaseClass::getRows();
foreach($delegates as $delegate)
    if(($tr_check==1 and $delegate['Status']=='Trainee') or ($tr_check==-1 and $delegate['Status']!='Trainee')){ 
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
    <?php 
    $SeniorTmp=$Seniors;
    $Seniors=[];
    foreach($SeniorTmp as $senior)if($senior['ID']==$Delegate['Delegate_ID']){
        $Seniors[]=$senior;
    }
    foreach($SeniorTmp as $senior)if($senior['ID']!=$Delegate['Delegate_ID']){
        $Seniors[]=$senior;
    }
    
    foreach($Seniors as $senior){ ?>
        <td>
            <?php if(isset($DelegateChange[$senior['ID']][$delegate['ID']])){
                $status_new=$DelegateChange[$senior['ID']][$delegate['ID']];
                $status_news[]=$status_new;?> 
                    <?php if($status_new=='?'){ ?>
                        <i class="far fa-question-circle"></i>
                    <?php }else{?>
                        <?php if($Statuses[$status_new]>$Statuses[$delegate['Status']]){ ?>
                            <span class="color_green"><i class="fas fa-arrow-alt-circle-up"></i></span>
                        <?php } ?>
                        <?php if($Statuses[$status_new]<$Statuses[$delegate['Status']]){ ?>
                            <span class="color_red"><i class="fas fa-arrow-alt-circle-down"></i></span>
                        <?php } ?> 
                        <?= $status_new ?>
                    <?php } ?>
            <?php }else{ ?>
                <i class="far fa-question-circle"></i>
            <?php } ?>            
        </td>
    <?php } ?>
        <td>
            <?php $status_news_unique=array_unique($status_news);
            if(sizeof($status_news)==sizeof($Seniors)){
                if(sizeof($status_news_unique)==1){ ?>
                    <?php if($Statuses[$status_news[0]]>$Statuses[$delegate['Status']]){ ?>
                        <span class="color_green"><i class="fas fa-arrow-alt-circle-up"></i></span>
                    <?php } ?>
                    <?php if($Statuses[$status_news[0]]<$Statuses[$delegate['Status']]){ ?>
                        <span class="color_red"><i class="fas fa-arrow-alt-circle-down"></i></span>
                    <?php } ?>                                
                    <?= $status_news[0] ?>
                <?php }else{ ?>
                    <i class="far fa-question-circle"></i>
                <?php } ?> 
            <?php }else{ ?>
                <i class="fas fa-hourglass-start"></i>
            <?php } ?>         
        </td>
</tr>
<?php } ?>
<tbody>
</table>
    <td><button><i class="fas fa-save"></i> Save votes</button></td>
</form>
<?php } ?>
