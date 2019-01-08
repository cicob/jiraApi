<?php



/********************************************************************************************************************************

The Open Source Initiative --  BSD 2-Clause License

Copyright (c) 2017, Christer Boberg  - GitHub cicob
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted
provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following
disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING,
BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


********************************************************************************************************************************/




/**
 * Jira API Class - for creating Issues
 * @package    Jira API
 * @author     Christer Boberg  
 */
class jiraApi {

	function __construct($hostname, $jiraUser, $jiraPw, $jiraType) {
		global $cmdline;
		$this->cmdline = $cmdline;
		
		$this->hostname = $hostname;
		$this->jiraUser = $jiraUser;
		$this->jiraPw = $jiraPw;
		$this->jiraType = $jiraType; // Customer (1), or Default (2)
		
		$this->debug = False;
		$this->result_json = 0;
		$this->ch = curl_init();
		$this->component = "";
		$this->issueType = "Task"; // Default
		$this->projectKey = "";
		$this->defaultEpic ="";
		
		/////////////////////////////
		// Required Constants
		/////////////////////////////
		
		$this->baseApiUrl =        $this->hostname . "/rest/api/2/";
		$this->greenhopperUrl = $this->hostname . "/rest/greenhopper/1.0/";
		
	}


	
	
	/**
	 * Add Comment
	 */
	function addComment($key, $comment){
		//rest/api/2/issue/{issuekey}/comment
	
		$myArray = array(  "body" => $comment);
		$result = $this->post("issue/".$key."/comment", json_encode($myArray));
	
	}
	

	
	
	
	/**
	 * Add Component - creates one if it does not exist.
	 */
	function addComponent($key, $component){
		//rest/api/2/issue/{issuekey}
		
		if ( !$this->existComponent($component) ) {
			$this->createComponent($component);
		}
		
		$myArray = array(
			"fields" => array( 
					"components" => array(array( "name" => $component ))  
			) // fields
		); // myArray
				
		$result = $this->put("issue/".$key, json_encode($myArray));
	
	}
	
	function createComponent($name, $description = "", $leadUserName ="", $assigneeType ="PROJECT_LEAD"){
	    
	    $myArray = array(
	        "name" => $name,
	        "description" => $description,
	        "leadUserName" => $leadUserName,
	        "assigneeType" => $assigneeType,
	        "isAssigneeTypeValid" => "false",
	        "project" => $this->projectKey
	    );
	    
	    $this->post("component/", json_encode($myArray));
	}
	
	private function existComponent($name){
			
		$componentExists = False;
		
		//      /rest/api/2/project/ <KEY> /components
		$this->httpget("project/". $this->projectKey ."/components");
		$res = json_decode($this->result_json); //Returning a Class
		foreach($res as  $object) {
			if ($object->name == $name) {
				$componentExists = True;
			}
		}
		
		return $componentExists;
	}
	
	
	
	
	/**
	 * Add Label
	 */
	function addLabel($key, $label){
		//rest/api/2/issue/{issuekey}
		
		$myArray = array(
			"fields" => array(
				"labels" => array( $label )
			) // fields
		); // myArray
		
		$result = $this->put("issue/".$key, json_encode($myArray));
		
	}
	
	
	
	
	
	
	
	
	/**
	 * Add FixVersion - creates one if it does not exist.
	 */
	function addFixVersion($key, $fixVersion){
		//rest/api/2/issue/{issuekey}
		
		if ( !$this->existFixVersion($fixVersion) ) {
			$this->createFixVersion($fixVersion);
		}
		
		$myArray = array(
			"fields" => array(
				"fixVersions" => array(array( "name" => $fixVersion ))
			) // fields
		); // myArray
		
		$result = $this->put("issue/".$key, json_encode($myArray));
	}
	
	function createFixVersion($name, $description = ""){

		$myArray = array(
			"name" => $name,
			"description" => $description,
			"archived" => "false",
			"released" => "true",
			"project" => $this->projectKey
		);
		
		// POST /rest/api/2/version
		$this->post("version/", json_encode($myArray));
	}
	
	private function existFixVersion($name){
	
		$fixVersionExists = False;
		
		//      /rest/api/2/project/ <KEY> /versions
		$this->httpget("project/". $this->projectKey ."/versions");
		$res = json_decode($this->result_json); //Returning a Class
		
		foreach($res as  $object) {
			if ($object->name == $name) {
				$fixVersionExists = True;
			}
		}
		
		return $fixVersionExists;
	}
	

	
	
	
	
	/**
	 * Create User
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string $displayname
	 * @param string $notification (default: false)
	 */
	function createUser($username, $password, $email, $displayname, $notification  = "false") {
	    
	    $myArray = array(
	        "name" => $username,
	        "password" => $password,
	        "emailAddress" => $email,
	        "displayName" => $displayname,
	        "notification" => $notification
	    ); 
	    
	    // Create User
	    $this->post("user/", json_encode($myArray));
	    
	    return TRUE;
	}
	
	

