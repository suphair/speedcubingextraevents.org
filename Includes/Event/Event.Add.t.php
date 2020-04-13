<h1>
    Event / Add
</h1>

<table class='table_info'>
    <form method='POST' action='<?= PageAction('Event.Add') ?>'>
        <tr>
            <td>
                Name
            </td>
            <td>
                <input type='text' name='Name' value='' />
            </td>
        </tr>    
        <tr>
            <td>
                Code
            </td>
            <td>
                <input type='text' name='Code' value='' />
            </td>
        </tr>   
        <tr>
            <td></td>
            <td>
                <button>
                    <i class='fas fa-plus-square'></i>
                    Create
                </button>
            </td>
        </tr>   
</table>