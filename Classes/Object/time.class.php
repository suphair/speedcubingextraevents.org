<?php

Class Time {

    public $out = false;
    public $second = 0;
    public $minute = 0;
    public $enable = false;
    public $inMilliseconds = 0;

    function set($minute, $second) {
        $this->enable = false;
        $this->out = false;
        if (!is_numeric($minute) or ! is_numeric($second)) {
            return;
        }
        if ($minute < 0 or $minute >= 60) {
            return;
        }
        if ($second < 0 or $second >= 60) {
            return;
        }

        $this->minute = $minute;
        $this->second = $second;
        $this->inMilliseconds = $minute * 60 * 100 + $second * 100;

        if ($minute > 0 or $second > 0) {
            $this->enable = true;
            $this->out = sprintf("%d:%'.02d", $minute, $second);
        }
    }

}
