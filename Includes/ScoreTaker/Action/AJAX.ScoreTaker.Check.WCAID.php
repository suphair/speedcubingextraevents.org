<?php

$return = (object) [];

$wcaid = strtoupper(
        DataBaseClass::Escape(
                filter_input(
                        INPUT_GET, 'wcaid'
                )
        )
);

$competitionId = filter_input(
        INPUT_GET, 'competition', FILTER_VALIDATE_INT
);

if (!$wcaid or ! $competitionId) {
    $return->status = 'error';
    $return->message = 'Invalid input parameters';
    echo json_encode($return);
    exit();
}
$competition = new Competition();
$competition->getById($competitionId);

if (!$competition->id) {
    $return->status = 'error';
    $return->message = 'Сompetitions not found';
    echo json_encode($return);
    exit();
}

if (!CheckAccess('Competition.Event.Settings', $competition->id)) {
    $return->status = 'error';
    $return->message = 'Access denied';
    echo json_encode($return);
    exit();
}

$return->wcaid = $wcaid;

$registration = DataBaseClass::getRowObject("
    SELECT Competitor.Name name 
    FROM Registration
    JOIN Competitor 
        ON Competitor.ID = Registration.Competitor 
    WHERE Registration.Competition = {$competition->id} 
        AND Competitor.WCAID = '{$wcaid}'");

if ($registration != new stdClass()) {
    $return->status = 'done';
    $return->message = "{{$registration->name}} is already registered";
    echo json_encode($return);
    exit();
}

$competitor = new Competitor();
$competitor->getByWcaid($wcaid);

DataBaseClass::activateWca();
$competitorWCA = DataBaseClass::getRowObject(" 
            SELECT 
                Persons.id,
                Persons.name,
                Countries.iso2
            FROM Persons
            JOIN Countries ON Countries.id = Persons.countryId 
            WHERE Persons.id = '$wcaid'
        ");
DataBaseClass::activateSee();

if ($competitorWCA != new stdClass() and $competitorWCA->id) {
    if (!$competitor->id) {
        DataBaseClass::Query("
            INSERT INTO Competitor (
                   Name,
                   WCAID,
                   Country
            ) VALUES (
                   '" . Short_Name($competitorWCA->name) . "',
                   '{$competitorWCA->id}',
                   '{$competitorWCA->iso2}'
            )
        ");
    }
    $competitor->getByWcaid($wcaid);

    $return->status = 'find';
    $return->message = "{$competitor->name} {$competitor->country->image}";
    echo json_encode($return);
    exit();
}

$person = getPersonWcaApi($wcaid, 'ScoreTaker.AddCompetitor');
if (!$person) {
    $return->status = 'error';
    $return->message = "{$wcaid} not found on the WCA";
    echo json_encode($return);
    exit();
}

$country = new Country();
$country->getByCode($person->country_iso2);

$return->status = 'find';
$return->message = "{$person->name} {$country->image}";
echo json_encode($return);
exit();
