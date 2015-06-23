#!/bin/bash

aws route53 change-resource-record-sets --hosted-zone-id Z11UORJ1AGZ33F --change-batch file://to_production.json