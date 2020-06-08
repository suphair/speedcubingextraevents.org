<?php

function delegateCandidatesDelete() {
    $_details = [];

    DataBaseClass::Query(" Select RC.Datetime, RC.Competitor, RC.ID,C.Name,C.WCAID from RequestCandidate RC"
            . " join Competitor C on C.ID=RC.Competitor "
            . "where RC.Status=-1 and (TO_DAYS(now()) - TO_DAYS(RC.Datetime)) >= 365");
    $rows = DataBaseClass::getRows();
    foreach ($rows as $row) {
        DataBaseClass::Query(" Delete from RequestCandidateVote where Competitor=" . $row['Competitor']);
        DataBaseClass::Query(" Delete from RequestCandidateField where RequestCandidate=" . $row['ID']);
        DataBaseClass::Query(" Delete from RequestCandidate where Competitor=" . $row['Competitor']);
        $_details[] = [
            'name' => $row['Name'],
            'wcaid' => $row['WCAID'],
            'dateTime' => $row['Datetime']
        ];
    }

    return json_encode($_details);
}
