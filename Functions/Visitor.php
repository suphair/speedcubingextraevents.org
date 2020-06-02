<?php 
function trackVisitor()
    {
    $clientIP = filter_input(INPUT_SERVER,'HTTP_CLIENT_IP');
    $forwardFor = filter_input(INPUT_SERVER,'HTTP_X_FORWARDED_FOR');
    $remoteAddr = filter_input(INPUT_SERVER,'REMOTE_ADDR');

    if(filter_var($clientIP, FILTER_VALIDATE_IP)){
        $ip = $clientIP;
    }elseif(filter_var($forwardFor, FILTER_VALIDATE_IP)){
        $ip = $forwardFor;
    }else{
        $ip = $remoteAddr;
    }
    
    $userAgent = DataBaseClass::Escape(filter_input(INPUT_SERVER,'HTTP_USER_AGENT'));
    
    DataBaseClass::Query("SELECT COUNT(*) count FROM Visitor WHERE IP='$ip' AND Date=CURDATE() ");

    if(!DataBaseClass::getRow()['count']){
        DataBaseClass::Query("INSERT INTO Visitor (IP,Date,User_Agent) VALUES ('$ip',CURDATE(),'$userAgent') ");
    }    

    DataBaseClass::Query("SELECT COUNT(DISTINCT IP) count FROM Visitor WHERE User_Agent='$userAgent' HAVING count>10");
    $row=DataBaseClass::getRow();
    if(isset($row) and $row['count']>10){
        DataBaseClass::Query("UPDATE Visitor SET Hidden=1 WHERE User_Agent='$userAgent'");
    }

    DataBaseClass::Query("UPDATE Visitor SET Hidden=1 WHERE LOWER(User_Agent) LIKE '%bot%'");        
}
