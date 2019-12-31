<h1><?= ml('Delegate.Add') ?></h1>
<div class="wrapper">
    <div class="form">
        <form method="POST" action="<?= PageAction("Delegate.Add")?>">
        <div class="form_field">
            WCA ID  
        </div>
        <div class="form_input">
            <input required type="text" name="WCAID" value="" />
        </div>
        <div class="form_enter">
            <input type="submit" value="<?= ml('*.Add',false)?>">
        </div>
        </form>
    </div>
</div>