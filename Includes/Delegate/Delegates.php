<?php

if(!CheckAccess('Delegates.Arhive')){
    $where = "AND D.Status!='Archive'";
}else{
    $where="";
}
    

$delegates = DataBaseClass::getRowsObject("
    SELECT 
        MAX(C.EndDate) endDate,
        D.Status status, 
        CASE D.Status 
            WHEN 'Archive' THEN true 
            ELSE false 
        END statusArchive, 
        D.WCA_ID wcaid, 
        D.Name name, 
        DelC.Country country,
        COUNT( DISTINCT Cm.ID) countCompetitors,
        COUNT( DISTINCT C.ID) countCompetitions
    FROM Delegate D 
        LEFT OUTER JOIN Competitor DelC ON 
            ((D.WCA_ID AND D.WCA_ID=DelC.WCAID) 
                OR (D.WID AND D.WID=DelC.WID)) 
        LEFT OUTER JOIN CompetitionDelegate CD ON CD.Delegate=D.ID
        LEFT OUTER JOIN Competition C ON C.ID=CD.Competition
        LEFT OUTER JOIN Event E ON E.Competition=C.ID
        LEFT OUTER JOIN Command Com ON 
            Com.Event=E.ID 
            AND Com.Decline!=1
        LEFT OUTER JOIN CommandCompetitor CC ON CC.Command=Com.ID
        LEFT OUTER JOIN Competitor Cm ON Cm.ID=CC.Competitor 
        WHERE 1 = 1
            $where
        GROUP BY D.ID, DelC.Country 
        ORDER BY 
            D.Status='Archive',
            DelC.Country, 
            CASE D.Status 
                WHEN 'Senior' THEN 1  
                WHEN 'Middle' THEN 2  
                WHEN 'Junior' THEN 3 
                WHEN 'Trainee' THEN 4 
            END,
            D.Name
");

foreach ($delegates as &$delegate) {
    $delegate->link = LinkDelegate($delegate->wcaid);
    $delegate->endDate = date_range($delegate->endDate);
    $delegate->country = getObjectCountry($delegate->country);
}

$data = arrayToObject([
    'delegates' => $delegates,
    'candidates' => [
        "show" => CheckAccess('Delegate.Candidates'),
        "link" => LinkDelegate("Candidates")
    ]
        ]);
IncludeClass::Template('Delegates', $data);
