<?php

Class FormatResult_data {

    static function getById($formatResultId) {
        if (!is_numeric($formatResultId)) {
            return false;
        }
        return DataBaseClass::getRowObject("
            SELECT
                ID id,
                Name name,
                Format format
            FROM FormatResult
            WHERE ID=$formatResultId
        ");
    }

}
