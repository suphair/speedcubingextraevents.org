<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
        
require_once "file_utils.php";
RequireDir ("Classes");
RequireDir ("Functions");
DataBaseInit();
IncluderAction();

$Competitor=GetCompetitorData();
$Delegate=CashDelegate(); 

if(isset($_GET['Script'])){
    if(CheckAccess('Scripts')){
        $Script="Script_{$_GET['Script']}";
        echo "[$Script]";
        if(function_exists($Script)){
            echo "Found ";
            eval("$Script();");
            echo "Complete ";
        }else{
            echo "Not found";
        }
        exit();
    }
}
$languages=getLanguages();

if(!isset($_SESSION['language_select'])){    
    $_SESSION['language_select']=$languages[0];
}

if(!in_array($_SESSION['language_select'],$languages)){
    $_SESSION['language_select']=$languages[0];
}

RequestClass::setRequest();

if(RequestClass::getError(404)){
    header('HTTP/1.0 404 not found');
}

if(RequestClass::getError(401)){
   header('HTTP/1.1 401 Unauthorized');
} ?>
<!DOCTYPE HTML>
<html  lang="<?= $_SESSION['language_select'] ?>">
<head>
    <meta name="Description" content="Fun Cubing">
    <title><?= RequestClass::getTitle(); ?></title>
    <link rel="icon" href="<?= PageLocal()?>Logo/Logo_Color.png" >
    
    <link rel="stylesheet" href="<?= PageLocal()?>style.css?t=2" type="text/css"/>
    <link rel="stylesheet" href="<?= PageLocal()?>jQuery/chosen_v1/chosen.css" type="text/css"/>
   <script src="<?= PageLocal()?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
    <script src="<?= PageLocal()?>jQuery/chosen_v1/chosen.jquery.js?4" type="text/javascript"></script>
    <script src="<?= PageLocal()?>jQuery/tooltip.js?2" type="text/javascript"></script>
    
    
