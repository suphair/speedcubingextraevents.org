<?php
$filename = str_replace('.', '_', $data['Competition_WCA']) . "_" . $data['Discipline_Code'] . "_" . $data['Event_Round'];

$cup = strpos($data['Discipline_CodeScript'], '_cup') !== FALSE;

$mguild = strpos($data['Discipline_CodeScript'], 'mguild') !== FALSE;


$see_option = (object) [
            'id' => $data['Event_ID'],
            'code' => $data['Discipline_Code'],
            'groups' => $data['Event_Groups'],
            'teams' => $data['Event_Competitors'],
            'team_persons' => $data['Discipline_Competitors'],
            'attemption' => $data['Format_Attemption'],
            'cup' => $cup,
            'mguild' => $mguild,
            'cut' => $data['Discipline_CutScrambles'],
            'name' => $data['Discipline_Name'],
            'code_script' => $data['Discipline_CodeScript'],
            'tnoodle' => $data['Discipline_TNoodle'],
            'tnoodles' => $data['Discipline_TNoodles'],
            'tnoodles_mult' => $data['Discipline_TNoodlesMult'],
            'format' => $format,
            'competition_name' => $data['Competition_Name'],
            'competition_wca' => $data['Competition_WCA'],
            'competition_id' => $data['Competition_ID'],
            'view_round' => $data['Event_vRound'],
            'round' => $data['Event_Round']
];

$upload_action = [];
$upload_action['JSON'] = 'CompetitionEvent.Scramble.LoadFile';

if ($see_option->tnoodles) {
    $upload_action['PDF'] = 'EventSetGlueScrambles.TNoodlesPDF';
} else {
    $upload_action['PDF'] = 'EventSetGlueScrambles.TNoodlePDF';
}


if ($see_option->cup) {
    $see_option->extra = 0;
} elseif ($see_option->tnoodles) {
    $see_option->extra = 1;
} elseif ($see_option->attemption == 5) {
    $see_option->extra = 2;
} else {
    $see_option->extra = 1;
}


$upload_instructions = [
    'JSON' => '/Interchange/' . $see_option->code_script . '.json',
    'PDF' => '/Printing/' . $see_option->code_script . ' - All Scrambles.pdf'
];


if ($cup) {
    $see_option->groups = $see_option->teams;
    $see_option->attemption = 5;
    $see_option->tnoodles_mult = $see_option->team_persons;
}

if ($see_option->code_script == 'all_scr') {
    $see_option->attemption = 1;
}

$see_option->count = $see_option->groups * ($see_option->attemption + $see_option->extra);


$wca_options = [];

foreach (explode(",", $see_option->tnoodles . $see_option->tnoodle) as $tnoodle) {
    $wca_option = (object) [
                'code' => $tnoodle,
                'attemption' => in_array($tnoodle, ['666', '777']) ? 3 : 5,
                'extra' => in_array($tnoodle, ['666', '777']) ? 1 : 2,
                'format' => in_array($tnoodle, ['666', '777']) ? 'm' : 'a',
                'need' => $see_option->count * $see_option->tnoodles_mult,
                'groups' => ceil(($see_option->count * $see_option->tnoodles_mult) / (5 + 2))
    ];

    while ($wca_option->groups > 26) {
        $wca_option_add = clone $wca_option;
        $wca_option_add->groups = 26;
        $wca_options[] = $wca_option_add;
        $wca_option->groups -= 26;
    }
    $wca_options[] = $wca_option;
}

foreach ($wca_options as $id => $wca_option) {
    $wca_options[$id]->count = $wca_option->groups * ($wca_option->attemption + $wca_option->extra);
}
?>
<br>
<table class="table_info">
    <tr>
        <td>Name</td>
        <td><?= $see_option->name ?></td>
    </tr>
    <tr>
        <td>Code</td>
        <td><?= $see_option->code ?></td>
    </tr>
    <tr>
        <td>Competition</td>
        <td><?= $see_option->competition_name ?></td>
    </tr>
    <tr>
        <td>Round</td>
        <td><?= $see_option->round ?></td>
    </tr>      
    <tr>
        <td>WCA events</td>
        <td>
            <?= $see_option->tnoodles . $see_option->tnoodle ?>
        </td>
    </tr>
    <tr>
        <td>Multiplier</td>
        <td><?= $see_option->tnoodles_mult ?></td>
    </tr>
    <tr>
        <td>Format</td>
        <td><?= $see_option->format ?></td>
    </tr>
