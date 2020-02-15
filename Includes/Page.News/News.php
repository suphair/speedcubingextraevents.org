<h1><?= ml('News.Title') ?></h1>
<?php DataBaseClass::Query("Select N.*,C.Name from News N left outer join Competitor C on C.WID=N.Delegate order by ID desc"); ?>
 
<?php foreach(DataBaseClass::getRows() as $news){ 
    if(ml_json($news['Text'])){ ?>
           <?php $text=ml_json($news['Text']);
            $text_line=explode("/n",$text); 
            if(isset($text_line[0])){
                $text_line[0]="###".date_range($news['Date']).": ".$text_line[0];
            }
            $text=implode("<br>",$text_line); ?>
            <?= Parsedown($text) ?>
        
        <?php if(CashDelegate()){ ?>
               Author - <?= Short_Name($news['Name']) ?>
        <?php } ?>
        <?php if(CheckAccess('aNews')){ ?>
                <a href='<?= PageIndex()?>aNews/<?= $news['ID'] ?>'>Edit</a>
        <?php } ?>
    <hr>            
<?php }
    } ?>