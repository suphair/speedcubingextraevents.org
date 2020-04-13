<h1>
    <?= $data->competitor->country->image ?>
    <?= $data->competitor->name ?>
</h1>  

<table class='table_double_info'>
    <tr>
        <td>
            <table class='table_info'>
                <tr>
                    <td>
                        <?= ml('Competitor.Country') ?>
                    </td>        
                    <td>
                        <?= $data->competitor->country->name ?>
                    </td>
                </tr>
                <tr> 
                    <td>
                        <?= ml('Competitor.Continent') ?>
                    </td>        
                    <td>
                        <?= $data->competitor->country->continent->name ?>
                    </td>
                </tr>
                <?php if ($data->competitor->wcaid) { ?>    
                    <tr>
                        <td>
                            WCA ID
                        </td>
                        <td>
                            <a data-external-link
                               href='<?= $data->competitor->linkWca ?>'>
                                   <?= $data->competitor->wcaid ?>
                            </a>
                        </td>
                    </tr>
                <?php } ?>  
                <tr data-hidden='<?= empty($data->competitor->email) ?>'>
                    <td>
                        Email
                    </td>
                    <td>
                        <span data-competitor-email='<?= $data->competitor->email ?>'></span>
                    </td>
                </tr> 
                <tr data-hidden='<?= empty($data->delegate->id) ?>'>
                    <td>
                        <?= ml('Competitor.Delegate') ?>
                    </td>            
                    <td>
                        <a href="<?= $data->delegate->link ?>">
                            <?= ml('Delegate.' . $data->delegate->status) ?>
                        </a>
                    </td>
                </tr>

            </table>
        </td>
        <?php if ($data->reload->access) { ?>
            <td>
                <form method='POST' action='<?= PageAction('Competitor.Reload') ?>'>
                    <input name="Competitor" hidden value="<?= $data->competitor->id ?>">
                    <table class='table_info'>
                        <tr>
                            <td>
                                id
                            </td>
                            <td>
                                <?= $data->competitor->id ?>
                            </td>
                        </tr>   
                        <tr>
                            <td>
                                user_id
                            </td>
                            <td>
                                <a data-external-link 
                                   href='<?= $data->competitor->linkApiUser ?>'>
                                       <?= $data->competitor->wid ?>
                                </a>
                            </td>
                        </tr> 
                        <tr>
                            <td>
                                wca_id
                            </td>
                            <td>
                                <a data-external-link 
                                   href='<?= $data->competitor->linkWca ?>'>
                                       <?= $data->competitor->wcaid ?>
                                </a>
                            </td>
                        </tr> 
                        <tr>
                            <td></td>
                            <td>
                                <button>
                                    <i class='fas fa-sync-alt'></i>
                                    Reload
                                </button>
                            </td>
                        </tr>    
                        <tr data-hidden='<?= empty($data->reload->message) ?>'>
                            <td></td>
                            <td>
                                <?= $data->reload->message ?>
                            </td>
                        </tr>       
                    </table>
                </form>
            </td>
        <?php } ?>    
    </tr>
</table> 

<h2>
    <?= ml('Competitor.Rank.Title'); ?>
