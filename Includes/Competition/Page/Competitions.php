<?php
includePage('News.Announce');
$competitor = getCompetitor();

$filterRequest = request(1);

$filter = ['type' => null];
if ($filterRequest == 'mine') {
    if ($competitor) {
        $filter = ['type' => 'Mine'];
    }
} elseif (is_numeric($filterRequest)) {
    $filter = ['type' => 'Year', 'value' => $filterRequest];
} elseif (countryExists($filterRequest)) {
    $filter = ['type' => 'Country', 'value' => $filterRequest];
}

if ($competitor) {
    $competitorCompetitions = DataBaseClass::getColumn("
        SELECT 
            DISTINCT(E.Competition) 
        FROM Competitor C
        JOIN CommandCompetitor CC ON CC.Competitor = C.ID
        JOIN Command Com ON Com.ID = CC.Command
        JOIN Event E ON E.ID = Com.Event 
        WHERE C.WID = {$competitor->id} 
    ");
}

switch ($filter['type']) {
    case 'Country':
        $where = "AND '{$filter['value']}' = Cn.Country";
        break;
    case 'Year':
        $where = "AND {$filter['value']} = YEAR(StartDate)";
        break;
    case 'Mine':
        if ($competitorCompetitions) {
            $where = "AND Cn.id IN (" . implode(",", $competitorCompetitions) . ")";
        }
        break;
    default:
        $where = '';
}

$whereAccess = '';
if (!CheckAccess('Competitions.Hidden')) {
    $whereAccess .= " AND Cn.Status != 0";
}
if (!CheckAccess('Competitions.Secret')) {
    $whereAccess .= " AND Cn.WCA NOT LIKE 't.%'";
}

$countryCountCompetitions = DataBaseClass::getRowsAssoc("
    SELECT 
        LOWER(Cn.Country) countryCode,
        Country.Name countryName,
        COUNT(*) count 
    FROM Competition Cn 
    JOIN Country ON Country.ISO2 = Cn.Country 
    WHERE 1 = 1 
        $whereAccess
    GROUP BY 
        Cn.Country, 
        Country.Name 
    ORDER BY Country.Name
");
$countCompetitions = array_sum(array_column($countryCountCompetitions, 'count'));

$competitions = DataBaseClass::getRowsAssoc("
    SELECT 
        Cn.*,
        COALESCE(Country.Name,'') CountryName,
        CASE
            WHEN Cn.Status=-1 THEN -2
            WHEN Cn.Status=0 THEN -1
            WHEN current_date < Cn.StartDate THEN 0
            WHEN current_date >= Cn.StartDate 
                AND current_date <= Cn.EndDate THEN 1
            WHEN current_date > Cn.EndDate THEN 2
        END UpcomingStatus
    FROM `Competition` Cn
    LEFT OUTER JOIN Country ON Country.ISO2=Cn.Country
    WHERE 1=1
        $where
        $whereAccess
    ORDER BY 
        Cn.StartDate DESC, 
        Cn.EndDate DESC
");

$competitionsID = array_column($competitions, 'ID');

$eventsCompetition = [];
$eventsPanel = [];

$events = DataBaseClass::getRowsAssoc("
    SELECT 
        D.Name, 
        D.Code, 
        D.CodeScript, 
        D.Status, 
        Cn.ID
    FROM `Competition` Cn
    JOIN `Event` E ON E.Competition = Cn.ID
    JOIN `DisciplineFormat` DF 
        ON DF.ID = E.DisciplineFormat 
        AND E.Round=1
    JOIN `Discipline` D ON D.ID = DF.Discipline
    WHERE Cn.ID IN (" . implode(',', array_merge($competitionsID, [-1])) . ")
    ORDER BY D.Code"
);

foreach ($events as $event) {
    $eventsPanel[$event['Code']] = $event;
    $eventsCompetition[$event['ID']][$event['Code']] = $event;
}

$eventsImage = [];
foreach ($eventsPanel as $event) {
    if ($event['Status'] == 'Active') {
        $eventsImage[] = ImageEvent($event['CodeScript'], 1, $event['Name']);
    }
}

$yearCountCompetitions = DataBaseClass::getColumnAssoc("
    SELECT 
        YEAR(StartDate) year,
        COUNT(*) count 
    FROM `Competition` C 
    WHERE 
        C.Status=1 
        AND C.WCA NOT LIKE 't.%' 
    GROUP BY YEAR(StartDate)
    ORDER BY 1"
);

$title = isset($filter['Mine']) ? ml('Competitions.My') : ml('Competitions.Title');

$accessCompetitionAdd = CheckAccess('Competition.Add');

$iconCompetitionStatus[-2] = <<<OUT
    <i style="color:var(--red)" class="fas fa-clinic-medical"></i>
OUT;
$iconCompetitionStatus[-1] = <<<OUT
    <i style="color:var(--red)" class="fas fa-eye-slash"></i>
OUT;
$iconCompetitionStatus[0] = <<<OUT
    <i style="color:var(--light_gray)" class="fas fa-hourglass-start"></i>
OUT;
$iconCompetitionStatus[1] = <<<OUT
    <i style="color:var(--green)" class="fas fa-hourglass-half"></i>
OUT;
$iconCompetitionStatus[2] = <<<OUT
    <i style="color:var(--black)" class="fas fa-hourglass-end"></i>
OUT;


$data = (object) [
            'TITLE' => $title,
            'ADD_SHOW' => $accessCompetitionAdd,
            'ADD_LINK' => PageIndex() . "Competition/Add",
            'FILTER' => $filterRequest,
            'COUNT_COMPETITIONS' => (object) [
                'ALL' => $countCompetitions,
                'MINE' => sizeof($competitorCompetitions),
                'YEARS' => $yearCountCompetitions,
                'COUNTRIES' => $countryCountCompetitions],
            'IS_COMPETITOR' => $competitor,
            'EVENTS' => $eventsImage
];

echo IncludeTemplate('Competitions', $data);
?>
<table class="table_new competitions">
    <?php
    foreach ($competitions as $competition) {
        $classUnnoficial = ($competition['Unofficial'] and $competition['UpcomingStatus'] != -1) ? 'unofficial' : '';
        ?>
        <tr class="competition <?= empty($eventsCompetition[$competition['ID']]) ? '' : implode(" ", array_column($eventsCompetition[$competition['ID']], 'CodeScript')) ?>">
            <td class="table_new_center">
                <?= $iconCompetitionStatus[$competition['UpcomingStatus']]; ?>
            </td>            
            <td>            
                <b><?= date_range($competition['StartDate'], $competition['EndDate']); ?></b>    
            </td>   
            <td>
                <?= ImageCountry($competition['Country']) ?>
            </td>
            <td>
                <a href="<?= LinkCompetition($competition['WCA']) ?>">
                    <span class="<?= $classUnnoficial ?>"><?= $competition['Name'] ?></span>
                </a>
            </td>
            <td>
                <b><?= $competition['CountryName'] ?></b>, <?= $competition['City'] ?>
            </td>
            <td>
                <?php if (empty($eventsCompetition[$competition['ID']])) { ?>
                    <i class="fas fa-ban"></i>
                <?php } else { ?>
                    <?php
                    $i = 0;
                    foreach ($eventsCompetition[$competition['ID']] as $event) {
                        if ($i++ > 9) {
                            break;
                        }
                        ?>
                        <?= ImageEvent($event['CodeScript'], 1.3, $event['Name']); ?>
                    <?php } ?>
                <?php } ?>    
            </td>
        </tr>
    <?php } ?>
</table>
<?php IncludeJavaScript('Competitions'); ?>