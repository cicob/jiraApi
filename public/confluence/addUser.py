#!/usr/bin/python
# This Python file uses the following encoding: utf-8
#
# File Name: AddUser.py
# Purpose: Sets up a user login and gives the user a personal space
#
# Notes:
#   All rights management is also done
#   User added to confluence-users
#
# File History:
# 2006-03-01 russ     - Created file
# 2019-01-08 C Boberg - looping though a bunch of new users

import xmlrpclib


#-----------------------------
# Parameters
#-----------------------------

import config as cfg

confluenceStdGroup = "confluence-users"




#-----------------------------
# Functions
#-----------------------------

def createCCuser(userName, userPassWord, fullName):
  
  if not s.confluence1.hasUser(token, userName):
    print "Adding user '%s'" % userName
    userDef = dict(email = "%s%s" % (userName, cfg.companyEmailSuffix),
                 fullname = fullName,
                 name = userName
              )
    s.confluence1.addUser(token, userDef, userPassWord)
  else:
    print "User '%s' already exists!" % userName
  
  
  #add user to the standard user group...
  print "Adding user '%s' to the group '%s'" % (userName, cfg.confluenceGroup)
  s.confluence1.addUserToGroup(token, userName, cfg.confluenceGroup)
  print "Remove user '%s' from the standard group '%s'"  % (userName, confluenceStdGroup)
  s.confluence1.removeUserFromGroup(token, userName, confluenceStdGroup)















#-----------------------------
# Main
#-----------------------------

print("")
print("")
print("# Create Confluence Users via XML-RPC API")
print(" ")
print(" ")

s = xmlrpclib.ServerProxy(cfg.hostName + "/rpc/xmlrpc")
print "Logging in..."
token = s.confluence1.login(cfg.confluenceUser, cfg.confluencePw)





createCCuser("boc7", "abcdef1234", "ChristerTest Hönspelle Boberg")
createCCuser('boc8', 'abcdef1234', 'ChristerTest René Boberg')
createCCuser('boc9', 'abcdef1234', 'ChristerTest Jürgen Boberg')


print(" ")
print(" ")
print "User management complete!"


