<?php
ini_set ( 'display_errors', 'On' );
error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT']."/stub.inc.php");
require_once($root."jira/jiraconfig.inc.php");
require_once($root."jira/jiraApi.inc.php");

?>


<a href="index.php">Start over</a>
<hr>


<?php 

///////////
// Startup
print("Hej! This script will rename Users<br>\n\n");




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

function renameMyUser ($oldUsername, $newUsername) {
    global $jiraApi;
    print("Renaming user from ".$oldUsername." to ".$newUsername."\n");
    $jiraApi->renameUser($oldUsername, $newUsername);
} // end function












/////////////////////////////////
//// Main
/////////////////////////////////


renameMyUser("cctest4", "cctest5" );








////////////
// Close down
$jiraApi->close();

?>


<hr>
<a href="index.php">Start over</a>


