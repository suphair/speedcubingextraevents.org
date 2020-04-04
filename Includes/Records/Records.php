<?php
$request = inputGet(
        [
            'event',
            'country',
            'continent'
        ]);

$event = new Event();
if($request->event){
    $event->getByCode($request->event);
}

$events = Event::getEventsByEventsID(
                Event::getEventsIdByFilter());

DataBaseClass::FromTable('Competition');
DataBaseClass::OrderClear('Competition', 'EndDate');
$competitions = DataBaseClass::QueryGenerate();
$res = array();
$results = array();
$formats = array();

foreach ($competitions as $competition) {
    DataBaseClass::FromTable("Competition", "ID='" . $competition['Competition_ID'] . "'");
    DataBaseClass::Where_current('Unofficial=0');
    DataBaseClass::Where_current("WCA not like 't.%'");
    DataBaseClass::Join_current("Event");
    DataBaseClass::Join_current("DisciplineFormat");
    DataBaseClass::Join_current("Format");
    DataBaseClass::Join("DisciplineFormat", "Discipline");
    DataBaseClass::Where_current("Status='Active'");
    DataBaseClass::Join("Event", "Command");
    DataBaseClass::Join("Command", "Attempt");
    DataBaseClass::Join("Command", "CommandCompetitor");
    DataBaseClass::Join("CommandCompetitor", "Competitor");
    if ($event->code) {
        DataBaseClass::Where('Discipline', "Code='{$event->code}'");
    }
    DataBaseClass::Where('A.Special in (F.Result,F.ExtResult)');
    DataBaseClass::Where('A.isDNF = 0');
    DataBaseClass::Where("Com.vCountry<>''");
    #if($country_filter!='all'){
    #    DataBaseClass::Where('Command',"vCountry='".strtoupper($country_filter)."'");    
    #}
    #if($continent_filter){
    #    DataBaseClass::Where('Command',"vCountry in('".implode("','",$Countries_code)."')");    
    #}

    DataBaseClass::OrderClear('Discipline', 'Code');
    DataBaseClass::Order('Attempt', 'vOrder');
    foreach (DataBaseClass::QueryGenerate() as $n => $row) {
        $formats[$row['Attempt_Special']] = 1;
        $MS = $row['Attempt_vOrder'];
        $row['Attempt_Special'] = str_replace('Mean', 'Average', $row['Attempt_Special']);
        if (!isset($cuts[$row['Discipline_Code']][$row['Attempt_Special']])
                or $MS < $cuts[$row['Discipline_Code']][$row['Attempt_Special']]) {
            $cuts[$row['Discipline_Code']][$row['Attempt_Special']] = $MS;
            $results[$competition['Competition_EndDate']][] = $row;
        }
    }
}

$results = array_reverse($results);

foreach ($results as $n => $comp) {
    $results[$n] = array_reverse($comp);
}

$countries = Country::getCountriesByCountriesCode(
                Competitor::getCountriesCodeByAllCompetitors());

$continents = Continent::getContinentsByCountries($countries);

?>

<h1>            
    <?= ml('Records.Records') ?>   
</h1>
<table class="table_double_info">
    <tr>
        <td>
            <table class="table_info">
                <tr>
                    <td>
                        <?= ml('Records.Show') ?>
                    </td>
                    <td>
                        <i class="far fa-check-square"></i>
                        <?= ml('Records.ShowHistory') ?>
                    </td>
                </tr
                <tr>
                    <td>
                        <?= ml('Records.Region') ?>
                    </td>
                    <td>
                        <select data-records-request='country' 
                                data-selected-continent='continent_<?= $request->continent ?>'
                                data-selected-country='<?= $request->country ?>' >

                            <option value=''>
                                <?= ml('Records.WorldRecord') ?>
                            </option>

                            <option disabled>
                                <?= ml('Records.ContinentsRecord') ?>
                            </option>

                            <?php foreach ($continents as $continent) { ?>
                                <option value='continent_<?= $continent->codeLower ?>' >        
                                    <?= $continent->name ?>
                                </option> 
                            <?php } ?>        

                            <option disabled>
                                <?= ml('Records.NationalsRecord') ?>
                            </option>

                            <?php foreach ($countries as $country) { ?>
                                <option value="<?= $country->codeLower ?>" >        
                                    <?= $country->name ?>
                                </option> 
                            <?php } ?>      

                        </select>
                    </td>
                </tr>    
                <tr>
                    <td>
                        <?= ml('Records.Event') ?>
                    </td>
                    <td>
                        <select 
                            data-records-request='event'
                            data-selected='<?= $request->event ?>' >

                            <option value=''>
                                All events
                            </option>

                            <?php foreach ($events as $option) { ?>   
                                <option value='<?= $option->code ?>' >
                                    <?= $option->name ?>
                                </option>
                            <?php } ?>  

                        </select>                
                    </td>
                </tr>    
            </table>
        </td>
        <td>
            <?php
            if ($event->id) {
                IncludeClass::Page(
                        'EventLinks', $event, [
                    'currentLink' => 'records',
                    'eventTitle' => true
                        ]
                );
            }
            ?>
        </td>
    </tr>
