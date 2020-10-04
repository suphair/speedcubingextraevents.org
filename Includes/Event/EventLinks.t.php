<table class='table_info' data-selected-value='<?= $data->currentLink ?>'>
    <tr data-hidden='<?= !$data->eventTitle ?>'> 
        <td>
            <?= $data->image ?>
        </td>
        <td>
            <?= $data->name ?>
        </td>
    </tr>
    <tr data-hidden='<?= !$data->accessEventSetting ?>'>
        <td>
            <i class='fas fa-cog'></i>
        </td>
        <td data-selected-condition='settings'>
            <a href='<?= "{$data->pageIndex}/Event/{$data->code}/Settings" ?>'>
                Main event setting
            </a>
        </td>
    </tr>    
    <tr data-hidden='<?= $data->isArchive ?>'>  
        <td>
            <i class='fas fa-book'></i>
        </td>
        <td data-selected-condition='regulations'>
            <a href='<?= "{$data->pageIndex}/Regulations/{$data->code}" ?>'>
                <?= ml('Competition.Regulation'); ?>
            </a>
        </td>
    </tr>    
    <tr>
        <td>
            <i class='fas fa-trophy'></i>
        </td>
        <td data-selected-condition='records'>
            <a href='<?= "{$data->pageIndex}/Records/?event={$data->code}" ?>'>
                <?= ml('Event.Records'); ?>
            </a>
        </td>
    </tr>    
    <tr>
        <td>
            <i class='fas fa-signal fa-rotate-90'></i>
        </td>
        <td data-selected-condition='rankings'>
            <a href='<?= "{$data->pageIndex}/Event/{$data->code}" ?>'>
                <?= ml('Competition.Rankings'); ?>
            </a>
        </td>    
    </tr>    
    <?php if (!$data->isArchive) { ?>       
        <?= scramble_block($data->id); ?>
        <?= scorecard_block($data->id); ?>
    <?php } ?>
    <?php
    $existsGenerateTraining = file_exists("Functions/GenerateTraining_{$data->codeScript}.php");
    $existsGenerate = file_exists("Functions/Generate_{$data->codeScript}.php");
    $existsScriptGenerate = file_exists("Script/{$data->codeScript}_generator.js");
    $existsScrambleImage = file_exists("Scramble/{$data->codeScript}.php");
    if ($existsScrambleImage and ( $existsGenerateTraining or $existsGenerate or $existsScriptGenerate)) {
        ?>
        <tr>
            <td>
                <i class='fas fa-random'></i>
            </td>
            <td data-selected-condition='training'>
                <a href='<?= "{$data->pageIndex}/Event/{$data->code}" ?>/Training'>
                    <?= ml('TrainingScrambling.Title') ?>
                </a>
            </td>            
        <?php } ?>
</table>