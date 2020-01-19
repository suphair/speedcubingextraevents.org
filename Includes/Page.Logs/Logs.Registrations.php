<?php IncludePage('Logs_navigator')?>
<h1><img src='<?= PageIndex()?>Image/Icons/persons.png' width='30px'> Logs registrations</h1>
            <?php DataBaseClass::Query("Select "
                    . " Cn.WCA, D.Code, D.CodeScript, LR.Timestamp,LR.Action, LR.Doing,LR.Details "
                    . " from LogsRegistration LR "
                    . " join Event E on E.ID=LR.Event "
                    . " join Competition Cn on Cn.ID=E.Competition "
                    . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
                    . " join Discipline D on D.ID=DF.Discipline"
                    . " where date(LR.Timestamp)>=DATE_ADD(current_date(),INTERVAL -14 Day)  "
                    . " order by LR.Timestamp desc");?>
        <table style="font-size: 14px">
            <?php foreach(DataBaseClass::getRows() as $row){ ?>
            <tr>
                <td><?= $row['Timestamp']?></td>
                <td><span style="
                    <?php if(substr($row['Action'],2,1)=='x' or substr($row['Action'],2,1)=='-'){ ?>
                          color:var(--red)
                    <?php } ?>
                    <?php if(substr($row['Action'],2,1)=='*' or substr($row['Action'],2,1)=='+' ){ ?>
                          color:var(--green)
                    <?php } ?>
                    ">
                    <?= str_replace(
                            ['x','-','*','+'],
                            ['Del','Rem','New','Add'],$row['Action']) ?>
                    </span>
                </td>
                <td>
                    <a href="<?= LinkCompetition($row['WCA'])?>/<?=$row['Code']  ?>"><?= $row['WCA']?></a>
                </td>    
                <td width=400px class="border-left-dotted border-right-dotted">
                            <?= ImageEvent($row['CodeScript'],20)?>
                            <?= $row['Details']?>
                </td>
                <td><?= $row['Doing']?></td>
            </tr>
            <?php } ?>
        </table>
        <span class="badge">C</span> - Competitor <span class="badge">D</span> - Delegate <span class="badge">S</span> - ScoreTaker