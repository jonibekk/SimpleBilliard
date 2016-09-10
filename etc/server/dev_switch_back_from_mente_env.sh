#!/bin/bash

SCRIPT_DIR=$(cd $(dirname $0);pwd)
CHANGE_ID=$(aws route53 change-resource-record-sets --hosted-zone-id Z11UORJ1AGZ33F --change-batch file://${SCRIPT_DIR}/mente_to_dev.json --output text --query ChangeInfo.Id)
aws route53 wait resource-record-sets-changed --id "${CHANGE_ID}"; aws ec2 stop-instances --instance-ids i-4d1c1abe

