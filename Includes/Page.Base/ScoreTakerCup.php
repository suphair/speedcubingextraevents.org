<?php

if(availableCupChange($event['ID'])){ ?>
    <h2><i class="fas fa-hand-paper"></i> You need to perform a distribution</h2>
    <a href="<?=PageAction('CompetitionEvent.Grid').'/'.$event['ID'] ?>">Distribution of teams by grid</a>
<?php exit();
    } 

DataBaseClass::Query("Select  group_concat(C.Name order by C.Name separator ', ') Competitors,Com.CardID,Com.ID,E.CommandsCup,E.Round,Com.Name from Event E "
        . " join Command Com on Com.Event=E.ID "
        . " join CommandCompetitor CC on CC.Command=Com.ID"
        . " join Competitor C on C.ID=CC.Competitor"
        . " where E.ID=".$event['ID'].""
        . " group by Com.CardID,Com.ID,E.CommandsCup,E.Round,Com.Name");

$commands=DataBaseClass::getRows();
$CommandsCup= json_decode($commands[0]['CommandsCup'],true);
$Commands=[];
foreach($commands as $command){
    $Commands[$command['ID']]=$command;
} ?>

<?php DataBaseClass::Query("Select  sum(CV.Point1) Point1,sum(CV.Point2) Point2,CC.*,C1.Name Command1Name,C2.Name Command2Name "
        . " from CupCell CC"
        . " left outer join Command C1 on CC.Command1=C1.ID "
        . " left outer join Command C2 on CC.Command2=C2.ID "
        . " left outer join CupValue CV on CV.CupCell=CC.ID"
        . " where CC.Event=".$event['ID'].""
        . " group by CC.ID,C1.ID,C2.ID");
$Cells=[];
$cell_rows=DataBaseClass::getRows();
foreach($cell_rows as $cell){
    $Cells[$cell['Round']][$cell['Number']]=$cell;
}

