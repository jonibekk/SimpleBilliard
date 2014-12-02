#!/bin/bash
echo "Update Application"

cd /vagrant;git submodule update --init --recursive
cd /vagrant;git fetch
cd /vagrant;sudo composer self-update
cd /vagrant;sudo composer update
cd /vagrant/app;./Console/cake remove_cache
cd /vagrant/app;./Console/cake migrations.migration run all
