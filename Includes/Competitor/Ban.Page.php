<?php
$data = ban::get_data();
ban::clear_data();
?>
<br>
<h2 class="error">
    <i class="fas fa-user-slash"></i>
    You are banned!
</h2>
<table class="table_info">
    <tr>
        <td>
            Name 
        </td>
        <td>
            <?= $data['competitor']->name ?>
        </td>
    </tr>
    <tr>
        <td>
            WCA ID 
        </td>
        <td>
            <?= $data['competitor']->wca_id ?>
        </td>
    </tr>
    <tr>
        <td>
            From date 
        </td>
        <td>
            <?= date_range($data['start_date']) ?>
        </td>
    </tr>   
    <tr>
        <td>
            Until date
        </td>
        <td>
            <?= date_range($data['end_date']) ?>
        </td>
    </tr>
    <tr>
        <td>
            Reason
        </td>
        <td class="error">
            <?= $data['reason'] ?>
        </td>
    </tr>
</table>
