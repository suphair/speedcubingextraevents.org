<?php $type = RequestClass::getPage();
if(explode('.',$type)[0]=='aNews')$type='News';
if(explode('.',$type)[0]=='Delegate' or explode('.',$type)[0]=='Delegates')$type='Delegates';
if(explode('.',$type)[0]=='Competitor')$type='Competitors';
if(explode('.',$type)[0]=='Event')$type='Events';
if(explode('.',$type)[0]=='Competition' or $type=='index')$type='Competitions';
?>
<div class="navigator">
        <div data-section="Competitions">
            <i class="fas fa-cube"></i> <?= ml('Navigator.Competitions') ?>
        </div>
    
        <div data-section="Regulations">
            <i class="fas fa-book"></i> <?= ml('Navigator.Regulations') ?>
        </div>
    
        <div data-section="Records">
            <i class="fas fa-trophy"></i> <?= ml('Navigator.Records') ?>
        </div>
 
        <div data-section="Competitors">
            <i class="fas fa-users"></i> <?= ml('Navigator.Competitors') ?>
        </div>

        <div data-section="Events">    
            <i class="fas fa-star"></i> <?= ml('Navigator.Events') ?>
        </div>
            
        <div data-section="Delegates">
            <i class="fas fa-sitemap"></i> <?= ml('Navigator.Delegates') ?>
        </div>
    
        <div data-section="News">
            <i class="far fa-newspaper"></i> <?= ml('Navigator.News') ?>
        </div>
</div>


<script>
    $('div.navigator div').on("mouseover",function(){
        $(this).addClass('navigator_hover');
    });    
    
    $('div.navigator div').on("mouseout",function(){
        $(this).removeClass('navigator_hover');
    });    
    
    $('div.navigator div').on("click",function(){
        document.location.href='<?= PageIndex()?>' + $(this).data('section');
    });    
    
    $('div.navigator div[data-section="<?= $type ?>"]').addClass("select");
</script>

