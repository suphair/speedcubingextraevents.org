<h1><?= ml('Navigator.Delegates') ?></h1>
<h2>
    <?php if ($data->candidates->show) { ?>    
        <a href='<?= $data->candidates->link ?>'>
            <?= ml('Delegate.Candidates') ?>
        </a>    
    <?php } elseif ($data->isDelegateWca) { ?>
        <a href="<?= PageIndex() . "Delegate/Candidate?code=" . $data->delegateCode ?>">
            <?= ml('Delegate.Candidate.Title') ?>
        </a>
    <?php } ?>    
</h2>  
<table class="table_new">
    <thead>
        <tr>
            <td/>
            <td>
                <?= ml('Delegates.Country') ?>
            </td>
            <td>
                <?= ml('Delegates.Name') ?>
            </td>
            <td/>
            <td/>
            <td>
                <?= ml('Delegates.Competitions') ?>
            </td>
            <td>
                <?= ml('Delegates.Persons') ?>
            </td>
            <td>
                <?= ml('Delegates.LatestActivity') ?>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data->delegates as $delegate) { ?>
            <tr>
                <td>
                    <?= $delegate->country->image ?>
                </td>
                <td class="table_new_bold">
                    <?= $delegate->country->name ?>
                </td>
                <td> 
                    <a href="<?= $delegate->link ?>">
                        <?= $delegate->name ?>
                    </a>
                </td>
                <td class="delegate_status <?= $delegate->status ?>">
                </td>
                <td>
                    <?= ml('Delegate.' . $delegate->status) ?>
                </td>
                <td class="table_new_right">
                    <?= $delegate->countCompetitions ?>
                </td>
                <td class="table_new_right">
                    <?= $delegate->countCompetitors ?>
                </td>
                <td class="table_new_right">
                    <?= $delegate->endDate ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>



<h1><?= ml('Delegates.WCA') ?></h1>
<table class="table_new">
    <thead>
        <tr>
            <td/>
            <td>
                <?= ml('Delegates.Country') ?>
            </td>
            <td>
                <?= ml('Delegates.Name') ?>
                <i class="fas fa-external-link-alt"></i>
            </td>
            <td>
                <?= ml('Delegates.Competitions') ?>
            </td>
            <td>
                <?= ml('Delegates.Persons') ?>
            </td>
            <td>
                <?= ml('Delegates.LatestActivity') ?>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data->delegatesWca as $delegate) { ?>
            <tr>
                <td>
                    <?= $delegate->country->image ?>
                </td>
                <td class="table_new_bold">
                    <?= $delegate->country->name ?>
                </td>
                <td> 
                    <a href="<?= $delegate->link ?>" target="_blank">
                        <?= $delegate->name ?>
                    </a>
                </td>
                <td class="table_new_right">
                    <?= $delegate->countCompetitions ?>
                </td>
                <td class="table_new_right">
                    <?= $delegate->countCompetitors ?>
                </td>
                <td class="table_new_right">
                    <?= $delegate->endDate ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>