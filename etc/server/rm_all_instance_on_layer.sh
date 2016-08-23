#!/usr/bin/env bash
if [ ! `which jq` ]; then
    echo 'pls install jq as "brew install jq"'
    exit
fi
while getopts l: OPT
do
  case ${OPT} in
    l) LAYER=${OPTARG} ;;
    *) exit 1 ;;
  esac
done

DESC_LAYER=`aws opsworks --region us-east-1 describe-instances --layer-id ${LAYER} | jq '.Instances[].InstanceId'`
while read line
do
echo ${line}
#    echo "test ${line} test"
#    aws opsworks --region us-east-1 delete-instance --instance-id ${line}
done <<END
$DESC_LAYER
END