	/**
	 * Add User to group
	 * @param string $username
	 * @param string $groupname
	 */
	function addUserToGroup($username, $groupname) {
	    
	    $myArray = array(
	        "name" => $username
	    ); 
	    
	    // Add to group
	    $this->post("group/user?groupname=".$groupname , json_encode($myArray));
	    
	    return TRUE;
	}
	
	
	
	
	
	
	
	
	
	
/**
 * Create Issue or Sub-Issue
 * @param string $summary
 * @param string $description
 * @param string $assignee
 * @param string $estimate
 * @param string $type
 * @param string $parent
 * @return string Generated Key
 */
	function createIssue($summary, $description, $assignee, $estimate = "0h", $type = "", $parent = "") {

		// Main-Ticket or Sub-Ticket?
		if ( $parent == "") {
			// Main Ticket, Default type
			$issueType = $this->issueType; 
		} else {
			// Sub-Ticket, Default is Sub-Task
			$issueType = "Sub-task"; 
		}

		// Ticket-Type, if not default
		if ( $type != "") {
			$issueType = $type;
		} 
		
		$myArray = array( 
					"fields" => array(  
						"project" => array( "key" => $this->projectKey),
						"summary" => $summary,
						"description" => $description,
						"issuetype" => array("name" => $issueType),
						"assignee" => array("name" => $assignee),
						"timetracking" => array( "originalEstimate" => $estimate)  
				) // fields
		); // myArray
		
		if ($parent != "") {
			$myArray["fields"]["parent"] =  array( "key" => $parent );
		}

		if ($this->component != "") {
			
			//Does this component exist?
			if ( !$this->existComponent($this->component) ) {
				$this->createComponent($this->component);
			}
			
			$myArray["fields"]["components"] =  array(array( "name" => $this->component ));
		}
		
		
		
		// Execute
		$this->post("issue/", json_encode($myArray));	
		
		// Print resulting URL on screen
		$burl = $this->hostname ."/browse/".$this->resultKey();
		$this->printStatus($summary.": ", False, True, $burl);

		return $this->resultKey();
	}
	
	

	
	
	
	function linkIssues($inward, $outward, $type = "Blocks", $comment = "" ) {
		// $type = "Duplicate" or "Blocks" or "Dependency", $comment = "Duplicate issues" etc
		// The comment goes into the Inward Issue
		
		$myArray = array( 
			"type" => array ("name" => $type),
			"inwardIssue" => array( "key" => $inward),
			"outwardIssue" => array( "key" => $outward),
			"comment" => array( "body" => $comment) 
		);
			
		$this->post("issueLink/", json_encode($myArray));
		
	}
	
	
	
	
	
	
	
	// DEPRECATED 
	private function call($url) {
		// This implies a HTTP-GET, right?
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($this->ch, CURLOPT_USERPWD,  $this->jiraUser.":".$this->jiraPw  );

		// Status
		$this->printStatus("Trying call with: ", False, False, $this->baseApiUrl.$url);
		curl_setopt($this->ch, CURLOPT_URL, $this->baseApiUrl.$url);
		$this->result_json=curl_exec($this->ch);
		if ($this->result_json === false) {
			$this->printStatus('Curl error: ' . curl_error($ch), True, True);
		}
	}
	
	private function httpget($url, $myBaseUrl ="") {
	    return $this->jiraRest($url, "", "GET", $myBaseUrl);
	}
	
	private function post($url, $json) {
		return $this->jiraRest($url, $json, "POST");
	}
	
