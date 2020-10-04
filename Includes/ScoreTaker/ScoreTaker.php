<!DOCTYPE HTML>
<html>
    <?php
    $Secret = getPathElement('scoretaker', 1);
    if (!$Secret) {
        exit();
    }

    $competitionEvent = new CompetitionEvent();
    $competitionEvent->getBySecret($Secret);
    $competitionEvent->event->getFormatResult();

    if (!$competitionEvent->id) {
        echo "not found";
        exit();
    }
    ?><head>
        <title>
            <?= $competitionEvent->event->name ?> /
            Round <?= $competitionEvent->round ?>
        </title>
        <?= IncludeClass::Page('Index.Head') ?>
    </head>
    <body>
        <?php
        $Next = false;
        DataBaseClass::Query("Select E.vRound,E.Round, E.Competitors, count(distinct Com.ID) Commands "
                . " from Event E "
                . " join DisciplineFormat DF on E.DisciplineFormat=DF.ID "
                . " left outer join Command Com on Com.Event=E.ID"
                . "  where Round=" . ($competitionEvent->round + 1) . " and Competition=" . $competitionEvent->competition->id . " and Discipline=" . $competitionEvent->event->id . " group by E.ID");
        if (DataBaseClass::rowsCount() > 0) {
            $Next = DataBaseClass::getRow();
        }


        DataBaseClass::Query("select GROUP_CONCAT(C.Name order by C.Name SEPARATOR ', ') vName, Com.Onsite, Com.Warnings, case when Com.Place>0 then Com.Place else '' end Place, Com.ID, Com.Decline,Com.CardID "
                . " from `Command` Com"
                . " join CommandCompetitor CC on CC.Command=Com.ID join Competitor C on C.ID=CC.Competitor "
                . " where Com.Event='{$competitionEvent->id}' "
                . " group by Com.ID order by case when Com.Place>0 then Com.Place else 999 end, Com.Decline, 1");
        $commands = DataBaseClass::getRows();

        foreach ($commands as $j => $command) {
            $names = array();
            foreach (explode(", ", $command['vName']) as $name) {
                $names[] = trim(explode("(", $name)[0]);
            }
            $commands[$j]['vName'] = implode(", ", $names);
        }

        DataBaseClass::Query("select *"
                . " from `Command` Com"
                . " left outer join Attempt A on A.Command=Com.ID and A.Attempt is not null"
                . " where A.ID is null and Com.Event='{$competitionEvent->id}' and Com.Decline=0");
        $notAttempts = sizeof(DataBaseClass::getRows());

        if ($Next) {
            $commandsWinner = min($Next['Competitors'], floor(sizeof($commands) * 0.75));
        } else {
            $commandsWinner = 3;
        }
        ?>
        <h2 data-competitionevent='<?= $competitionEvent->id ?>'>
            <?php if (CheckAccess('Competition.Event.Settings', $competitionEvent->competition->id)) { ?>
                <?php $competitionEvent->competition->getCompetitionEvents(); ?>
                <?php foreach ($competitionEvent->competition->competitionEvents as $competitionEventSelect) { ?>
                    <a  href="<?= PageIndex() ?>/ScoreTaker/<?= $competitionEventSelect->secret ?>">
                        <span data-competitionevent-list='<?= $competitionEventSelect->id ?>'>
                            <?= $competitionEventSelect->event->image ?>
                        </span>
                    </a>
                <?php } ?>
            <?php } ?>
            <span class='score_taker_title'>
                <?= $competitionEvent->competition->country->image ?>
                <?= $competitionEvent->competition->name ?>
                <i class="fas fa-chevron-right"></i>
                <?= $competitionEvent->event->image ?>
                <?= $competitionEvent->event->name ?> 
                <i class="fas fa-chevron-right"></i>
                Round <?= $competitionEvent->round ?> 
            </span>
        </h2>

        <?php if ($competitionEvent->event->isCup) { ?>
            <?php include 'ScoreTakerCup.php'; ?>
        <?php } ?>

        <script>
            var submitResult;
            var Attemption =<?= $competitionEvent->attemptions ?>;
            var isCutoff =<?= $competitionEvent->cutoff->enable ? 'true' : 'false' ?>;
            var cutoff_minute =<?= $competitionEvent->cutoff->minute ?>;
            var cutoff_second =<?= $competitionEvent->cutoff->second ?>;
            var limit_minute =<?= $competitionEvent->limit->minute ?>;
            var limit_second =<?= $competitionEvent->limit->second ?>;
            var limits = [];
            var cutoffs = [];
            var disciptions = [];
            var CutoffN =<?= $competitionEvent->attemptionsCutoff + 1 ?>;
        </script>    
        <?php if ($competitionEvent->event->isTeam) { ?>    
            <p>
                Find the Team using ID on a score card, WCA ID or Name of one of the Team members 
                OR choose the Team from the table (click Team member’s Name).
            </p>
            <?php if ($competitionEvent->competition->onsite) { ?>
                <p>
                    Create a new Team using <?= $competitionEvent->event->competitorsTeam ?>
                    WCA Registrations (you can find them by WCA ID or Name).
                </p>                
            <?php } ?>
        <?php } else { ?>
            <p>
                Find the Competitor using ID on a score card, WCA ID or Name 
                OR choose the Competitor from the table (click Competitor’s Name).
            </p>
            <?php if ($competitionEvent->competition->onsite) { ?>
                <p>
                    Create a new Competitor using WCA Registration (you can find it by WCA ID or Name).
                </p>
            <?php } ?>
        <?php } ?>

        <?php
        $message = GetMessage('ResultsSave');
        $warning = GetMessage('ResultsSaveWarning');
        ?>

        <?php if ($message) { ?>
            <p>
                <i class="fas fa-check-circle"></i>
                <?= $message ?>
            </p>       
        <?php } ?>

        <?php if ($warning) { ?>
            <p>
                <i class="fas fa-exclamation-circle"></i>
                <?= $warning ?>
            </p>       
        <?php } ?>

        <table width="100%">
            <tr>            
                <td width="10%">
                    <form data-form-scoretaker
                          method="POST" 
                          action="<?= PageAction('ScoreTaker.Add') ?>" >    
                        <select tabindex="1" ID="Registration" style="width: 300px;" Name="Selected[]" 
                        <?php if ($competitionEvent->competition->onsite) { ?> 
                                    data-placeholder="<?= $competitionEvent->event->isTeam ? ("Choose {$competitionEvent->event->competitorsTeam} Registrations or Team" ) : 'Choose Registration or Competitor' ?>"
                                <?php } else { ?>
                                    data-placeholder="<?= $competitionEvent->event->isTeam ? 'Choose Team' : 'Choose Competitor' ?>" 
                                <?php } ?>
                                class="chosen-select chosen-select-<?= $competitionEvent->event->competitorsTeam ?>" multiple
                                onchange="
                                        var competitors =<?= $competitionEvent->event->competitorsTeam ?>;
                                        $('.CommandSelect').attr('disabled', false);
                                        updatedChosen();
                                        var selected_count = $('#Registration option:selected').length;
                                        if (selected_count > 0) {
                                            var id = $('#Registration option:selected').attr('id');
                                            if (id.indexOf('Command') >= 0) {
                                                setChosenOptions(1);
                                                chosenSelectCommandID($('#Registration').val());
                                                $('#value1').focus();
                                                $('#amount1').focus();
                                            } else {
                                                $('.CommandSelect').attr('disabled', true);
                                                setChosenOptions(competitors);
                                                updatedChosen();
                                                if (selected_count === competitors) {
                                                    chosenSelectCompetitorID();
                                                    $('.value_input').attr('disabled', false);
                                                    $('.value_amount').attr('disabled', false);
                                                    $('#value1').focus();
                                                    $('#amount1').focus();
                                                } else {
                                                    $('.chosen-search-input').focus();
                                                }
                                            }
                                        } else {
                                            setChosenOptions(competitors);
                                            chosenSelectCommandID();
                                            $('.chosen-search-input').focus();
                                            $('.value_input').attr('disabled', true);
                                        }
                                ">
                            <option value=""></option>
                            <optgroup label="<?= $competitionEvent->event->isTeam ? 'Teams' : 'Competitors' ?>">
                                <?php
                                DataBaseClass::FromTable("Command", "Event={$competitionEvent->id}");
                                foreach (DataBaseClass::QueryGenerate() as $command) {
                                    DataBaseClass::FromTable("CommandCompetitor", "Command=" . $command['Command_ID']);
                                    DataBaseClass::Join_current("Competitor");
                                    DataBaseClass::OrderClear("Competitor", "Name");
                                    $competitors = DataBaseClass::QueryGenerate();
                                    ?>
                                    <option
                                        class="CommandSelect"
                                        ID="CommandIDSelect<?= $command['Command_ID'] ?>" 
                                        value="<?= $command['Command_ID'] ?>">
                                        <?= $command['Command_CardID'] ?> : <?php
                                        foreach ($competitors as $competitor) {
                                            ?> <?= Short_Name($competitor['Competitor_Name']) ?> 
                                            <?= Short_Name($competitor['Competitor_WCAID']) ?> $BR$
                                        <?php } ?>
                                    </option>                                
                                <?php } ?> 
                            </optgroup>                        
                            <?php if ($competitionEvent->competition->onsite) { ?>
                                <optgroup label="WCA Registrations">
                                    <?php
                                    DataBaseClass::Query(" Select distinct C.* from Competitor C 
                                        join Registration Reg on Reg.Competitor=C.ID and Reg.Competition=" . $competitionEvent->competition->id . "  and C.ID 
                                        not in(select CC.Competitor from CommandCompetitor CC 
                                             join Command Com on CC.Command=Com.ID and Com.Event={$competitionEvent->id} ) order by C.Name");
                                    foreach (DataBaseClass::getRows() as $competitor) {
                                        ?>
                                        <option style="text-align:left"
                                                class="RegistrationSelect"
                                                ID="CompetitorIDSelect<?= $competitor['ID'] ?>" 
                                                value="<?= $competitor['ID'] ?>">
                                                    <?= Short_Name($competitor['Name']) ?> <?= $competitor['WCAID'] ? (' ' . $competitor['WCAID']) : '' ?>
                                        </option>    
                                    <?php } ?>
                                </optgroup> 
                            <?php } ?>           
                        </select>  
                        <div style=" margin:10px;">
                            <?php if ($competitionEvent->cutoff->enable) { ?>
                                <span id="cutoff_pre"></span>
                                <span id="cutoff" 
                                      data-cutoff-millisecond='<?= $competitionEvent->cutoff->inMilliseconds ?>'>
                                    Cutoff <?= $competitionEvent->cutoff->out ?>
                                </span> &nbsp; 
                            <?php } ?>
                            <span id="limit_pre"></span>
                            <span id="limit"
                                  data-limit-millisecond='<?= $competitionEvent->limit->inMilliseconds ?>'>
                                <?= ($competitionEvent->cumulative ? "Cumulative limit " : "Limit ") ?>
                                <?= $competitionEvent->limit->out ?>  
                            </span>
                        </div>
                        <?php for ($i = 1; $i <= $competitionEvent->attemptions; $i++) { ?>
                            <?php if ($i == $competitionEvent->attemptionsCutoff + 1) { ?>
                                <?php if ($competitionEvent->cutoff->enable) { ?>
                                    <hr style="height:2px; background: gray;" id="cutoff_hr">
                                <?php } ?>    
                            <?php } ?>     
                            <p style='white-space:nowrap'>    
                                <?php if ($competitionEvent->event->codes) { ?>                
                                    <span class=" cubing-icon event-<?= explode(",", $competitionEvent->event->codes)[$i - 1] ?>"></span>
                                <?php } else { ?>
                                    <?= $i ?>
                                <?php } ?>            
                                <?php if ($competitionEvent->event->formatResult->amount) { ?>
                                    <input tabindex="<?= $i * 2 ?>" maxlength='2' autocomplete="off" size="2" style="width:60px;  font-family: monospace; font-size: 35px;text-align: right" name="Amount<?= $i ?>" ID="amount<?= $i ?>" class="amount_input" oninput="AmountEnterOne(<?= $i ?>)" onclick="this.select();" onfocus="this.select();">
                                <?php } ?>

                                <?php if ($competitionEvent->event->formatResult->time) { ?>
                                    <input 
                                        tabindex="<?= 1 + $i * 2 ?>"
                                        maxlength='8'
                                        autocomplete="off"
                                        disabled
                                        size="8"
                                        style="width:180px; font-family: monospace; font-size: 35px;text-align: right" 
                                        name="Value<?= $i ?>" 
                                        ID="value<?= $i ?>" 
                                        class="value_input" 
                                        oninput="ValueEnterOne(<?= $i ?>)"
                                        onclick="this.select();"
                                        onfocus="this.select();">
                                <?php } ?>
                                    <span 
                                        hidden
                                        class='score_taker_warning_input' 
                                        data-warning>
                                        !
                                    </span>
                            </p>
                            <span ID="description<?= $i ?>_pre" class="description" style="color:red"></span>
                            <span ID="description<?= $i ?>" class="description" style="color:red" ></span><br>
                        <?php } ?>
                        <span style="font-size:30px;"> </span>
                        <button data-form-scoretaker-confirm
                                type='button' disabled style="width:200px;font-size:30px;">
                            Confirm
                        </button>
                        <input name="AttempsWarning" ID="AttempsWarning" type="hidden" value="" />
                        <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                        <input name="Type" ID="Type" type="hidden"  value="" />
                    </form>
                    <b>DNF</b> - F,D or *<br>
                    <b>DNS</b> - S or /            
                    <br>
                </td>
                <td width="90%">
                    <div style='height: 600px;overflow: scroll;border:1px dotted black'>        
                        <table class="table_new">    
                            <thead>
                                <tr> 
                                    <td>ID</td>            
                                    <td>
                                        <?php if ($competitionEvent->event->isTeam) { ?>
                                            Team : <?= html_spellcount($competitionEvent->event->competitorsTeam, 'competitor', 'competitors', 'competitors') ?>
                                        <?php } else { ?>
                                            Competitor
                                        <?php } ?>
                                    </td>
                                    <?php if ($competitionEvent->event->codes) { ?>                
                                        <?php for ($i = 0; $i < $competitionEvent->attemptions; $i++) { ?>
                                            <td class='table_new_right'>             
                                                <span class=" cubing-icon event-<?= explode(",", $competitionEvent->event->codes)[$i] ?>"></span>
                                            </td>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <td class='table_new_center' 
                                            colspan='<?= $competitionEvent->attemptions ?>'>  
                                            Solves
                                        </td>   
                                    <?php } ?>
                                    <?php if ($competitionEvent->formatsAmount >= 1) { ?>
                                        <td class="table_new_right">
                                            <?= $competitionEvent->formats[0] ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($competitionEvent->formatsAmount >= 2) { ?>
                                        <td class="table_new_right">
                                            <?= $competitionEvent->formats[1] ?>
                                        </td>
                                    <?php } ?>
                                    <td  class="table_new_right">Place</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </thead>
                            </tbody>
                            <script>
                                ValuesSave = [];
                                AmountsSave = [];
                            </script>
                            <?php
                            foreach ($commands as $c => $command) {
                                $team = new Team();
                                $team->getById($command['ID']);
                                $team->getAttempts();

                                DataBaseClass::Query("select * from `Attempt` A where Command='" . $command['ID'] . "' ");
                                $attempt_rows = DataBaseClass::getRows();
                                $attempts = array();


                                for ($i = 1; $i <= $competitionEvent->attemptions; $i++) {
                                    $attempts[$i] = "";
                                }
                                foreach (DataBaseClass::SelectTableRows("Format") as $format) {
                                    $attempts[$format['Format_Result']] = "";
                                }
                                ?>
                                <script>
    <?php foreach ($attempt_rows as $attempt_row) { ?>


        <?php
        $attempt = $attempt_row['vOut'];

        if ($attempt_row['Except']) {
            $attempt = "($attempt)";
        }

        if ($attempt_row['Attempt']) {
            $attempts[$attempt_row['Attempt']] = $attempt;


            if ($attempt_row['IsDNF']) {
                $string = 'DNF';
            } elseif ($attempt_row['IsDNS']) {
                $string = 'DNS';
            } else {
                $string = sprintf("%d%02d%02d", $attempt_row['Minute'], $attempt_row['Second'], $attempt_row['Milisecond']);
            }
            ?>
                                            ValuesSave['<?= $command['ID'] ?>_<?= $attempt_row['Attempt'] ?>'] = '<?= $string ?>';
                                                AmountsSave['<?= $command['ID'] ?>_<?= $attempt_row['Attempt'] ?>'] = '<?= round($attempt_row['Amount']) ?>';
            <?php
        } else {
            $attempts[$attempt_row['Special']] = $attempt;
        }
    }
    ?>
                                </script>  

                                <?php
                                DataBaseClass::FromTable('Command', 'ID=' . $command['ID']);
                                DataBaseClass::Join_current('CommandCompetitor');
                                DataBaseClass::Join_current('Competitor');
                                DataBaseClass::OrderClear('Competitor', 'ID');
                                $competitors = array();
                                foreach (DataBaseClass::QueryGenerate() as $competitor) {
                                    $competitors[] = $competitor['Competitor_ID'];
                                }
                                ?>

                                <tr data-attempts-team='<?= $team->id ?>'>
                                    <td class="table_new_bold">
                                        <span id="rowCardID<?= $team->id ?>" 
                                              data-attempt-warning = "<?= !empty($team->warnings) ?>">
                                                  <?= $team->cardId ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#">
                                            <?= $team->name ?>
                                        </a>
                                    </td>
                                    <?php for ($i = 1; $i <= $competitionEvent->attemptions; $i++) { ?>
                                        <td class="table_new_right">
                                            <span  data-attempt-except='<?= $team->attempts[$i]->except ?>'
                                                   data-attempt-number='<?= $i ?>'
                                                   data-attempt-amount='<?= $team->attempts[$i]->amount; ?>'
                                                   data-attempt-time='<?= $team->attempts[$i]->time; ?>'
                                                   id="Value<?= $team->id . "_" . $i ?>" 
                                                   data-attempt-warning = "<?= $team->attempts[$i]->warning ?>">
                                                       <?= $team->attempts[$i]->out; ?>

                                            </span>
                                        </td>
                                    <?php } ?>

                                    <?php if ($competitionEvent->formatsAmount >= 1) { ?>
                                        <td  class="table_new_right table_new_bold">
                                            <?= $attempts[$competitionEvent->formats[0]] ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($competitionEvent->formatsAmount >= 2) { ?>
                                        <td  class="table_new_right">
                                            <?= $attempts[$competitionEvent->formats[1]] ?>
                                        </td>
                                    <?php } ?>

                                    <td class="table_new_right <?= ($command['Place'] <= $commandsWinner and $command['Place'] > 0) ? "podium" : ""; ?>">
                                        <b><?= $command['Place'] ?></b>
                                    </td>
                                    <td>
                                        <?php if (!$command['Decline']) { ?>
                                            <form  method="POST" action="<?= PageAction('ScoreTaker.Decline') ?>"  
                                                   onsubmit="
                                                   <?php if ($command['Onsite']) { ?>
                                                               return confirm('Confirm delete')
                                                   <?php } else { ?>
                                                               return confirm('Confirm refusal')
                                                   <?php } ?>
                                                   ">      
                                                <input id="ID<?= $command['ID'] ?>" name="ID" type="hidden" value="<?= $command['ID'] ?>" />
                                                <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                                                <button class="delete button_row">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>                    
                                            </form>
                                        <?php } else { ?>
                                            <form   method="POST" action="<?= PageAction('ScoreTaker.Accept') ?>">
                                                <input name="ID" type="hidden" value="<?= $command['ID'] ?>" />
                                                <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                                                <button  class="button_row">
                                                    <i class="fas fa-undo-alt"></i>
                                                </button>                    
                                            </form>
                                        <?php } ?>
                                    </td>
                                    <td data-team-onsite='<?= $team->onsite ?>'>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>   
                        </table> 
                    </div>   
                    <?php if ($notAttempts) { ?>
                        <form 
                            method = 'POST' 
                            action = '<?= PageAction('ScoreTaker.DeclineAll') ?>'
                            data-confirm-message = 'Confirm: Complete this round'>
                            <input 
                                name = 'ID' 
                                type = 'hidden' 
                                value = '<?= $competitionEvent->id ?>' />
                            <input 
                                name = 'Secret' 
                                type = 'hidden' 
                                value = '<?= $Secret ?>' />
                            <br>
                            <button>
                                Complete this round
                                <i class="fas fa-arrow-alt-circle-right"></i>
                            </button>
                        </form>
                    <?php } else { ?>
                        <?php if ($Next and $commandsWinner > 3 and ! $Next['Commands']) { ?>
                            <form method="POST" action="<?= PageAction('ScoreTaker.NewRound') ?>"  onsubmit="return confirm('Confirm: Сreate a new round')">
                                <input name="ID" type="hidden" value="<?= $competitionEvent->id ?>" />
                                <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                                <br>
                                <button>
                                    <i class="fas fa-users-cog"></i>
                                    Сreate a new round
                                </button>
                            </form>

                        <?php } ?>        
                        <br>
                        <button data-window-open='<?= PageAction('CompetitonEvent.Results.Print') . "/{$competitionEvent->id}" ?>'>
                            <i class="fas fa-print"></i>
                            Print results
                        </button>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <?php
        if ($competitionEvent->competition->onsite
                and CheckAccess('Competition.Event.Settings', $competitionEvent->competition->id)) {
            ?>
            <form
                data-registration-add-form
                method='POST' 
                action="<?= PageAction('ScoreTaker.Registartion.Add') ?>">
                    <?php $message = GetMessage('ScoreTaker.Registartion.Add'); ?>
                <p data-hidden='<?= empty($message) ?>'>
                    <?= $message ?>
                </p>       
                <?= ml('ScoreTaker.Registartion.Add') ?>
                <input name="Competition" type="hidden" value="<?= $competitionEvent->competition->id ?>" />
                <input data-wcaid='<?= PageAction('AJAX.ScoreTaker.Check.WCAID') ?>?competition=<?= $competitionEvent->competition->id ?>'
                       name='WCAID'/>
                <span data-wcaid-prev></span>
                <span data-icon></span>
                <span data-result></span>
                <button data-button hidden>
                    Add registration
                </button>
            </form>
        <?php } ?>
    <body>
</html>

<?php IncludeClass::Template('ScoreTaker'); ?>
<script src="<?= PageIndex(); ?>/index.js" type="text/javascript"></script>