# 負荷試験環境構築マニュアル
1. AWS management consoleにログイン。
1. リージョンを`アジア・パシフィック(東京)`に変更。

## ロードバランサ作成
1. メニューから`EC2`->`ロードバランサ`
1. ステップ１   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807370/feb0ef2e-03c5-11e5-9ef2-6932183e7b15.png)   
1. ステップ２   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807402/392b130a-03c6-11e5-8c47-649c743cb7f7.png)   
1. ステップ３   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807411/586575e4-03c6-11e5-8c90-08e5312ca1d4.png)   
1. ステップ４   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807445/b065faf2-03c6-11e5-92db-db6a36a2e187.png)   
1. ステップ５   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807467/d9dfacfc-03c6-11e5-9d57-42ef6e98e25d.png)   
1. ステップ６   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807477/fae094f2-03c6-11e5-8f8b-0166716eaade.png)   
1. ステップ７   
  `作成`を押す。  
1. ロードバランサ名を控える。
1. 以上。

## Route53設定
1. Route53のページに移動。
1.    
![route_53_management_console](https://cloud.githubusercontent.com/assets/3040037/7807540/5e98df72-03c7-11e5-88f5-e8b5a6c346c4.png)   
1.   
![route_53_management_console](https://cloud.githubusercontent.com/assets/3040037/7807550/842cb8f8-03c7-11e5-9191-5bf42a6d9006.png)   
1. `Create Record Set`ボタンを押す。
1.   
![route_53_management_console](https://cloud.githubusercontent.com/assets/3040037/7807608/ed0097c8-03c7-11e5-81e6-ac0b67d99fa8.png)

## RDS作成
1. スナップショットから復元(rds01-stress-test01)
1.    
![rds_ _aws_console](https://cloud.githubusercontent.com/assets/3040037/7807848/bb721eb4-03c9-11e5-8610-624b1c246035.png)   
1. インスタンス作成完了まで待つ   
1.    
![fullscreen_5_26_15__5_13_pm](https://cloud.githubusercontent.com/assets/3040037/7807999/a7dd0552-03ca-11e5-81d6-ce4cbe068ad6.png)   
1.   
![rds_ _aws_console](https://cloud.githubusercontent.com/assets/3040037/7808081/7b6c5fda-03cb-11e5-99b7-0283fa97678e.png)   
1. `DBインスタンスの変更`を押す。
1. 再起動。   
![fullscreen_5_26_15__5_30_pm](https://cloud.githubusercontent.com/assets/3040037/7808268/f4391010-03cc-11e5-99b3-10d0305a106b.png)   
1. エンドポイントを控える。   
![rds_ _aws_console](https://cloud.githubusercontent.com/assets/3040037/7808435/2937332c-03ce-11e5-9763-2a1812724833.png)   

1. 以上。

## S3バケット作成
1. S3ページに移動。
1. `バケットを作成`ボタンを押す。
1. 以下の２つのバケットを作成。
 - `goalous-stress-test01-logs`
 - `goalous-stress-test01-assets`
1. バケット名を控えておく。

## ElastiCacheインスタンス作成
1. ElastiCacheページに移動。
1. `Launch Cache Cluster`ボタンを押す。
1. Redisを選択。   
![elasticache_management_console](https://cloud.githubusercontent.com/assets/3040037/7808593/62db5ea4-03cf-11e5-8c7a-ac1548515805.png)   
1.   
![elasticache_management_console](https://cloud.githubusercontent.com/assets/3040037/7808619/8da22be0-03cf-11e5-9f95-418723151559.png)   
1.   
![elasticache_management_console](https://cloud.githubusercontent.com/assets/3040037/7808760/76135f20-03d0-11e5-942c-1a6f514a318b.png)   
1. Endpointを控える。   
![elasticache_management_console](https://cloud.githubusercontent.com/assets/3040037/7808903/778424ec-03d1-11e5-9bd0-8cac4acec7ed.png)   
![elasticache_management_console](https://cloud.githubusercontent.com/assets/3040037/7808920/8e756c74-03d1-11e5-9df0-d6551aba23fd.png)   


## Opsworks設定
### 事前確認(名前は今回の場合です。用途に応じて変わる可能性があるのでちゃんと確認しときましょう。)
- ElastiCache
  `gl-stress-test-cache.xnlqxt.0001.apne1.cache.amazonaws.com`
- S3
  `goalous-stress-test01-assets`
  `goalous-stress-test01-logs`
- RDS
  `gls-rds01-stress-test01.cjoncmeaeph3.ap-northeast-1.rds.amazonaws.com`
- ELB
  `gls-stress-test01`

### 作業
1. Opsworksのページに移動。
1. `Goalous Production`を`clone`する。   
![fullscreen_5_26_15__4_28_pm](https://cloud.githubusercontent.com/assets/3040037/7807201/5ea62018-03c4-11e5-8166-d26c2917fd01.png)   
1. 名前を変更して`Advanced`を押す。   
![clone_stack_01_goalous_production_copy_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7809438/0b705ce0-03d5-11e5-9cd2-0567d6dd4e20.png)   
1. Custom Json を変更する。  
![clone_stack_01_goalous_production_copy_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7821044/16cf7efc-0427-11e5-9d32-309ae3b35031.png)  
![clone_stack_01_goalous_production_copy_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7821048/1b600e50-0427-11e5-9695-7dbe56d6f4db.png)  
1. `Clone Stack`ボタンを押す。  
![clone_stack_01_goalous_production_copy_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7809668/f0f7f740-03d6-11e5-8884-5aebff652656.png)   
1. ELBを紐付ける。   
![goalous_app_-_layers_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7809735/5b34d790-03d7-11e5-8699-4ea5c0fad6c9.png)   
1.   
![edit_goalous_app_-_layers_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810081/2162c7f4-03da-11e5-83a2-f4d6fe83af32.png)   
1. レイヤ設定   
![goalous_app_-_layers_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810144/8d61577c-03da-11e5-866d-c696758b24f0.png)   
![edit_goalous_app_-_layers_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810174/b94d46de-03da-11e5-9ece-ab56db3d5050.png)   
1. インスタンスを追加。   
![instances_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810101/43d2def0-03da-11e5-9949-62ba530e6049.png)   
![instances_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810195/efd6579a-03da-11e5-8416-f3f99be9f978.png)   
1. インスタンス起動(15分くらいかかります)   
![instances_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810206/0ba118a2-03db-11e5-8c14-229292dd9380.png)   
1. 起動したら、以下にアクセスしてひと通り動けばOK   
   `https://stress-test01.goalous.com`
1. 以上！！



