
<h1>
    <?php if ($data->filter->mine) { ?>
        <?= ml('Competitions.Mine'); ?>
    <?php } else { ?>
        <?= ml('Competitions.Title'); ?>
    <?php } ?>
</h1>
<table>
    <tr>
        <td>
            <table class="table_info">
                <?php if ($data->competitionAdd) { ?>
                    <tr>
                        <td><i class="fas fa-plus-square"></i></td>
                        <td><a href='<?= PageIndex() ?>Competition/Add'>Add Competition</a></td>
                    </tr>    
                <?php } ?>
                <tr>
                    <td>

                    </td>
                    <td>
                        <button data-competitions-filter-all>
                            <?= ml('Competitions.All') ?> 
                        </button>
                        <button data-competitions-filter-mine="<?= $data->filter->mine ?>">
                            <?= ml('Competitions.Mine') ?>
                        </button>    
                    </td>   
                </tr>
                <tr>
                    <td>
                        <?= ml('Competitions.FilterByCountry') ?>
                    </td>
                    <td>
                        <select data-competitions-filter="country" data-selected='<?= $data->filter->country->value ?>'>
                            <option value="">
                                <?= ml('Competitions.FilterByCountry.All') ?> 
                            </option>
                            <?php foreach ($data->filter->country->options as $country) { ?>
                                <option  value="<?= $country->code ?>">        
                                    <?= $country->name ?>
                                </option> 
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= ml('Competitions.FilterByYear') ?>
                    </td>
                    <td>
                        <select data-competitions-filter="year" data-selected='<?= $data->filter->year->value ?>'>
                            <option value="">
                                <?= ml('Competitions.FilterByYear.All') ?> 
                            </option>
                            <?php foreach ($data->filter->year->options as $year) { ?> 
                                <option value="<?= $year ?>">
                                    <?= $year ?>
                                </option>    
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= ml('Competitions.FilterByEvents') ?>
                    </td>
                    <td>
                        <span class="competitions_events_panel events-image">
                            <?php foreach ($data->filerEvents as $event) { ?>
                                <span data-event="<?= $event->code ?>">
                                    <?= $event->image ?>
                                </span>    
                            <?php } ?>
                        </span>
                        <span class="hidden competitions_events_panel_none">
                            <button>
                                <?= ml('Competitions.FilterEventAll') ?>
                            </button>
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>   
