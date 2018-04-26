#!/bin/bash
#Reader for MS Windows 3.1 Ini-files (copied from https://stackoverflow.com/questions/6318809/how-do-i-grab-an-ini-value-within-a-shell-script)
#Author: Valentin Heinitz
#Usage: inireader.sh

# e.g.: inireader.sh win.ini ERRORS DISABLE
# would return value "no" from the section of win.ini
# [ERRORS]
# DISABLE=no
INIFILE=$1
SECTION=$2
ITEM=$3
cat $INIFILE | sed -n '/^\['$SECTION'\]/,/^\[.*\]/p' | grep "^[[:space:]]*$ITEM[[:space:]]*=" | sed s/.*=[[:space:]]*//
