<?php

Class Attempt_data {

    CONST WORLD = 'world';
    CONST COUNTRY = 'country';
    CONST CONTINENT = 'continent';
    CONST ATTEMPT_BASE_FILTER = ' AND Attempt.Special IS NOT NULL';

    public static function getFormat($format) {
        if ($format == 'average') {
            return " AND Attempt.Special IN ('Average', 'Mean')";
        }

        if ($format == 'single') {
            return " AND Attempt.Special IN ('Best', 'Sum')";
        }
        return " AND 1 = 2";
    }

    public static function getByTeamIdFormat($teamId, $format) {
        if (!is_numeric($teamId)) {
            return false;
        }

        return self::getByFilter(
                        "WHERE Command = $teamId"
                        . self::getFormat($format));
    }

    public static function getByTeamIdNumber($teamId, $number) {
        if (!is_numeric($teamId) or ! is_numeric($number)) {
            return false;
        }

        return self::getByFilter(
                        "WHERE Command = $teamId  "
                        . "AND Attempt = $number");
    }

    private static function getByFilter($filter) {
        return DataBaseClass::getRowObject("
            SELECT 
                vOut 'out',
                Minute minute,
                Second second,
                Milisecond millisecond,
                IsDNF isDnf,
                IsDNS isDns,
                Command teamId,
                vOrder value,
                Amount amount,
                Except except,
                worldRecord,
                countryRecord,
                continentRecord,
                CASE Special
                    WHEN 'Sum' THEN 'single'
                    WHEN 'Best' THEN 'single'
                    WHEN 'Mean' THEN 'average'
                    WHEN 'Average' THEN 'average'
                END format    
            FROM Attempt 
            $filter   
        ");
    }

    static function getRecords($format, $type) {

        $select = ",1 countryCode, 1 continentCode";
        $where = '';
        $group = '';
        switch ($type) {
            case self::COUNTRY:
                $select = ",LOWER(Country.ISO2) countryCode, 1 continentCode";
                $where = "AND Country.ISO2 IS NOT NULL";
                $group = ",Country.ISO2";
                break;
            case self::CONTINENT:
                $select = ",1 countryCode ,LOWER(Continent.Code) continentCode";
                $where = "AND Continent.Code IS NOT NULL";
                $group = ",Continent.Code";
                break;
        }

        return DataBaseClass::getRowsObject("
        SELECT 
            MIN(Attempt.vOrder) attemptValue, 
            Discipline.ID eventID,
            Competition.EndDate endDate
            $select
        FROM 
            " . self::SqlRecord() . "
        WHERE 1 = 1
            " . self::getFormat($format) . "
            AND Attempt.IsDNF = 0
            AND Attempt.IsDNS = 0
            " . Competition_data::COMPETIITON_OFFICIAL . "
            $where
        GROUP BY 
            Discipline.ID,
            Competition.EndDate
            $group
        ORDER BY 
            endDate, 
            attemptValue 
        ");
    }

    static function updateRecords($records, $format, $type) {

        foreach ($records as $record) {
            $where = '';
            switch ($type) {
                case self::WORLD:
                    $update = "worldRecord = 1";
                    break;
                case self::COUNTRY:
                    $update = "countryRecord = 1";
                    $where = " AND LOWER(Country.ISO2) = '{$record->countryCode}'";
                    break;
                case self::CONTINENT:
                    $update = "continentRecord = 1";
                    $where = " AND LOWER(Continent.Code) = LOWER('{$record->continentCode}')";
                    break;
            }

            DataBaseClass::Query("
                UPDATE 
                " . self::SqlRecord() . "
                SET $update
                WHERE 1 = 1
                    " . self::getFormat($format) . "
                    " . Competition_data::COMPETIITON_OFFICIAL . "
                    AND Attempt.vOrder = {$record->attemptValue}
                    AND Competition.EndDate = '{$record->endDate}'    
                    AND Discipline.ID = {$record->eventID}   
                    $where    
            ");
        }
    }

    static function clearRecords($format, $type) {
        switch ($type) {
            case self::WORLD:
                $update = "worldRecord = 0";
                break;
            case self::COUNTRY:
                $update = "countryRecord = 0";
                break;
            case self::CONTINENT:
                $update = "continentRecord = 0";
                break;
        }

        DataBaseClass::Query("
            UPDATE 
            " . self::SqlRecord() . "
            SET $update
            WHERE 1 = 1
                " . self::getFormat($format) . "
        ");
    }

    private static function SqlRecord() {
        return "
            Attempt            
            JOIN Command ON Command.ID = Attempt.Command
            JOIN Event ON Event.ID = Command.Event
            JOIN Competition ON Competition.ID = Event.Competition
            JOIN DisciplineFormat ON DisciplineFormat.ID = Event.DisciplineFormat
            JOIN Discipline ON Discipline.ID = DisciplineFormat.Discipline
            LEFT JOIN Country ON Command.vCountry = Country.ISO2
            LEFT JOIN Continent ON Continent.Code = Country.Continent
        ";
    }

    static function getAttemptIdRecordByEventIdFilter($eventId, $type, $filter) {
        $filterType = "";
        $filterValue = "";
        switch ($type) {
            case self::WORLD:
                $filterType = " AND worldRecord = 1 ";
                break;
            case self::COUNTRY:
                $filterType = " AND countryRecord = 1 ";
                $filterValue = " AND LOWER(Country.ISO2) = LOWER('$filter') ";
                break;
            case self::CONTINENT:
                $filterType = " AND continentRecord = 1";
                $filterValue = " AND LOWER(Continent.Code) = LOWER('$filter') ";
                break;
        }

        return DataBaseClass::getColumn("
            SELECT 
                Attempt.ID
            FROM
            " . self::SqlRecord() . "
            WHERE 1 = 1
            $filterType
            $filterValue
           AND Discipline.ID = $eventId        
        ");
    }

    static function getById($attemptId) {
        return self::getByFilter(" WHERE ID = $attemptId ");
    }

    static function getCountriesCodeForAttempts() {
        return DataBaseClass::getColumn("
            SELECT DISTINCT 
                LOWER(Command.vCountry) countryCode
            FROM 
                " . self::SqlRecord() . "
            WHERE 1 = 1
                AND Command.vCountry != '' 
                AND Attempt.Special IS NOT NULL
                " . Competition_data::COMPETIITON_OFFICIAL . "
        ");
    }

    static function getContinentsCodeForAttempts() {
        return DataBaseClass::getColumn("
            SELECT DISTINCT 
                LOWER(Continent.Code) continentCode
            FROM 
                " . self::SqlRecord() . "
            WHERE 1 = 1
                AND Continent.Code IS NOT NULL
                AND Attempt.Special IS NOT NULL
                " . Competition_data::COMPETIITON_OFFICIAL . "
        ");
    }

}
