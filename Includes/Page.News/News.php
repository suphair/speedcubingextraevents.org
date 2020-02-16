<h1><?= ml('News.Title') ?></h1>

<?php if(CheckAccess('aNews')){ ?>
<table class="table_info">
    <tr>
        <td><i class="fas fa-plus-square"></i></td>
        <td><a href='<?= PageIndex() ?>/aNews/Add'>Add aNews</a></td>
    </tr>
</table> 
<?php } ?>
<table class="table_new" width="80%">
    <tbody>
<?php 
DataBaseClass::Query("Select N.*,C.Name from News N left outer join Competitor C on C.WID=N.Delegate order by ID desc");
foreach(DataBaseClass::getRows() as $news){ ?>
    <tr>    
    <?php if(ml_json($news['Text'])){ ?>
           <?php $text=ml_json($news['Text']);
            $text_line=explode("\n",$text); 
            $text_header=$text_line[0];
            unset($text_line[0]);
            $text_body=implode("<br>",$text_line); ?>
            <td><?= date_range($news['Date'])?></td>
            <td class="table_new_bold"><?= $text_header ?></td>
            <td><?= Parsedown($text_body) ?></td>
        <?php if(CheckAccess('aNews')){ ?>
            <td><?= Short_Name($news['Name']) ?></td>
            <td><button onclick="document.location.href='<?= PageIndex()?>aNews/<?= $news['ID'] ?>'"><i class="fas fa-edit"></i> Edit</button>
        <?php } ?>            
<?php } ?>
    </tr>
    <?php } ?>
    <tbody>
</table>