<script>
    scramble = '';
</script>    
<h1>
    <?= $data->event->image ?>
    <?= $data->event->name ?> 
    / <?= ml('TrainingScrambling.Title') ?>
</h1>

<table class="table_info">
    <tr>
        <td>Extra event</td>
        <td>
            <select data-event-select data-selected="<?= $data->event->code ?>">
                <?php if (!($data->event->generate->function or $data->event->generate->script)) { ?>
                    <option selected>
                        - <?= $data->event->name ?> 
                    </option>
                <?php } ?>
                <?php foreach ($data->events as $event) { ?>   
                    <option value="<?= $event->code ?>">
                        <?= $event->name ?>
                    </option>
                <?php } ?>    
            </select>                
        </td>
    </tr> 
</table>             
<?php
if ($data->event->generate->function or $data->event->generate->script) {
    if ($data->event->generate->script) {
        ?>
        <script src="<?= PageLocal() . $data->event->generate->script ?>" type="text/javascript"></script>
        <script>
            var scramble = getscrambles(1);
        </script>
    <?php } ?>
    <table width="100%">
        <tr>
            <td>
                <table class="table_info">
                    <?php if ($data->event->scrambleComment) { ?>
                        <tr>
                            <td>Inctruction</td>
                            <td><?= str_replace("\n", "<br>", $data->event->scrambleComment); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($data->event->tnoodle) { ?>
                        <tr>
                            <td>TNoodle-WCA</td>
                            <td>The competition uses scrambles from the event {<?= $data->event->tnoodle ?>}</td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Scramble</td>
                        <td style="font-size:18px; font-family: monospace; border:1px solid black">
                            <span ID="Scramble"><?= str_replace("&", "<br>", $data->event->generate->scramble); ?></span>
                        </td>    
                    </tr>    
                    <tr>
                        <td>Next scramble</td>
                        <td>Press the [SPACE] to generate new scramble</td>
                    </tr>    
                </table> 
            </td><td>            
                <div style="width:400px;height:300px">
                    <img ID="ScrambleImage" style="max-width: 100%; max-height: 100%;" src="<?= !$data->event->generate->script ? (PageIndex().'/'. $data->event->generate->scrambleImage . '?t=' . time()) : '' ?>">
                </div>   
            </td>
        </tr>
    </table> 

    <?php if ($data->event->generate->function) { ?>
        <h3>Generate scramble sets PDF</h3>
        <form target="_blank" method="POST" data-add-date action="<?= PageAction("Event.Training.Scramble.Print") ?>">
            <table class="table_info">
                <input hidden name="code" value="<?= $data->event->code ?>">
                <tr>
                    <td>
                        Name
                    </td>
                    <td>
                        <input name="name" value="SEE Training <?= $data->event->code ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        Groups
                    </td>
                    <td>
                        <select name="groups">
                            <option value="1">1</option>
                            <option value="2" selected>2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </td>
                <tr>
                    <td>
                        Attemptions
                    </td>
                    <td>
                        <select name="attemptions" data-selected ="<?= $data->event->max_attempts ?>">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="5">5</option>
                        </select>
                    </td>
                </tr>    
                <tr>
                    <td></td>
                    <td>
                        <button>
                            <i class="fas fa-random"></i> Generate 
                        </button>
                    </td>
                </tr>
            </table>
        </form>   
    <?php } ?>
    <hr>
    <?php
    IncludeClass::Page(
            'EventLinks', $data->event, [
        'currentLink' => 'training',
        'eventTitle' => true
    ]);
    ?>
    <script>
        if (scramble) {
            $('#Scramble').html(scramble);

            $.get('<?= PageAction('AJAX.Scramble.Image') ?>/?CodeScript=<?= $data->event->codeScript ?>&Scramble=' + encodeURI(scramble), function (data) {
                $('#ScrambleImage').attr('src', data + '?t=<?= time() ?>');
            });
        }
    </script>            
<?php } else { ?>
    <i class="fas fa-exclamation-triangle"></i>
    The event uses an external scramble generator.
<?php } ?>

