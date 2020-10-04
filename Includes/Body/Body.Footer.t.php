<div class='footer'>
    <a href="mailto:<?= $data->contacts->seniors->mail ?>?subject=<?= $data->contacts->seniors->subject ?>">
        <i class="far fa-envelope"></i>
        <?= ml('Footer.Contact.Delegates') ?>
    </a>
    <a href="mailto:<?= $data->contacts->support->mail ?>?subject=<?= $data->contacts->support->subject ?>">
        <i class="far fa-envelope"></i>
        <?= ml('Footer.Contact.Support') ?>
    </a>
    <a href="<?= PageIndex() ?>/Icons">
        <i class="fas fa-image"></i>
        <?= ml('Footer.Icons') ?>
    </a>
    <a target="_blank" href="https://github.com/suphair/speedcubingextraevents.org">
        <i class="fab fa-github"></i>
        GitHub
    </a>
    <a href="<?= PageIndex() ?>/Export">
        <i class="fas fa-download"></i>
        <?= ml('Footer.Export') ?>
    </a>
</div>    