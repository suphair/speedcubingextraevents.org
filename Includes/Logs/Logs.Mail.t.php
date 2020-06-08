<h1>Logs mail</h1>
<table class="table_new">
    <thead>
        <tr>
            <td>
                DateTime
            </td>
            <td>
                To
            </td>
            <td>
                Subject
            </td>
            <td>
                Result
            </td>
        </tr>
    </thead>
    <tbody>    
        <?php foreach ($data as $row) { ?>
            <tr>
                <td>
                    <?= $row->timestamp ?>
                </td>
                <td>
                    <?= $row->to ?>
                </td>
                <td>
                    <?= $row->subject ?>
                </td>
                <td> <span class="status_mail <?= $row->status ?>"></span>                        
                    <?= $row->result ?>
                </td>
                <td>
                    <a href="#" data-message>
                        message
                    </a>
                </td>    
            </tr>        
            <tr data-hidden = 1>    
                <td colspan="4" class="mail_body">
                    <?= $row->message ?>
                </td>
            </tr>
        <?php } ?>         
    </tbody>
</table>