</h2>
<table class='table_new'>  
    <thead>
        <tr>
            <td/>
            <td>
                <?= ml('Competitor.Rank.Table.Event'); ?>
            </td> 
            <td>
                NR
            </td>
            <td>
                CR
            </td>
            <td> 
                WR
            </td>
            <td class='table_new_attempt'>
                <?= ml('Competitor.Rank.Table.Single'); ?>
            </td>
            <td class='table_new_attempt'>
                <?= ml('Competitor.Rank.Table.Average'); ?>
            </td>
            <td>
                WR
            </td>
            <td>
                CR
            </td>
            <td>
                NR
            </td>
            <td/>
        </tr>   
    </thead>
    <tbody>
        <?php foreach ($data->eventsRank as $event) { ?>
            <tr>
                <td class="events-image">
                    <?= $event->image ?>
                </td>  
                <td>
                    <a class="local_link" href='#' 
                       data-competitor-event-select='<?= $event->code ?>' >
                        <nobr>
                            <?= $event->name ?>
                        </nobr>    
                    </a>
                </td>
                <td data-competitor-rank class='table_new_right'>
                    <?= $event->rank->countrySingle ?>
                </td>
                <td data-competitor-rank class='table_new_right'>
                    <?= $event->rank->continentSingle ?>
                </td>
                <td data-competitor-rank class='table_new_right'>
                    <?= $event->rank->wordSingle ?>
                </td>
                <td class='table_new_attempt table_new_bold'>
                    <?= $event->rank->attemptSingle->out ?>
                </td>
                <td class='table_new_attempt table_new_bold'>
                    <?= $event->rank->attemptAverage->out ?>
                </td>
                <td data-competitor-rank class='table_new_right'>
                    <?= $event->rank->wordAverage ?>
                </td>
                <td data-competitor-rank class='table_new_right'>
                    <?= $event->rank->continentAverage ?>
                </td>
                <td data-competitor-rank class='table_new_right'>
                    <?= $event->rank->countryAverage ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<h2>
    <?= ml('Competitor.Results.Title'); ?>
</h2>

<span data-competitor-events-panel class='competitor-event-panel'>
    <?php foreach ($data->eventsResult as $event) { ?>
        <a href="#" data-competitor-event-select='<?= $event->code ?>' >
            <?= $event->image ?>
        </a>    
    <?php } ?>   
</span>        

<?php foreach ($data->eventsResult as $event) { ?>
    <span data-competitor-event-block='<?= $event->code ?>'>
        <h2>
            <?= $event->image ?>
            <?= $event->name ?>
        </h2>
        <p data-hidden='<?= !$event->isArchive ?>'>
            <?= ml('Competitor.Event.Archive') ?>
        </p>
        <table class="table_new">
            <thead>      
                <tr> 
                    <td>
                        <?= ml('Competitor.Result.Table.Competition'); ?>
                    </td>
                    <td></td>
                    <td>
                        <?= ml('Competitor.Result.Table.Round'); ?>
                    </td>
                    <td class="table_new_right">
                        <?= ml('Competitor.Result.Table.Place'); ?>
                    </td>
                    <td class="table_new_right">
                        <?= ml('Competitor.Result.Table.Single'); ?>
                    </td>
                    <td></td>
                    <td class="table_new_right">
                        <?= ml('Competitor.Result.Table.Average'); ?>
                    </td>
                    <td></td>
                    <?php if ($event->codes) { ?>                
                        <?php foreach ($event->codes as $code) { ?>
                            <td class='table_new_center event-codes'>             
                                <span class='cubing-icon event-<?= $code ?>'></span>
                            </td>
                        <?php } ?>
                    <?php } else { ?>
                        <td class='table_new_center' colspan='<?= $event->attemptionCount ?>'>
                            <?= ml('Competitior.Solves') ?>
                        </td>
                    <?php } ?>
                </tr>  
            </thead>
            <tbody>
                <?php foreach ($event->teams as $team) { ?>
                    <tr>
                        <td>
                            <i class='status_icon <?= $team->competitionEvent->competition->status ?>'></i>
                            <a href="<?= $team->competitionEvent->link ?>">
                                <?= $team->competitionEvent->competition->name ?>
                            </a>
                            <?php if ($team->competitionEvent->competition->unofficial) { ?>
                                <i title='<?= ml('Competitor.Competition.Unofficial', false) ?>'
                                   class='fas fa-exclamation-triangle'></i>
                               <?php } ?>

                            <?php foreach ($team->competitors as $teamCompetitor) { ?>
                                <?php if ($teamCompetitor->id != $data->competitor->id) { ?>
                                    <p>
                                        <i class="fas fa-user-plus"></i>
                                        <a href="<?= $teamCompetitor->link ?>">
                                            <?= $teamCompetitor->name ?>      
                                        </a>
                                    </p>
                                <?php } ?>
                            <?php } ?>
                        </td>
                        <td>
                            <span data-hidden-href-empty>
                                <a target='_blank' href='<?= $team->video ?>'>
                                    <i class='fas fa-video'></i>
                                </a>
                            </span>
                        </td>
                        <td>
                            <?= ml('Competitor.Round.' . $team->competitionEvent->round); ?>
                        </td>
                        <td class="table_new_right">
                            <?= $team->place ?>
                        </td>
                        <td data-event-single='<?= $team->attemptSingle->value ?>'
                            class="table_new_right table_new_bold"> 
                                <?= $team->attemptSingle->out ?>
                        </td>  
                        <td data-event-record='<?= $team->attemptSingle->record ?>'></td>       
                        <td data-event-average='<?= $team->attemptAverage->value ?>'
                            class="table_new_right table_new_bold"> 
                                <?= $team->attemptAverage->out ?>
                        </td>  
                        <td data-event-record='<?= $team->attemptAverage->record ?>'></td>
                        <?php foreach ($team->attempts as $attempt) { ?>
                            <td data-attempt-except='<?= $attempt->except ?>' 
                                class="table_new_attempt">
                                <?= $attempt->out ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>    
        </table>
    </span>
<?php } ?>

