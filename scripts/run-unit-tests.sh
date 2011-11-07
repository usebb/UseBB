#!/bin/sh

if [ $# -ne 1 ]; then
	do="."
else
	do=$1
fi

phpunit -c includes/phpunit.xml $do
