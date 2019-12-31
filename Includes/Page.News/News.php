<?php includePage('Navigator'); ?>
<h1><?= ml('News.Title') ?></h1>
<?php DataBaseClass::Query("Select N.*,C.Name from News N left outer join Competitor C on C.WID=N.Delegate order by ID desc"); ?>
<table  width="100%"> 
<?php foreach(DataBaseClass::getRows() as $news){ 
    if(ml_json($news['Text'])){ ?>
    <tr>
        <td width="100px">
           <?= date_range($news['Date']) ?>
        </td>
        <td>
           <?php $text=ml_json($news['Text']);
            $text_line=explode("<br>",str_replace("\n","<br>",$text)); 
            if(isset($text_line[0])){
                $text_line[0]="<span class=message>".$text_line[0]."</span>";
            }
            $text=implode("<br>",$text_line); ?>
            <?= $text ?>
            <br><br>
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