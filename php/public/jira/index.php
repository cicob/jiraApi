<?php
require_once($_SERVER['DOCUMENT_ROOT']."/stub.inc.php");
require_once($root."jira/jiraconfig.inc.php");
?>
<!DOCTYPE html>
<html>
<head>


<?php
print("<h1>JIRA Ticket generator - ".date("d M Y  H:i:s")."</h1>\n");
?>


Enter one of the links below to start the Ticket generation.<br>
<hr>


<?php


print("Environment in use: <b>".$hostname."</b><br>\n");
print("JIRA System-type: <b>".$jiraType."</b>\n");

?>

<hr>

 <br>
 <br>
 <br>


<table border=3>
  <tbody>
    <tr>


      <td>  <a href="go-ahead.php">GO Ahead</a> <br>  generate some tickets    
      </td>
      
      
      <td>    Test-shot instead: <br>
      <a href="test.php">TEST</a> a few tickets
      </td>



    </tr>
  </tbody>
</table>

 <br>
 <br>
 <br>
 
<table border=3>
  <tbody>
    <tr>



      
      <td>    Create tickets for a Security Patch: <br>
      <a href="go-patch.php">GO Patch!</a> 
      </td>


      
      <td>    Create tickets for something else: <br>
      <a href="xyz.php">GO!</a> 
      </td>



    </tr>
  </tbody>
</table>

 <br>
 <br>
 <br>
 


</body>
</html>


