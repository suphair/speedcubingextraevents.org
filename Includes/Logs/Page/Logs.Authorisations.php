<?php IncludePage('Logs_navigator')?>
<h1>Logs authorisations</h1>
<table class='table_new'>
<thead>
<tr>
    <td>DateTime</td>
    <td>Competitor</td>
    <td>Country</td>
    <td>Action</td>
    <td>WCA ID <i class="fas fa-external-link-alt"></i></td>
    <td>WID <i class="fas fa-external-link-alt"></i></td>
</tr>
</thead>
<tbody>
    <?php DataBaseClass::Query("Select "
            . " A.Object, A.ID, Country.ISO2 Country_ISO2,  Country.Name Country_Name, C.ID Competitor_ID, C.Name Competitor_Name, A.Timestamp,"
            . " C.WID, C.WCAID "
            . " from WCAauth A "
            . " join Competitor C on C.WID=A.WID "
            . " left outer join Country on Country.ISO2=C.Country "
            . " where date(A.Timestamp)>=DATE_ADD(current_date(),INTERVAL -14 Day)"
            . " union "
            . " Select L.Object Object, CONCAT('00',L.ID) ID, Country.ISO2 Country_ISO2,  Country.Name Country_Name, C.ID Competitor_ID, C.Name Competitor_Name,L.DateTime,"
            . " C.WID, C.WCAID "
            . " from Logs L "
            . " join Competitor C on C.WID=L.Competitor "
            . " left outer join Country on Country.ISO2=C.Country "
            . " where L.Action='Login' and L.Object='Alternative' "
            . " and date(L.DateTime)>=DATE_ADD(current_date(),INTERVAL -14 Day)"
            . " union "
            . " Select L.Object Object, CONCAT('00',L.ID) ID, Country.ISO2 Country_ISO2,  Country.Name Country_Name, C.ID Competitor_ID, C.Name Competitor_Name,L.DateTime,"
            . " C.WID, C.WCAID "
            . " from Logs L "
            . " join Competitor C on C.WID=L.Competitor "
            . " left outer join Country on Country.ISO2=C.Country "
            . " where L.Action='Logout' and L.Object='WCA_Auth' "
            . " and date(L.DateTime)>=DATE_ADD(current_date(),INTERVAL -14 Day)"
            . "  order by Timestamp desc");
        foreach(DataBaseClass::getRows() as $log){ ?>
<tr>
<td><?= $log['Timestamp'] ?></td>
<td><a href="<?= LinkCompetitor($log['Competitor_ID']) ?>"><?= $log['Competitor_Name'] ?></a></td>
<td><?php ImageCountry($log['Country_ISO2'],20)?> <?= $log['Country_Name']?></td>
<?php if($log['Object']=='Alternative'){ ?>
    <td><i class="fas fa-user-secret"></i> Altenative</td>
<?php }elseif($log['Object']=='WCA_Auth'){ ?>
    <td><i class="fas fa-sign-out-alt"></i> Logout</td>
<?php }else{ ?>
    <td><i class="fas fa-sign-in-alt"></i> Auth</td>
<?php } ?>             
    
    <td><a target="_blank" href="https://www.worldcubeassociation.org/persons/<?= $log['WCAID'] ?>"><?= $log['WCAID'] ?></a></td>
    <td class="table_new_right"><a target="_blank" href="https://www.worldcubeassociation.org/api/v0/users/<?= $log['WID'] ?>"><?= $log['WID'] ?></a></td>
        <?php } ?>    
</tbody>    
</table>
