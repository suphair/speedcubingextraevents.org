<h1>
    Logo
</h1>
<h3>
    JPG
</h3>
<a data-image-link='<?= PageIndex() ?>/Logo/Logo_Color.jpg'></a>
<a data-image-link='<?= PageIndex() ?>/Logo/Logo_Black.jpg'></a>
<h3>
    PNG
</h3>
<a data-image-link='<?= PageIndex() ?>/Logo/Logo_Color.png'></a>
<a data-image-link='<?= PageIndex() ?>/Logo/Logo_Black.png'></a>
<h1>
    Events
</h1>
<h3>
    SVG
</h3>
<?php foreach ($data->filenames as $filename) { ?>
    <a data-image-link='<?= PageIndex() ?>/Svg/<?= $filename ?>'></a>
<?php } ?>