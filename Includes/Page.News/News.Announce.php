<?php DataBaseClass::Query('Select * from  News where TO_DAYS(now()) - TO_DAYS(Date) <14 order by ID desc');
            $News=DataBaseClass::getRows();
 
 if(sizeof($News)){ ?>
<div class='form2'>
    <?php foreach($News as $news){ ?>
           <?php $text=ml_json($news['Text']);
           $text_new=$text;
           $text_line=explode("<br>",Echo_format($text));
           if(isset($text_line[0])){
               $text_new=$text_line[0];
           } ?>
           <p><?= date_range($news['Date']) ?>: <?= $text_new ?>    
           <?php if($text_new!=$text){ ?>
               <a href='<?= PageIndex()?>News'><?= ml('News.Announce.More') ?></a>
           <?php } ?>
           </p>
   <?php  }  ?>
</div>
<?php } ?>
 
<?= mlb('News.Announce.More') ?>