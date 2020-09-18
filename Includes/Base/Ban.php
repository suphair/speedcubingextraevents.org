<?php 
$data=ban::get_data();
ban::clear_data();
?>
<h1 class="error">You are banned!</h1>
<h2><?= $data['competitor']->wca_id ?>: <?= $data['competitor']->name ?></h2>
<p><?= date_range($data['start_date'],$data['end_date']) ?></p>
<p><?= $data['reason'] ?></p>
