<?php

require_once($root."jira/jiraApi.inc.php");




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
 * Jira Issues  - Wrapper Class
 *
 * @package    Jira API
 * @author     Christer Boberg 
 */
class jiraIssue {

	function __construct($jira, $key="") {
		$this->jira = $jira;
		$this->key = $key;
	}



	/**
	 * Set Issue-Key
	 */
	function setKey($key) {
		$this->key = $key;
	}



	/**
	 * Get Issue-Key
	 */
	function getKey() {
		return $this->key;
	}
	
	
	
	function create( $summary="No Summary", $description="No Description", $assignee="taaboch5", $estimate="1h", $type = "", $epic = "" ) {
		
		$key = $this->jira->createIssue($summary, $description, $assignee, $estimate, $type);

		//Store the key we just created, as a future reference
		$this->setKey( $key);

		// Set the EPIC if present
		if ($epic != "") {
			$this->setEpic($epic);
		}
		
	}
	



	function createSub( $summary="No Summary", $description="No Description", $assignee="taaboch5", $estimate="1min", $type = "", $key ) {
	
	    $keySub = $this->jira->createIssue($summary, $description, $assignee, $estimate, $type, $key);
	    
		//Store the key we just created
		$this->setKey( $keySub);
	
	}

		
	
	
	/**
	 * Issue depends on firstOne
	 */
	function dependsOn($firstOne) {
	
		switch ($this->jira->getJiraType()) {
			case "1":
				// Customer JIRA Installation - issue.xxx.com
				//
				// Sample:
				// {"id":"10001","name":"Dependency","inward":"is prerequisite for","outward":"depends on","self":"https://issue.xxx.com/rest/api/2/issueLinkType/10001"}
				//
				$this->jira->LinkIssues( $this->getKey(), $firstOne, "Dependency", "This one can only be done after other linked ticket(s)." );
				$this->jira->addComment( $firstOne, "This one needs to be done prior to other linked ticket(s).");
				break;
			case "2":
				// Default out-of-the-box JIRA isntallation
				//
				// Sample
				// {"id":"10000","name":"Blocks","inward":"is blocked by","outward":"blocks","self":"https://jira.mecke.com/rest/api/2/issueLinkType/10000"}
				//
				$this->jira->LinkIssues( $firstOne, $this->getKey(), "Blocks", "This one needs to be done prior to other linked ticket(s)." );
				$this->addComment("This one can only be done after other linked ticket(s).");
				break;
			default:
				//Unknown Target Project
				$this->jira->printStatus("**Error**  Unknown jiraType: [".$this->jira->getJiraType()."]", True, True);
		} // end switch
	} //end Method






	/**
	 * Wrapper for API addComment()
	 */
	function addComment($message) {
		$this->jira->addComment( $this->getKey(), $message );
	}



	/**
	 * Wrapper for API addComponent()
	 * The component is created if it does not exist already.
	 */
	function addComponent($component){
		// Current implementation: The component must exist already
		$this->jira->addComponent($this->getKey(), $component);
	}



	/**
	 * Wrapper for API addFixVersion()
	 * TODO: The fixVersion has to exist already
	 */
	function addFixVersion($fixVersion){
		// Current implementation: The fixVersion has to exist already
		$this->jira->addFixVersion($this->getKey(), $fixVersion);
	}



	/**
	 * Wrapper for API addLabel()
	 * TODO: The label has to exist already
	 */
	function addLabel($label){
		// Current implementation: The label has to exist already
		$this->jira->addLabel($this->getKey(), $label);
	}



	/**
	 * Wrapper for API setEpic()
	 */
	function setEpic($epicKey) {
		$this->jira->setEpicToIssue($this->getKey(), $epicKey);
	}


	/**
	 * Wrapper for API setDueDate()
	 */
	function setDueDate($dateString) {
		$this->jira->setDueDate($this->getKey(), $dateString);
	}
	
	

	
	/**
	 * Wrapper for API setReporter()
	 */
	function setReporter($reporter) {
		$this->jira->setReporter( $this->getKey(), $reporter );
	}
	
	
	
	
	
	
} // end class

?>