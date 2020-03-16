<?php $Delegate= getDelegate(); ?>
<h1>Competition / Add</h1>
<table class="table_info">
    <form method="POST" action="<?= PageAction('Competition.Add') ?>">
    <tr>
        <td>Competition ID</td>
        <td><input required type="text" name="WCA" value="" /></td>
    </tr>    
    <?php if(CheckAccess('Competition.Add.Ext')){ ?>
    <tr>
        <td>Delegate SEE <i class="fas fa-crown"></i></td>
        <td>
             <select name="Delegate">
                <?php foreach(DataBaseClass::SelectTableRows('Delegate') as $delegate_row){ ?>
                    <option  <?= $delegate_row['Delegate_ID']==$Delegate['Delegate_ID']?'selected':'' ?> value="<?= $delegate_row['Delegate_ID'] ?>">
                        <?= $delegate_row['Delegate_Status']=='Archive'?'- ':'' ?>
                        <?= Short_Name($delegate_row['Delegate_Name']) ?>
                    </option>
                <?php } ?>
            </select>           
        </td>
    </tr>   
    <?php }else{ ?>
    <tr>
        <td>Delegate SEE</td>
        <td><?= Short_Name($Delegate['Delegate_Name']) ?></td>
    </tr>   
    <?php } ?>
    <tr>
        <td></td>
        <td><button><i class="fas fa-plus-square"> Create</button></td>
    </tr>
        </form>
    <?php $err=GetMessage("CompetitionCreate");
        if($err){ ?>
    <tr>
        <td><i class="fas fa-exclamation-triangle"></i></td>
        <td><?= $err?></td>
    </tr>    
        <?php } ?>
  </table>