$Count=$CommandsCup['Count']; 
$Rounds=$CommandsCup['Round']; 
?>
<div style="height: 90px;padding:0px; ">
<?php foreach($cell_rows as $cell){
    $comwin=$cell['CommandWin']; ?>
    <div class="cell_info table_new" ID="cell_info_<?= $cell['ID'] ?>" style="display: none;padding:0px;">
    <p>
        <?php $cn=1; 
        if(isset($Commands[$cell['Command'.$cn]])){ 
            $com=$Commands[$cell['Command'.$cn]]; ?>
            <i><?= $com['CardID']  ?></i> <?= $com['Name'] ?>
            <?php if($comwin){ ?>
                <?php if($comwin==$com['ID']){ ?>
                    <i class="color_green fas fa-thumbs-up"></i>
                <?php }else{ ?>
                    <i class="color_red far fa-times"></i>
                <?php } ?>
            <?php }elseif($cell['Status']=='wait'){ ?>
                <i class="fas fa-hourglass-start"></i>
            <?php }elseif($cell['Status']=='run'){ ?>
                <i class="fas fa-running"></i>
            <?php } ?>  
            &nbsp;&nbsp;<font color='gray'><i class="fas fa-users"></i> <?= $Commands[$cell['Command'.$cn]]['Competitors'] ?></font>
        <?php }elseif($cell['Round']>1){ ?>
            <i class="far fa-question-circle"></i>
        <?php }else{ ?>
          <i class="fas fa-ban"></i>
        <?php } ?>
    </p>
    <table>
        <tbody>
            <tr>     
                <?php DataBaseClass::Query("Select * from CupValue where CupCell=".$cell['ID']." order by Attempt");
                $attempts=DataBaseClass::getRows();
                foreach($attempts as $attempt){ ?>
                <td>
                    <form class="inputCupValue" method="POST" action="<?= PageAction('ScoreTakerCup.deleteAttempt')?>">
                        <input hidden name="ID" value="<?= $cell['ID'] ?>"/>
                        <input hidden name="Attempt" value="<?= $attempt['ID'] ?>"/>
                        <input hidden name="Secret" value="<?= $Secret ?>" />
                    </form>        
                    <table 
                        <?php if($cell['Status']=='run'){ ?>
                            ondblclick="if(confirm('Do you really want to delete this attempt?') & confirm('Confirm the deletion of this attempt again, please.')){ 
                            $(this).closest('td').find('form').submit() };" 
                        <?php } ?>
                        style=" margin:0px;border: 2px solid red; border-radius: 10px;padding:0px;">
                        <?php foreach([1,2] as $cn_){ ?>
                            <tr>
                                <td class='table_new_right'><?= getTimeStrFromValue($attempt['Value1_'.$cn_]);?></td>
                                <td class='table_new_right'><?= getTimeStrFromValue($attempt['Value2_'.$cn_]);?></td>
                                <td class='table_new_right'><?= getTimeStrFromValue($attempt['Value3_'.$cn_]);?></td>
                                <td class='table_new_bold table_new_right'>
                                    <span class='<?=$attempt['Point'.$cn_]?'color_green':'' ?>'>
                                        <?= getTimeStrFromValue($attempt['Sum'.$cn_]);?>
                                    </span>    
                                </td>
                            </tr>
                        <?php } ?>
                    </table>   
                </td>    
                <?php } ?>
                <?php if($cell['Status']=='run'){ ?>
                    <td>
                        <form class="inputCupValue" method="POST" action="<?= PageAction('ScoreTakerCup.enterValues')?>" 
                            onsubmit="
                            return confirm(
'Please check the results!\n \
<?= $Commands[$cell['Command1']]['Name'] ?>: ' + $(this).find('table input').eq(0).val() + ' ' + $(this).find('table input').eq(1).val() + ' ' + $(this).find('table input').eq(2).val() + ' ' +'\n \
<?= $Commands[$cell['Command2']]['Name'] ?>: ' + $(this).find('table input').eq(3).val() + ' ' + $(this).find('table input').eq(4).val() + ' ' + $(this).find('table input').eq(5   ).val() + ' ' );">
                            <input hidden name="ID" value="<?= $cell['ID'] ?>"/>
                            <input hidden name="Attempt" value="<?= sizeof($attempts)+1 ?>"/>
                            <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                        <table style="white-space: nowrap; margin:0px;border: 2px solid black; border-radius: 10px;padding:0px;border-right: 4px solid red;">
                            <tr>
                                <td><input required autocomplete="off" name='Value[1][1]' ID="inputFocus_<?= $cell['ID'] ?>"/></td>
                                <td><input required autocomplete="off" name='Value[1][2]'/></td>
                                <td><input required autocomplete="off" name='Value[1][3]'/></td>
                            </tr>
                            <tr>
                                <td><input required autocomplete="off" name='Value[2][1]'/></td>
                                <td><input required autocomplete="off" name='Value[2][2]'/></td>
                                <td><input required autocomplete="off" name='Value[2][3]'/></td>
                                <td>
                                    <button disabled><i class="fas fa-check-double"></i> Save</button>
                                </td>    
                            </tr>
                        </table>
                        </form>
                    </td> 
                    <td>
                        <table width="130px" style=" margin:0px;border: 2px solid black; border-radius: 10px;padding:0px;border-right: 4px solid blue;">
                            <tr>
                                <td>
                                    <?= $cell['Point1']+0 ?>
                                </td>    
                                <td align='center'>
                                    <form  method="POST" action="<?= PageAction('ScoreTakerCup.runToDone')?>" onsubmit="return confirm('The team <?= $Commands[$cell['Command1']]['Name']?> won?')">
                                        <input hidden name="ID" value="<?= $cell['ID'] ?>"/>
                                        <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                                        <input name="Winner" type="hidden" value="<?= $cell['Command1'] ?>" />
                                        <button <?= $cell['Point1']<$cell['Point2']?'hidden':'' ?>>Winner <i class="fas fa-thumbs-up"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $cell['Point2']+0 ?>
                                </td>    
                                <td align='center'>
                                    <form  method="POST" action="<?= PageAction('ScoreTakerCup.runToDone')?>" onsubmit="return confirm('The team <?= $Commands[$cell['Command2']]['Name']?> won?')">
                                        <input hidden name="ID" value="<?= $cell['ID'] ?>"/>
                                        <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                                        <input name="Winner" type="hidden" value="<?= $cell['Command2'] ?>" />
                                        <button <?= $cell['Point2']<$cell['Point1']?'hidden':'' ?>>Winner <i class="fas fa-thumbs-up"></i></button>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td> 
                <?php }elseif($cell['Status']=='done'){ ?>
                    <td>
                        <table width="130px" style=" margin:0px;border: 2px solid black; border-radius: 10px;padding:0px;border-right: 4px solid green;">
                            <tr><td align="center">
                                    <form  method="POST" action="<?= PageAction('ScoreTakerCup.doneToRun')?>">
                                        <input hidden name="ID" value="<?= $cell['ID'] ?>"/>
                                        <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                                        <button><i class="fas fa-edit"></i> Edit</button>
                                    </form>
                            </td></tr>
                            <tr><td align="center">&nbsp;</td></tr>
                        </table>
                    </td> 
                <?php }elseif($cell['Status']=='skip'){ ?>
                    <td>
                        <table width="130px" style=" margin:0px;border: 2px solid black; border-radius: 10px;padding:0px;border-right: 4px solid black;">
                            <tr><td align="center">The fight</td></tr>
                            <tr><td align="center">skipped</td></tr>
                        </table>
                    </td>     
                <?php }elseif($cell['Status']=='blank'){ ?>
                    <td>
                        <table width="130px" style=" margin:0px;border: 2px solid black; border-radius: 10px;padding:0px;border-right: 4px solid black;">
                            <tr><td align="center">Blank</td></tr>
                            <tr><td>&nbsp;</td></tr>
                        </table>
                    </td>  
                <?php }elseif($cell['Status']=='wait'){ ?>
                    <td>
                        <table width="130px" style=" margin:0px;border: 2px solid black; border-radius: 10px;padding:0px;border-right: 4px solid yellow;">
                            <tr><td align="center">Waiting for teams</td></tr>
                            <tr><td align="center">to fight</td></tr>
                        </table>
                    </td> 
                <?php }elseif($cell['Status']=='fix'){ ?>
                    <td>
                        <table width="130px" style=" margin:0px;border: 2px solid black; border-radius: 10px;padding:0px;border-right: 4px solid black;">
                            <tr><td align="center">The results</td></tr>
                            <tr><td align="center">is fixed</td></tr>
                        </table>
                    </td> 
                <?php } ?>
            </tr>  
        </tbody>
    </table> 
    <p>
        <?php $cn=2; 
        if(isset($Commands[$cell['Command'.$cn]])){ 
            $com=$Commands[$cell['Command'.$cn]]; ?>
            <i><?= $com['CardID']  ?></i> <?= $com['Name'] ?>
            <?php if($comwin){ ?>
                <?php if($comwin==$com['ID']){ ?>
                    <i class="color_green fas fa-thumbs-up"></i>
                <?php }else{ ?>
                    <i class="color_red far fa-times"></i>
                <?php } ?>
            <?php }elseif($cell['Status']=='wait'){ ?>
                <i class="fas fa-hourglass-start"></i>
            <?php }elseif($cell['Status']=='run'){ ?>
                <i class="fas fa-running"></i>
            <?php } ?>  
            &nbsp;&nbsp;<font color='gray'><i class="fas fa-users"></i> <?= $Commands[$cell['Command'.$cn]]['Competitors'] ?></font>
        <?php }elseif($cell['Round']>1){ ?>
            <i class="far fa-question-circle"></i>
        <?php }else{ ?>
          <i class="fas fa-ban"></i>
        <?php } ?>
    </p>
    </div>
