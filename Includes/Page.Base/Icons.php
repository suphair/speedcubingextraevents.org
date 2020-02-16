<h1>Logo</h1>
<h3>JPG</h3>
<a target='_blank'  href='<?= PageIndex() ?>Logo/Logo_Color.jpg'><img width='50px' src='<?= PageIndex() ?>Logo/Logo_Color.jpg'></a>
<a target='_blank'  href='<?= PageIndex() ?>Logo/Logo_Black.jpg'><img width='50px' src='<?= PageIndex() ?>Logo/Logo_Black.jpg'></a>
<h3>PNG</h3>
<a target='_blank'  href='<?= PageIndex() ?>Logo/Logo_Color.png'><img width='50px' src='<?= PageIndex() ?>Logo/Logo_Color.png'></a>
<a target='_blank' href='<?= PageIndex() ?>Logo/Logo_Black.png'><img width='50px' src='<?= PageIndex() ?>Logo/Logo_Black.png'></a>
<h1>Events</h1>
<h3>SVG</h3>
<?php
    foreach (scandir('Svg') as $filename){
        if(strpos($filename,".svg")){ ?>
        <a target='_blank'  href='<?= PageIndex() ?>Svg/<?= $filename ?>'>
            <img width=50px title='<?= $filename ?>' src='<?= PageIndex() ?>Svg/<?= $filename ?>'>
        </a>
        <?php }
    }
?>