<?php $Language=$_SESSION['language_select'];?>
<h2><?= ml('Regulations.Description'); ?></h2>
<h3><?= ml('Regulations.Documents') ?></h3>
<table class="table_info">

    <tr>
        <td>Speecubing Extra Events</td>
        <td><a target="_blank" href="<?= PageIndex() ?>MainRegulations"><i class="fas fa-book"></i> SEE Regulations</a></a></td>
    </tr>
    <tr>
        <td>World Cube Association</td>
        <td><a target="_blank" href="https://www.worldcubeassociation.org/regulations"><i class="fas fa-book"></i> WCA Regulations <i class="fas fa-external-link-alt"></i></a></td>
    </tr>
</table>    

<?php 
$event_selected=false;
$request= getRequest();
$event_request=false;
if(isset($request[1])){
    $event_request=$request[1];
}


$discipline_default=[];
$language_default=[];
DataBaseClass::Query("Select D.ID, D.Name, D.Code,R.Text,R.Language from Discipline D  "
        . " left outer join Regulation R on R.Event=D.ID where D.Status='Active' and R.Text!='' order by R.ID");
foreach(DataBaseClass::getRows() as $row){
    $discipline_default[$row['ID']]=$row['Text'];
    $language_default[$row['ID']]=$row['Language'];
} ?>


<?php 
$eventwithoutregulations=[];
if(CheckAccess('Event.Settings')){
DataBaseClass::Query("Select D.* from Discipline D"
                         . " Left outer join Regulation R on D.ID=R.Event"
                         . " where D.Status='Active'  and R.ID is null"); 
foreach(DataBaseClass::getRows() as $row){ 
    $eventwithoutregulations[]=$row['CodeScript'];
    } 
} ?>
    
<?php DataBaseClass::Query("Select D.Comment,D.ID, D.Name, D.Code,D.CodeScript,R.Text,D.Inspection,D.Competitors,D.TNoodles from Discipline D  "
        . " left outer join Regulation R on R.Event=D.ID and R.Language='$Language' where D.Status='Active' order by D.Name");
$disciplines=DataBaseClass::getRows(); 

foreach($disciplines as $discipline_row){
    if($event_request==strtolower($discipline_row['Code'])){
        $event_selected=$discipline_row['Code'];
    }
}
if(!$event_selected)$event_selected=$disciplines[0]['Code'];
?>

<h3><?= ml('Regulations.ExtraEvents') ?></h3>
<table width="100%"><tr><td width="10%">
<table class="table_info" style="white-space: nowrap">
    <?php foreach($disciplines as $d=>$discipline_row){ ?>
        <tr>
            <td><?= ImageEvent($discipline_row['CodeScript'],1) ?></td>
            <td>
                <a class="<?= $event_selected==$discipline_row['Code']?'list_select':''?>" href="<?= PageIndex()?>Regulations/<?= $discipline_row['Code'] ?>"><?= $discipline_row['Name'] ?></a>
                <?php if(in_array($discipline_row['CodeScript'],$eventwithoutregulations)){ ?>
                    <i class="fas fa-exclamation-triangle"></i>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>     
</td><td width="90%">            
    <?php foreach($disciplines as $discipline_row)if($event_selected==$discipline_row['Code']){ 
    $other_language=false;
    if(!$discipline_row['Text'] and isset($discipline_default[$discipline_row['ID']])){
        $discipline_row['Text']=$discipline_default[$discipline_row['ID']];
        $other_language=$language_default[$discipline_row['ID']];
    }?>
<h2>
   <?= ImageEvent($discipline_row['CodeScript']) ?>
   <?= $discipline_row['Name'] ?> / Regulations
</h2>
<table class="table_info">
    <?php if($other_language){ ?>
    <tr>
        <td><?= ml('Regulations.Language') ?></td>
        <td><?= ImageCountry($other_language); ?> <?= CountryName($other_language,true)?></td>
    </td>    
    <?php } ?>
    <tr>
        <td></td>
        <td>
        <?php $Text='';
        
        if($discipline_row['Text']){
            $Text.=Parsedown($discipline_row['Text']);
        }else{
            $Text.="<i class='fas fa-exclamation-triangle'></i> ".ml('Regulation.Writing')."";
        } ?>
            <?= $Text;?>
   </tr>
   
    <?php if($discipline_row['Competitors']>1){ ?>
    <tr>
        <td>Team</td>
        <td><?php Parsedown(str_replace("%1",$discipline_row['Competitors'],GetBlockText('Regulation.Competitors',$Language))) ?></td>
    </tr>       
    <?php } ?>     
   <?php if($discipline_row['Inspection']==20){ ?>
    <tr>
        <td><?= ml('Regulations.Inspect') ?></td>
        <td><?php Parsedown(GetBlockText('Regulation.Inspect.20',$Language)); ?></td>
    </tr>       
    <?php } ?>
   
   <?php if(strpos($discipline_row['CodeScript'],'mguild')!==false){ ?>
    <tr>
        <td><?= ml('Regulations.Position') ?></td>
        <td><?php Parsedown(GetBlockText('Regulation.mguild',$Language)); ?></td>
    </tr>       
    <?php } ?>
   
   <?php if($discipline_row['TNoodles']){ ?>
    <tr>
        <td><?= ml('Regulations.Penalties') ?></td>
        <td><?php Parsedown(GetBlockText('Regulation.puzzles',$Language)) ?></td>
    </tr>    
    <?php  } ?> 
    <tr>
        <td><hr></td>
        <td><hr></td>
    </tr>    

    <?= EventBlockLinks(['Discipline_CodeScript'=>$discipline_row['CodeScript'] ,'Discipline_Code'=>$discipline_row['Code'] ,'Discipline_ID'=>$discipline_row['ID'],'Discipline_Status'=>'Active'],'regulations',true); ?>
<br>
<?php ?>
</td>
</tr>
</table>
<?php } ?>
</td></tr></table>