<?php } ?>
</div>
<br>
<table class="table_new_border" >
    <thead>
        <tr>
            <?php for($round=1;$round<=$Rounds;$round++){ ?>
            <td width="200px" class="table_new_center table_new_bold" >
                <?php if($round==$Rounds){ ?>
                    Final
                <?php }elseif($round==$Rounds-1){ ?>
                    Semifinal
                <?php }else{ ?>
                    1 / <?= pow(2,$Rounds-$round)?>
                <?php } ?>
            </td>
            <?php } ?>
        </tr>  
    </thead>
    <tbody>
        <?php for($i=1;$i<=$Count;$i++){  ?>
        <tr>
            <?php for($round=1;$round<=$Rounds;$round++){ ?>
                <?php if($i%(pow(2,$round))-1==0){ 
                    $cell=$Cells[$round][$i/pow(2,$round)+1];
                    $comwin=$cell['CommandWin'];
                    ?>
            
                        <td onmouseover="$(this).addClass('cell_hover')"
                            onmouseout="$(this).removeClass('cell_hover')"
                            onclick="cellClick(<?= $cell['ID'] ?>);"
                            id='cell_<?= $cell['ID'] ?>'    
                            class="table_new_left cell" style="vertical-align: middle;white-space: normal; border-right-width:4px;
                            <?= $cell['Status']=='done'?'border-right-color:green;':'' ?>
                            <?= $cell['Status']=='wait'?'border-right-color:yellow;':'' ?>
                            <?= $cell['Status']=='run'?'border-right-color:blue':'' ?>
                            " rowspan="<?= pow(2,$round) ?>">
                        <table class="table_new_background_white" width="100%">    
                            <?php foreach([1,2] as $cn){ ?>
                            <?php if($com=$cell['Command'.$cn]){ ?>
                            <tr>
                                <td width="20px">
                                    <i><?= $Commands[$com]['CardID'] ?></i>
                                </td>
                                <td  width="100px">
                                <?= $Commands[$com]['Name']; ?>
                                </td>
                                <td width="10px" class='table_new_bold'><?= $cell['Point'.$cn] ?></td>
                                <td>
                                <?php if($comwin){ ?>
                                    <?php if($comwin==$com){ ?>
                                        <?php if($cell['Status']=='skip'){ ?>
                                            <i class="fas fa-arrow-right"></i>
                                        <?php }elseif($round!=$Rounds){ ?>
                                            <i class="color_green fas fa-thumbs-up"></i>
                                        <?php }else{ ?>    
                                            <i class="color_gold fas fa-trophy"></i>
                                        <?php } ?>    
                                    <?php }else{ ?>
                                        <i class="color_red far fa-times"></i>
                                    <?php 
                                        }
                                    }elseif($cell['Status']=='wait'){ ?>
                                        <i class="fas fa-hourglass-start"></i>
                                    <?php }elseif($cell['Status']=='run'){ ?>
                                        <i class="fas fa-running"></i>
                                    <?php } ?>         
                                </td>        
                            </tr>
                            <?php }else{ ?>
                                <tr>
                                    <td width="20px"></td>
                                    <td width="100px"></td>
                                    <td></td>
                                    <td>
                                        <?php if($cell['Status']=='skip'){ ?>
                                            <i class="fas fa-ban"></i>
                                        <?php }elseif($cell['Status']=='blank'){ ?>
                                            &nbsp;
                                        <?php }else{ ?>
                                            <i class="far fa-question-circle"></i>
                                        <?php } ?>
                                    </td> 
                                </tr>
                            <?php } ?>
                        <?php } ?>                         
                        </table>
                    </td>
                <?php } ?>
            <?php } ?>     
        </tr>
        <?php } ?>
    </tbody>    
