<?php
$requests = getRequest();
if (!isset($requests[2]) or!is_numeric($requests[2])) {
    echo 'Wrong event ID';
    exit();
} else {
    $ID = $requests[2];
}

if (isset($row['Event_Competition'])) {
    $Competition = $row['Event_Competition'];
} else {
    $Competition = -1;
}
RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings', $Competition);

Databaseclass::FromTable('Event', "ID='$ID'");
Databaseclass::Join_current('DisciplineFormat');
Databaseclass::Join_current('Discipline');
Databaseclass::Join('DisciplineFormat', 'Format');
Databaseclass::Join('Event', 'Competition');
$data = Databaseclass::QueryGenerate(false);
$Discipline = $data['Discipline_Code'];
$Attemption = $data['Format_Attemption'];

$s = $data['Event_Groups'] * ($data['Format_Attemption'] + 2);
?>

<head>
    <script src="<?= PageIndex() ?>/Script/fifteen_generator.js" type="text/javascript"></script>
</head>
<form hidden method="POST" ID="form" action="<?= PageAction('CompetitionEvent.Scramble.Edit') ?>">
    <input name="ID" type="hidden" value="<?= $data['Event_ID'] ?>" />
    <textarea id="Scrambles" cols="60" rows="30" name="Scrambles"></textarea><br>
    <input style="background-color:lightgreen" type="submit" value="Set new scrambles">
</form>

<script>
    scrambles = getscrambles(<?= $s ?>);
    document.getElementById('Scrambles').value = scrambles.join('\n');
    document.getElementById('form').submit();
</script>  

<?php
exit();
