<?php DataBaseClass::Query('Select * from  News where TO_DAYS(now()) - TO_DAYS(Date) <14 order by ID desc');
            $News=DataBaseClass::getRows();
 
 if(sizeof($News)){ ?>
<table class="table_info">
    <tbody>
    <?php foreach($News as $news){ ?>
    <tr>
           <?php $text=ml_json($news['Text']);
           $text_new=$text;
           $text_line=explode("\n",$text);
           if(isset($text_line[0])){
               $text_new=$text_line[0];
           } ?>
           <td><i class="far fa-newspaper"></i> <?= date_range($news['Date']) ?></td>
           <td>    
               <?= $text_new ?>
               <?php if($text_new!=$text){ ?>
                   <a href='<?= PageIndex()?>News'><?= ml('News.Announce.More') ?></a>
               <?php } ?>
           </td>
    </tr>       
   <?php  }  ?>
    </tbody>
</table>
<?php } ?>
 
<?= mlb('News.Announce.More') ?>