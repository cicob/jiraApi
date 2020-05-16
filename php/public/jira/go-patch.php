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
print("Hej! This script will generate tickets for a Security patch<br>\n\n");





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

$jiraApi = new jiraApi($hostname, $jiraUser, $jiraPw, $jiraType);
$jiraApi->setProjectKey($jiraProjectKey);
$jiraApi->setDefaultEpic($factoryEpic);

$jiraApi->setDebug(False);
//$jiraApi->setDebug(True);




$defaultReporter = "cicob"; 
$defaultAssignee = "cicob"; 







/////////////////////////////////
//// FUNCTION Sub-Ticket generation
/////////////////////////////////

function createSecTicket ($environment, $sprint, $urlCore) {

	// TODO: Rewrite as a CLASS to avoid global vars
	global $jiraApi, $taskTop, $defaultAssignee, $defaultReporter, $fixVersion, $startDate, $startTime, $endTime;


	$body = ' '.PHP_EOL;
	$body .= 'h1. Hintergrund'.PHP_EOL;
	$body .= 'Security Updates: All environments shall be updated in accorance with Spacewalk.'.PHP_EOL;
	$body .= ' '.PHP_EOL;
	$body .= 'h1. Task'.PHP_EOL;
	$body .= 'Rollout of the planned Security Patches.'.PHP_EOL;
	$body .= ' '.PHP_EOL;
	$body .= 'h1. Environment'.PHP_EOL;
	$body .= $environment.PHP_EOL;
	$body .= "URL: https://www.".$urlCore.".myisp.com"   .PHP_EOL;
	$body .= ''.PHP_EOL;
	$body .= 'h1. Clear for Deployment'.PHP_EOL;
	$body .= "Yes. ".PHP_EOL;	
	$body .= ' '.PHP_EOL;
	$body .= 'h1. Service-Window'.PHP_EOL;
	$body .= 'Start: '.$startDate.', '.$startTime.PHP_EOL;
	$body .= 'End: '.$startDate.', '.$endTime.PHP_EOL;
	$body .= ' '.PHP_EOL;

	$taskSub01 = new jiraIssue($jiraApi);
	$taskSub01->createSub($environment.": Security Patching", $body, $defaultAssignee, "15m", "Change (sub)", $taskTop->getKey() );
	$taskSub01->addlabel("Ops-Team-B");
	$taskSub01->addFixVersion($fixVersion);
	$taskSub01->setReporter($defaultReporter);
	$taskSub01->addComponent("HA-proxy");
	$taskSub01->addComponent("Nginx");
	$taskSub01->setDueDate($startDate);

	$taskSub01->addComponent("EF".$sprint);


} // end function











/////////////////////////////////
//// Main
/////////////////////////////////


// In this example, the main-ticket CAAP-29 with details already exists.

$taskTop = new jiraIssue($jiraApi);
$taskTop->setKey('CAAP-29');

$fixVersion = "171120";




//  Name  -  Customer order  -  URL-core


$startDate = "2017-11-20";
$startTime = "08h30";
$endTime = "17h00";

createSecTicket ( "Customer 07 test", "07", "ttth-test");
createSecTicket ( "Customer 08 test", "08", "eefe-test");



$startDate = "2017-11-28";
createSecTicket ( "Customer 02 stage", "02", "frfr-stage");
createSecTicket ( "Customer 03 stage", "03", "aaa-stage");
createSecTicket ( "Customer 06 stage", "06", "fff-stage");


$startDate = "2017-11-29";
createSecTicket ( "Customer 17 prod", "17", "ee");
createSecTicket ( "Customer 24 test", "24", "rrr-test");



$startDate = "2017-11-30";
$startTime = "18h00";
$endTime = "22h00";

createSecTicket ( "Customer 10 Prod", "10", "eee");
createSecTicket ( "Customer 04 Prod", "04", "eee");


$startDate = "2017-12-01";
$startTime = "08h30";
$endTime = "17h00";

createSecTicket ( "Customer 15 dev", "15", "my-dev");









////////////
// Close down
$jiraApi->close();

?>


<hr>
<a href="index.php">Start over</a>


