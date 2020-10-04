<h1>
    <?= ml('Events.Title'); ?>
</h1>
<table class="table_info">
    <?php if ($data->accessEventAdd) { ?>
        <tr>
            <td>
                <i class="fas fa-plus-square"></i>
            </td>
            <td>
                <a href='<?= PageIndex() ?>/Event/Add'>
                    Add Event
                </a>
            </td>
        </tr>    
    <?php } ?>
    <tr>
        <td>
            <i class="fas fa-filter"></i>
            <?= ml('Events.Filer'); ?>
        </td>
        <td>
            <select data-events-filter data-selected="<?= $data->filter ?>">
                <option value>
                    <?= ml('Events.All.Title'); ?>
                </option>
                <option value='team'>
                    <?= ml('Events.Team.Title'); ?>
                </option>
                <option value='puzzles'>
                    <?= ml('Events.Puzzles.Title'); ?>
                </option>
                <option value='333cube'>
                    <?= ml('Events.333Cube.Title'); ?>
                </option>
                <option value='wcapuzzle'>
                    <?= ml('Events.WCAPuzzle.Title'); ?>
                </option>
                <option value='nonwcapuzzle'>
                    <?= ml('Events.nonWCAPuzzle.Title'); ?>
                </option>
                <option value='simple'>
                    <?= ml('Events.Simple.Title'); ?>
                </option>
                <option value='nonsimple'>
                    <?= ml('Events.nonSimple.Title'); ?>
                </option>
                <option value='inscpection20'>
                    <?= ml('Events.Inscpection20.Title'); ?>
                </option>
            </select>    
        </td>
    </tr>
</table> 

<table class="table_new" data-access-event-settings="<?= $data->accessEventSettings ?>" >
    <thead>
        <tr>
            <td></td>
            <td>
                <?= ml('Events.Table.Name') ?>
            </td>
            <td data-event-settings></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <?= ml('Events.Table.Single') ?>
            </td>
            <td></td>
            <td>
                <?= ml('Events.Table.Average') ?>
            </td>
            <td></td>   
            <td>
                <?= ml('Events.Table.Persons') ?>
            </td>
            <td>
                <?= ml('Events.Table.Competitions') ?>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data->events as $event) { ?>
            <tr>
                <td class="events-image">
                    <?= $event->image ?>
                </td>    
                <td>
                    <?= $event->name ?>
                    <span data-event-archive="<?= $event->isArchive ?>"></span>
                </td>
                <td data-event-settings>
                    <a href="<?= PageIndex() ?>/Event/<?= $event->code ?>/Settings">
                        <i class="fas fa-cog"></i>
                    </a>
                </td>
                <td>
                    <i data-event-show='<?= $event->isTeam ?>' class="fas fa-users"></i>
                </td>
                <td>
                    <i data-event-show='<?= $event->multiPuzzles ?>' class="fas fa-cubes"></i>
                </td>
                <td>
                    <i data-event-show='<?= $event->longInspection ?>' class="fas fa-stopwatch"></i>
                </td>
                <td class="table_new_right">
                    <?= $event->wordRecords->single->result ?>
                </td> 
                <td>
                    <?= $event->wordRecords->single->country->image ?>
                </td>
                <td class="table_new_right">
                    <?= $event->wordRecords->average->result ?>
                </td> 
                <td>
                    <?= $event->wordRecords->average->country->image ?>
                </td>
                <td class="table_new_center">
                    <?= $event->countCompetitors ?> 
                </td>
                <td class="table_new_center">     
                    <?= $event->countCompetitions ?>
                </td>
                <td>
                    <a href="<?= PageIndex() ?>/Regulations/<?= $event->code ?>">
                        <i class="fas fa-book"></i>
                        <?= ml('Events.Regulations') ?>
                    </a>
                </td>
                <td>
                    <a href="<?= PageIndex() ?>/Event/<?= $event->code ?>">
                        <i class="fas fa-signal fa-rotate-90"></i>
                        <?= ml('Events.Rankings') ?>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>


