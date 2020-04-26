<h1>
    <?= ml('Regulations.Title'); ?>
</h1>
<i class="fas fa-star"></i>
<?= ml('Regulations.Description'); ?>
<h3>
    <?= ml('Regulations.Documents') ?>
</h3>
<table class = "table_info">
    <tr>
        <td>
            Speecubing Extra Events
        </td>
        <td>
            <a href = "<?= PageIndex() ?>MainRegulations">
                <i class="fas fa-book"></i>
                SEE Regulations
            </a>    
        </td>
    </tr>
    <tr>
        <td>
            World Cube Association
        </td>
        <td>
            <a data-external-link
               href="https://www.worldcubeassociation.org/regulations">
                <i class="fas fa-book"></i>
                WCA Regulations
            </a>
        </td>
    </tr>
</table>    

<h3>
    <?= ml('Regulations.ExtraEvents') ?>
</h3>
<table class = 'table_double_info'>
    <tr>
        <td class = 'td_border_right'>
            <table class = 'table_info'>
                <td></td>
                <td>
                    <select data-regulations-event data-selected = '<?= $data->event->code ?>'>
                        <?php foreach ($data->events as $event) { ?>   
                            <option value = '<?= $event->code ?>'>
                                <?= $event->name ?>
                            </option>
                        <?php } ?>    
                    </select>                
                </td>
                <?php foreach ($data->events as $event) { ?>
                    <tr>
                        <td>
                            <?= $event->image ?>
                        </td>
                        <td>
                            <a  data-regulations-event-list = '<?= $event->code ?>'
                                href = '<?= PageIndex() ?>Regulations/<?= $event->code ?>'>
                                    <?= $event->name ?>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </table>     
        </td>
        <td>            
            <h2>
                <?= $data->event->image ?>
                <?= $data->event->name ?>
                <?php if ($data->event->isArchive) { ?>
                    <p>
                        <?= ml('Event.Archive.Title') ?>
                    </p>   
                <?php } ?>
            </h2>
            <table class="table_info">
                <?php if ($data->event->regulation_language) { ?>
                    <tr>
                        <td>
                            <?= ml('Regulations.Language') ?>
                        </td>
                        <td>
                            <?= $data->event->regulation_language->image ?>
                            <?= $data->event->regulation_language->name ?>
                        </td>    
                    </tr>    
                <?php } ?>
                <tr>
                    <td></td>
                    <td>
                        <?php if ($data->event->regulation) { ?>
                            <?= Parsedown($data->event->regulation) ?>
                        <?php } else { ?>
                            <i class='fas fa-exclamation-triangle'></i>
                            <?= ml('Regulation.Writing') ?>
                        <?php } ?>
                    </td>   
                </tr>

                <?php if ($data->text->team) { ?>
                    <tr>
                        <td>
                            <?= ml('Regulations.Team') ?>
                        </td>
                        <td>
                            <?= $data->text->team ?>
                        </td>
                    </tr>       
                <?php } ?>

                <?php if ($data->text->longInspection) { ?>
                    <tr>
                        <td>
                            <?= ml('Regulations.Inspect') ?>
                        </td>
                        <td>
                            <?= $data->text->longInspection ?>
                        </td>
                    </tr>       
                <?php } ?>

                <?php if ($data->text->mguild) { ?>
                    <tr>
                        <td>
                            <?= ml('Regulations.Position') ?>
                        </td>
                        <td>
                            <?= $data->text->mguild ?>
                        </td>
                    </tr>       
                <?php } ?>

                <?php if ($data->text->multiPuzzles) { ?>
                    <tr>
                        <td>
                            <?= ml('Regulations.Penalties') ?>
                        </td>
                        <td>
                            <?= $data->text->multiPuzzles ?>
                        </td>
                    </tr>    
                <?php } ?> 
                <tr>
                    <td colspan='2'>
                        <hr>
                    </td>
                </tr>
                <tr>
                    </td>
                    <?php
                    if ($data->event->id) {
                        IncludeClass::Page(
                                'EventLinks', $event, [
                            'currentLink' => 'regulations',
                            'eventTitle' => false
                        ]);
                    }
                    ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
