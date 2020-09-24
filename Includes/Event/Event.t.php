<h1>
    <?= $data->event->image ?>
    <?= $data->event->name ?> / <?= ml('Event.Rankings') ?>
    <?php if ($data->event->isArchive) { ?>
        <p>
            <?= ml('Event.Archive.Title') ?>
        </p>   
    <?php } ?>
</h1>

<table class='table_double_info'>
    <tr>
        <td>     
            <table class='table_info'>
                <tr>
                    <td>
                        Event
                    </td>
                    <td>
                        <select 
                            data-event-request='event' 
                            data-selected='<?= $data->event->code ?>'
                            ID="FilterEvent">
                                <?php foreach ($data->events as $eventOption) { ?>   
                                <option value="<?= $eventOption->code ?>">
                                    <?= $eventOption->name ?>
                                </option>
                            <?php } ?>    
                        </select>                
                    </td>
                </tr>  
                <tr>
                    <td>
                        Type
                    </td>
                    <td>
                        <select data-event-request = 'format'
                                data-selected = '<?= $data->filter->format->value ?>'>
                                    <?php foreach ($data->filter->format->values as $format) { ?>
                                <option value = '<?= $format ?>' >
                                    <?= ml('Event.Filter.' . $format) ?>
                                </option> 
                            <?php } ?>
                        </select>       
                    </td>
                </tr>
                <tr>
                    <td>Country</td>
                    <td>
                        <select data-event-request='country' 
                                data-selected-continent='continent_<?= $data->filter->continent->value ?>'
                                data-selected-country='<?= $data->filter->country->value ?>' >

                            <option value='' >
                                <?= ml('Event.World') ?>
                            </option>

                            <option disabled >
                                <?= ml('Event.Continents') ?>
                            </option>

                            <?php foreach ($data->filter->continent->values as $continent) { ?>
                                <option value='continent_<?= $continent->code ?>' >        
                                    <?= $continent->name ?>
                                </option> 
                            <?php } ?>        

                            <option disabled >
                                <?= ml('Event.Nationals') ?>
                            </option>

                            <?php foreach ($data->filter->country->values as $country) { ?>
                                <option value='<?= $country->code ?>' >        
                                    <?= $country->name ?>
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
                    'currentLink' => 'rankings',
                    'eventTitle' => true
                ]);
            }
            ?>
        </td>
    </tr>
</table>    

<table class='table_new' data-event-results>
    <thead>
        <tr>
            <td/>
            <td>
                <?= ml('Event.Competitor') ?>
            </td>
            <td class="table_new_right">
                <?= ml('Event.' . $data->filter->format->value); ?>
            </td>
            <td>
                <?= ml('Event.Country') ?>
            </td>
            <td>
                <?= ml('Event.Competition') ?>
            </td>
            <?php if ($data->filter->format->value == Attempt::AVERAGE) { ?>
                <td class="table_new_center" colspan="<?= $data->event->attemptionCount ?>">
                    Solves
                </td>
            <?php } ?>
            <?php if ($data->event->codeScript == 'all_scr') { ?>
                <?php if ($data->event->codes) { ?>                
                    <?php for ($i = 0; $i < $data->event->attemptionCount; $i++) { ?>
                        <td class="table_new_center">             
                            <span class=" cubing-icon event-<?= $data->event->codes[$i] ?>"></span>
                        </td>
                    <?php } ?>
                <?php } else { ?>
                    <td class="table_new_center" colspan="<?= $data->event->attemptionCount ?>">
                        Solves
                    </td>
                <?php } ?>
            <?php } ?>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($data->competitors as $competitor) { ?>
            <tr>
                <td data-event-result-value='<?= $competitor->team->attemptSpecial->value ?>'></td>
                <td>
                    <a href='<?= $competitor->link ?>'>
                        <b><?= $competitor->name ?></b>
                    </a>
                    <?php foreach ($competitor->team->competitors as $teamCompetitor) { ?>
                        <?php if ($teamCompetitor->id != $competitor->id) { ?>

                            <small>
                                +
                                <a href="<?= $teamCompetitor->link ?>">
                                    <?= $teamCompetitor->name ?>      
                                </a>
                            </small>

                        <?php } ?>
                    <?php } ?>
                </td>
                <td class='table_new_bold'>
                    <?= $competitor->team->attemptSpecial->out ?>
                </td>
                <td>
                    <?= $competitor->country->image ?>
                    <?= $competitor->country->name ?>
                </td>
                <td>
                    <?= $competitor->team->competitionEvent->competition->image ?>
                    <a href='<?= $competitor->team->competitionEvent->competition->link ?>'>
                        <?= $competitor->team->competitionEvent->competition->name ?>
                    </a>
                </td>
                <?php if ($data->filter->format->value == Attempt::AVERAGE or $data->event->codeScript == 'all_scr') { ?>
                    <?php foreach ($competitor->team->attempts as $attempt) { ?>
                        <td class='table_new_attempt'
                            data-attempt-except='<?= $attempt->except ?>'>
                                <?= $attempt->out ?>
                        </td>        
                    <?php } ?>
                <?php } ?>
                <td>
                    <span data-hidden-href-empty>
                        <a target='_blank' href='<?= $competitor->team->video ?>'>
                            <i class='fas fa-video'></i>
                        </a>
                    </span>
                </td>     
            </tr>
        <?php } ?>
    </tbody>
</table>

<h2 ID='resutsNotFound'>
    <i class='fas fa-exclamation-circle'></i>
    <?= ml('Resuts.NotFound') ?>
</h2>