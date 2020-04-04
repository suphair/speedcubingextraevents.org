<?php

$url_refer = PageIndex() . GetIni('WCA_AUTH', 'url_refer');
if (strpos($_SERVER['PHP_SELF'], '/' . GetIni('LOCAL', 'PageBase') . '/') !== false) {
    $url_refer = "http:" . $url_refer;
} else {
    $url_refer = "https:" . $url_refer;
}
$client_id = GetIni('WCA_AUTH', 'client_id');
$client_secret = GetIni('WCA_AUTH', 'client_secret');
$scope = GetIni('WCA_AUTH', 'scope');
$url = "https://www.worldcubeassociation.org/oauth/authorize?client_id=$client_id&redirect_uri=" . urlencode($url_refer) . "&response_type=code&scope=$scope";

unset($_SESSION['Competitor']);

if (isset($_GET['error']) and $_GET['error'] == 'access_denied') {
    header('Location: ' . $_SESSION['ReferAuth']);
    exit();
}

$code = "";
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $postdata = http_build_query(
            array(
                'grant_type' => 'authorization_code',
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'code' => $code,
                'redirect_uri' => $url_refer
            )
    );
    $opts = array('http' =>
        array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context = stream_context_create($opts);
    $result = @file_get_contents("https://www.worldcubeassociation.org/oauth/token", false, $context);
    if (isset(json_decode($result)->access_token)) {
        $access_token = json_decode($result)->access_token;


        $ch = curl_init('https://www.worldcubeassociation.org/api/v0/me'); // Initialise cURL

        $authorization = "Authorization: Bearer " . $access_token; // Prepare the authorisation token
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        if (isset($result->me)) {
            $result->me->name = Short_Name($result->me->name);
            $CompetitorID = CompetitorReplace($result->me);

            $competitor = (object) array_merge((array) $result->me, array('local_id' => $CompetitorID));
            $_SESSION['Competitor'] = $competitor;
            $_SESSION['competitorWid'] = $competitor->id;
            DataBaseClass::Query("Insert into `WCAauth` (WID,Object) values ('" . $result->me->id . "','" . json_encode($result->me) . "')");

            $Language = DataBaseClass::SelectTableRow("Competitor", "ID=$CompetitorID")['Competitor_Language'];

            if ($Language) {
                $_SESSION['language_select'] = $Language;
            }

            AddLog('WCA_Auth', 'Login', $result->me->name);
        }
    }
    if (isset($_SESSION['ReferAuth']) and strpos($_SESSION['ReferAuth'], 'favicon.ico') === false) {
        header('Location: ' . $_SESSION['ReferAuth']);
    } else {
        header('Location: ' . PageIndex());
    }
    exit();
}
?>