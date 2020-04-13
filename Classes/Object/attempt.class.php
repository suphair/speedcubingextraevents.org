<?php

Class Attempt {

    public $value = false;
    public $out = false;
    public $except = false;
    public $record = false;

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
            if ($attempt->worldRecord) {
                $this->record = 'world';
            } elseif ($attempt->continentRecord) {
                $this->record = 'continent';
            } elseif ($attempt->countryRecord) {
                $this->record = 'country';
            }
        }
    }

    function getByTeamIdNumber($teamId, $number) {
        $attempt = Attempt_data::getByTeamIdNumber($teamId, $number);
        if ($attempt and $attempt != new stdClass()) {
            $this->out = $attempt->out;
            $this->except = $attempt->except;
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

}
