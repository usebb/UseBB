#!/bin/sh

if [ $# -ne 2 ];
then
	echo "Usage: $0 <old release> <new release>"
	exit 1
fi

oldrelease=$1
newrelease=$2

tmpdir=".tmp-$$"
outputdir="build"

mkdir $tmpdir
mkdir $tmpdir/UseBB
mkdir $tmpdir/UseBB-old

git archive "v$newrelease" | tar -x -C $tmpdir/UseBB
git archive "v$oldrelease" | tar -x -C $tmpdir/UseBB-old

cd $tmpdir

echo "docs
install
scripts
Changelog.txt
config.php
AUTHORS
COPYING
README" > .diff-ignore

mkdir ../$outputdir

difffile="../$outputdir/usebb-$newrelease.diff"
diff -X .diff-ignore -I 'Copyright (C) 2003-.* UseBB' -I '$Header[$:]' -I '$Id[$:]' -I '* @version' -I '* @copyright' \
	-wBdr -U5 UseBB-old UseBB > $difffile

changedfiles=$(grep '^+++ ' $difffile | awk '{print $2}')

cd UseBB/

mv config.php config.php-dist
chmod a+rw config.php-dist

cd ..

tar --gzip -cf ../$outputdir/usebb-$newrelease.tar.gz UseBB/
tar --bzip2 -cf ../$outputdir/usebb-$newrelease.tar.bz2 UseBB/
zip -qr ../$outputdir/usebb-$newrelease.zip UseBB/

addfiles="UseBB/COPYING UseBB/AUTHORS UseBB/README UseBB/docs"

tar --gzip -cf ../$outputdir/usebb-$newrelease-changedfiles.tar.gz \
	$addfiles $changedfiles
tar --bzip2 -cf ../$outputdir/usebb-$newrelease-changedfiles.tar.bz2 \
	$addfiles $changedfiles
zip -qr ../$outputdir/usebb-$newrelease-changedfiles.zip \
	$addfiles $changedfiles

cd ..
rm -rf $tmpdir

exit 0
