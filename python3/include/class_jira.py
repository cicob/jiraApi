
#-----------------------------
# Libraries
#-----------------------------
import logging
import json, requests
import urllib.parse

# https://glom.readthedocs.io/en/latest/tutorial.html
# This requires:
# sudo apt-get install python3-pip
# sudo pip3 install requests
# sudo pip3 install glom
#
from glom import glom

#-----------------------------
# Class
#-----------------------------


class Jira:

    def __init__(self, config):
        self.jiraUrl = config.get('jira', 'url')
        self.user = config.get('jira', 'user')
        self.password = config.get('jira', 'password')

    #######################################
    # JIRA API Query - considered PRIVATE
    def query(self, url):
        logging.debug('url: ' + url)
        myResponse = requests.get(url, auth=(self.user, self.password))
        # For successful API call, response code will be 200 (OK)
        logging.debug('Response: ' + str(myResponse))
        if(myResponse.ok):
            # Loading the response data into a dict variable
            # json.loads takes in only binary or string variables
            # so using content to fetch binary content
            #
            # Loads (Load String) takes a Json file and converts into python
            # data structure (dict or list, depending on JSON)
            jData = json.loads(myResponse.content)
        else:
            # If response code is not ok (200), print the resulting http error code with description
            myResponse.raise_for_status()
            jData = {}
        logging.debug('jData: ' + str(jData))
        return jData


    #######################################
    # When a method is called externally, always return JSON
    # When a method is called internally, keep the Python Dict/List
    # for further processing, i.e. jsonResult = False
    def formatResult(self, theContent, jsonResult=True):
        if (jsonResult):
            return json.dumps(theContent, indent=2, sort_keys=False)
        else:
            return theContent


    #######################################
    # ##  curl -s -u 'cccc:xxxxxx' --basic --insecure 'https://jira.mycompany.com/rest/api/2/project' |
    # ##        jq '.[] | {Name: .name, Key: .key}  '
    def scanProjectsRaw(self, jsonResult=True):
        url = self.jiraUrl + "/rest/api/2/project"
        queryResult = self.query(url)
        return self.formatResult(queryResult, jsonResult)

    def scanProjects(self):
        queryResult = self.scanProjectsRaw(False)
        spec = [{'Name': 'name',
        'Key': 'key',
        'Id': 'id'}]
        self.projects = glom(queryResult, spec)
        return self.formatResult(glom(queryResult, spec))


    def getProjectKey(self, key):
        queryResult = self.scanProjectsRaw(False)
        spec = [{'Name': 'name',
        'Key': 'key',
        'Id': 'id'}]
        projects = glom(queryResult, spec)
        result = next((item for item in projects if item["Key"] == key), None)
        return result['Id']



    #######################################
    # ##  curl -s -u 'cccc:xxxxxxx' --basic --insecure 'https://jira.mycompany.com/rest/api/2/project/INFRA' |
    # ##        jq '. | {issueTypeName: .issueTypes[].name }'
    def getIssueTypesRaw(self, project, jsonResult=True):
        url = self.jiraUrl + "/rest/api/2/project/" + project
        queryResult = self.query(url)
        return self.formatResult(queryResult, jsonResult)

    def getIssueTypes(self, project):
        queryResult = self.getIssueTypesRaw(project, False)
        spec = {'IssueTypesName': ("issueTypes", ["name"])}
        return self.formatResult(glom(queryResult, spec))


    #######################################
    # ##  curl -s -u 'cccc:xxxxxxx' --basic --insecure 'https://jira.mycompany.com/rest/api/2/search?jql=project%3D10201%20AND%20type%3D%22MFT%22' |
    # ##   jq '. | {issueKey: .issues[].key }'
    def getIssuesRaw(self, projectKey, issuetype, jsonResult=True):
        url = self.jiraUrl + "/rest/api/2/search?jql=project=" + \
        urllib.parse.quote (projectKey + ' AND type=\"' + issuetype + '\"')
        queryResult = self.query(url)
        return self.formatResult(queryResult, jsonResult)

    def getIssues(self, projectKey, issuetype):
        queryResult = self.getIssuesRaw(projectKey, issuetype, False)
        spec = {'IssueKey': ("issues", ["key"])}
        return self.formatResult(glom(queryResult, spec))



    #######################################
    #  jql: 'project=10201 AND type="MFT"'
    # ##  curl -s -u 'cccc:xxxxxxx' --basic --insecure 'https://jira.mycompany.com/rest/api/2/search?jql=project=10201%20AND%20type%3D%22MFT%22' |
    # ##   jq '. | {issueKey: .issues[].key }'
    def jql(self, query):
        url = self.jiraUrl + "/rest/api/2/search?jql=" + urllib.parse.quote( query ) 
        #logging.debug('url: ' + url)
        queryResult = self.query(url)
        return self.formatResult(queryResult)








