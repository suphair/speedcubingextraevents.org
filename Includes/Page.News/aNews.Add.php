<h1>News / Add</h1>
<table class="table_info">
    <tr>
        <td></td>
        <td>The first line is the title</td>
    </tr>   
    <tr>
        <td></td>
        <td>Markdown is used</td>
    </tr> 
    <form method="POST" action="<?= PageAction('aNews.Add') ?>">
    <?php foreach(getLanguages() as $language){ ?>
    <tr>
        <td>
            <?= ImageCountry($language, 30)?>
            <?= CountryName($language,true)?>
        </td>
        <td>
            <textarea class="big_data" name="anews[<?=$language ?>]"></textarea>
        </td>        
    </tr>    
    <?php } ?>
    <tr>
        <td></td>
        <td><button><i class="fas fa-plus-circle"></i> Add a news</button></td>
    </tr> 
    </form>
</table>