<h1><?= $data->TITLE ?></h1>
<table>
    <tr>
        <td>
            <table class="table_info">
                <?php if ($data->ADD_SHOW) { ?>
                    <tr>
                        <td><i class="fas fa-plus-square"></i></td>
                        <td><a href='<?= $data->ADD_LINK ?>'>Add Competition</a></td>
                    </tr>    
                <?php } ?>
                <tr>
                    <td><?= ml('Competitions.Filter') ?></td>
                    <td>
                        <select data-filter='<?= $data->FILTER ?>' ID="filter">
                            <option value="">
                                <?= ml('Competitions.All') ?> (<?= $data->COUNT_COMPETITIONS->ALL ?>)
                            </option>
                            <?php if ($data->IS_COMPETITOR) { ?>
                                <option value="mine">
                                    <?= ml('Competitions.My') ?> (<?= $data->COUNT_COMPETITIONS->MINE ?>)
                                </option>
                            <?php } ?>
                            <option disabled>------</option>
                            <?php foreach ($data->COUNT_COMPETITIONS->YEARS as $year => $count) { ?>
                                <option value="<?= $year ?>">
                                    <?= $year ?> (<?= $count ?>)
                                </option>    
                            <?php } ?>
                            <option disabled>------</option>
                            <?php foreach ($data->COUNT_COMPETITIONS->COUNTRIES as $value) { ?>
                                <option  value="<?= $value['countryCode'] ?>">        
                                    <?= $value['countryName'] ?> (<?= $value['count'] ?>)
                                </option> 
                            <?php } ?>      
                        </select>
                    </td>
                    <td>
                        <span class="competitions_events_panel">
                            <?php foreach ($data->EVENTS as $value) { ?>
                                <?= $value ?>
                            <?php } ?>
                        </span>
                        <i title="Clear filter" class=" competitions_events_panel_none fas fa-ban"></i>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>   




<h3 ID='competitionsNotFound'><?= ml('Competitions.NotFound') ?></h3>
