<?php IncludePage('Logs_navigator')?>
<h1>Logs cron</h1>
<table class="table_new">
<thead>
<tr>
    <td>DateTime</td>
    <td>Cron</td>
    <td>Details</td>
</tr>
</thead>
<tbody>
    <?php DataBaseClass::Query("Select C.Name Competitor_Name, C.ID Competitor_ID, L.*  from Logs L"
            . " left outer join Competitor C on C.WID=L.Competitor "
            . " where date(DateTime)>=DATE_ADD(current_date(),INTERVAL -14 Day) and Action='Cron'"
            . "  order by L.ID desc");
foreach(DataBaseClass::getRows() as $log){ ?>
    <tr>
    <td><?= $log['DateTime'] ?></td>
    <td><?= $log['Object'] ?></td>
    <td><?= $log['Details'] ?></td>
    </tr>
<?php } ?>         
<tbody>
</table>
