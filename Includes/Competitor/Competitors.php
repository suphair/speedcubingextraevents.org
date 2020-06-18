<?php

$countries = Country :: getCountriesByCountriesCode(
                Competitor::getCountriesCodeByAllCompetitors());

$request = filter_input(INPUT_GET, 'country');

$countryWhere = "";
if ($request) {
    $countryWhere = "AND '" . strtoupper($request) . "' = C.Country";
}

$sortSittings = [
    'name' => 'ASC',
    'wcaid' => 'ASC',
    'competitions' => 'DESC',
    'events' => 'DESC',
    'podiums' => 'DESC',
];

$sortVar = filter_input(INPUT_GET, 'sort');
if (isset($sortSittings[$sortVar])) {
    $sortQuery = "$sortVar {$sortSittings[$sortVar]},";
    $sortValue = $sortVar;
} else {
    $sortQuery = "";
    $sortValue = array_keys($sortSittings)[0];
}

$competitors = DataBaseClass::getRowsObject("
    SELECT
        C.WCAID wcaid,
        C.Name name,
        C.Country countryCode,
        Country.Name countryName,
        C.ID id, 
        SUM( 
            CASE 
                WHEN Com.Place IN (1,2,3) 
                    AND E.RoundType IN ('f','c') 
                THEN 1 
                ELSE 0 
            END) podiums, 
        COUNT( DISTINCT Cm.ID) competitions,
        COUNT( DISTINCT D.ID) events
    FROM Competitor C 
    JOIN CommandCompetitor CC ON CC.Competitor=C.ID 
    JOIN Country ON Country.ISO2=C.Country
    JOIN Command Com ON Com.ID=CC.Command 
        AND Com.Decline!=1 
    JOIN Event E ON E.ID=Com.Event
    JOIN DisciplineFormat DF ON E.DisciplineFormat=DF.ID 
    JOIN Discipline D ON D.ID=DF.Discipline 
    JOIN Competition Cm ON Cm.ID=E.Competition 
        AND Cm.Technical =0 
    WHERE 1 = 1 
    $countryWhere
        AND (COALESCE(C.WCAID,0) != 0 
            OR COALESCE(C.WID,0) != 0)
    GROUP BY
        C.WCAID,
        C.Name,
        C.Country,
        C.ID,
        Country.Name 
    ORDER BY 
        $sortQuery 
        Name, 
        WCAID, 
        Competitions desc,
        Events desc,
        Podiums desc
");

foreach ($competitors as &$competitor) {
    if ($competitor->wcaid) {
        $competitor->link = PageIndex() . "Competitor/" . $competitor->wcaid;
    } else {
        $competitor->link = PageIndex() . "Competitor/" . $competitor->id;
    }

    $country = new Country();
    $country->getByCode($competitor->countryCode);
    $competitor->country = $country;
}

$data = arrayToObject([
    'competitors' => $competitors,
    'counties' => $countries,
    'filter' => [
        'country' => $request
    ],
    'sort' => $sortValue
        ]);

IncludeClass::Template('Competitors', $data);
