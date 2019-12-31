<H1><?= ml('Access.Title'); ?></h1>

<?php
$GrandRoles=[];
DataBaseClass::Query("Select * from `GrandRole` order by Level");
foreach(DataBaseClass::getRows()as $row){
    for($i=1;$i<=$row['Level'];$i++){
       $GrandRoles[$i][]= $row['Name'];
    }
}

DataBaseClass::Query("Select Type,Competition,GR.Level, '' as `Group`, '' Name, GA.Description from `GrandAccess` GA
left outer join GrandRole GR on GR.Level=GA.Level where GA.Level is not null

union 
select Type, '' Competition, '' Level, GG.Name `Group`, D.Name,GA.Description  from `GrandAccess` GA join
`GrandGroup` GG on GG.ID=GA.Group 
join `GrandGroupMember` GGM on GGM.Group=GG.ID
join Delegate D on D.ID=GGM.Delegate
order by 1,2");
?>
<table>
    <tr class="tr_title">
        <td>Description</td>
        <td colspan="2">Have the right</td>
        <td>Code</td>
    </tr>
<?php foreach(DataBaseClass::getRows() as $row){ ?>
    <tr>
        <td class='border-right-solid'><?= $row['Description'] ?></td>
        <td><?php if($row['Competition']){?><img width='15px' src='<?= PageIndex() ?>Image/Icons/competitions.png'><?php } ?></td>
        <td>
            <?= isset($GrandRoles[$row['Level']])?implode(", ",$GrandRoles[$row['Level']]):''?>
            <span class='message'><?= $row['Name'] ?></span>
        </td>
        <td class='border-left-solid'><?= $row['Type'] ?></td>
    </tr>
<?php }?>
</table>