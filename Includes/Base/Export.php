<?php

$directories = [
    'SQL' => 'Export_sql',
    'TSV' => 'Export_tsv'
];

$files = [];
foreach ($directories as $format => $directory) {
    foreach (scandir($directory) as $file) {
        if (strpos($file, '.zip') == false) {
            continue;
        }
        $filename = "$directory/$file";
        $files[] = (object) [
                    'format' => $format,
                    'name' => $file,
                    'link' => PageIndex() .'/'. $filename,
                    'time' => date('F d Y H:i:s', filectime($filename)),
                    'size' => round(filesize($filename) / 1024, 1)
        ];
    }
}

$data = arrayToObject([
    'files' => $files
        ]);

IncludeClass::Template('Export', $data);
