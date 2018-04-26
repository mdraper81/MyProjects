#!/bin/bash

#Parse the command line.  We are looking for environment and file
CONFIG_FILENAME=""
NEW_VERSION=""
ENVIRONMENT=""
USAGE_PROMPT="USAGE: updateDB.sh -e <test|prod> -v <desired version number>"

if [ "$#" -ne 4 ]; then
    echo "${USAGE_PROMPT}"
    exit 1
fi

while [ "$1" != "" ]; do
    case $1 in
        -e|--env)
            if [ -n "$2" ]; then
                if [ "$2" == "test" ]; then
                    CONFIG_FILENAME="website/dbConfig.ini"
		    ENVIRONMENT="Test"
		    shift 2
		    continue
		elif [ "$2" == "prod" ]; then
                    CONFIG_FILENAME="website/dbConfig_PROD.ini"
		    ENVIRONMENT="Prod"
		    shift 2 
		    continue
                else
                    echo "ERROR: '--env' only supports test or prod"
		    exit 1
                fi
            else
                echo "ERROR: '--env' requires a non-empty option argument."
		exit 1
            fi
	    ;;
        -v|--version)
            if [ -n "$2" ]; then
                NEW_VERSION="$2"
                shift 2
		continue
            else
                echo "ERROR: '--version' requires a non-empty option argument."
		exit 1
            fi
	    ;;
	-h|--help)
            echo "${USAGE_PROMPT}"
	    ;;
    esac
    shift
done

if [ -z $NEW_VERSION ]; then
    echo "You must specify a --version to upgrade to"
    exit 1
fi

if [ -z $ENVIRONMENT ]; then
    echo "You must specify an --env to run against"
    exit 1
fi

if [ ! -f ${CONFIG_FILENAME} ]; then
    echo "Failed to find config file ${CONFIG_FILENAME}. Aborting."
    exit 1
fi

# Print details about what script we are running
echo "Running script to upgrade the '${ENVIRONMENT}' environment to version ${NEW_VERSION} using db config '${CONFIG_FILENAME}'"

# Extract username and dbname from config file
USERNAME=`./inireader.sh ${CONFIG_FILENAME} database username`
DBNAME=`./inireader.sh ${CONFIG_FILENAME} database dbname`
PASSWORD=`./inireader.sh ${CONFIG_FILENAME} database password`

# Grab current version number from database, if table does not exist assume version 0 and create table
VERSION_TABLE_COUNT=$(mysql $DBNAME -u $USERNAME -p$PASSWORD -se "SELECT COUNT(*) FROM INFORMATION_SCHEMA.tables WHERE table_schema = database() AND table_name = 'Versions'")
if [ $VERSION_TABLE_COUNT == 1 ]; then
    CURRENT_VERSION=$(mysql $DBNAME -u $USERNAME -p$PASSWORD -se "SELECT MAX(version) FROM Versions")
else
    CURRENT_VERSION=0

    # Create the versions table for future runs
    echo "Version table does not exist, creating it"
    mysql ${DBNAME} -u ${USERNAME} -p${PASSWORD} < mysql/CreateVersionsTable.sql 
fi

echo "Current version is ${CURRENT_VERSION} upgrading to ${NEW_VERSION}"
# Loop through all versions until we are at the desired version
while [ $CURRENT_VERSION -lt $NEW_VERSION ]
do
    # Increment the current_version first since we are going to run a script
    # to upgrade to this version number and it does not make sense to upgrade
    # to the version that we already have installed
    ((CURRENT_VERSION++))

    # Get the script name and run it if the file exists
    SCRIPT_NAME=$(printf mysql/%03d.sql $CURRENT_VERSION)
    if [ -f $SCRIPT_NAME ]; then
        echo "Running script ${SCRIPT_NAME}"
        mysql ${DBNAME} -u ${USERNAME} -p${PASSWORD} < ${SCRIPT_NAME}
	    
        # Update the Versions table to record this new version
        mysql ${DBNAME} -u ${USERNAME} -p${PASSWORD} -se "INSERT INTO Versions (version) VALUES (${CURRENT_VERSION})"
    else
        echo "Failed to find script ${SCRIPT_NAME}.  Aborting."
        exit 1
    fi
done

echo "Logging into ${DBNAME} with user ${USERNAME} and password ${PASSWORD} version = ${NEW_VERSION}"
