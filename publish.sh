#!/bin/sh

# TODO check if installable (test installation) and if JED checker is fine
# TODO impl question in publish script with possibility to abort

if [ $# -ne 1 ]; then
	echo "usage: $0 VERSION";
	exit;
fi

# update changelog
vim README.md

# update version # TODO update creation date
sed -i "s/<version>.*<\/version>/<version>$1<\/version>/g" src/update.xml

# commit changes and create git tag
git add .
git commit
git push
git tag -a $1 -m "published $1"
git push origin $1

# build package
phing package

# copy to static.marcobeierer.com
cp packages/com_update-latest.zip ~/www/websites/static.marcobeierer.com/joomla-extensions/update/com_update-latest.zip
cp packages/com_update-latest.zip ~/www/websites/static.marcobeierer.com/joomla-extensions/update/com_update-${1}.zip

# update update.xml
vim /home/marco/www/websites/static.marcobeierer.com/joomla-extensions/update/update.xml

# push changes to static.marcobeierer.com
push-static.marcobeierer.com.sh

# TODO update JED
