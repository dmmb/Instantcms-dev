#!/bin/bash

VERSION=$1;
TO_DIR=$2;
TO_DIR_DEFAULT='/var/www/icms_build';

SRC_DIR='/var/www/icms';
DISTR_NAME='instantCMS_'`date +%Y%m%d`'_v'$VERSION'.zip';

if [[ $VERSION = "" ]]
then
	echo "Usage:   	build.sh VERSION [TARGET_PATH]";
	echo "Example: 	build.sh 1.2.1 /var/www/icms_build";
	echo "";
	exit 1;
fi

if [[ $TO_DIR = "" ]]
then
	TO_DIR=$TO_DIR_DEFAULT;
fi

echo "";
echo "Building version:  $VERSION";
echo "Building to:       $TO_DIR";
echo "";

echo "Copying files...";
cp -R $SRC_DIR/. $TO_DIR/. && chmod -R 777 $TO_DIR

echo "Deleting SVN folders...";
find $TO_DIR -type d -iname '.svn' -print0 | xargs -0 rm -rf

echo "Deleting .tmp files...";
find $TO_DIR -type f -iname '*.tmp' -print0 | xargs -0 rm -f

echo "Deleting configuration file...";
rm -f $TO_DIR/includes/config.inc.php

echo "Deleting cache...";
rm -f $TO_DIR/cache/*;

echo "Deleting backups...";
rm -f $TO_DIR/backups/*.sql;
rm -f $TO_DIR/backups/*.sql~;

echo "Deleting RSS cache...";
rm -f $TO_DIR/includes/rss/cache/*;

echo "Deleting files in root...";
find $TO_DIR -maxdepth 1 -mindepth 1 -type f -not -name cron.php -not -name .htaccess -not -name url_rewrite.php -not -name readme.txt -not -name version_log.txt -not -name license.txt -not -name license.rus.win.txt -not -name license.rus.utf.txt -not -name index.php -not -name $DISTR_NAME -print0 | xargs -0 rm -f;
echo "";

echo "Building archive...";
cd $TO_DIR;
zip -r -q $TO_DIR/distr.zip * .htaccess;

echo "Renaming archive to $DISTR_NAME...";
mv $TO_DIR/distr.zip $TO_DIR/$DISTR_NAME;

echo "Cleaning target directory...";
find $TO_DIR -maxdepth 1 -mindepth 1 -type d -print0 | xargs -0 rm -rf;
find $TO_DIR -maxdepth 1 -mindepth 1 -type f -not -name $DISTR_NAME -print0 | xargs -0 rm -f;

echo "";
echo "Finished.";
echo "";

nautilus $TO_DIR &> /dev/null &
