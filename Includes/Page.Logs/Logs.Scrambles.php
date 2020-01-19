<?php IncludePage('Logs_navigator')?>
<h1><img src='<?= PageIndex()?>Image/Icons/scramble.png' width='30px'> Logs scrambles</h1>
    <table>
    <?php DataBaseClass::Query(""
            . "Select Cn.WCA, SP.Action, SP.Timestamp, D.Name, SP.Secret,E.ScrambleSalt,E.ScramblePublic, Discipline.Code,Discipline.CodeScript, Discipline.Name, E.vRound,D.Name Delegate from ScramblePdf SP "
            . " join Event E on SP.Event=E.ID "
            . " join Competition Cn on Cn.ID=E.Competition"
            . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat "
            . " join Discipline on Discipline.ID=DF.Discipline "
            . " join Delegate D on D.ID=SP.Delegate "
            . " where date(SP.Timestamp)>=DATE_ADD(current_date(),INTERVAL -14 Day)  "
            . " order by SP.Timestamp desc");
    foreach(DataBaseClass::getRows() as $row){ ?>
        <tr>
            <td>
                <?php if(file_exists("Image/Scramble/".$row['Secret'].".pdf")){ ?>
                    <td>
                        <a href="<?= LinkCompetition($row['WCA']) ?>"><?= $row['WCA'] ?></a>
                    </td>
                    <td>
                    <?php if($row['ScrambleSalt']==$row['Secret']){ ?>
                        <?= svg_blue(10); ?>Last
                    <?php } ?>
                    <?php if($row['ScramblePublic']==$row['Secret']){ ?>
                        <?= svg_green(10); ?>Public 
                    <?php } ?>
                     </td>   
                <?php }else{ ?>
                    <td>
                        <?= $row['Secret'] ?>
                    </td>
                    <td>
                     <?php if($row['ScrambleSalt']==$row['Secret']){ ?>
                        <?= svg_red(10); ?>Last
                    <?php } ?>
                    <?php if($row['ScramblePublic']==$row['Secret']){ ?>
                        <?= svg_red(10); ?>Public 
                    <?php } ?>
                     </td>      
                <?php } ?>
            </td>
            <td><?= ImageEvent($row['CodeScript'],25)?> <?= $row['Name'] ?><?= $row['vRound'] ?></td>
            <td class="border-right-dotted border-left-dotted"><?= $row['Timestamp'] ?></td>
            <td class="border-right-solid"><?= $row['Delegate'] ?></td>
            <td><a target="_blank" href="<?= PageIndex()?>Scramble/<?= $row['Secret'] ?>"><?= $row['Action'] ?></a></td>                
                

    </tr>
    <?php } ?>
    </table>