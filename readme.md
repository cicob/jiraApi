
# Generating JIRA-Tickets via the Jira-API

Author: Christer Boberg

With this utility we can generate JIRA-Tickets form a script. It was decided to implement this in PHP, but it can of course be done with any language. 
 
You execute your script in one of two ways
- A) from the **Command-Line** 
- B) from a **Browser** 

The latter is only feasable provided you have a web-server running PHP tied to your local file-system (i.e. a [Vagrant](https://www.vagrantup.com/)-box, [WAMP](https://sourceforge.net/projects/wampserver/) or equivalent).






---


# A) From the command-line with PHP

* Edit or create a script analogue to **'go-ahead.php'** (included in Git)
* Execute your script by running the following command:

OsX:
```
 $ php bootstrap.php go-ahead.php
```

Windows:
```
C:\Users\John Wahyne\tools\jira-api-php\public\jira> php.exe bootstrap.php go-ahead.php
```


# B) From your browser with PHP 
If you run a local PHP-Nginx or PHP-Apache on your laptop, you must hook up the local file-tree to be run by the web-serverexecuted there.

Launch the start-page by pointing your browser to 

```
http://{your site}/jira/
```

The file **'index.php'** is offering a rudimentary menu in your browser for selecting the desired script to generate tickets.
They are kept in Git to serve as examples for future similar one-off scripts.

* go-ahead.php - Generate sample tickets
* go-patch.php - Generate tickets for a security-patch Service Window
* Etc





---


# Initial Configuration


## A) Set up your Command-line PHP-Stack

1. Install [PHP](http://php.net/) (i.e. 'php.exe' on a Windows-Machine)
1. Copy the file 'public/jira/bootstrap.php.template' to 'public/jira/bootstrap.php'
1. Edit the file **'public/jira/bootstrap.php'** so that the path to 'src' matches your setup
1. Copy the file 'src/jira/jiraconfig.inc.php.template' to 'src/jira/jiraconfig.inc.php'
1. Edit the file **'src/jira/jiraconfig.inc.php'** to include your JIRA-URL and personal JIRA-Credentials. A generic user is not foreseen (yet).

NOTE: The path to 'src' in the file 'bootstrap.php' above must have forward-slash even if you run this on a PC.

NOTE 2 for PC: 
- In the same folder where 'php.exe' resides; copy the content from the provided file 'php.ini-development' into 'php.ini' and edit the created file.
- The following two lines are important:

```
extension_dir = "D:/prg/php-7.1.9-nts-Win32-VC14-x86/ext"

extension=php_curl.dll
```



## B) Set up your Web-Server PHP-Stack

1. Install VirtualBox
1. Bring up Nginx and PHP, preferably with Ansible
1. Align the folder-structure from this Git-package to your web-root. Note the file **'stub.inc.php'** that will point to the include-files in 'src'
1. Copy the file 'src/jira/jiraconfig.inc.php.template' to 'src/jira/jiraconfig.inc.php'
1. Edit the file **'src/jira/jiraconfig.inc.php'** to include your JIRA-URL and JIRA-Credentials. 
   (A generic JIRA Tech-user could of couse be used if available).


The srcipt 'go.php' will require up to 60s to execute, which is more than the default 30s. 

- This is catered for within PHP by using **set_time_limit()** in the script.
- To prevent a timeout from the hosting web-server, make sure the NGINX-setting is also set to 60s.

```
fastcgi_connect_timeout 60s
```

It is recommended to have at least 2GB of RAM for PHP. Ansible Conde-snippet

```
- name: set memory limit for Jira-PHP
  ini_file:
    dest: /etc/php.ini
    section: PHP
    option: memory_limit
    value: 2048M
```


## Parameters in 'jiraconfig.inc.php'

* $hostname - URL to your JIRA-Server

* $jiraUser - username

* $jiraPw  - password

* $jiraType - Set to 1 or 2.  
 1 = Swisscom  
 2 = Plain-vanilla Jira v7.3.0  
The **Swisscom-Instance** is different from the out-of-the-box **JIRA v7.3.0**, in that they use different issueLink-types. 
See Sample-output below and the implementation in the wrapper-class for further details.




---


# Design 

For a brief overview, please see these pictures: [Overview in Powerpoint](jira-api-presentation.pdf)



- The main PHP-script (such as 'go-patch.php') includes the wrapper-class **'jiraIssue.inc.php'** 
  as well as the base-class **'jiraApi.inc.php'**. With these classes, ticket-generation turns into an array of simple one-liners.
- Your *JIRA-credentials* are kept locally in 'jiraconfig.inc.php' and are **not** checked into Git. (See [.gitignore](https://github.xxx/.gitignore).)

```
$task3->create($environment.", task 33: Trigger Test-Suite", $coreText, "johnWayne", "1h","Task");
```



## PHP-Class 'jiraIssue'
The wrapper-class is instantiated once per issue. This way, we can retain the KEY-id 
which is needed when we cross-reference ('link') tickets with each other.

Methods include:
* create( $summary, $description, $assignee, $estimate, $type, $epic ) - Create a Ticket
* createSub( $summary, $description, $assignee, $estimate , $type, $key ) - Create a Sub-Ticket
* getKey() - To use for linking with another ticket
* dependsOn() - Linking two tickets
* addComment() - Add a comment
* addComponent() - Add a component
* addFixVersion() - Stick a Version to a Change-Request
* addLabel() - Add a label
* setEpic() - set Epic for a ticket
* setDueDate() - Set Due Date for a ticket
* setReporter() - Set Reporter for a ticket





## PHP-Class 'jiraApi'
This is the core class that initiate the communication to the JIRA-server. 
All API-calls go through this class, but is normally accessed via the wrapper-class above.


Methods include:
* __construct() - Initate the communication
* setProjectKey() - set the JIRA-Project to use
* setIssueType() - Set Issue-Type for new issues where it is not specified explicitly. The default ist 'Task'
* setDefaultEpic() - Set the Epic for all newly created issues where it is not specified explicitly
* setDebug( True / False ) - set to 'True' to enable more verbose output. Very useful when identifying problems, such as identifying non-existing components or issue-types
* close() - Finish off, terminate the connection



---

# Usage (Class Invocation)

1. Initiate a connection to JIRA by instantiating the class 'jiraApi':
```
	$jiraApi = new jiraApi($hostname, $jiraUser, $jiraPw, $jiraType);
```

2. Prepare a new issue by instantiating the class 'jiraIssue':
```
	$task01 = new jiraIssue($jiraApi);
```

3. Create this issue by calling the create-method
```
	$task01->create( $summary, $description, $assignee,  ... );
```

4. Add attributes to the generated ticket with further calls:

```
	$task01->addlabel("Dev-Team-A");
	$task01->addFixVersion($fixVersion);
	$task01->setReporter($defaultReporter);
	$task01->addComponent("nginx");
	$task01->setDueDate($startDate);
```







---


# References

Starting point: <https://docs.atlassian.com/jira/REST/server/>




## Jira API - Samples with CURL

```

http://hostname/rest/<api-name>/<api-version>/<resource-name>

curl -u admin:admin http://localhost:8090/jira/rest/api/2/issue/MKY-1

curl -D- -X GET -H "Content-Type: application/json"  https://issue.xyz.com/browse/HAAG-162

```





## How to update Issues

<https://developer.atlassian.com/jiradev/jira-apis/jira-rest-apis/jira-rest-api-tutorials/updating-an-issue-via-the-jira-rest-apis>


## Add a Comment to an Issue

<https://developer.atlassian.com/jiradev/jira-apis/jira-rest-apis/jira-rest-api-tutorials/jira-rest-api-example-add-comment>


## JSON Array with PHP

<http://stackoverflow.com/questions/6739871/how-to-create-an-array-for-json-using-php>

Simple: Just create a (nested) PHP array and call json_encode on it.
Numeric arrays translate into JSON lists ([]), 
associative arrays and PHP objects translate into objects ({}). Example:

```
$a = array(
        array('foo' => 'bar'),
        array('foo' => 'baz'));
$json = json_encode($a);
```

Gives you:

```
[{"foo":"bar"},{"foo":"baz"}]
```





## Sample output



Lab, what link-types do we have?
http://10.40.0.10:8080/rest/api/2/issueLinkType

```
{"issueLinkTypes":[
{"id":"10000","name":"Blocks","inward":"is blocked by","outward":"blocks","self":"https://jira.meckecico.com/rest/api/2/issueLinkType/10000"},
{"id":"10001","name":"Cloners","inward":"is cloned by","outward":"clones","self":"https://jira.meckecico.com/rest/api/2/issueLinkType/10001"},
{"id":"10002","name":"Duplicate","inward":"is duplicated by","outward":"duplicates","self":"https://jira.meckecico.com/rest/api/2/issueLinkType/10002"},
{"id":"10003","name":"Relates","inward":"relates to","outward":"relates to","self":"https://jira.meckecico.com/rest/api/2/issueLinkType/10003"}]}
```





