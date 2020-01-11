<?php includePage('Navigator'); ?>
<h1><?= ml('News.Title') ?></h1>
<?php DataBaseClass::Query("Select N.*,C.Name from News N left outer join Competitor C on C.WID=N.Delegate order by ID desc"); ?>
<table  width="100%"> 
<?php foreach(DataBaseClass::getRows() as $news){ 
    if(ml_json($news['Text'])){ ?>
    <tr>
        <td width="100px" style="padding:4px 0px 12px 0px">
           <b><?= date_range($news['Date']) ?></b>
        </td>
        <td style="padding:4px 0px 12px 0px">
           <?php $text=ml_json($news['Text']);
            $text_line=explode("/n",$text); 
            if(isset($text_line[0])){
                $text_line[0]="###".$text_line[0];
            }
            $text=implode("<br>",$text_line); ?>
            <?= Parsedown($text) ?>
        </td>
        <?php if(CashDelegate()){ ?>
            <td width="200px">
               <?= Short_Name($news['Name']) ?>
            </td>
        <?php } ?>
        <?php if(CheckAccess('aNews')){ ?>
            <td>
                <a href='<?= PageIndex()?>aNews/<?= $news['ID'] ?>'>Edit</a>
            </td>
        <?php } ?>
    </tr>      
<?php }
    } ?>
</table>    