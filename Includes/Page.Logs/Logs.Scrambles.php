<?php IncludePage('Logs_navigator')?>
<h1>Logs scrambles</h1>

  <table class='table_new' width='80%'>
        <thead>
            <td>DateTime</td>
            <td>Scrambles</td>
            <td>Status</td>
            <td>Competition</td>
            <td>Event</td>
            <td>Round</td>
            <td>Delegate</td>
            <td>Action</td>
        </thead>
        <tbody>
    <?php DataBaseClass::Query(""
            . "Select SP.Action, SP.Timestamp, D.WCA_ID,D.Name, SP.Secret,E.ScrambleSalt,E.ScramblePublic, Discipline.Code, Discipline.CodeScript, Discipline.Name, E.Round,D.Name Delegate,C.Name Competition "
            . " from ScramblePdf SP "
            . " join Event E on SP.Event=E.ID "
            . " join Competition C on C.ID=E.Competition "
            . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat "
            . " join Discipline on Discipline.ID=DF.Discipline "
            . " join Delegate D on D.ID=SP.Delegate "
            . " where date(SP.Timestamp)>=DATE_ADD(current_date(),INTERVAL -14 Day)  "
            . " order by SP.Timestamp desc");
    foreach(DataBaseClass::getRows() as $row){ ?>
        <tr>
            <td><?= $row['Timestamp'] ?></td>
            <td>
                <?php if(file_exists("Image/Scramble/".$row['Secret'].".pdf")){ ?>
                    <a target="_blank" href="<?= PageIndex()?>Scramble/<?= $row['Secret'] ?>"><?= $row['Secret'] ?></a>
                <?php }else{ ?>
                    <?= $row['Secret'] ?>
                <?php } ?>    
            </td>
            <td>
            <?php if($row['ScrambleSalt']==$row['Secret']){ ?>
                <i class="fas fa-file-image"></i> Actual
            <?php } ?>
            <?php if($row['ScramblePublic']==$row['Secret']){ ?>
                <i class="fas fa-upload"></i> Published
            <?php } ?>
            <td><?= $row['Competition'] ?></td></td>
            <td><?= ImageEvent($row['CodeScript'])?> <?= $row['Name'] ?></td>
            <td><?= $row['Round'] ?></td>
            <td><a href="<?= LinkDelegate($row['WCA_ID'])?>"><?= $row['Delegate'] ?></a></td>
            <td><?= $row['Action'] ?></td>


    </tr>
    <?php } ?>
    </tbody>
    </table>