</table>

<script>
    
    function cellClick(id){
        location.hash = '#' + id;
        $('.cell').removeClass('cell_selected');
        $('#cell_'+id).addClass('cell_selected');
        $('.cell_info').hide();
        $('#cell_info_' + id).show();
        $('#inputFocus_'+ id).focus();
    }
    
    if(location.hash){
        cellClick(location.hash.replace('#',''));
    }
    
    function valueProcessing(el){
        var value;
        var value_out;
        var button=el.closest("table").find("button");
        
        button.prop('disabled', false);
        
        var inputs=el.closest("table").find("input");
        inputs.each(function(){
            if($(this).val()=='-' || $(this).val()=='DNF'){
                value_out='DNF';
            }else{
                value=$(this).val().replace(/[^-0-9]/gim,'');
                if(value.length>5){
                    value=(value).substr(0,5);    
                }
                value=("00000"+value).substr(-5,5);
                second=Number.parseInt(value.substr(1,2));
                minute=Number.parseInt(value.substr(0,1));
                milisecond=Number.parseInt(value.substr(3,2));
                value_out='';
                if(minute>0){
                    value_out = minute +':' + ('0' + second).substr(-2,2) + '.' + ('0'+milisecond).substr(-2,2);
                }else{
                    if(second>0){    
                        value_out = second + '.' + ('0'+milisecond).substr(-2,2);    
                    }else{
                        value_out = '0.' + ('0'+milisecond).substr(-2,2);    
                    }
                }

                if(value==='00000'){
                    value_out='';
                    button.prop('disabled', true);
                }
            }
            $(this).val(value_out);
            
        });
    }
    
    $('.inputCupValue input').on("input",function(){
        valueProcessing($(this));
    });
    
</script>
<br>
<p><i class="fas fa-info-circle"></i> Double click on an attempt to delete it</p>
<p><i class="fas fa-info-circle"></i> Enter '-' for DNF</p>