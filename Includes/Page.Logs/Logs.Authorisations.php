<?php IncludePage('Logs_navigator')?>
<h1><img src='<?= PageIndex()?>Image/Icons/persons.png' width='30px'> Logs Authorisations</h1>
<table style="white-space:nowrap" >
<thead>
<tr class="tr_title">
    <td>DateTime</td>
    <td>Competitor</td>
    <td>Country</td>
</tr>
</thead>
    <?php DataBaseClass::Query("Select "
            . " A.Object, A.ID, Country.ISO2 Country_ISO2,  Country.Name Country_Name, C.ID Competitor_ID, C.Name Competitor_Name, A.Timestamp"
            . " from WCAauth A "
            . " join Competitor C on C.WID=A.WID "
            . " left outer join Country on Country.ISO2=C.Country "
            . " where date(A.Timestamp)>=DATE_ADD(current_date(),INTERVAL -14 Day)"
            . " union "
            . " Select L.Object Object, CONCAT('00',L.ID) ID, Country.ISO2 Country_ISO2,  Country.Name Country_Name, C.ID Competitor_ID, C.Name Competitor_Name,L.DateTime"
            . " from Logs L "
            . " join Competitor C on C.WID=L.Competitor "
            . " left outer join Country on Country.ISO2=C.Country "
            . " where L.Action='Login' and L.Object='Alternative' "
            . " and date(L.DateTime)>=DATE_ADD(current_date(),INTERVAL -14 Day)"
            . " union "
            . " Select L.Object Object, CONCAT('00',L.ID) ID, Country.ISO2 Country_ISO2,  Country.Name Country_Name, C.ID Competitor_ID, C.Name Competitor_Name,L.DateTime"
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
    <td><span class="message">Altenative</span></td>
<?php }elseif($log['Object']=='WCA_Auth'){ ?>
    <td><span class="error">Logout</span></td>
<?php }else{ ?>
    <td><a href="#" onclick="$(this).hide(); $('#LogID<?= $log['ID'] ?>').show(); return false;">Auth</a>
    <tr  ID="LogID<?= $log['ID'] ?>"  style="display: none">    
    <td colspan="4" width="400px">
    <div class="block_comment">
        <?= str_replace(",",",<br>",$log['Object']); ?>
    </div>
    </td>
    </tr>
<?php } ?>             
        <?php } ?>         
</table>
