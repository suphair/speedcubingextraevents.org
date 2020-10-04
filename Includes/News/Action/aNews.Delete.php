<?php

RequestClass::CheckAccessExit(__FILE__, 'aNews');
CheckPostIsset('ID');
CheckPostNotEmpty('ID');
CheckPostIsNumeric('ID');

$ID = $_POST['ID'];

DataBaseClass::Query("Delete from `News` where ID=$ID");

header('Location: ' . PageIndex() . "/News");
exit();
