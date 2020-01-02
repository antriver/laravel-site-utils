#!/bin/bash

set -e
set -x

# cd to the directory of this script
BASEDIR=$(dirname $0)
cd $BASEDIR

# storage directory itself to 777
# storage subdirectories to 777
# files in storage (including in subdirectories) to 666
chmod 777 storage
find storage -type d -exec chmod 777 {} +
find storage -type f -exec chmod 666 {} +

# files directly in storage inherit user and  group of directory
# files directly in storage get group rw
# files directly in storage get other rw
chmod ug+s storage
setfacl -d -m g::rw storage
setfacl -d -m o::rw storage

# files directly in {subdirectory} inherit user and group of directory
# files directly in {subdirectory} get group rw
# files directly in {subdirectory} get other rw
find storage -type d -exec chmod ug+s {} +
find storage -type d -exec setfacl -d -m g::rw {} +
find storage -type d -exec setfacl -R -d -m o::rw {} +

# cache folder itself to 777
# subdirectories of cache to 777
# files (including in subdirectories) to 666
chmod 777 bootstrap/cache
find bootstrap/cache -type d -exec chmod 777 {} +
find bootstrap/cache -type f -exec chmod 666 {} +

# files directly in cache inherit group of directory
# files directly in cache get group rw
# files directly in cache get other rw
chmod g+s bootstrap/cache
setfacl -d -m g::rw bootstrap/cache
setfacl -d -m o::rw bootstrap/cache
