<?php
ini_set ( 'display_errors', 'On' );
error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT']."/stub.inc.php");
require_once($root."jira/jiraconfig.inc.php");
require_once($root."jira/jiraApi.inc.php");
require_once($root."jira/jiraIssue.inc.php");


// This ticket-generation can take some time to execute.
set_time_limit(60);


?>
<a href="index.php">Start over</a>
<hr>
<?php 

///////////
// Startup
print("Hej! V1.6<br>\n");




/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
// Jira Project Parameters


$jiraProjectKey = "CAAP";
$factoryEpic = "CAAP-9";





/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
// Init

$jira = new jiraApi($hostname, $jiraUser, $jiraPw, $jiraType);
$jira->setProjectKey($jiraProjectKey);
$jira->setDefaultEpic($factoryEpic);

$jira->setDebug(False);
//$jira->setDebug(True);




/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
//Sprint 75
$sprint = "75";
$environment = "Customer Fasttrack GmbH";
$urlCore = "fast";
$jira->setComponent("FAC".$sprint);




/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
// Core Text - shown in several tickets


$coreText  = 'h1. Sprint '.$sprint.' Parameters'.PHP_EOL;
$coreText .= ' '.PHP_EOL;
$coreText .= $environment.PHP_EOL;
$coreText .= 'URL: https://www.'.$urlCore.'.mytestbench.com'.PHP_EOL;
$coreText .= ' '.PHP_EOL;
$coreText .= ' '.PHP_EOL;
$coreText .= ' '.PHP_EOL;
$coreText .= 'h1. Details:'.PHP_EOL;
$coreText .= 'https://wiki.mycompany.com/pages/viewpage.action?pageId=12345'.PHP_EOL;
$coreText .= ' '.PHP_EOL;






// Task 18; Retrigger Nginx

$t18t  = ' '.PHP_EOL;
$t18t .= 'h1. Task'.PHP_EOL;
$t18t .= 'Retrigger Nginx'.PHP_EOL;
$t18t .= ' '.PHP_EOL;
$t18t .= ' '.PHP_EOL;
$t18t .= $coreText;
$t18t .= ' '.PHP_EOL;

$task18= new jiraIssue($jira);
$task18->create($environment.", task 18: Retrigger Nginx from Ansible", $t18t, "cicob", "0h", "Task");
$task18->addlabel("Dev-Team-1");





// Task 20, Sanity Check

$t20t = ' '.PHP_EOL;
$t20t .= 'h1. Test Report'.PHP_EOL;
$t20t .= 'Description and documentation of Sanity Check:'.PHP_EOL;
$t20t .= 'https://wiki.mycompany.com/pages/viewpage.action?pageId=54321'.PHP_EOL;
$t20t .= ' '.PHP_EOL;
$t20t .= ' '.PHP_EOL;
$t20t .= 'The following test-users shall be used:'.PHP_EOL;
$t20t .= ' '.PHP_EOL;
$t20t .= '|| Username ||'.PHP_EOL;
$t20t .= '| john |'.PHP_EOL;
$t20t .= '| paul |'.PHP_EOL;
$t20t .= '| carl |'.PHP_EOL;
$t20t .= '| albert |'.PHP_EOL;
$t20t .= ' '.PHP_EOL;
$t20t .= 'The passwords are available in the KeePass-file'.PHP_EOL;
$t20t .= ' '.PHP_EOL;
$t20t .= ' '.PHP_EOL;
$t20t .= $coreText;
$t20t .= ' '.PHP_EOL;


$task20= new jiraIssue($jira);
$task20->create($environment.", task 20: Sanity Check", $t20t, "cicob", "2h", "Test Case");
$task20->dependsOn($task18->getKey());







////////////
// CLose down
$jira->close();

?>

<hr>
<a href="index.php">Start over</a>


