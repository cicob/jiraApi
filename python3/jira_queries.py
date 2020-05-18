#!/usr/bin/env python3
# This Python3 file uses the following encoding: utf-8
# -----------------------------
#
# Making JIRA-API calls from Python
#
# Prerequisites for using the module 'class_jira-py':
#
#
#    sudo apt-get install python3-pip
#    sudo pip3 install glom
#
# -----------------------------
#
# Default logfile is '/tmp/jira_api.log'
#
# File History:
# 2020-05-17 C Boberg - Initial stab. Heja Norge!
#



#-----------------------------
# Import libraries
#-----------------------------
import logging
from logging.handlers import RotatingFileHandler

import configparser, os, sys, json, requests
import urllib.parse

from argparse import ArgumentParser



#-----------------------------
# Include-path
#-----------------------------
argv0 = sys.argv[0]
if (argv0[0] == "." ):
    relPathAndScript = argv0[1:] #Remove initial '.'
    fullPathAndFilename = os.getcwd() + relPathAndScript
else:
    fullPathAndFilename = argv0

path, filename = os.path.split( fullPathAndFilename )

# The following allows us to have config and classes in a separate directory
sys.path.append(path + '/include/')




#-----------------------------
# Parameters in Win-INI format
#-----------------------------

config = configparser.ConfigParser()
config.read(os.path.join(path, 'jira.conf'))



#-----------------------------
# Classes
#-----------------------------

import class_jira as cj
#import class_jira_old as cj




#-----------------------------
# Functions
#-----------------------------






#==============================
# MAIN
#==============================



#----------
# Arguments
#----------

# Verify arguments
aparser = ArgumentParser(description='This script sends Icinga-events to MS Teams chat.')

aparser.add_argument('-x', '--example',
                    help="Show example of typical usage.", action="store_true")
aparser.add_argument('-q', '--query',
                    help="Run a JQL-qurey and return a JSON-string."),
aparser.add_argument('-n', '--name',
                    help="Enter name of project and get the project-KEY. Needed in JQL-queries"),
aparser.add_argument('-l', '--loglevel', choices=['DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL'],
                     help= "Set the log-level. Default is INFO")
aparser.add_argument('-d', '--logdir',
                    help="Set log directory. Default is '/tmp'")
aparser.add_argument('-f', '--logfile',
                    help="Set log filename. Default is 'jira_api.log'")
args = aparser.parse_args()


#----------
# Logging
#----------

logLevel = "INFO"
if (args.loglevel):
    logLevel = args.loglevel

logFolder = "/tmp"
if (args.logdir):
    logFolder = args.logdir

logFile = 'jira_api.log'
if (args.logfile):
    logFile = args.logfile

logging.basicConfig(
    level=logLevel,
    format='%(asctime)s %(levelname)-5.5s %(name)s %(message)s',
    handlers=[
        #logging.FileHandler(logFile),
        logging.handlers.RotatingFileHandler(
            os.path.join(logFolder, logFile), 
            maxBytes=(1048576*5), 
            backupCount=10 ),
        logging.StreamHandler()
    ]
)



#----------
# Start
#----------
logging.debug(' ')
logging.debug('------ Hello! ------')

jira = cj.Jira(config)


if (args.example):
    # Show examples
    # Print demo-commands and quit
    print(' ')
    print('Command usage examples: ')
    print(' ')
    print('  ',sys.argv[0], '-q \'project=10201 AND type="MFT"\'')
    print(' ')
    print('  ',sys.argv[0], '-n "INFRA"')
    print(' ')
    print(' ')
    exit()
elif (args.query):

    # Run a JQL Query
    print( jira.jql(args.query) )
    exit()
else:
    ### Custom code



    #print( jira.getIssueTypesRaw("INFRA", False) )
    #print( jira.getIssueTypesRaw("INFRA") )
    #print( jira.getIssueTypes("INFRA") )

    #print( jira.scanProjectsRaw() )
    #print( jira.scanProjects() )

    #print( jira.getIssuesRaw("10201", "MFT", False) )
    #print( jira.getIssuesRaw("10201", "MFT") )
    #print( jira.getIssues("10201", "MFT") )

    print( jira.getProjectKey("INFRA") )


#   "Key": "INFRA",
#   "Id": "10201"




