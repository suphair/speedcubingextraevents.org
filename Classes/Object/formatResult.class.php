<?php

Class FormatResult {

    public $id = false;
    public $time = false;
    public $name = false;
    public $amount = false;

    function getById($formatResultId) {
        $formatResult = FormatResult_data::getById($formatResultId);
        if ($formatResult and $formatResult != new stdClass()) {
            $this->id = $formatResult->id;
            if (strpos($formatResult->format, 'A') !== false) {
                $this->amount = true;
            }

            if (strpos($formatResult->format, 'T') !== false) {
                $this->time = true;
            }
            $this->name = $formatResult->name;
        }
    }

}
