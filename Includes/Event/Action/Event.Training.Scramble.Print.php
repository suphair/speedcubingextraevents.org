<?php

$code = filter_input(INPUT_POST, 'code');
$name = filter_input(INPUT_POST, 'name');
$groups = filter_input(INPUT_POST, 'groups', FILTER_VALIDATE_INT
        , ['options' => ['min_range' => 1, 'max_range' => 4]]);
$attemptions = filter_input(INPUT_POST, 'attemptions', FILTER_VALIDATE_INT
        , ['options' => ['min_range' => 1, 'max_range' => 5]]);


$event = new Event;
$event->getByCode($code);
if (!$event->id or!$groups or!$attemptions) {
    die("Wrong values");
}

$scrambles = [];
$extra = 2;
if ($attemptions < 5) {
    $extra = 1;
}
for ($attemption = 1; $attemption <= $attemptions + $extra; $attemption++) {
    for ($group = 0; $group < $groups; $group++) {
        $scramble = GenerateScramble($event->codeScript, true);
        if ($scramble) {
            $scrambles[$group][$attemption] = $scramble;
        }
    }
}

$pdf = ScramblePrint::getPDF(
                $name
                , $scrambles
                , $event->codeScript
                , $event->scrambleComment
                , $event->name
                , $event->isCup
                , $event->isCut
                , ''
                , $attemptions);

$pdf->Output($name . $code . ".pdf");
$pdf->Close();
exit();
