<?php $type = RequestClass::getPage();
if(explode('.',$type)[0]=='aNews')$type='News';
if(explode('.',$type)[0]=='Delegate' or explode('.',$type)[0]=='Delegates')$type='Delegates';
if(explode('.',$type)[0]=='Competitor')$type='Competitors';
if(explode('.',$type)[0]=='Event')$type='Events';
if(explode('.',$type)[0]=='Competition' or $type=='index')$type='Competitions';
?>
<div class="navigator">
        <div data-section="Competitions">
            <a href="#"><i class="fas fa-cube"></i> <?= ml('Navigator.Competitions') ?></a>
        </div>
    
        <div data-section="Regulations">
            <a href="#"><i class="fas fa-book"></i> <?= ml('Navigator.Regulations') ?></a>
        </div>
    
        <div data-section="Records">
            <a href="#"><i class="fas fa-trophy"></i> <?= ml('Navigator.Records') ?></a>
        </div>
 
        <div data-section="Competitors">
            <a href="#"><i class="fas fa-users"></i> <?= ml('Navigator.Competitors') ?></a>
        </div>

        <div data-section="Events">    
            <a href="#"><i class="fas fa-star"></i> <?= ml('Navigator.Events') ?></a>
        </div>
            
        <div data-section="Delegates">
            <a href="#"><i class="fas fa-sitemap"></i> <?= ml('Navigator.Delegates') ?></a>
        </div>
    
        <div data-section="News">
            <a href="#"><i class="far fa-newspaper"></i> <?= ml('Navigator.News') ?></a>
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
    $('div.navigator div').each(function( index ) {
        var section=$(this).data('section');
        $(this).find('a').attr('href','<?= PageIndex()?>' + section);
    });
</script>

