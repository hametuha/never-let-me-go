#!/usr/bin/env bash

set -e

composer install --no-dev
npm install
npm start
rm -rf node_modules
