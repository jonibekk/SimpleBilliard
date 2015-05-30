# Management Console

# Command line

- Install
  https://github.com/aws/aws-cli#installation

## Opsworks

### Deploy
#### stg
```
aws opsworks --region us-east-1 create-deployment --stack-id 07838a54-a9ae-4df1-b7dc-747b6ace1c66 --app-id d1b2b17e-2be3-4a94-90dc-03b1767bd786 --command "{\"Name\":\"deploy from cli\"}"
```
#### www 
```
aws opsworks --region us-east-1 create-deployment --stack-id e09a695a-0631-4c60-be82-cf498ea49317 --app-id 77c4fc53-40c3-4a73-b532-18d5ef1beff7 --command "{\"Name\":\"deploy from cli\"}"
```
#### hotfix
```
aws opsworks --region us-east-1 create-deployment --stack-id 8d158e51-2c9b-4cf4-876b-5f11ab8280e9 --app-id feaeb538-35a5-4a1c-bab7-01a70c666987 --command "{\"Name\":\"deploy from cli\"}"
```
#### stress test
```
aws opsworks --region us-east-1 create-deployment --stack-id 086f0871-7c09-4d3e-8f81-4e64174793fe --app-id a62504ee-0dcc-4dab-a51b-6583bd9234ff --command "{\"Name\":\"deploy from cli\"}"
```

### Add instance

#### stg
```
aws opsworks --region us-east-1 create-instance --stack-id 07838a54-a9ae-4df1-b7dc-747b6ace1c66 --layer-ids 2d6c1798-497e-4b41-8855-037254854f05 --instance-type c3.large --os "Ubuntu 12.04 LTS"
```
#### www
```
aws opsworks --region us-east-1 create-instance --stack-id e09a695a-0631-4c60-be82-cf498ea49317 --layer-ids dc17e1b0-296a-4836-bc26-e1626a39e59a --instance-type c3.large --os "Ubuntu 12.04 LTS"
```
#### hotfix

```
aws opsworks --region us-east-1 create-instance --stack-id 8d158e51-2c9b-4cf4-876b-5f11ab8280e9 --layer-ids 85ba1a73-4700-4f10-8afb-beb53a2a09ef --instance-type c3.large --os "Ubuntu 12.04 LTS"
```

#### stress test

```
aws opsworks --region us-east-1 create-instance --stack-id 086f0871-7c09-4d3e-8f81-4e64174793fe --layer-ids ee21144f-4afb-4268-8b81-647d06f75168 --instance-type c3.large --os "Ubuntu 12.04 LTS"
```

### Start instance
```
aws opsworks start-instance --instance-id [id]
```

### Oters
refer to http://docs.aws.amazon.com/cli/latest/reference/opsworks/index.html#cli-aws-opsworks
