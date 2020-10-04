<?php

RequestClass::CheckAccessExit(__FILE__, 'Event.Add');

CheckPostIsset('Name', 'Code');
CheckPostNotEmpty('Name', 'Code');

$Name = $_POST['Name'];
$Code = str_replace(" ", "", $_POST['Code']);
$Delegate = getDelegate();

DataBaseClass::Query("Insert into  `Discipline` ( Name,Code,Status,CodeScript) VALUES('$Name','$Code','Archive','$Code')");


SendMail(Suphair \ Config :: get('Seniors', 'email'), 'SEE: New event ' . $Name, "<pre>" . $Delegate['Delegate_Name'] . " <br>Event <a href='" . PageIndex() . "/Event/$Code'>$Name</a>");

AddLog("Event", "Create", $Delegate['Delegate_Name'] . ' / ' . $Name);
SetMessage("Event create $Name");

$url = PageIndex() . "/Event/" . $Code . "/Settings";

header('Location: ' . $url);
exit();
