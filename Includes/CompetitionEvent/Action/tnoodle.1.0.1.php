<?php
$myCurl = curl_init();

$data = [
    "wcif" => [
        "formatVersion" => "1.0",
        "name" => "test",
        "shortName" => "test",
        "id" => "test",
        "events" => [
            0 => ["id" => "333",
                "rounds" => [
                    0 => [
                        "format" => "a",
                        "id" => "333-r1",
                        "scrambleSetCount" => 1
                    ]
                ]
            ]
        ],
        "persons" => [],
        "schedule" => [
            "numberOfDays" => 0,
            "venues" => []
        ]
    ]
];
$data_string = json_encode($data);
?>
<script>
    $.ajax({
        url: 'http://localhost:2014/wcif/zip',
        type: 'POST',
        data: '<?= $data_string ?>',
        contentType: 'application/json',
        success: function (data, status, xhr) {
            var blob = new Blob([data], {type: xhr.getResponseHeader('Content-Type')});
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'download.zip';
            link.click();
        }
    });
</script>