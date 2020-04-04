<h1 data-set-title='<?= $data->competitor->name ?>,Delegate'>
    <?= $data->competitor->country->image ?>
    <?= $data->competitor->name ?>
</h1>  
<table class="table_info">
    <?php if ($data->accessSettings) { ?>
        <tr>
            <td>
                <i class="fas fa-cog"></i>
            </td>
            <td>
                <a href="<?= $data->link ?>/Settings">
                    Settings
                </a>
            </td>
        </tr>   
    <?php } ?>
    <td>
        <?= ml('Delegate.Delegate') ?>
    </td>        
    <td>
        <?= ml("Delegate.{$data->status}") ?>
    </td>
    <tr>
        <td>
            <?= ml('Delegate.Country') ?>
        </td>        
        <td>
            <?= $data->competitor->country->name ?>
        </td>
    </tr>
    <?php if ($data->competitor->linkWca) { ?>    
        <tr>
            <td>
                WCA ID
            </td>
            <td>
                <a target="_blank" href="<?= $data->competitor->linkWca ?>">
                    <?= $data->competitor->wcaid ?>
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </td>
        </tr>
    <?php } ?>  
    <tr>
        <td>
            <?= ml('Delegate.Competitor') ?>
        </td>        
        <td>
            <a href="<?= $data->competitor->link ?>">
                <?= ml('Delegate.CompetitorLink') ?>
            </a>
        </td>    
    </tr>
    <?php if ($data->contact) { ?>
        <tr>
            <td>
                <?= ml('Delegate.Contacts') ?>
            </td>        
            <td>
                <?= Parsedown($data->contact) ?>
            </td>
        <tr>
        <?php } ?>    
</table>
<hr>
<h2>
    <?= ml('Delegate.Competitions') ?>
</h2>