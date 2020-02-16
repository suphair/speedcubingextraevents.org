<?php IncludePage('Logs_navigator')?>
<h1>Logs registrations</h1>

<table class="table_info">
    <tr>
        <td>Legend</td>
        <td/>
    </tr>    
    <tr>
        <td><i class="far fa-user"></i></td>
        <td>Competitor</td>
    </td>    
    <tr>
        <td><i class="fas fa-user-tie"></i></td>
        <td>Delegate</td>
    </td>    
    <tr>
        <td><i class="far fa-list-alt"></i></td>
        <td>ScoreTaker</td>
    </td>    
<table>
            <?php DataBaseClass::Query("Select "
                    . " E.Round, D.Code,D.Name, D.CodeScript, LR.Timestamp,LR.Action, LR.Doing,LR.Details,C.Name Competition "
                    . " from LogsRegistration LR "
                    . " join Event E on E.ID=LR.Event "
                    . " join Competition C on C.ID=E.Competition "
                    . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
                    . " join Discipline D on D.ID=DF.Discipline"
                    . " where date(LR.Timestamp)>=DATE_ADD(current_date(),INTERVAL -14 Day)  "
                    . " order by LR.Timestamp desc");?>
<table class="table_new" width='80%'>
    <thead>
        <td>DateTime</td><td>Action</td><td>Competition</td><td>Event</td><td>Round</td><td>Name</td><td>Who did it</td>
    </thead>
    <?php foreach(DataBaseClass::getRows() as $row){ ?>
    <tr>
        <td><?= $row['Timestamp']?></td>
        <td>
            <?= str_replace(
                    ['x','-','*','+','!','C ','D ','S '],
                    ['Delete','Remove','New','Add','Link',
                        '<i class="far fa-user"></i> ',
                        '<i class="far fa-user-tie"></i> ',
                        '<i class="far fa-list-alt"></i> '],
                    $row['Action']) ?>
        </td>
        <td><?= $row['Competition']?></td>
        <td><?= ImageEvent($row['CodeScript'])?> <?= $row['Name'] ?></td>
        <td class="table_new_center"><?= $row['Round'] ?></td>
        <td><?= str_replace([": ",","],[":<br>","<br>"],$row['Details'])?></td>
        <td><?= str_replace(['Competitor: ','Delegate: ','ScoreTaker'],
                ['<i class="far fa-user"></i> ','<i class="far fa-user-tie"></i> ','<i class="far fa-list-alt"></i> ScoreTaker'],
                $row['Doing'] ) ?></td>
    </tr>
    <?php } ?>
</table>