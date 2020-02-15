<form name="LoginAlternative"  method="POST" action="<?= PageAction('Competitor.Login.Alternative')?>">           
<table class="table_info">
    <tr>
        <td>Your secret code</td>
        <td><input required name="Secret"/></td>
    </tr>
    <tr>
        <td>as WCA Delegate</td>
        <td><input name="WCA" type="checkbox"></td>
    </tr>
    <?php $message=GetMessage('Alternative');
    if($message){ ?>
    <tr>
        <td><?= svg_red(); ?></td>
        <td><?= $message ?></td>
    </tr>    
    <?php } ?>        
    <tr>
        <td/>
        <td><button><i class="fas fa-user-secret"></i> Enter</button></td>
    </tr>
</table>        
</form> 
