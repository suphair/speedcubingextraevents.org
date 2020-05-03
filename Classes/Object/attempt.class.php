<?php

Class Attempt {

    CONST SINGLE = 'single';
    CONST AVERAGE = 'average';

    public $out = false;
    public $team = false;
    public $time = false;
    public $value = false;
    public $amount = false;
    public $except = false;
    public $record = false;
    public $format = false;
    public $warning = false;

    function __construct($attempt = false) {
        if (isset($attempt->value)) {
            $this->value = $attempt->value;
        }
        if (isset($attempt->out)) {
            $this->out = $attempt->out;
        }
    }

    static function getFormat($format) {
        return Attempt_data::getFormat($format);
    }

    function getByTeamIdFormat($teamId, $format, $unofficial = false) {
        $attempt = Attempt_data::getByTeamIdFormat($teamId, $format);
        if ($attempt and $attempt != new stdClass()) {
            if (!$unofficial) {
                $this->value = $attempt->value;
            }
            $this->out = $attempt->out;
            $this->record = self::getRecordType($attempt);
        }
    }

    function getByTeamIdNumber($teamId, $number) {
        $attempt = Attempt_data::getByTeamIdNumber($teamId, $number);
        if ($attempt and $attempt != new stdClass()) {
            $this->out = $attempt->out;
            if($attempt->isDnf){
               $this->time = 'DNF'; 
            }elseif($attempt->isDns){
                $this->time = 'DNS';
            }else{
                $this->time = sprintf("%d:%'.02d.%'.02d", $attempt->minute, $attempt->second,$attempt->millisecond);
            }
            $this->except = $attempt->except;
            $this->amount = (int)$attempt->amount;
        }
    }

    static function updateRecords() {
        $formats = [
            'average',
            'single'
        ];
        $types = [
            Attempt_Data::WORLD,
            Attempt_Data::COUNTRY,
            Attempt_Data::CONTINENT
        ];
        foreach ($types as $type) {
            foreach ($formats as $format) {
                $records = [];
                $recordsValue = [];
                foreach (Attempt_Data::getRecords($format, $type) as $record) {
                    $eventId = $record->eventID;
                    $key = "$eventId {$record->countryCode} {$record->continentCode}";
                    if (!isset($recordsValue[$key])) {
                        $recordsValue[$key] = $record->attemptValue;
                        $records[] = $record;
                    }

                    if ($recordsValue[$key] > $record->attemptValue) {
                        $recordsValue[$key] = $record->attemptValue;
                        $records[] = $record;
                    }
                }

                Attempt_Data::clearRecords($format, $type);
                Attempt_Data::updateRecords($records, $format, $type);
            }
        }
    }

    static function getRecordsByEventIdCountryCode($eventId, $countryCode) {
        return self::getRecordsByRecordsId(
                        Attempt_Data::getAttemptIdRecordByEventIdFilter(
                                $eventId, Attempt_data::COUNTRY, $countryCode));
    }

    static function getRecordsByEventIdContinentCode($eventId, $continentCode) {
        return self::getRecordsByRecordsId(
                        Attempt_Data::getAttemptIDRecordByEventIdFilter(
                                $eventId, Attempt_data::CONTINENT, $continentCode));
    }

    static function getRecordsByEventIdWorld($eventId) {
        return self::getRecordsByRecordsId(
                        Attempt_Data::getAttemptIDRecordByEventIdFilter(
                                $eventId, Attempt_data::WORLD, false));
    }

    static function getRecordsByRecordsId($attemptsID) {
        $attempts = [];
        foreach ($attemptsID as $attemptID) {
            $attempt = new Attempt();
            $attempt->getById($attemptID);
            $attempts[] = $attempt;
        }
        return $attempts;
    }

    function getById($attemptID) {
        $attempt = Attempt_Data::getById($attemptID);
        $this->out = $attempt->out;
        $this->value = $attempt->out;
        $this->except = $attempt->out;
        $this->record = self::getRecordType($attempt);
        $team = new Team();
        $team->getById($attempt->teamId);
        $this->team = $team;
        $this->format = $attempt->format;
    }

    function getRecordType($attempt) {
        if ($attempt->worldRecord) {
            return Attempt_Data::WORLD;
        } elseif ($attempt->continentRecord) {
            return Attempt_Data::CONTINENT;
        } elseif ($attempt->countryRecord) {
            return Attempt_Data::COUNTRY;
        }
    }

    static function getCountriesCodeForAttempts() {
        return Attempt_data::getCountriesCodeForAttempts();
    }

    static function getContinentsCodeForAttempts() {
        return Attempt_data::getContinentsCodeForAttempts();
    }

}
