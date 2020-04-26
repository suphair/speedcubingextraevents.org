<?php if ($data->newsExists) { ?>
    <div class="news_annuonce">
        <table class="table_info">
            <tbody>
                <?php foreach ($data->news as $news) { ?>
                    <tr>
                        <td>
                            <?= $news->date ?>
                        </td>
                        <td>
                            <?php if ($news->text) { ?>
                                <a class="news_panel_link local_link" href='#'>
                                    <?= $news->title ?>
                                </a>
                                <p>
                                    <?= $news->text ?>
                                </p>

                            <?php } else { ?>
                                <?= $news->title ?>                                
                            <?php } ?>
                        </td>
                    </tr>       
                <?php } ?>
            </tbody>
        </table>
    </div>    
<?php } ?>