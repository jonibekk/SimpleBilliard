#!/bin/sh
SCRIPT_DIR=$(cd $(dirname $0); pwd)
echo $SCRIPT_DIR
cd $SCRIPT_DIR
composer install
php script/sync.php