</table>

<table class="table_new" data-table-records>
    <thead>
        <tr >
            <td>
                <?= ml('Records.Date') ?>
            </td>
            <td>
                <?= ml('Records.Event') ?>
            </td>
            <td class="table_new_right">
                <?= ml('Records.Single') ?>
            </td>
            <td class="table_new_right">
                <?= ml('Records.Average') ?>
            </td>
            <td>
                <?= ml('Records.Competitor') ?>
            </td>
            <td>
                <?= ml('Records.Country') ?>
            </td>
            <td>
                <?= ml('Records.Competition') ?>
            </td>
            <td></td>
        </tr>
    </thead>
    <tbody>
        <?php
        $record_out = array();
        foreach ($results as $date => $comp) {
            foreach ($comp as $ci => $c) {
                if (strpos($c['Competition_WCA'], 't.') === false) {
                    ?>
                    <tr>
                        <td>
                            <?= date_range($c['Competition_EndDate']); ?>
                        </td> 
                        <td>
                            <?= ImageEvent($c['Discipline_CodeScript'], 25, $c['Discipline_Name']); ?> 
                            <a href="<?= LinkDiscipline($c['Discipline_Code']) ?>">
                                <?= $c['Discipline_Name'] ?>
                            </a>
                        </td>
                        <?php
                        $class = "";
                        if (!in_array($c['Discipline_ID'] . '_' . $c['Attempt_Special'], $record_out)) {
                            $record_out[] = $c['Discipline_ID'] . '_' . $c['Attempt_Special'];
                            $class = "table_new_PB";
                        }
                        ?>   
                        <td class="<?= $class ?> table_new_right table_new_bold">
                            <?php if (in_array($c['Attempt_Special'], array('Best', 'Sum'))) { ?>
                                <?= $c['Attempt_vOut'] ?>
                            <?php } ?>
                        </td>

                        <td class="<?= $class ?> table_new_right table_new_bold">
                            <?php if (!in_array($c['Attempt_Special'], array('Best', 'Sum'))) { ?>
                                <?= $c['Attempt_vOut'] ?>
                            <?php } ?>
                        </td>
                        <?php
                        DataBaseClass::FromTable("Command", "ID=" . $c['Command_ID']);
                        DataBaseClass::Join_current("CommandCompetitor");
                        DataBaseClass::Join_current("Competitor");
                        DataBaseClass::OrderClear("Competitor", "Name");
                        $competitors = DataBaseClass::QueryGenerate();
                        ?>
                        <td>
                            <?php foreach ($competitors as $competitor) { ?>
                                <p>
                                    <a href="<?= PageIndex() ?>Competitor/<?= $competitor['Competitor_WCAID'] ? $competitor['Competitor_WCAID'] : $competitor['Competitor_ID'] ?>"><?= trim(explode("(", $competitor['Competitor_Name'])[0]) ?></a>
                                </p>
                            <?php } ?>
                        </td>
                        <td>
                            <?php foreach ($competitors as $competitor) { ?>
                                <p>
                                    <?= ImageCountry($competitor['Competitor_Country']) ?>
                                    <?= CountryName($competitor['Competitor_Country']) ?>
                                </p>
                            <?php } ?>
                        </td>
                        <td>
                            <?= ImageCountry($c['Competition_Country']) ?>
                            <a href="<?= LinkCompetition($c['Competition_WCA']) ?>">
                                <?= $c['Competition_Name'] ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($c['Command_Video']) { ?>            
                                <a target=_blank" href="<?= $c['Command_Video'] ?>"><i class="fas fa-video"></i></a>
                            <?php } ?>
                        </td>    
                    </tr>
                    <?php
                }
            }
        }
        ?>
    </tbody>
</table>

<h2 ID='RecordsNotFound'>
    <i class="fas fa-exclamation-circle"></i>
    <?= ml('Records.NotFound') ?>
</h2>

<?php IncludeClass::Template('Records', $data); ?>