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

DESC_LAYER=$(aws opsworks --region us-east-1 describe-instances --layer-id ${LAYER})

len=$(echo $DESC_LAYER | jq '.Instances | length')
removed_count=0
for i in $( seq 0 $(($len - 1)) ); do
  InstanceId=$(echo $DESC_LAYER | jq .Instances[$i].InstanceId)
  Status=$(echo $DESC_LAYER | jq .Instances[$i].Status)
  if [ $Status = '"stopped"' ]; then
    $(aws opsworks delete-instance --region us-east-1 --instance-id ${InstanceId:1:36})
    echo "$InstanceId was deleted"
    removed_count=$((removed_count+1))
  fi

done

echo  "$removed_count/$len has been deleted!"
