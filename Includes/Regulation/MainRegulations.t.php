<?php if ($data->accessEdit) { ?>
    <p>
        <i class="fas fa-cog"></i>
        <a href="<?= PageIndex() ?>MainRegulations/Edit">
            Edit the main regulations
        </a>
    </p>    
<?php } ?>
<div class='main_regulations'>
    <?= $data->regulations ?>
</div>

