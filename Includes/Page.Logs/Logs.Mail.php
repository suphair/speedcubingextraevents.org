<?php IncludePage('Logs_navigator')?>
<?php DataBaseClass::Query("Select * from LogMail ML"
            . " where date(DateTime)>=DATE_ADD(current_date(),INTERVAL -14 Day) "
            . " order by ML.ID desc");
    $logs=DataBaseClass::getRows(); ?>
    
<h1>Logs mail</h1>
<table class="table_new">
<thead>
<tr>
    <td>DateTime</td>
    <td>To</td>
    <td>Subject</td>
    <td>Result</td>
</tr>
</thead>
<tbody>    
<?php foreach($logs as $log){ ?>
    <tr>
        <td><?= $log['DateTime'] ?></td>
        <td><?= $log['To'] ?></td>
        <td><?= $log['Subject'] ?></td>
        <td>
            <?php if($log['Result']!=1){ ?>
                <i class="fas fa-exclamation-triangle"></i> <?= $log['Result']?>
            <?php } ?>
        </td>
        <td><a href="#" onclick="$(this).hide(); $('#LogID<?= $log['ID'] ?>').show(); return false;">message</a>
        <tr  ID="LogID<?= $log['ID'] ?>"  style="display: none">    
        <td colspan="4" width="400px">
        <p>
            <?= $log['Body'] ?>
        </p>
        </td>
    </tr>
<?php } ?>         
</tbody>
</table>
