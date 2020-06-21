#!/bin/sh
# Replace wb.dev.watfordconsulting.com and wantsumbrewery.co.uk in SQL files with localhost

# This script is expected to be sourced, therefore test path against arg $1 rather than $0.
SCRIPT=$(readlink -f "$1")
# If nothing found in $1 then fall back to $0.
if [ -z "$SCRIPT" ]; then
SCRIPT=$(readlink -f "$0")
fi
SCRIPTPATH=$(dirname "$SCRIPT")

echo "\$0: $0"
echo "\$1: $1"
echo "PWD: $PWD"
echo "SCRIPT: $SCRIPT"
echo "SCRIPTPATH: $SCRIPTPATH"

for x in $SCRIPTPATH/*.sql; do
    echo "Processing SQL file: $x"
	sed -i 's#https://wb.dev.watfordconsulting.com#https://localhost#g' $x
	sed -i 's#https://wantsumbrewery.co.uk#https://localhost#g' $x
done