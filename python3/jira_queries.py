#!/usr/bin/env python3
# This Python3 file uses the following encoding: utf-8
#
# -----------------------------


# -----------------------------
#
#
#

#
# Default logfile is '/tmp/jira_api.log'
#
#
#
# File History:
# 2020-05-17 C Boberg - Initial stab. Heja Norge!




#-----------------------------
# Import libraries
#-----------------------------
import logging
from logging.handlers import RotatingFileHandler

import configparser, os, sys, json, requests
import urllib.parse

from argparse import ArgumentParser
#from datetime import datetime



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



#-----------------------------
# Parameters in Win-INI format
#-----------------------------

config = configparser.ConfigParser()
config.read(os.path.join(path, 'jira.conf'))



#-----------------------------
# Classes
#-----------------------------

import class_jira as cj




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
                    help="Show example of typical command.",
aparser.add_argument('-s', '--server',
                    help="xxx")
aparser.add_argument('-u', '--user',
                    help="xxx")
aparser.add_argument('-p', '--password',
                    help="xxx")

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

if (args.example):
    ### Show examples
    # Print demo-commands and quit
    print(' ')
    print('Command usage examples: ')
    print(' ')
    print(sys.argv[0], '-u xxx -p xxx sdjkjkervr')
    print(' ')
    print(sys.argv[0], '-u xxx -p xxx sdjkjkervr')
    print(' ')
    print(' ')
    exit()
else:
    ### Standard




    url= cfg.jiraServer + '/rest/plugins/applications/1.0/installed/jira-software'



    jira = cj.Jira(config)

    print( jira.getIssueTypes("INFRA") )






# Normal exit
sys.exit(os.EX_OK) # code 0, all ok


