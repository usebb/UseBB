#!/bin/sh

if [ $# -ne 1 ];
then
	echo "Usage: $0 <release>"
	exit 1
fi

release=$1

if [ -n "$(grep "installer_run" config.php)" ];
then
	echo "installer_run in config.php."
	exit 1
fi

if [ -z "$(grep -e "USEBB_VERSION.*$release" sources/common.php)" ];
then
	echo "Wrong USEBB_VERSION value."
	exit 1
fi

if [ -z "$(grep -e "USEBB_IS_PROD_ENV.*TRUE" sources/common.php)" ];
then
	echo "USEBB_IS_PROD_ENV not TRUE."
	exit 1
fi

if [ -n "$(git status -s)" ];
then
	echo "There are modified files."
	exit 1
fi

git tag "v$release"

exit 0
