<?php

Suphair \ Wca \ Oauth::set(
        Suphair \ Config :: get('WCA_AUTH', 'client_id')
        , Suphair \ Config :: get('WCA_AUTH', 'client_secret')
        , Suphair \ Config :: get('WCA_AUTH', 'scope')
        , PageIndex() . Suphair \ Config :: get('WCA_AUTH', 'url_refer')
        , DataBaseClass::getConection()
);
$competitor = Suphair \ Wca \ Oauth::authorize();

unset($_SESSION['Competitor']);

if ($competitor) {
    $competitor->name = Short_Name($competitor->name);


    $CompetitorID = CompetitorReplace($competitor);

    $competitor_merge = (object) array_merge((array) $competitor, array('local_id' => $CompetitorID));
    $_SESSION['Competitor'] = $competitor_merge;
    $_SESSION['competitorWid'] = $competitor->id;
    $Language = DataBaseClass::SelectTableRow("Competitor", "ID=$CompetitorID")['Competitor_Language'];
    if ($Language) {
        $_SESSION['language_select'] = $Language;
    }
}
Suphair \ Wca \ Oauth::location();
