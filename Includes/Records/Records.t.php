<h1>            
    <?= ml('Records.Records') ?>   
</h1>

<table class="table_double_info">
    <tr>
        <td>
            <table class="table_info">
                <tr>
                    <td>
                        <?= ml('Records.Region') ?>
                    </td>
                    <td>
                        <select data-records-request='country' 
                                data-selected-continent='continent_<?= $data->request->continent ?>'
                                data-selected-country='<?= $data->request->country ?>' >

                            <option value='' >
                                <?= ml('Records.WorldRecord') ?>
                            </option>

                            <option disabled >
                                <?= ml('Records.ContinentsRecord') ?>
                            </option>

                            <?php foreach ($data->continents as $continent) { ?>
                                <option value='continent_<?= $continent->code ?>' >        
                                    <?= $continent->name ?>
                                </option> 
                            <?php } ?>        

                            <option disabled >
                                <?= ml('Records.NationalsRecord') ?>
                            </option>

                            <?php foreach ($data->countries as $country) { ?>
                                <option value='<?= $country->code ?>' >        
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
                        <select data-records-request='event' 
                                data-selected='<?= $data->request->event ?>' >

                            <option value='' >
                                All events
                            </option>

                            <?php foreach ($data->events as $event) { ?>   
                                <option value='<?= $event->code ?>' >
                                    <?= $event->name ?>
                                </option>
                            <?php } ?>  

                        </select>                
                    </td>
                </tr>    
            </table>
        </td>
        <td>
            <?php
            if ($data->event->id) {
                IncludeClass::Page(
                        'EventLinks', $data->event, [
                    'currentLink' => 'records',
                    'eventTitle' => true
                ]);
            }
            ?>
        </td>
    </tr>
</table>

<table class='table_new' data-table-records>
    <thead>
        <tr>
            <td>
                <?= ml('Records.Date') ?>
            </td>
            <td>
                <?= ml('Records.Event') ?>
            </td>
            <td class='table_new_right'>
                <?= ml('Records.Single') ?>
            </td>
            <td class='table_new_right'>
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
        <?php foreach ($data->records as $record) { ?>
            <tr>
                <td> 
                    <?= $record->team->competitionEvent->competition->endDateFormat ?>
                </td> 
                <td>
                    <?= $record->team->competitionEvent->event->image ?> 
                    <a href="<?= PageIndex() ?>Event/<?= $record->team->competitionEvent->event->code ?>">
                        <?= $record->team->competitionEvent->event->name ?>
                    </a>
                </td>
                <td class='table_new_right table_new_bold'>
                    <?php if ($record->format == 'single') { ?>
                        <span data-record-single='<?= $record->team->competitionEvent->event->code ?>'>
                            <?= $record->out ?>
                        </span>
                    <?php } ?>
                </td>
                <td class='table_new_right table_new_bold'>
                    <?php if ($record->format == 'average') { ?>
                        <span data-record-average='<?= $record->team->competitionEvent->event->code ?>'>
                            <?= $record->out ?>
                        </span>
                    <?php } ?>
                </td>
                <td>
                    <?php foreach ($record->team->competitors as $competitor) { ?>
                        <p>
                            <a href="<?= $competitor->link ?>">
                                <?= $competitor->name ?>
                            </a>
                        </p>
                    <?php } ?>
                </td>
                <td>
                    <?php foreach ($record->team->competitors as $competitor) { ?>
                        <p>
                            <?= $competitor->country->image ?>
                            <?= $competitor->country->name ?>
                        </p>
                    <?php } ?>
                </td>
                <td>
                    <?= $record->team->competitionEvent->competition->country->image ?>
                    <a href="<?= $record->team->competitionEvent->competition->link ?>">
                        <?= $record->team->competitionEvent->competition->name ?>
                    </a>
                </td>
                <td>   
                    <span data-hidden-href-empty>
                        <a target='_blank' href='<?= $record->team->video ?>'>
                            <i class='fas fa-video'></i>
                        </a>
                    </span>
                </td>    
            </tr>
        <?php } ?>
    </tbody>
</table>

<h2 ID='RecordsNotFound'>
    <i class='fas fa-exclamation-circle'></i>
    <?= ml('Records.NotFound') ?>
</h2>