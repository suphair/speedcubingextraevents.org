<?php
$competition_event_id = Request(2);
$competition_event = new CompetitionEvent;
$competition_event->getById($competition_event_id);
if (!$competition_event->id) {
    die("not found {$competition_event_id}");
}

RequestClass::CheckAccessExit(__FILE__, 'Competition.Event.Settings', $competition_event->competition->id);

$competition_event->getScrambles();
$pdf = ScramblePrint::getPDF(
                $competition_event->competition->name
                , $competition_event->scrambles
                , $competition_event->event->codeScript
                , $competition_event->event->scrambleComment
                , $competition_event->event->name
                , $competition_event->event->isCup
                , $competition_event->event->isCut
                , $competition_event->view_round
                , $competition_event->attemptions,
                filter_input(INPUT_GET, 'date'));

$rand = random_string(20);
$timestamp_sql = date("Y-m-d H:i:s");
DataBaseClass::Query("Update Event set ScrambleSalt='$rand' where ID='" . $competition_event->id . "'");
$file = "Image/Scramble/$rand.pdf";
$pdf->Output($file, 'F');
$pdf->Close();
DataBaseClass::Query("Insert into ScramblePdf (Event,Secret,Delegate,Timestamp, Action) values ('" . $competition_event->id . "','$rand','" . getDelegate()['Delegate_ID'] . "','$timestamp_sql','Generation')");
header('Location: ' . PageIndex() . "/Scramble/" . $competition_event->id);
exit();
