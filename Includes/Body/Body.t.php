<?php IncludeClass::Page('Body.Header', $data); ?>
<?php IncludeClass::Page('Body.Competitor', $data); ?>
<div>
    <?php IncludeClass::Page('Body.Navigator', $data); ?>
    <?php IncludeClass::Page(RequestClass::getPage()); ?>
    <?php IncludeClass::Page('Body.Footer', $data); ?>
</div>     