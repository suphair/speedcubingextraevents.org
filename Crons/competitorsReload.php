<?php

$limitApi = 25;
$limitBd = 250;
$depth = 14;


# Updating Countries And Continents

DataBaseClass::activateWca();

$continents = DataBaseClass::getRowsObject("
    SELECT 
        recordName continentCode,
        name continentName
    FROM 
    Continents
    ");

$countries = DataBaseClass::getRowsObject("
    SELECT 
        Countries.name countryName,
        Countries.iso2 countryISO2,
        Countries.id countryCode,
        Continents.recordName continentCode 
    FROM Countries
    JOIN Continents 
        ON Continents.id = Countries.continentid
    ");

DataBaseClass::activateSee();

foreach ($continents as $continent) {
    $continent->continentName = DataBaseClass::Escape($continent->continentName);
    DataBaseClass::Query("
        REPLACE INTO Continent (
            Code,
            Name)
        VALUES (
            '{$continent->continentCode}',
            '{$continent->continentName}')
        ");
}

foreach ($countries as $country) {
    $country->countryName = DataBaseClass::Escape($country->countryName);
    DataBaseClass::Query("
        REPLACE INTO Country (
            Code,
            ISO2,
            Name,
            Continent) 
        VALUES (
            '{$country->countryCode}',
            '{$country->countryISO2}',
            '{$country->countryName}',
            '{$country->continentCode}')
        ");
}

if ($limitApi) {

    #Updating a Competitor using getUserWcaApi by WID. With an empty WCAID.

    $usersWid = DataBaseClass::getRowsObject("
        SELECT 
            ID id, 
            WID wid 
        FROM Competitor
        WHERE 
            WID IS NOT NULL 
            AND WCAID = '' 
            AND TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth
        ORDER BY UpdateTimestamp 
        LIMIT $limitApi
    ");

    foreach ($usersWid as $user) {
        Competitors_Reload(
                $user->id, $user->wid
        );
    }

    #Updating a Competitor using getUserWcaApi by WID. With an empty WID.

    $usersWcaid = DataBaseClass::getRowsObject("
        SELECT 
            ID id, 
            WCAID wcaid 
        FROM Competitor
        WHERE  
            WID IS NULL 
            AND WCAID <> ''
            AND TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth
        ORDER BY UpdateTimestamp 
        LIMIT $limitApi
    ");

    foreach ($usersWcaid as $user) {
        Competitors_Reload(
                $user->id, $user->wcaid
        );
    }
}

if ($limitBd) {

    #Updating a Competitor using database WCA by WCAID.

    $usersDb = DataBaseClass::getRowsObject("
        SELECT 
            ID id, 
            WCAID wcaid, 
            Country country, 
            Name name
        FROM Competitor 
        WHERE 
            WCAID <> '' 
        AND TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth 
        ORDER BY UpdateTimestamp
        LIMIT $limitBd
    ");

    foreach ($usersDb as $user) {

        DataBaseClass::activateWca();
        $userWca = DataBaseClassWCA::getRowObject("
            SELECT 
                Persons.id wcaid,
                Countries.iso2 country,
                trim(SUBSTRING_INDEX(Persons.name,'(',1)) name 
            FROM Persons 
            JOIN Countries 
                ON Countries.id = Persons.countryId 
            WHERE Persons.id='{$user->wcaid}'
        ");
        DataBaseClass::activateSee();

        if (isset($userWca->wcaid)) {
            if ($userWca->country != $user->country) {
                AddLog(
                        'CompetitorsReload', 'Cron', "Update country {$user->wcaid}: {$user->country}->{$userWca->country}");
            }
            if ($userWca->name != $user->name) {
                AddLog(
                        'CompetitorsReload', 'Cron', "Update name {$user->wcaid}: {$user->name}->{$userWca->name}");
            }

            $userWca->name = DataBaseClass::Escape($userWca->name);
            DataBaseClass::Query("
                UPDATE Competitor 
                SET 
                    Country = '{$userWca->country}',
                    Name = '{$userWca->name}',
                    UpdateTimestamp = now() 
                WHERE ID='{$user->id}'
            ");
        } else {
            DataBaseClass::Query("
                UPDATE Competitor 
                SET 
                    UpdateTimestamp = now() 
                WHERE ID='{$user->id}'
            ");
        }
    }
}

Competitors_RemoveDuplicates();

$countWid = DataBaseClass::getValue("
    SELECT count(*)
    from Competitor 
    where
        WID is not null 
        AND WCAID = '' 
        AND TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth
    ");


$countWidTotal = DataBaseClass::getValue("
    SELECT count(*)
    from Competitor 
    where
        WID is not null 
        AND WCAID='' 
    ");

$countWcaid = DataBaseClass::getValue("
    SELECT count(*)
    from Competitor 
    where
        WID is null 
        AND WCAID <> '' 
        AND TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth
    ");


$countWcaidTotal = DataBaseClass::getValue("
    SELECT count(*)
    from Competitor 
    where
        WID is null 
        AND WCAID <> ''
    ");


$countDb = DataBaseClass::getValue("
    SELECT count(*)
    from Competitor 
    where
        WCAID <> '' 
        AND TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth
    ");


$_details['wid']=[
    'total' => $countWidTotal,
    'current' => $countWid
];

$_details['wcaid']=[
    'total' => $countWcaidTotal,
    'current' => $countWcaid
];

$_details['db']=[
    'current' => $countDb
];
