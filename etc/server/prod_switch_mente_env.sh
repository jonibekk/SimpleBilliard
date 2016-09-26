#!/bin/bash

SCRIPT_DIR=$(cd $(dirname $0);pwd)
aws ec2 start-instances --instance-ids i-4d1c1abe
aws ec2 wait instance-running --instance-ids i-4d1c1abe; aws route53 change-resource-record-sets --hosted-zone-id Z11UORJ1AGZ33F --change-batch file://${SCRIPT_DIR}/json/prod_to_mente.json
