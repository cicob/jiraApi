<?php
////////////////////////////////////////////////////////////
///
///    Define Include-ROOT
///
///
////////////////////////////////////////////////////////////


//Now figure out where our libs are

static $root = "";
static $logroot = "";
static $cmdline = False;

//error_log("Server: [" . $_SERVER['HTTP_HOST'] ."]");

switch ($_SERVER['HTTP_HOST']){
    case "cron":
        $root =  $_SERVER['DOCUMENT_ROOT'] . "/../include/";
        $logroot =  $_SERVER['DOCUMENT_ROOT'] . "/../log/";
        $cmdline = True;
        break;
        
    default:
        $root =  $_SERVER['DOCUMENT_ROOT'] . "/../include/";
        $logroot =  $_SERVER['DOCUMENT_ROOT'] . "/../log/";
        $cmdline = False;
        break;
        
} // end switch

// Watch out, no empty lines after the closing tag
?>