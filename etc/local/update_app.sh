#!/bin/bash
echo "### Update Application. ###"

git submodule update --init --recursive
git fetch
git pull
vagrant provision

