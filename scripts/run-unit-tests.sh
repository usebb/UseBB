#!/bin/sh

bootstrap=includes/init.php

if [ $# -ne 1 ]; then
	do="."
else
	do=$1
fi

phpunit --verbose --bootstrap $bootstrap $do
