<style>
    .Logo img {
        margin:5px;
        border:1px solid white;
        background-color: #EEE;
        height:90px;
    }
    .Logo img:hover{
        border:1px solid gray;
        background-color: #FFF;
    }
</style>
<div class='Logo'>
<h2>Logos .JPG</h2>
<a target='_blank'  href='<?= PageIndex() ?>Logo/Logo_Color.jpg'><img src='<?= PageIndex() ?>Logo/Logo_Color.jpg'></a>
<a target='_blank'  href='<?= PageIndex() ?>Logo/Logo_Black.jpg'><img src='<?= PageIndex() ?>Logo/Logo_Black.jpg'></a>
<h2>Logos .PNG</h2>
<a target='_blank'  href='<?= PageIndex() ?>Logo/Logo_Color.png'><img src='<?= PageIndex() ?>Logo/Logo_Color.png'></a>
<a target='_blank' href='<?= PageIndex() ?>Logo/Logo_Black.png'><img src='<?= PageIndex() ?>Logo/Logo_Black.png'></a>
</div>
<div class='Logo'>
<h2>Icons .SVG</h2>
<?php
    foreach (scandir('Svg') as $filename){
        if(strpos($filename,".svg")){ ?>
        <a target='_blank'  href='<?= PageIndex() ?>Svg/<?= $filename ?>'><img title='<?= $filename ?>' src='<?= PageIndex() ?>Svg/<?= $filename ?>'></a>
        <?php }
    }
?>
</div>