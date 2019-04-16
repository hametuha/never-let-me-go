#!/usr/bin/env bash

set -e

composer install --no-dev
npm install
npm start
echo 'Generate readme.'
curl -L https://raw.githubusercontent.com/fumikito/wp-readme/master/wp-readme.php | php
rm -rf node_modules
rm -rf .git
rm -rf tests
rm -rf .travis.yml
rm -rf .gitignore
rm -rf phpcs.ruleset.xml
rm -rf phpunit.xml.dist
rm -rf readme.md

if [ $TRAVIS_TAG ]; then
    echo $TRAVIS_TAG
fi

if [ $SVN_USER ]; then
    echo "SVN_USER exists."
fi

if [ $SVN_PASS ]; then
    echo "SVN_PASS exists."
fi
