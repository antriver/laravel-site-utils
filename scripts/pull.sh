#!/bin/bash

set -e
set -x

cd "$(dirname "$0")" # cd to directory containing this script

git reset --hard HEAD
git pull
./post-pull.sh
