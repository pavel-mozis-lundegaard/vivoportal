#!/bin/bash

# Installation script for Vivo
# download vivo from git, composer and dependencies
# usage : vivo_install.sh [VivoFolderName]

URL="git://github.com/vivoportal/v2.git" 
BRANCH="develop"
VIVO_DIR="v2"

set -- `getopt u:b: $*`

while [ $1 ];
do

	case "$1" in

		-u) URL=$2;;
		-b) BRANCH=$2;;
		--) VIVO_DIR=$2;;

	esac
	shift

done

if [ -d "$VIVO_DIR" ] ; then

	echo "Folder '$VIVO_DIR' already exists"
	exit 1

fi

git clone $URL $VIVO_DIR
cd $VIVO_DIR
git checkout $BRANCH

#set enviroment
cp public/.htaccess.dist public/.htaccess
chmod -R 777 data


#download composer
curl -s https://getcomposer.org/installer | php


#download dependencies
echo "Resolving dependencies..."
php composer.phar install

echo "Vivo 2 installation complete."
echo "Please check configuration in public/.htaccess file."
