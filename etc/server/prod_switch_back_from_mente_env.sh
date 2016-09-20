#!/bin/bash

SCRIPT_DIR=$(cd $(dirname $0);pwd)
aws route53 change-resource-record-sets --hosted-zone-id Z11UORJ1AGZ33F --change-batch file://${SCRIPT_DIR}/json/mente_to_prod.json
