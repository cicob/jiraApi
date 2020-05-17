
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
    self.config = config
    self.projects = []
    self.issueTypes = []

  # JIRA API Query - considered PRIVATE
  def query(self, url):
   myResponse = requests.get(url, auth=(self.config.jiraUser, self.config.jiraPw))
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

  # ##  curl -s -u 'cbob:xxxxxx' --basic --insecure 'https://jira.serafe.ch/rest/api/2/project' |
  # ##        jq '.[] | {Name: .name, Key: .key}  '
  def scanProjects(self):
#    print('--- === scanProjects === ---')
    url = self.config.jiraServer + "/rest/api/2/project"
    queryResult = self.query(url)
    spec = [{'Name': 'name',
          'Key': 'key'}]
    self.projects = glom(queryResult, spec)


  # ##  curl -s -u 'cbob:xxxxxxx' --basic --insecure 'https://jira.serafe.ch/rest/api/2/project/INFRA' | 
  # ##        jq '. | {issueTypeName: .issueTypes[].name }'
  def getIssueTypes(self, project):
    url = self.config.jiraServer + "/rest/api/2/project/" + project
    queryResult = self.query(url)
    spec = {'IssueTypes': ('issueTypes', ['name'])}
    self.issueTypes = glom(queryResult, spec)
    return self.issueTypes

  def getIssues(self, project):
    url = self.config.jiraServer + "/rest/api/2/search?jql=project=" + project
    queryResult = self.query(url)
    spec = {'IssueTypes': ('issueTypes', ['name'])}
    self.issueTypes = glom(queryResult, spec)


#issuetype = MFT






  def queryAndReportToPrometheus(self, project, issuetype):
    url = self.config.jiraServer + "/rest/api/2/search?jql=project=" + urllib.parse.quote (project['Key'] + 
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

