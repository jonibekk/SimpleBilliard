# 構築・運用手順書

<hr id="env4foreigner">
## 海外開発者向け環境構築手順書

**もう使っておりません。**

1. `vagrant plugin install vagrant-proxyconf`でvagrantプラグイン(proxy設定を可能にするやつ)をインストール。
1. `vagrant up ec2`でec2インスタンス起動およびchefの適用(30min)
1. AWS Management Consoleにログインし以下を実行
 - EIPを紐付ける
 - Instance Nameを変更
1. `vagrant ssh ec2`でssh接続
1. `vncserver :1`でvncserverを起動  
  パスワードを設定
1. vncserver接続テスト
1. firefox, chromeで以下のサイトにそのユーザのIDでログイン
 - github
 - waffle
 - coveralls
 - travis
 - slack
1. `ssh-keygen`でsshキーを発行
1. githubにsshキーを登録
1. `ssh -T git@github.com`でssh接続確認
1. phpstormのライセンス紐付け

以上。

<hr id="proxy4foreigner">
**もう使ってない**
## 海外開発拠点用プロキシサーバ海外開発運用手順書
### プロキシサーバへの接続
1. sshキーを追加  
  以下ファイルをダウンロード  
  http://bit.ly/1XzyyqD  
  以下の場所にファイルをコピー  
  `~/.ssh/`  

1. sshでproxyサーバーに接続  
  `sh -i ~/.ssh/isao-gls-singapore.pem ubuntu@52.74.224.129`  
  ※もし接続できない場合は、接続IPアドレスが許可されていない可能性があるので、管理者に自分の環境のパブリックIPアドレスを連絡する。

### 許可するドメインを追加する手順
1. ホワイトリストを開く  
  `sudo vim /etc/squid3/whitelist`
1. 末尾に許可するドメインを追加し、ホワイトリストを保存(アクセスログ等を確認して拒否されたドメインを特定する)
1. プロキシサーバのリロード  
  `sudo service squid3 reload`
1. 以上

### アクセスログ確認手順
1. tailで以下ファイルを開く  
  `sudo tail -1000f /var/log/squid3/access.log`  
  ※プロキシサーバに拒否されたドメインを追加する場合は、`TCP_DENIED`のドメインを探す。  
  e.g.  
  `1441584136.154      2 10.0.0.194 TCP_DENIED/403 3467 CONNECT mtalk.google.com:5228 - HIER_NONE/- text/html`  
  この場合は、`mtalk.google.com`へのアクセスが拒否されているという事。

<hr id="aws-operation">

## AWSオペレーション

### Command line

- Install
  https://github.com/aws/aws-cli#installation
- Keys
  http://bit.ly/29CJqBf
  ※もしアクセスできない場合は、 @koheikikuchi or @bigplants まで。

### Opsworks

### Deploy
#### stg
```
aws opsworks --region us-east-1 create-deployment --stack-id 07838a54-a9ae-4df1-b7dc-747b6ace1c66 --app-id d1b2b17e-2be3-4a94-90dc-03b1767bd786 --command "{\"Name\":\"deploy\"}"
```
#### release
```
aws opsworks --region us-east-1 create-deployment --stack-id f7b25c63-458c-497c-aeb1-267d7506defc --app-id 786fd3c0-1b5f-49d9-b707-27dba2909c84 --command "{\"Name\":\"deploy\"}"
```
#### www
```
aws opsworks --region us-east-1 create-deployment --stack-id e09a695a-0631-4c60-be82-cf498ea49317 --app-id 77c4fc53-40c3-4a73-b532-18d5ef1beff7 --command "{\"Name\":\"deploy\"}"
```
#### hotfix
```
aws opsworks --region us-east-1 create-deployment --stack-id 8d158e51-2c9b-4cf4-876b-5f11ab8280e9 --app-id feaeb538-35a5-4a1c-bab7-01a70c666987 --command "{\"Name\":\"deploy\"}"
```
#### stress test
```
aws opsworks --region us-east-1 create-deployment --stack-id 086f0871-7c09-4d3e-8f81-4e64174793fe --app-id a62504ee-0dcc-4dab-a51b-6583bd9234ff --command "{\"Name\":\"deploy\"}"
```

### Add instance

