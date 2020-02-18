<h1>SEE results export</h1>
On this page we offer the SEE results for download in format SQL statements, for import into SQL databases.<br>
So you can use/analyze them at large. <br>
The results archive is created daily. <br>
<br>
<?php            
$dir = "Export_sql";
$files = array();
foreach (scandir($dir) as $file)if(strpos($file,".zip")) $files[$file] = filectime('Export_sql/'.$file);
asort($files);
$files= array_reverse($files);
foreach($files as $file=>$time)
    if($time > time() - 60*60*24*7*2){ ?>
    <p>
      <?= date("F d Y H:i:s",$time)?> (UTC 0)
      <b><a href="<?= PageIndex()?>Export_sql/<?= $file ?>"><i class="fas fa-download"></i> <?= $file ?></a></b>  
      (<?= round(filesize('Export_sql/'.$file)/1024,1); ?> KB)
    </p>  
<?php }