<style>
<?php     if(GetCompetitorData()){ ?>
    body{
        background: linear-gradient(to bottom, #fdd,#fcc);
    }
<?php }else{ ?>
    body{
        background: linear-gradient(to bottom, rgb(225,225,225),rgb(186,186,186));
    }
<?php } ?>
   :root{
    --base_color: rgb(186,186,186);
    --back_color: rgb(225,225,225);
    }

</style>
</head>
<body>
  
<?php 


/*
if(strpos($_SERVER['PHP_SELF'],'/'.GetIni('LOCAL','PageBase').'/')===false){
    if(!$Competitor){ ?>
        <?php  $_SESSION['ReferAuth']=$_SERVER['REQUEST_URI']; ?> 
        <a href="<?= GetUrlWCA(); ?>">Sign in with WCA (only SEE Delegates)</a>
    <?php 
    exit();
    }elseif(!$Delegate){ ?>
        <?= $Competitor->name ?> <a href="<?= PageIndex() ?>Actions/Competitor.Logout"><font color="red"><?= ml('Competitor.SignOut')?></font></a>
        <?php exit();
    } 
 }
 */
?>
    
        <div class="header" style='clear:both; position: relative;'>
            <div style='float: left;'>
                <a href="<?= PageIndex(); ?>" class="title_link">
                    <img class="logo" src="<?= PageIndex() ?>Logo/Logo_Color.png">
                    <?= ml('*.Title') ?> 
                </a>
            </div>
            <?php if($Competitor and !$Competitor->avatar->is_default){ ?>
                <div style='float: right;'>
                    <img class="logo" style="border-radius:20px"  src="<?= $Competitor->avatar->thumb_url ?>">
                </div>
            <?php } ?>
        </div>   
    <div class="content">    
      <?php IncludePage("Competitor.Login"); ?>
    </div>    
        
    <div class="content">
        <?php 
        $type=RequestClass::getPage();
        IncludePage($type); ?>
    </div>    
       <?php  $actions_grand=[]; 
            if(CheckAccess('Visiters')){ $actions_grand[]="<nobr><a href='".PageIndex()."Visiters'>".ml('Footer.Visiters')."</a></nobr>"; } 
            if(CheckAccess('Texts')){ $actions_grand[]="<nobr><a href='".PageIndex()."Texts'>".ml('Footer.Texts')."</a></nobr>"; } 
            if(CheckAccess('Competition.Add')){ $actions_grand[]="<nobr><a href='".LinkCompetition('Add')."'>".ml('Footer.Competition.Add')."</a></nobr>"; } 
            if(CheckAccess('Event.Add')){ $actions_grand[]="<nobr><a href='".LinkDiscipline('Add')."'>".ml('Footer.Event.Add')."</a></nobr>"; }             
            if(CheckAccess('aNews')){ $actions_grand[]="<nobr><a href='".PageIndex()."aNews/Add'>".ml('Footer.aNews.Add')."</a></nobr>"; } 
            if(CheckAccess('Delegate.Candidates')){ $actions_grand[]="<nobr><a href='".LinkDelegate("Candidates")."'>".ml('Footer.Delegate.Candidates')."</a></nobr>"; } 
            if(CheckAccess('Registrations')){ $actions_grand[]="<nobr><a href='".PageIndex()."Registrations'>".ml('Footer.Registrations')."</a></nobr>"; } 
            if(CheckAccess('Scrambles')){ $actions_grand[]="<nobr><a href='".PageIndex()."Scrambles'>".ml('Footer.Scrambles')."</a></nobr>"; } 
            if(CheckAccess('Competition.Report')){ $actions_grand[]="<nobr><a href='".PageIndex()."Reports'>".ml('Footer.Reports')."</a></nobr>"; } 
            if(CheckAccess('Delegates.Settings')){ $actions_grand[]="<nobr><a href='".PageIndex()."Delegates/Settings'>".ml('Footer.Delegates.Settings')."</a></nobr>"; } 
            if(CheckAccess('MultiLanguage')){ $actions_grand[]="<nobr><a href='".PageIndex()."MultiLanguage'>".ml('Footer.MultiLanguage')."</a></nobr>"; } 
            if(CheckAccess('Access')){ $actions_grand[]="<nobr><a href='".PageIndex()."Access'>".ml('Footer.Access')."</a></nobr>"; } 
            
            
            
            if(!empty($actions_grand)){ ?>
                <div class="content"> 
                    <?=implode(" &#9642; ",$actions_grand); ?>
                </div>
            <?php } ?>
            
           
        <div class="content">    
                <?= ml('Footer.Contact.Delegates') ?>: <a href="mailto:<?= urlencode(getini('Seniors','email')) ?>?subject=<?= ml('*.Title',false) ?>"><?= getini('Seniors','email') ?></a>
                ▪ 
                <nobr>
                    <?= svg_red(10)?>
                    <a href="mailto:<?= urlencode(getini('Support','email')) ?>?subject=Support: <?= ml('*.Title',false) ?>"><?= ml('Footer.Contact.Support') ?></a>
                    <?= svg_red(10)?>
                </nobr>
                ▪ 
                <nobr>
                    <a href="<?= PageIndex()?>Icons"><?= ml('Footer.Icons') ?></a>
                </nobr>    
       </div>         
    
<?php 
DataBaseClass::Query("select Object,Name from WCAauth join Competitor C on C.WID=WCAauth.WID where WCAauth.ID in(select min(ID) ID "
        . " from WCAauth where Timestamp>TIME(DATE_SUB(NOW(), INTERVAL 1 HOUR)) group by WID) order by WCAauth.ID desc limit 30 ");
$rows=DataBaseClass::GetRows();
if(sizeof($rows)){ ?>
    <?= ml('Footer.Authorizations') ?>: <?= sizeof($rows)?>
<div style="padding:0px;margin:0px">
        <?php foreach($rows as $row){
            $user=json_decode($row['Object']);
                if($user->wca_id){ ?>
                    <a target="_blank" title="<?= $row['Name'] ?>" href="https://www.worldcubeassociation.org/persons/<?= $user->wca_id ?>">
                <?php }else{ ?>
                    <span title="<?= $row['Name'] ?>">    
                <?php } ?>       
                <div style="padding:0px;margin:0px; display: inline-block;">
                    <img src="<?= $user->avatar->thumb_url; ?>"
                         width="31px"
                        style="position:relative; top:8px;left:2px"><br>
                        <?= ImageCountry($user->country_iso2,35); ?><br>
                </div>
                <?php if($user->wca_id){ ?>
                    </a>
                 <?php }else{ ?>
                    </span>   
                <?php } ?>  
        <?php } ?>
</div>
<?php } ?>        
        
        
    <?php  add_visit(); ?>
    <script>
      $(".chosen-select-1").chosen({max_selected_options: 1});
      $(".chosen-select-2").chosen({max_selected_options: 2});
      $(".chosen-select-3").chosen({max_selected_options: 3});
      $(".chosen-select-4").chosen({max_selected_options: 4});
    </script>
    <script src="<?= PageLocal()?>jQuery/chosen_v1/docsupport/init.js" type="text/javascript" charset="utf-8"></script>

</body>


<?= mlb('Month')?>
<?= mlb('MultiLanguage.Title')?>
<?= mlb('Footer.Authorizations') ?>
<?php # echo(DataBaseClass::getCount()); ?>

</html>   
