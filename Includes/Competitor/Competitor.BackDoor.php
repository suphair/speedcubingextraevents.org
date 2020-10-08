<h1 class="error">
    Back door
</h1>
<pre>
    <?php
    $competitor = getCompetitor();
    if ($competitor) {
        print_r($competitor);
    }
    ?>
</pre>
<form action="<?= PageAction('Competitor.BackDoor.Login') ?>" method="POST">
    <input required="" name="ID">
    <button>
        <i class="fas fa-key"></i> 
        Enter
    </button>
    <p class="error">
        <?= GetMessage('BackDoor') ?>
    </p>
</form>
