<?php
ini_set ( 'display_errors', 'On' );
error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT']."/stub.inc.php");
require_once($root."jira/jiraconfig.inc.php");
require_once($root."jira/jiraApi.inc.php");
require_once($root."jira/jiraIssue.inc.php");


// This ticket-generation can take some time to execute.
//set_time_limit(60);


?>


<a href="index.php">Start over</a>
<hr>


<?php 

///////////
// Startup
print("Hej! This script will generate Users<br>\n\n");






/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
// Init

$jiraApi = new jiraApi($hostname, $jiraUser, $jiraPw, $jiraType);


$jiraApi->setDebug(False);
//$jiraApi->setDebug(True);







/////////////////////////////////
//// FUNCTIONS
/////////////////////////////////

function createCCuser ($username, $password, $displayname) {
 
    global $jiraApi;
    $theGroup = "confluence-callpoint-ma";
    $dummyEmail ="cc.no.email@serafe.ch";
    
    print("Creating user ". $username."\n");
    
    $jiraApi->createUser($username, $password, $dummyEmail, $displayname );
    $jiraApi->addUserToGroup($username, $theGroup );
    
} // end function












/////////////////////////////////
//// Main
/////////////////////////////////


createCCuser("boc26", "alohaCico99", "Christer Test Boberg" );
createCCuser("boc27", "alohaCico99", "Christer Test Boberg" );
createCCuser("boc28", "alohaCico99", "Christer Test Boberg" );
createCCuser("boc29", "alohaCico99", "Christer Test Boberg" );
createCCuser("boc30", "alohaCico99", "Christer Test Boberg" );








////////////
// Close down
$jiraApi->close();

?>


<hr>
<a href="index.php">Start over</a>


