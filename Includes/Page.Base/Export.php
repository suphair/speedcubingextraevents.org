<h1>SEE results export</h1>
On this page we offer the SEE results for download. <br>
So you can use/analyze them at large. <br>
The results archive is created daily. <br>

<h3>SQL statements, for import into SQL databases</h3>
<?php            
$dir = "Export_sql";
$files = array();
foreach (scandir($dir) as $file)if(strpos($file,".zip")) $files[$file] = filectime($dir.'/'.$file);
asort($files);
$files= array_reverse($files);
if(sizeof($files)){
    $file=array_keys($files)[0]; 
    $time= $files[$file] ?>
    <p>
        <?= date("F d Y H:i:s",$time)?> (UTC +3)
        <br><a href="<?= PageIndex().$dir?>/<?= $file ?>"><i class="fas fa-download"></i> <?= $file ?></a> 
        (<?= round(filesize($dir.'/'.$file)/1024,1); ?> KB)
    </p>    
<?php } ?>
    
<h3>Tab-separated values, for spreadsheets in OpenOffice.org, Excel, etc.</h3>
<?php            
$dir = "Export_tsv";
$files = array();
foreach (scandir($dir) as $file)if(strpos($file,".zip")) $files[$file] = filectime($dir.'/'.$file);
asort($files);
$files= array_reverse($files);
if(sizeof($files)){
    $file=array_keys($files)[0]; 
    $time= $files[$file] ?>
    <p>
      <?= date("F d Y H:i:s",$time)?> (UTC +3)
      <br><a href="<?= PageIndex().$dir ?>/<?= $file ?>"><i class="fas fa-download"></i> <?= $file ?></a>
      (<?= round(filesize($dir.'/'.$file)/1024,1); ?> KB)
    </p>  
<?php } ?>