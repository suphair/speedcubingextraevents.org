<?php IncludePage('Logs_navigator')?>
<h1>Logs mail</h1>
<table style="white-space:nowrap" >
<thead>
<tr class="tr_title">
    <td>DateTime</td>
    <td>To</td>
    <td>Subject</td>
    <td>Result</td>
</tr>
</thead>
    <?php DataBaseClass::Query("Select * from LogMail ML"
            . " where date(DateTime)>=DATE_ADD(current_date(),INTERVAL -14 Day) "
            . " order by ML.ID desc");
        foreach(DataBaseClass::getRows() as $log){ ?>
<tr>
<td><?= $log['DateTime'] ?></td>
<td><?= $log['To'] ?></td>
<td><?= $log['Subject'] ?></td>
<td>
    <?php if($log['Result']!=1){ ?>
    <span class="error">
        <?= $log['Result']?>
    </span>    
    <?php } ?>
</td>
<td><a href="#" onclick="$(this).hide(); $('#LogID<?= $log['ID'] ?>').show(); return false;">message</a>
<tr  ID="LogID<?= $log['ID'] ?>"  style="display: none">    
<td colspan="4" width="400px">
<div class="block_comment">
    <?= $log['Body'] ?>
</div>
</td>
</tr>
        <?php } ?>         
</table>
