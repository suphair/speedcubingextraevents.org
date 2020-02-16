<?php DataBaseClass::Query('Select * from  News where TO_DAYS(now()) - TO_DAYS(Date) <14 order by ID desc');
            $News=DataBaseClass::getRows();
 
if(sizeof($News)){ ?>
<div class="AnnuonceNew">
<table class="table_info">
    <tbody>
    <?php foreach($News as $news){ ?>
    <tr>
           <?php $text=ml_json($news['Text']);
           $text_new=$text;
           $text_line=explode("\n",$text);
           if(isset($text_line[0])){
               $text_new=$text_line[0];
           }
           unset($text_line[0]); ?>
           <td><?= date_range($news['Date']) ?></td>
           <td>
               <?php if($text_new==$text){ ?>
                    <?= $text_new ?>
               <?php }else{ ?>
                   <div id="new_<?= $news['ID']?>_title">
                       <a class="news_panel_link local_link" href='#' 
                          onclick="
                            if($(this).hasClass('news_panel_link')){
                                $('#news_panel_link_open_<?= $news['ID']?>').show('fast');
                                $(this).addClass('news_panel_open_link');
                                $(this).removeClass('news_panel_link');
                            }else{
                                $('#news_panel_link_open_<?= $news['ID']?>').hide('fast');
                                $(this).addClass('news_panel_link');
                                $(this).removeClass('news_panel_open_link');
                            }
                            return false;" class="local_link">
                           <?= $text_new ?></a>
                   </div>
                   <div style="display:none" id="news_panel_link_open_<?= $news['ID']?>">
                       <?= implode('<br>',$text_line); ?>
                   </div>
               <?php } ?>
           </td>
    </tr>       
   <?php  }  ?>
    </tbody>
</table>
</div>    
<?php } ?>
 
<?= mlb('News.Announce.More') ?>