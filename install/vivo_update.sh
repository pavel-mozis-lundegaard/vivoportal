#!/bin/bash

#BRANCH="develop"

VIVO_DIR="v2"

if [ ! -z $1 ] ; then
VIVO_DIR=$1
fi

if [ ! -d "$VIVO_DIR" ] ; then
echo "Folder '$VIVO_DIR' doesn't exists."
exit 1

fi

cd $VIVO_DIR

BRANCH=`git rev-parse --abbrev-ref HEAD`
git fetch
#git checkout $BRANCH
git reset --hard origin/$BRANCH
git checkout $BRANCH

#update dependencies
php composer.phar install

