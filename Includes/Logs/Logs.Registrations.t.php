<h1>
    Logs registrations
</h1>

<table class="table_info">
    <tr>
        <td>
            Legend
        </td>
        <td/>
    </tr>    
    <tr>
        <td>
            <span class="registration_icon competitor"></span>
        </td>
        <td>
            Competitor
        </td>
    </tr>    
    <tr>
        <td>
            <span class="registration_icon delegate"></span>
        </td>
        <td>
            Delegate
        </td>
    </tr>    
    <tr>
        <td>
            <span class="registration_icon scoretaker"></span>
        </td>
        <td>
            ScoreTaker
        </td>
    </tr>    
</table>
<table class="table_new">
    <thead>
    <td>
        DateTime
    </td>
    <td>
        Action
    </td>
    <td>
        Competition
    </td>
    <td>
        Event
    </td>
    <td>
        Round
    </td>
    <td>
        Name
    </td>
    <td>
        Who did it
    </td>
</thead>
<?php foreach ($data as $row) { ?>
    <tr>
        <td>
            <?= $row->timestamp ?>
        </td>
        <td>
            <span class="registration_icon <?= $row->actionIcon ?>"></span>
            <?= $row->action ?>    
        </td>
        <td>
            <?= $row->competitionEvent->competition->country->image ?>
            <?= $row->competitionEvent->competition->name ?>
        </td>
        <td>
            <?= $row->competitionEvent->event->image ?>
            <?= $row->competitionEvent->event->name ?>
        </td>
        <td class="table_new_center">
            <?= $row->competitionEvent->round ?>
        </td>
        <td>
            <?= $row->details ?>
        </td>
        <td>
            <span class="registration_icon <?= $row->activistIcon ?>"></span>    
            <?= $row->activist ?>
        </td>
    </tr>
<?php } ?>
</table>