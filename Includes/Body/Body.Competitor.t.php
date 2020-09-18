<span class="hidden" data-block-name='body_competitor_panel'>
    <form class='form_inline' method="POST" action="<?= PageAction('Language.Set') ?> "> 

        <?php if ($data->competitor) { ?>
            <a href="#" id="competitor_panel" class="local_link competitor_panel_link">

                <?php if ($data->competitor->ban->ban) { ?>
                    <i class="fas fa-user-slash"></i>
                <?php } elseif ($data->delegate) { ?>
                    <i class="fas fa-user-tie"></i>
                <?php } else { ?>
                    <i class="fas fa-user"></i>    
                <?php } ?>
                <?= $data->competitor->name ?>
                <?php if ($data->competitor->ban->ban) { ?>
                    <span class="error">
                        - Banned!
                    </span>
                <?php } ?>
            </a>
            <a href="<?= PageIndex() ?>Actions/Competitor.Logout">
                <i class="fas fa-sign-out-alt"></i> 
                <?= ml('Competitor.SignOut') ?>
            </a>
        <?php } else { ?>
            <a href="<?= GetUrlWCA(); ?>">
                <i class="fas fa-sign-in-alt"></i> 
                <?= ml('Competitor.SignIn') ?>
            </a>
        <?php } ?>   
        <span class='language_set'>
            <?= $data->language->image ?>
            <select name='language' data-selected='<?= $data->language->code ?>'>
                <?php foreach ($data->languages as $language) { ?>
                    <option value="<?= $language->code ?>"><?= $language->name ?></option>
                <?php } ?>
            </select>
        </span>
    </form> 
</span>

<div class="competitor-panel">
    <?php if ($data->competitor) { ?>

        <?php if ($data->competitor->ban->ban) { ?>
            <span class="error">
                <i class="fas fa-user-slash"></i>
                You are banned (<?= date_range($data->competitor->ban->start_date, $data->competitor->ban->end_date) ?>)
                - <?= $data->competitor->ban->reason ?>
            </span>
        <?php } ?>
        <a href="<?= $data->competitor->link ?>">
            <i class="fas fa-user"></i>
            <?= ml('Competitor.MyResults') ?>
        </a>
        <a href="<?= PageIndex() ?>Competitions/Mine">
            <i class="fas fa-cube"></i>
            <?= ml('Competitor.MyCompetitions') ?>
        </a>
        <?php if ($data->delegate) { ?>
            <a href="<?= $data->delegate->link ?>">
                <i class="fas fa-user-tie"></i> 
                Delegate page
            </a>
            <?php if ($data->delegate->candidates->show) { ?>
                <a href='<?= $data->delegate->candidates->link ?>'>
                    <i class="fas fa-baby"></i>
                    <?= $data->delegate->candidates->new ?> Candidates for delegates
                </a>
            <?php } ?>
            <?php foreach ($data->delegate->links as $link) { ?>
                <a href="<?= $link->link ?>"><?= $link->value ?></a>
            <?php } ?>
        <?php } ?>
    <?php } ?>

</div>     