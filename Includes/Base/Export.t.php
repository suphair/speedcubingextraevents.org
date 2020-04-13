<h1>
    SEE results export
</h1>

On this page we offer the SEE results for download.<br>
So you can use/analyze them at large.<br>
The results archive is created daily.<br>

<table class='table_new'>
    <tbody>
        <?php foreach ($data->files as $file) { ?>
            <tr>
                <td>
                    <span data-export-icon='<?= $file->format ?>'></span>
                    <?= $file->format ?>
                </td>
                <td>
                    <a href='<?= $file->link ?>'>
                        <i class='fas fa-download'></i>
                        <?= $file->name ?>
                    </a>
                </td>
                <td>
                    <?= $file->size ?> 
                    KB
                </td>
                <td>
                    <?= $file->time ?> (UTC +3)
                </td>
            </tr>    
        <?php } ?>
    </tbody>
</table>

<b><span data-export-icon='SQL'></span>
    SQL statements</b>, for import into SQL databases<br>
<b><span data-export-icon='TSV'></span>
    Tab-separated values</b>, for spreadsheets in OpenOffice.org, Excel, etc.<br>