#### stg
```
aws opsworks --region us-east-1 create-instance --stack-id 07838a54-a9ae-4df1-b7dc-747b6ace1c66 --layer-ids 2d6c1798-497e-4b41-8855-037254854f05 --instance-type c3.large --os "Ubuntu 12.04 LTS"
```
#### release
```
aws opsworks --region us-east-1 create-instance --stack-id f7b25c63-458c-497c-aeb1-267d7506defc --layer-ids 1737799d-6ae5-42d4-8636-3f825b211d70 --instance-type c3.large --os "Ubuntu 12.04 LTS"
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


<hr id="maintenance-mode">
## Maintenance mode
### Install AWS CLI
- Install
  https://github.com/aws/aws-cli#installation
- Required Keys
  http://bit.ly/29CJqBf  
  ※もしアクセスできない場合は、 @koheikikuchi or @bigplants まで。

### Switch to maintenance env from ISAO env
```
sh etc/server/isao_switch_mente_env.sh
```

### Switch back ISAO env from maintenance env
```
sh etc/server/isao_switch_back_from_mente_env.sh
```

### Switch to maintenance env from production env
```
sh etc/server/prod_switch_mente_env.sh
```

### Switch back production env from maintenance env
```
sh etc/server/prod_switch_back_from_mente_env.sh
```

### Remove all instances on Layer of OpsWorks
```
sh etc/server/rm_all_instance_on_layer.sh -l [layer-id]
```
 

<hr id="db-migration">
## DB migration

```php
vagrant@precise64:/vagrant/app$ ./Console/cake migrations.migration generate -f
/vagrant/app/Vendor/cakephp/cakephp/libCake Migration Shell
---------------------------------------------------------------
Do you want compare the schema.php file to the database? (y/n)
[y] >
---------------------------------------------------------------
Comparing schema.php to the database...
Do you want to preview the file before generation? (y/n)
[y] >
<?php
class PreviewMigration extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
        public $description = 'Preview of migration';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
        public $migration = array(
                'up' => array(
                        'create_table' => array(
                                'evaluate_scores' => array(
                                        'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                                        'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                                        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価スコア名', 'charset' => 'utf8'),
                                        'indexes' => array(
                                                'PRIMARY' => array('column' => 'id', 'unique' => 1),
                                        ),
                                        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                                ),
                                'evaluate_terms' => array(
                                        'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                                        'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                                        'indexes' => array(
                                                'PRIMARY' => array('column' => 'id', 'unique' => 1),
                                                'team_id' => array('column' => 'team_id', 'unique' => 0),
                                        ),
                                        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                                ),
                        ),
                        'create_field' => array(
                                'evaluators' => array(
                                        'evaluate_term_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '評価対象期間ID(belongsToでEvaluateTermモデ  ルに関連)', 'after' => 'team_id'),
                                        'indexes' => array(
                                                'evaluate_term_id' => array('column' => 'evaluate_term_id', 'unique' => 0),
                                        ),
                                ),
                        ),
                        'alter_field' => array(
                                'evaluators' => array(
                                        'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
                                        'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
                                ),
                        ),
                ),
                'down' => array(
                        'drop_table' => array(
                                'evaluate_scores', 'evaluate_terms', 'evaluation_settings', 'evaluations'
                        ),
                        'drop_field' => array(
                                'evaluators' => array('evaluate_term_id', 'indexes' => array('evaluate_term_id')),
                        ),
                        'alter_field' => array(
                                'evaluators' => array(
                                        'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
                                        'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
                                ),
                        ),
                ),
        );

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
        public function before($direction) {
                return true;
        }

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
        public function after($direction) {
                return true;
        }
}

Please enter the descriptive name of the migration to generate:
add_evaluation_relation_tables_0310
Generating Migration...

Done.
Do you want update the schema.php file? (y/n)
[y] >

Welcome to CakePHP v2.5.8 Console
---------------------------------------------------------------
App : app
Path: /vagrant/app/
---------------------------------------------------------------
Cake Schema Shell
---------------------------------------------------------------
Generating Schema...
Schema file exists.
 [O]verwrite
 [S]napshot
 [Q]uit
