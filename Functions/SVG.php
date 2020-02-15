<?php
function svg_green($size=10,$title=''){
    ob_start(); ?><i style="color:var(--light_green)" class="fas fa-check-circle"></i><?php
   $return=ob_get_contents();
   ob_end_clean();
   return $return;
}

function svg_red($size=10,$title=''){
    ob_start(); ?><i style="color:var(--light_red)" class="fas fa-minus-circle"></i><?php
   $return=ob_get_contents();
   ob_end_clean();
   return $return;
}

function svg_blue($size=10,$title=''){
    ob_start(); ?><i style="color:var(--light_blue)" class="fas fa-plus-circle"></i><?php
   $return=ob_get_contents();
   ob_end_clean();
   return $return;
}

