<?php

class RequestClass {
    protected static $_instance; 
    protected static $titles;
    protected static $page;
    protected static $error;
    protected static $objects;

    private function __construct() {        
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;   
        }
 
        return self::$_instance;
    }
  
    private function __clone() {
    }

    private function __wakeup() {
    }   
    
    public static function setRequest(){
        
        self::$titles[]= GetIni('TEXT', 'title');
        self::$page="index";
        self::$error[401]="";
        self::$error[404]="";
        
        $request=getRequest();

        if(!isset($request[0])){
            return;
        }
        
        for($i=1;$i<=4;$i++){
            if(!isset($request[$i])){
                $request[$i]='null';
            }else{
                $request[$i]= DataBaseClass::Escape($request[$i]);
            }
        }
        
        $type=$request[0];
        
$typesSimplePage=[
    'competitions','competition',
    'events','event',
    'delegates','delegate',
    'competitors','competitor',
    'records',
    'regulations',
    'visitors','texts','logs','alternative','icons',
    'multilanguage','401',
    'news','anews',
    'scramble','scrambleszip',
    'scoretaker' ,'mainregulations','registrations','scrambles','access','reports','api','logs','export'
    ];

        if(substr($type,0,1)!='?'){
            if(!in_array($type,$typesSimplePage)){
                self::set404();
                return;
            }
        }
        
        foreach($typesSimplePage as $typeSimplePage){
            if($typeSimplePage==$type){
                self::$titles[]=ucfirst($type);
                self::$page=ucfirst($type);
            }
        }
        switch ($type):
            case 'api':
                if($request[1]=='v0' and $request[2]=='competitions' and $request[4]=='results'){
                    $Competition = DataBaseClass::SelectTableRow('Competition', "WCA='$request[3]'");
                    ObjectClass::setObjects('PageCompetition', $Competition);
                    if($Competition){
                        IncludeClass::Page('Api.Competition.Results');
                    }
                }
                exit();
            case 'scramble':
               $EventCode = $request[1];
                if(!is_numeric($EventCode)){
                    DataBaseClass::Query("Select Event from Event E join ScramblePdf SP on SP.Secret=E.ScramblePublic and lower(E.ScramblePublic)=lower('$EventCode')");
                    $row=DataBaseClass::getRow();
                    if(isset($row['Event'])){
                        IncludeClass::Page('Scramble');
                        exit();
                    }    
                    
                    DataBaseClass::Query("Select Event,Competition from Event E join ScramblePdf SP on SP.Event=E.ID and lower(SP.Secret)=lower('$EventCode')");
                    $row=DataBaseClass::getRow();
                    
                    if(isset($row['Event'])){
                        $EventCode=$row['Event'];
                        if(self::CheckAccess("Scramble","Competition.Settings",$row['Competition'])===true){
                            IncludeClass::Page('Scramble');
                            exit();
                        }
                    }
                    echo 'Access to the scramble is denied';
                    exit();
                }
                
                DataBaseClass::FromTable('Event', "ID='$EventCode'");
                DataBaseClass::Join_current('Competition');
                $CompetitionEvent = DataBaseClass::QueryGenerate(false);
                
                if(isset($CompetitionEvent['Competition_ID'])){
                    if($CompetitionEvent['Competition_ID']==129){
                       IncludeClass::Page('Scramble');
                       exit();
                    }else{
                        if(self::CheckAccess("Scramble","Competition.Event.Settings",$CompetitionEvent['Competition_ID'])===true){
                            IncludeClass::Page('Scramble');
                            exit();
                        }
                   }
                }
                echo 'Access to the scramble is denied';
                exit();
                break;
            
            case 'scoretaker':
                IncludeClass::Page('ScoreTaker');
                exit();
                break;
            
            case 'mainregulations':
                if($request[1]=='edit' and self::CheckAccess("MainRegulations.Edit")===true){
                    IncludeClass::Page('MainRegulations.Edit');    
                }else{
                    IncludeClass::Page('MainRegulations');
                }
                exit();
                break;
               
            case 'scrambleszip':
               $CompetitionCode = $request[1];
               if(self::CheckAccess("Scramble","Competition.Settings",$CompetitionCode)===true);  
               IncludeClass::Page('ScramblesZip');
               exit();
               break;
               
               
            case 'delegates':
                $DelegateCode = $request[1];
                if($request[1]=='settings'){           
                    self::$titles[]='Settings';
                    self::CheckAccess('Delegates.Settings');
                }elseif($request[1]!='null'){
                    self::set404();    
                }                    
               break;
               
               
            case 'delegate':
                $DelegateCode = $request[1];
                self::$titles[] = $DelegateCode;
                switch ($DelegateCode):
                    case 'candidates':
                        if($request[2]=='settings'){           
                            self::$titles[]='Settings';
                            self::CheckAccess('Delegate.Candidates.Settings');   
                        }elseif($request[2]!='null'){
                            self::set404();    
                        }else{
                            self::$titles[2]='Candidates';
                            self::CheckAccess('Delegate.Candidates');
                        }
                        break;
                    case 'candidate':
                        self::$page ='Delegate.Candidate';
                        break; 
                    default:
                       /* $Delegate = DataBaseClass::SelectTableRow('Delegate', "WCA_ID='$DelegateCode'");
                        
                        ObjectClass::setObjects('PageDelegate', $Delegate);
                        if($Delegate){                            
                            self::$titles[2] = Short_Name($Delegate['Delegate_Name']);
                            if($request[2]=='settings'){
                                self::$titles[]="Settings";
                                self::CheckAccess("Delegate.Settings");    
                            }elseif($request[2]!='null'){
                                self::set404();    
                            }   
                        }else{
                            self::set404();
                        }*/             
                endswitch; #$DelegateCode
                break;
            
            case 'event':
                $EventCode=$request[1];
                self::$titles[]=$EventCode;
                switch ($EventCode):
                  case 'add':
                      self::CheckAccess('Event.Add');
                      break;
                  default:
                      $Event = DataBaseClass::SelectTableRow('Discipline', "Code='$EventCode'");
                      ObjectClass::setObjects('PageEvent', $Event);
                      if($Event){
                          self::$titles[1]=$Event['Discipline_Name'];
                          unset(self::$titles[2]);
                          if($request[2]=='settings'){
                              self::$titles[]="Settings";
                              self::CheckAccess("Event.Settings");
                          }elseif($request[2]=='training'){   
                              self::$titles[]="Training";
                              self::$page ='Event.Training';
                          }elseif(in_array($request[2],['average','single','sum'])){
                              if(!in_array($request[3],['results','persons'])){
                                   self::set404();    
                              } 
                          }elseif($request[2]!='null'){
                                self::set404();    
                          }  
                      }else{     
                          self::set404();
                      }   
                endswitch; #$EventCode
                break;
            
            case 'competitor':
                $Competitor=false;
                $CompetitorCode=$request[1];
                if(is_numeric($CompetitorCode)){
                    $Competitor = DataBaseClass::SelectTableRow('Competitor', "ID='$CompetitorCode'");
                }elseif($CompetitorCode!='null'){
                    $Competitor = DataBaseClass::SelectTableRow('Competitor', "WCAID='$CompetitorCode'");    
                }            
                if($Competitor){
                    ObjectClass::setObjects('PageCompetitor', $Competitor);
                    self::$titles[1]=Short_Name($Competitor['Competitor_Name']);
                }else{
                    self::set404();
                }                   
                break;
            
            case 'anews':
                $aNewsCode=$request[1];
                self::$titles[]=$aNewsCode;
                
                switch ($aNewsCode):
                    case 'add':
                        self::CheckAccess('aNews.Edit','aNews');
                        break;
                    default:
                        DataBaseClass::Query("Select N.*,C.Name from News N left outer join Competitor C on C.WID=N.Delegate where N.ID='".$aNewsCode."'");
                        $aNews = DataBaseClass::getRow();
                        ObjectClass::setObjects('PageaNews', $aNews);   
                        if($aNews){
                            self::CheckAccess('aNews.Edit','aNews');
                        }else{     
                            self::set404();
                        } 
                 endswitch; #$aNewsCode
                 break;
                 
            case 'competition':
                $CompetitionCode=$request[1];
                self::$titles[]=$CompetitionCode;
                
                switch ($CompetitionCode):
                    case 'add':
                        self::CheckAccess('Competition.Add');
                        break;
                    default:
                        $Competition = DataBaseClass::SelectTableRow('Competition', "WCA='$CompetitionCode'");
                        ObjectClass::setObjects('PageCompetition', $Competition);
                        if($Competition){
                            //ObjectClass::setObjects('Competition',$Competition);
                            
                            DataBaseClass::FromTable('Competition', "WCA='$CompetitionCode'");
                            DataBaseClass::Join_current('CompetitionDelegate');
                            DataBaseClass::Join_current('Delegate');
                            ObjectClass::setObjects('PageCompetitionDelegates',DataBaseClass::QueryGenerate());
                            
                            DataBaseClass::FromTable('Competition', "WCA='$CompetitionCode'");
                            DataBaseClass::Join_current('Event');
                            DataBaseClass::Join_current('DisciplineFormat');
                            DataBaseClass::Join_current( 'Discipline');
                            DataBaseClass::Join( 'DisciplineFormat','Format');
                            DataBaseClass::OrderClear('Discipline','Name');
                            $CompetitionEvents=DataBaseClass::QueryGenerate();
                            ObjectClass::setObjects('PageCompetitionEvents',$CompetitionEvents);
                            
                            self::$titles[1]=$Competition['Competition_Name'];
                            unset(self::$titles[2]);
                            if($request[2]!='null'){
                                $Code=$request[2];
                                self::$titles[]=$Code;
                                #self::$param2=$Code;        
                                switch ($Code):
                                    case 'settings':
                                        self::CheckAccess("Competition.Settings","",$Competition['Competition_ID']); 
                                        break;
                                    case 'report':
                                        self::CheckAccess("Competition.Report","",$Competition['Competition_ID']); 
                                        break;
                                    default:
                                        DataBaseClass::FromTable('Competition', "WCA='$CompetitionCode'");
                                        DataBaseClass::Join('Competition', 'Event');
                                        DataBaseClass::Join('Event', 'DisciplineFormat');
                                        DataBaseClass::Join_current('Discipline');
                                        DataBaseClass::Join('DisciplineFormat','Format');
                                        if(is_numeric($request[3])){
                                            DataBaseClass::Where('Event',"Round='$request[3]'");
                                        }else{
                                            DataBaseClass::Where('Event',"Round=1");
                                            $request[3]=1;
                                        }
                                        DataBaseClass::Where('Discipline',"Code='$Code'");
                                        $CompetitionEvent=DataBaseClass::QueryGenerate(false);
                                        ObjectClass::setObjects('PageCompetitionEvent', $CompetitionEvent);
                                        if($CompetitionEvent){
                                            self::$titles[3]=$CompetitionEvent['Discipline_Name'];
                                            if(str_replace(": ","",$CompetitionEvent['Event_vRound'])){
                                                self::$titles[]=str_replace(": ","",$CompetitionEvent['Event_vRound']);
                                            }
                                            if($request[4]=='settings'){
                                                self::$titles[]='Settings';
                                                self::CheckAccess("CompetitionEvent.Settings","Competition.Event.Settings",$Competition['Competition_ID']);
                                            }elseif($request[4]!='null'){
                                                self::set404();    
                                            }
                                        }else{
                                            self::set404();
                                        }
                                endswitch; #$Code    

                            }
                        }else{
                            self::set404();
                        }                        
                endswitch; #$CompetitionCode  
                break;
            
            case 'texts':
                self::CheckAccess("Texts"); 
                break;    
            
            case 'visitors':
                self::CheckAccess("Visitors"); 
                break;    
            
            case 'access':
                self::CheckAccess("Access"); 
                break;    
            
            case 'reports':
                self::CheckAccess("Reports","Competition.Report"); 
                break;    
            
            case 'registrations':
                self::CheckAccess('Registrations');
                break; 
            
            case 'scrambles':
                self::CheckAccess('Scrambles');
                break; 
            
            case 'alternative':
                self::$page='Competitor.Login.Alternative';
                break;     
            
            case 'multilanguage':
                self::CheckAccess('MultiLanguage');
                self::$titles[1]='Multi Language';
                break;    
            
            case 'logs':
                $LogType=$request[1];
                if($request[1]=='null'){
                    self::CheckAccess('Logs');
                    self::$titles[1]='Logs';
                    unset(self::$titles[2]);
                }else{
                    self::CheckAccess('Logs.'.ucfirst($request[1]));
                    self::$titles[1]='Logs '.ucfirst($request[1]);
                    unset(self::$titles[2]);
                }
                break;    
                
            case '401':
                self::set401(ml('401'));
                break;
                
        endswitch; #$type
    }
    
    private static function set404(){
        self::$titles[]='404';
        
        self::$error[404]="404 - ".ml('404')."<br>". json_encode(getRequest());
        self::$page="error";  
    }
 
    public static function set401($error){
        self::$titles[]='401';
        self::$error[401]='401<br>'.$error;
        self::$page="error";  
    }
    
    public static function getPage(){
        return self::$page;
    }
    
    public static function getTitle(){
        return implode(" &#9642; ", array_reverse(self::$titles));
    }

    public static function getError($n=0){
        if($n){
            return self::$error[$n];
        }else{
            $error_return="";
            foreach(self::$error as $error){
                $error_return.=$error;
            }
            return $error_return;
        }
    }
          
    public static function CheckAccessExit($page,$type,$competitionID=false){
        $err=self::CheckAccess($page,$type,$competitionID);
        if($err!==true){
            header('HTTP/1.1 401 Unauthorized');
            echo "<a href='".PageIndex()."'>".GetIni('TEXT', 'title')."</a>";
            echo $err;
            if(sizeof($_POST)){
                echo '<hr> Your sent data';
                echo '<pre>';
                    print_r($_POST);
                echo '</pre>';
                if($Competitor= getCompetitor()){
                    echo '<hr> Your authorization data';
                    echo '<pre>';
                        print_r($Competitor);
                    echo '</pre>';
                }
                
            }
            exit();
        }
    }
    
    
     private static function CheckAccess($page,$type=false,$competitionID=false){
        if(!$type){
            $type=$page;
        }
        $GrandResult=CheckAccess($type,$competitionID);
        
        if($GrandResult){   
            self::$page=$page; 
        }else{
            $s=Explode('Includes/',$page);
            $page=sizeof($s)>1?$s[1]:$s[0];
            $page=str_replace(".php","",$page);
            
            $page=sizeof(explode("/",$page))>1?explode("/",$page)[1]:$page;
            
            $err="<h2 style='color:red'>You do not have permission [".$type."] to use action [".$page."]</h2>";
            self::set401($err);
            return $err;
        }
        return true;
    }
}