Would you like to do? (o/s/q)
[s] > o
Schema file: schema.php generated
vagrant@precise64:/vagrant/app$
```

<hr id="github-waffle">

## 開発(チームメンバー)
### 通常時
1. 作業を開始する前に`ToDo`から`In Progress`にカードを移動する。
1. `develop`ブランチからブランチを作成し、チェックアウト。
1. コーディング
1. git add .
1. git commit
1. git push
1. PR発行(develop <- 作業したブランチ)
1. 作業が全て終わるまで、コーディング〜`git push`。
1. メンバーにレビュー依頼(Issueのコメントでmentionを付ける)。
1. カードを`Review`に移動。

### release(リリース前準備)
1. 作業を開始する前に`ToDo`から`In Progress`にカードを移動する。
1. `master`ブランチを更新。
1. `master`ブランチから`release`ブランチを作成する。
1. `release`ブランチからブランチを作成し、チェックアウト。
1. コーディング
1. git add .
1. git commit
1. git push
1. PR発行(`release`<- 作業したブランチ)
1. 作業が全て終わるまで、コーディング〜`git push`。
1. メンバーにレビュー依頼(Issueのコメントでmentionを付ける)。
1. カードを`Review`に移動。

### hotfix(緊急時)
1. 作業を開始する前に`ToDo`から`In Progress`にカードを移動する。
1. `master`ブランチを更新。
1. `master`ブランチから`hotfix`ブランチを作成する。
1. `hotfix`ブランチからブランチを作成し、チェックアウト。
1. コーディング
1. git add .
1. git commit
1. git push
1. PR発行(`hotfix`<- 作業したブランチ)
1. 作業が全て終わるまで、コーディング〜`git push`。
1. メンバーにレビュー依頼(Issueのコメントでmentionを付ける)。
1. カードを`Review`に移動。

## Deploy(プロダクトオーナー)
実行前に、`travis`, `coveralls`のエラーが無い事を確認する。

### ステージング
1. `Ready`にあるカードのPRを確認の上、PRマージ(`develop` <- `xxx`)。(約6分〜10分でdeploy完了)
  もし、複数PRが存在するIssueの場合は、マージ後に`In Progress`に戻す。
1. ステージング環境で動作確認。
1. IssueをCloseする。
1. 以上。

### 本番
1. 本番deploy用のPR発行(`master` <- `release`)
1. deployされる内容を確認の上、PRマージ。
1. 本番環境で動作確認。
1. 以上。

### リリース
1. `Ready`にあるカードのPRを確認の上、PRマージ(`release` <- `release-xxx`)。(約6分〜10分でdeploy完了)
  もし、複数PRが存在する、もしくは継続のIssueの場合は、マージ後に`In Progress`に戻す。
1. `release`環境で動作確認。
  もし、複数PRが存在する、もしくは継続のIssueの場合は、以下手順は不要。
1. 本番deploy用のPR発行(`master` <- `release`)
1. deployされる内容を確認の上、PRマージ。
1. 本番環境で動作確認。
1. `develop`に`release`をマージ。
1. `release`ブランチを削除。
1. 以上。


### hotfix
1. `Ready`にあるカードのPRを確認の上、PRマージ(`hotfix` <- `hotfix-xxx`)。(約6分〜10分でdeploy完了)
  もし、複数PRが存在する、もしくは継続のIssueの場合は、マージ後に`In Progress`に戻す。
1. `hotfix`環境で動作確認。
  もし、複数PRが存在する、もしくは継続のIssueの場合は、以下手順は不要。
1. 本番deploy用のPR発行(`master` <- `hotfix`)
1. deployされる内容を確認の上、PRマージ。
1. 本番環境で動作確認。
1. `develop`に`hotfix`をマージ。
1. `hotfix`ブランチを削除。
1. 以上。

<hr id="cs-operation">
# CSオペレーション
## 概要
- CLIによって処理を行います。

## 本番環境にssh接続
- 以下で接続
```shell
ssh -i ~/.ssh/isao-goalous-opsworks.pem ubuntu@52.68.180.173
```
- sshキーが存在しない場合
  1. http://bit.ly/29ytWhO から`isao-goalous-opsworks.pem`をダウンロード。
  1. `~/.ssh/`にコピー。
  1. `chmod 600 ~/.ssh/isao-goalous-opsworks.pem`で権限を変更。

## ユーザ退会処理
以下を実行。
```shell
ubuntu@goalous2-app1:/srv/www/cake/current/app$ sudo Console/cake cs_operation user_withdrawal -u [user_id]
```

---

次のドキュメントへ進んでください。  

**リンク貼る予定です**

トップへ戻りますか？  
[GitHub - Goalous](https://github.com/IsaoCorp/goalous2)

----

**他の情報をお探しですか？**

- [基本ポリシー](./general.md)
- [開発ガイドライン](./development.md)
- [運用ガイドライン](./operations.md)
- [コーディングガイドライン](./coding.md)
- [プラグイン・ライブラリ](./plugins_libraries.md)
- [構築・運用手順書（マニュアル）](./manuals.md)
- [翻訳手順書](./translation.md)
- [テスト手順書（マニュアル）](./manuals-test.md)
- [使用ツールについて](./tools.md)
- [リサーチ](./research.md)
