<?php
function getUserWcaApi($userId,$context)
{
    $contents = file_get_contents_curl("https://www.worldcubeassociation.org/api/v0/users/".$userId); 
    $userData=json_decode($contents);
    if($userData and isset($userData->user)){     
        $user=$userData->user;
        unset($user->class);
        unset($user->url);
        unset($user->gender);
        unset($user->created_at);
        unset($user->updated_at);
        unset($user->teams);
        unset($user->avatar); 
        $user->name=short_Name($user->name);
    }else{
        $user=false;
    }
    logUseWcaApi($userId, json_encode($user),'users',$context);
    return $user;
}

function getPersonWcaApi($personWcaid,$context)
{
    $contents = file_get_contents_curl("https://www.worldcubeassociation.org/api/v0/persons/".$personWcaid); 
    $personData=json_decode($contents);
    if($personData and isset($personData->person)){     
        $person=$personData->person;
        unset($person->url);
        unset($person->gender);
        unset($person->teams);
        unset($person->avatar); 
        $person->name=short_Name($person->name);
    }else{
        $person=false;
    }
    logUseWcaApi($personWcaid, json_encode($person),'persons',$context);
    return $person;
}

function getCompetitionWcaApi($competitionId,$context)
{
    $contents = file_get_contents_curl("https://www.worldcubeassociation.org/api/v0/competitions/".$competitionId); 
    $competitionData=json_decode($contents);
    if($competitionData and !isset($competitionData->error)){     
        $competition=$competitionData;
        unset($competition->class);
        unset($competition->url);
        unset($competition->short_name);
        unset($competition->venue_address);
        unset($competition->venue_details);
        unset($competition->latitude_degrees);
        unset($competition->longitude_degrees);
        unset($competition->announced_at);
        unset($competition->organizers);
        unset($competition->event_ids);
        foreach($competition->delegates as $d=>$delegate){
            unset($competition->delegates[$d]->class);
            unset($competition->delegates[$d]->url);
            unset($competition->delegates[$d]->id);
            unset($competition->delegates[$d]->name);
            unset($competition->delegates[$d]->gender);
            unset($competition->delegates[$d]->country_iso2);
            unset($competition->delegates[$d]->delegate_status);
            unset($competition->delegates[$d]->created_at);
            unset($competition->delegates[$d]->updated_at);
            unset($competition->delegates[$d]->teams);
            unset($competition->delegates[$d]->avatar);
            unset($competition->delegates[$d]->email);
            unset($competition->delegates[$d]->region);
            unset($competition->delegates[$d]->senior_delegate_id);
        }
    }else{
        $competition=false;
    }
    logUseWcaApi($competitionId, json_encode($competition),'competitions',$context);
    return $competition;
}

function getCompetitionRegistrationsWcaApi($competitionId,$context)
{
    $contents = file_get_contents_curl("https://www.worldcubeassociation.org/api/v0/competitions/".$competitionId."/registrations"); 
    $registrationsData=json_decode($contents);
    
    $registrationsLog=[];
    if($registrationsData and !isset($registrationsData->error)){     
        $registrations=$registrationsData;
        foreach($registrations as $r=>$registration){
            unset($registrations[$r]->competition_id);
            unset($registrations[$r]->id);
            unset($registrations[$r]->event_ids);
        }
        
        foreach($registrations as $r=>$registration){
            $registrationsLog['user_id'][]=$registrations[$r]->user_id;
        }
    }else{
        $registrations=false;
    }
    
    logUseWcaApi($competitionId, json_encode($registrationsLog),'competitions/registrations',$context);
    return $registrations;
}

function getCompetitionCompetitorsWcaApi($competitionId,$context)
{
    $contents = file_get_contents_curl("https://www.worldcubeassociation.org/api/v0/competitions/".$competitionId."/competitors"); 
    $competitorsData=json_decode($contents);
    
    $competitorsLog=[];
    if($competitorsData and !isset($competitorsData->error)){     
        $competitors=$competitorsData;
        foreach($competitors as $c=>$competitor){
            unset($competitors[$c]->class);
            unset($competitors[$c]->url);
            unset($competitors[$c]->gender);
            unset($competitors[$c]->delegate_status);
            unset($competitors[$c]->created_at);
            unset($competitors[$c]->updated_at);
            unset($competitors[$c]->teams);
            unset($competitors[$c]->avatar);
        }
        
        foreach($competitors as $c=>$competitor){
            $competitorsLog['wca_id'][]=$competitors[$c]->wca_id;
        }
        
    }else{
        $competitors=false;
    }
    
    logUseWcaApi($competitionId, json_encode($competitorsLog),'competitions/competitors',$context);
    return $competitors;
}


function logUseWcaApi($request,$response,$method,$context)
{
    $request=DataBaseClass::Escape($request);
    $response=DataBaseClass::Escape($response);
    $method=DataBaseClass::Escape($method);
    $context=DataBaseClass::Escape($context);
    DataBaseClass::Query("Insert into LogWcaApi (request,response,method,context) values ('$request','$response','$method','$context')");    
}

function getTnoodleVersion($context)
{
    $contents = file_get_contents_curl("https://www.worldcubeassociation.org/api/v0/scramble-program"); 
    $scramble_info=json_decode($contents);
    
    logUseWcaApi('', json_encode($scramble_info),'scramble-program',$context);
    return $scramble_info;
}