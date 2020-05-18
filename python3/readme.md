
# Querying JIRA-Tickets via the Jira-API

Author: Christer Boberg

Ref: <https://github.com/cicob/jiraApi>


With this piece of Python-code, you can generate JSON-results on your JQL-queries from **simple one-liners**.




## 1A: Prerequisite Windows
As an Win Administrator, in Powershell:
```

choco install git
choco install python3 
py -m pip install glom

```


## 1B: Prerequisite Linux
```

sudo apt-get install python3-pip
sudo pip3 install glom

```





# Configuration
* Add your Jira-URL and -credentials to the config-file:
  This file is ignored by Git, thus no passwords will be stored in Gitlab/GitHub.

```
 cp jira.conf.template jira.conf
 vi jira.conf
```



# Usage
* When creating JQL-queries you need the Jira project-key. This can be found by using the script with the option -n below.

* A JQL-query can be executed with -q, the result is plain JSON, as expected.
  
* With no options submitted, your custom code in the script is executed.

* Change the file 'jira_queries.py' to your liking.

* Execute your script by running the following command:
(OsX / Linux / Powershell)

```
 ./jira_queries.py -h   (for help)

 ./jira_queries.py -n "INFRA"

 ./jira_queries.py -q 'project=10201 AND type="MFT"'

 ./jira_queries.py

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






