<h1>
    <?= ml('News.Title') ?>
</h1>

<?php if ($data->access) { ?>
    <button data-href = '<?= PageIndex() ?>/aNews/Add'>
        <i class="fas fa-plus-square"></i>
        Add
    </button>
<?php } ?>

<table class="table_new" data-access-news-edit="<?= $data->access ?>">
    <tbody>
        <?php foreach ($data->news as $news) { ?>
            <tr>    
                <td>
                    <?= $news->date ?>
                </td>
                <td class="news_text">
                    <span class="news_title"><?= $news->title ?></span>
                    <?= $news->text ?>
                </td>
                <td data-news-edit>
                    <?= $news->author->competitor->name ?>
                </td>
                <td data-news-edit>
                    <button data-href = '<?= PageIndex() ?>aNews/<?= $news->id ?>'>
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                </td>
            </tr>
        <?php } ?>
    <tbody>
</table>