</table>

<br>
<table width='100%'>
    <tr>
        <td  width='50%'>
            Required scambles
            <table class="table_new">
                <thead>
                    <tr>
                        <td>Groups</td>
                        <td>Attemptions</td>
                        <td>Extra</td>
                        <td>Cup: T, P</td>
                        <td>Total</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $see_option->groups ?></td>
                        <td><?= $see_option->attemption ?></td>
                        <td><?= $see_option->extra ?></td>
                        <td>
                            <?php if ($see_option->cup) { ?>
                                <?= $see_option->teams ?>, <?= $see_option->team_persons ?>
                            <?php } ?>
                        </td>
                        <td class="table_new_bold"><?= $see_option->count ?></td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td  width='50%'>
            Scambles in TNoodle WCA
            <table class="table_new">
                <thead>
                    <tr>
                        <td>Event</td>
                        <td>Groups</td>
                        <td>Attemptions</td>
                        <td>Extra</td>
                        <td>Need</td>
                        <td>Total</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wca_options as $wca_option) { ?>
                        <tr>
                            <td><?= $wca_option->code ?></td>
                            <td><?= $wca_option->groups ?></td>
                            <td><?= $wca_option->attemption ?></td>
                            <td><?= $wca_option->extra ?></td>
                            <td><?= $wca_option->need ?></td>
                            <td class="table_new_bold"><?= $wca_option->count ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </td>
    </tr>
</table>
<hr>
<?php
$data_tnoodle = [
    "wcif" => [
        "formatVersion" => "1.0",
        "name" => "$filename",
        "shortName" => $see_option->code_script,
        "id" => "",
        "events" => [],
        "persons" => [],
        "schedule" => [
            "venues" => [],
            "numberOfDays" => 0
        ]
    ]
];

foreach ($wca_options as $wca_option) {
    $data_tnoodle["wcif"]["events"][] = [
        "id" => $wca_option->code,
        "rounds" => [
            [
                "format" => $wca_option->format,
                "id" => $wca_option->code . "-r1",
                "scrambleSetCount" => $wca_option->groups
            ]
        ]
    ];
}
$scramble_info = getTnoodleVersion('scramble.main');
$scramble_allowed = json_encode($scramble_info->allowed);
?>
<h3>Step 1. Run <?= $scramble_info->current->name ?></h3>
<a target="_blank" href="<?= $scramble_info->current->information ?>">
    Detailed Instructions for TNoodle
</a>
<h3>Step 2. Download ZIP archive from TNoodle</h3>
<a target='_blank'href='http:<?= PageIndex() ?>/tnoodle_redirect.php/?data=<?= json_encode($data_tnoodle) ?>&filename=<?= $filename ?>&allowed=<?= $scramble_allowed ?>'>
    Generate and download
</a>
<h3>Step 3. Unpack the downloaded archive</h3>
<h3>Step 4. Upload <?= $see_option->format ?> file</h3>
[ <?= $upload_instructions[$see_option->format] ?> ]
<form name="EventSetScrambleFile" enctype="multipart/form-data" method="POST" action="<?= PageAction($upload_action[$see_option->format]) ?>">           
    <input type="file" required="" accept="application/<?= strtolower($see_option->format) ?>" name="file" multiple="false"/>
    <input name="ID" type="hidden" value="<?= $see_option->id ?>" />
    <button>Upload <?= $see_option->format ?></button> 
    <input hidden value='<?= json_encode($see_option) ?>' name='see_option'>
    <input hidden value='<?= json_encode($wca_options) ?>' name='wca_options'>
    <input hidden value='<?= json_encode($data_tnoodle) ?>' name='data_tnoodle'>
</form>     