	private function put($url, $json, $myBaseUrl ="") {
		$result = $this->jiraRest($url, $json, "PUT", $myBaseUrl);
	}
	
	
	private function jiraRest($url, $json = "", $method, $myBaseUrl = "") {
	
		if ($myBaseUrl != "") {
			$myUrl = $myBaseUrl.$url;
		} else {
			$myUrl = $this->baseApiUrl.$url;  // JIRA API URL
		}
		
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($this->ch, CURLOPT_USERPWD,  $this->jiraUser.":".$this->jiraPw  );

		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		if ($json != "") {
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($json))
					);
		}
		
		// Status
		$this->printStatus("----REST Start------------", False);
		$this->printStatus("Trying ".$method." with: [".$myUrl."]", False);
		$this->printStatus("Trying ".$method." with JSON: [".$json."]", False);
		$this->printStatus("----REST End------------", False);
		
		
		curl_setopt($this->ch, CURLOPT_URL, $myUrl);
	
		// Execution - CURL
		$this->result_json=curl_exec($this->ch);

		// Error-check #1
		if ($this->result_json === false) {
			$this->printStatus('Curl ".$method." error: ' . curl_error($this->ch), False, True);
		}
		
		// Error-check #2
		if (strpos($this->result_json, 'errorMessages') !== false) {
			$this->printStatus(" ", False, True); // Empty line
			$res = json_decode($this->result_json); //Returning a Class
			foreach($res->errorMessages as  $key => $value) {
			$this->printStatus("**ErrorMessage** : [".$key."] [".$value."]", False, True);
			}
			$this->printStatus(" ", False, True); // Empty line
		}
		
		// Error-check #3
		if (strpos($this->result_json, 'errors') !== false) {
			$this->printStatus(" ", False, True); // Empty line
			$res = json_decode($this->result_json); //Returning a Class
			foreach($res->errors as $key => $value) {
				$this->printStatus("**Error**: [".$key."] [".$value."]", False, True);
			}
			$this->printStatus(" ", False, True); // Empty line
		}
		
		$this->printStatus("Debug Return: [".$this->result_json."]", True);
		
		return $this->result_json;
	}
	
	
	
	

	function resultJson() {
		return $this->result_json;
	}


	function resultClass() {
		return json_decode($this->result_json); //Returning a Class
	}


	function result() {
		return json_decode($this->result_json, true); // Returnign an Array
	}


	function resultKey() {
		$res = json_decode($this->result_json); //Returning a Class
		return $res->key; // Returning the issue-key
	}


	function resultSummary() {
		$res = json_decode($this->result_json); //Returning a Class
		return $res->fields->summary;
	}
	
	


	
	function printStatus ($message, $border = False, $force = False, $url = "") {
		
		if ($this->debug or $force) {
			if ($border) {
				$this->printLine("--------------");
			}

			// Print text
			$this->printLine($message);

			// Add URL to output
			if ($url != "") {
				
				
				if ($this->cmdline) {
					// Command-line
					$this->printLine("[ ".$url." ]");
				} else {
					//Web-Server
					$this->printLine(" <a href=\"".$url."\">".$url."</a>");
				}
				
				
			}
			
			
			if ($border) {
				$this->printLine("--------------");
			}
		} // end debug/force
	} // end function
	



	private function printLine ($message) {
	
		print($message);

		if ($this->cmdline) {
			// Command-line
			print(PHP_EOL);
		} else {
			//Web-Server
			print("<br>".PHP_EOL);
		}
	} // end function
	
	
	
	
	


	//TODO: Not in use yet. Not ready.
	function query($issue) {
		$this->call("issue/". $issue);
	}
	


	//TODO: Not in use yet. Not ready.
	function queryLinkIssue($issue) {
		$this->call("issueLink/". $issue);
	}
	
	
	


	

	/////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////
	// Getters
	
	

	function getDebug() {
		return $this->debug;
	}
	
	

	/**
	 * Get type of JIRA-Installation
	 * (Customer or default out-of-the-box)
	 * This influences methods for linking tickets.
	 */
	function getJiraType() {
		return $this->jiraType;
	}
	
	
	
	


	
	/////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////
	// Setters
	
	
	
	


	function setComponent($component) {
		// Set component to use when creating issues
		$this->component = $component;
	
	
		////////////////////////////////
		// TODO: Add component if it doesn't exist yet.
		//    GET /rest/api/2/component/{id}
	
		//		$allComponents = $this->httpget("project/".$this->projectKey."/components");
	
		//		var_dump(  json_decode(  $allComponents  )   );
	
	}
	
	
	

	function setDebug($flag) {
		$this->debug = $flag;
	}
	

	function setDefaultEpic($key) {
		$this->defaultEpic = $key;
	}
	
	
	private function setDefaultEpicToIssue($issue) {
		if ($this->defaultEpic != "") {
			$this->setEpicToIssue($issue, $this->defaultEpic);
		}
	}
	
	
	
	


	function setDueDate($key, $dateString){
		//rest/api/2/issue/{issuekey} 
		
		$myArray = array(
				"fields" => array(
						"duedate" => $dateString 
				) // fields
		); // myArray
		
		$result = $this->put("issue/".$key , json_encode($myArray));
	}
	
	

	function setEpicToIssue($issue, $epicIssue) {
		$url = "epics/".$epicIssue."/add";
		$json = '{"ignoreEpics":true,"issueKeys":["'.$issue.'"]}';
		$result = $this->put($url, $json, $this->greenhopperUrl); // JIRA Greenhopper URL
		return $result;
	}
	
	
	
	
	/**
	 * Set new Issue-Type for new issues where it is not specified. The default ist 'Task'
	 */
	function setIssueType($issueType) {
		$this->issueType = $issueType;
	}


	/**
	 * Set Project Key. Used in all API-calls
	 */
	function setProjectKey($projectKey) {
		$this->projectKey = $projectKey;
		$this->printStatus("Using JIRA-Project ".$projectKey, False, True, $this->hostname ."/browse/".$this->projectKey);
	}
	
	
	
	


	function setReporter($key, $reporter){
		//rest/api/2/issue/{issuekey}/comment

		$myArray = array( "fields" =>
				array( "reporter" =>
						array( "name" => $reporter)		
					) // reporter
		); // fields
		
		$result = $this->put("issue/".$key , json_encode($myArray));
		
		
	}
	
	

	
	
	
	
	/*-
	 * Close the API-channel. Terminate the communication.
	 */
	function close() {
		curl_close($this->ch);
	}
	
	
	
	


} // end class

?>