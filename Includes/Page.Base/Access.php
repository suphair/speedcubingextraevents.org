<H1>Access</h1>

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
<table class="table_new">
    <thead>
    <tr>
        <td>Description</td>
        <td>Competition</td>
        <td>Have the right</td>
        <td>Code</td>
    </tr>
    </thead>
    <tbody>
<?php foreach(DataBaseClass::getRows() as $row){ ?>
    <tr>
        <td><?= $row['Description'] ?></td>
        <td><?php if($row['Competition']){?>Competition<?php } ?></td>
        <td>
            <?= isset($GrandRoles[$row['Level']])?implode(", ",$GrandRoles[$row['Level']]):''?>
            <?php if($row['Name']){ ?>
                <i class="fas fa-user-tie"></i> <?= $row['Name'] ?>
            <?php } ?>
        </td>
        <td><?= $row['Type'] ?></td>
    </tr>
<?php }?>
    <tbody>
</table>