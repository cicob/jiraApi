
#-----------------------------
# Libraries
#-----------------------------
import json
import requests
import urllib.parse

# https://glom.readthedocs.io/en/latest/tutorial.html
# This requires:
# sudo apt-get install python3-pip
# sudo pip3 install glom
from glom import glom

#-----------------------------
# Class
#-----------------------------


class Jira:

  def __init__(self, config):
    self.jiraUrl =  config.get('jira', 'url')
    self.user =     config.get('jira', 'user')
    self.password = config.get('jira', 'password')


  # JIRA API Query - considered PRIVATE
  def query(self, url):
   myResponse = requests.get(url, auth=(self.user, self.password))
   # For successful API call, response code will be 200 (OK)
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
   return jData


  # When a method is called externally, always return JSON
  # When a method is called internally, keep the Python Dict/List 
  # for further processing, i.e. jsonResult = False
  def formatResult(self, theContent, jsonResult=True):
    if (jsonResult):
        return json.dumps(theContent, indent=2, sort_keys=False)
    else:
        return theContent



  # ##  curl -s -u 'cbob:xxxxxx' --basic --insecure 'https://jira.serafe.ch/rest/api/2/project' |
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



  # ##  curl -s -u 'cbob:xxxxxxx' --basic --insecure 'https://jira.serafe.ch/rest/api/2/project/INFRA' | 
  # ##        jq '. | {issueTypeName: .issueTypes[].name }'
  def getIssueTypesRaw(self, project, jsonResult=True):
    url = self.jiraUrl + "/rest/api/2/project/" + project
    queryResult = self.query(url)
    return self.formatResult(queryResult, jsonResult)


  def getIssueTypes(self, project):
    queryResult = self.getIssueTypesRaw(project, False)
    spec = {'IssueTypesName': ("issueTypes", ["name"])}
    return self.formatResult(glom(queryResult, spec))



  # ##  curl -s -u 'cbob:xxxxxxx' --basic --insecure 'https://jira.serafe.ch/rest/api/2/search?jql=project%3D10201%20AND%20type%3D%22MFT%22' | 
  # ##   jq '. | {issueKey: .issues[].key }'
  def getIssuesRaw(self, projectKey, issuetype, jsonResult=True):
    url = self.jiraUrl + "/rest/api/2/search?jql=project=" + \
        urllib.parse.quote (projectKey +  ' AND type=\"' + issuetype + '\"')    
    queryResult = self.query(url)
    return self.formatResult(queryResult, jsonResult)

    
  def getIssues(self, projectKey, issuetype):
    queryResult = self.getIssuesRaw(projectKey, issuetype, False)
    spec = {'IssueKey': ("issues", ["key"])}
    return self.formatResult(glom(queryResult, spec))






  def queryAndReportToPrometheus(self, project, issuetype):
    url = self.jiraUrl + "/rest/api/2/search?jql=project=" + urllib.parse.quote (project['Key'] + 
    " AND type=\"" + issuetype + 
    "\" AND status not in (Closed, Approved, Done, Resolved)")
    queryResult = self.query(url)

    self.prometheusInstance.setLabel("project", project['Key'])  
    self.prometheusInstance.setLabel("projectname", project['Name'])  
    self.prometheusInstance.setLabel("issuetype", issuetype)
    self.prometheusInstance.setLabel("state", "Open")
    self.prometheusInstance.sendValue(str(queryResult['total']))


  def main(self):
    self.scanProjects()
    for projectItem in self.projects:
      self.getIssueTypes(projectItem['Key'])
      for iType in self.issueTypes['IssueTypes']:
        self.queryAndReportToPrometheus(projectItem, iType)

