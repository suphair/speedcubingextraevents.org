<?php

$filenames = [];
foreach (scandir('Svg') as $filename) {
    if (strpos($filename, ".svg")) {
        $filenames[] = $filename;
    }
}
$data = arrayToObject([
    'filenames' => $filenames
        ]);

IncludeClass::Template('Icons', $data);
