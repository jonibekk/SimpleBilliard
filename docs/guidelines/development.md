
<hr id="setup_stack">

## Set-up guild for development

## Introduction
We are trying to start development quickly for Goalous.
If there is a more efficient procedure or operation method, we will fix it more and more.

## Requirements
Please be sure to install the following tools as they are essential for development on local environment.

- Virtual Box `version = latest`
- Vagrant `version = 1.8.7` https://releases.hashicorp.com/vagrant/1.8.7/
- Git `version >= latest`
- Chef Development Kit `version = 0.17.17` https://downloads.chef.io/chefdk/stable/0.17.17


- [Installation guild for tools(windows)](http://bit.ly/2aIo1KH) written in Japanese.
- [Installation guild for tools(mac)](http://bit.ly/2adyLyR) written in Japanese.

## Recommended
- [hub command](http://qiita.com/yaotti/items/a4a7f3f9a38d7d3415e3)（Only mac and linux）

## IDE
Goalous development is optimized for using [Phpstorm](http://www.jetbrains.com/phpstorm/).
However you can use another IDE or editor as well.

## Installation
1. Clone the Source files.  
run below command on terminal.  
`git clone --recursive git@github.com:IsaoCorp/goalous.git`  
1. Start vagrant  
run below command on terminal.  
`cd goalous`  
`vagrant up`  
[Troubleshooting of vagrant](http://bit.ly/1TnOYjQ)  
1. Operation check  
Access below url  
[http://192.168.50.4](http://192.168.50.4)  

## Test Users
You don't need to make an user registration on local env.
There are test users already.
However, of course you can register user by own e-mail address as well.

User name list:
- goalous.test01@gmail.com
- goalous.test02@gmail.com
- goalous.test03@gmail.com
- goalous.test04@gmail.com
- goalous.test05@gmail.com
- goalous.test06@gmail.com
- goalous.test07@gmail.com
- goalous.test08@gmail.com
- goalous.test09@gmail.com
- goalous.test10@gmail.com

Password: `12345678`

### Updating local environment
On Host OS:
```shell
$ sh etc/local/update_app.sh
```

### Updating DB Schemas
On Guest OS(VM):
```shell
vagrant@precise64:/vagrant/app$ ./Console/cake migrations.migration run all
```

### DB information for local environment
- DB name: `myapp`
- DB user: `root`
- DB password: blank
- DB host: localhost

You can connect the DB from VM.

### To run test cases
#### In Goal model case
On Guest OS(VM):
```shell
vagrant@precise64:/vagrant/app$ ./Console/cake test app Model/Goal
```

#### In GoalsController case
On Guest OS(VM):
```shell
vagrant@precise64:/vagrant/app$ ./Console/cake test app Controller/GoalsController
```

#### Only Single method( e.g. testGetXxx() on Goal model)
On Guest OS(VM):
```
vagrant@precise64:/vagrant/app$ ./Console/cake test app Model/Goal --filter testGetXxx
```

### Creating DB migration file
On Guest OS(VM):  
To Run below command after change schema
```shell
vagrant@precise64:/vagrant/app$ ./Console/cake migrations.migration generate -f
/vagrant/app/Vendor/cakephp/cakephp/libCake Migration Shell
---------------------------------------------------------------
Do you want to compare the schema.php file to the database? (y/n)
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
                        'create_field' => array(
                                'evaluations' => array(
                                        'evaluate_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '評価タイプ(0:自己評価,1:評価者評価,2:リーダー評価,3:最終者評価)', 'after' => 'evaluate_term_id'),
                                        'goal_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)', 'after' => 'evaluate_type'),
                                        'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価コメント', 'charset' => 'utf8', 'after' => 'goal_id'),
                                        'indexes' => array(
                                                'evaluatee_user_id' => array('column' => 'evaluatee_user_id', 'unique' => 0),
                                                'evaluator_user_id' => array('column' => 'evaluator_user_id', 'unique' => 0),
                                                'goal_id' => array('column' => 'goal_id', 'unique' => 0),
                                        ),
                                ),
                        ),
                        'drop_field' => array(
                                'evaluations' => array('name'),
                        ),
                        'alter_field' => array(
                                'evaluations' => array(
                                        'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
                                        'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
                                ),
                        ),
                ),
                'down' => array(
                        'drop_field' => array(
                                'evaluations' => array('evaluate_type', 'goal_id', 'comment', 'indexes' => array('evaluatee_user_id', 'evaluator_user_id', 'goal_id')),
                        ),
                        'create_field' => array(
                                'evaluations' => array(
                                        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価スコア名', 'charset' => 'utf8'),
                                ),
                        ),
                        'alter_field' => array(
                                'evaluations' => array(
                                        'evaluatee_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '被評価者ID(belongsToでUserモデルに関連)'),
                                        'evaluator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '評価者ID(belongsToでUserモデルに関連)'),
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
> add_some_column_to_evaluations_0311
Generating Migration...

Done.
Do you want to update the schema.php file? (y/n)
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


<hr id="development_flow">

# Development flow
## Description
Goalous team adapts Pull Request driven development.  
All deployment should be already reviewed Pull Request.  
The flow is here: http://bit.ly/1PEeE9D

## Procedures
To run the following commands.

1. Switch directory to Goalous root
1. Start vagrant  
`vagrant up`
1. Checkout to `develop` branch    
`git checkout develop`
1. Updating the branch  
`git pull`
1. Updating local environment  
`sh etc/local/update_app.sh`
1. Creating a working branch  
`git branch topic-xxxx`
1. Checkout to working branch  
`git checkout topic-xxxx`
1. To make changes  
Append the Issue number of JIRA to the end of the commit log.
1. Push to GitHub    
`git push origin topic-xxx`  
1. Creating Pull Request  
Append the Issue number of JIRA to the end of the title.
1. To comment to other developers on the Pull Request with mention as requesting to review the changes  
1. Merge Pull Request after a developer said LGTM.

<hr id="agile">

## アジャイル開発
アジャイルプロセスの**スクラム**を採用する。(スクラムマスターは不在。。。)
### アジャイルについて
Goalousプロジェクトにおける活動は全て**アジャイル**の原則に従う。
以下の宣言を十分に理解し、定期的に復唱する事を勧める。
[アジャイルソフトウェア開発宣言](http://www.t-doi.org/agile/index.html)

### スクラムについて
> スクラム（英: Scrum）は、ソフトウェア開発における反復的で増分的なアジャイルソフトウェア開発手法の1つである。中心点は「開発チームが一体にすると共有されている目的を追い求める柔軟なプロダクト開発ストラテジー」である。比較的に「伝統的と順次的なさしかける方法」の優先度が低い。
スクラムはチームメンバー全員の口頭のコミュニケーションを励ますとチームが自動で揃うことを可能にする。
スクラムの主な点1つとは、プロジェクト開発の途中で対象顧客の欲求が変わる可能性も予想も簡単に解決もできない問題が発生される可能性も認める。よって、スクラムはプロジェクトの問題を全幅的に理解することができないので、チームのタスクを早く済ませる力と新しい変更に急に対応する力を上げる実験的な手法となる。

引用元：[Wiki](http://ja.wikipedia.org/wiki/%E3%82%B9%E3%82%AF%E3%83%A9%E3%83%A0_%28%E3%82%BD%E3%83%95%E3%83%88%E3%82%A6%E3%82%A7%E3%82%A2%E9%96%8B%E7%99%BA%29)

### 補足
各工程および、レイヤー(バックエンド、フロントエンド)の厳密な役割分担はしない。ただし、実際はある程度得意な人が作業を行う。お互い助けあう。
自分がいなくても問題にならないように作業内容のシェアは頻繁に行う。Issueで作業内容と進捗がわかるようにしておく。

<hr id="iteration">

## `Before 1 Iteration`の定義
### 期間
次のイテレーション開始前の１週間

### 前提条件
- 要求がfix
- 要件がfix

### 含まれる工程
ストーリ(issue)別に、
- ストーリーポイントの見積もり
- ストーリーの優先順位付け
- 次のイテレーションで行うストーリの選別
- デモ手法の確率
- イテレーション前レビュー(関係者)

### 各工程の流れ
**ToDo - 修正**
|誰が？|何をする？|どこで？どこに？(Waffle)|
|:--|:--|:--|
|菊|Issue(ストーリ)を立てる|Icebox|
|菊|要件を定義する|Icebox|
|だい・菊|要件レビューする|Icebox|
|菊|レビューしたらBacklogに移動する|Backlog|
|全員|Issueのサイズを見積もる(プランニングポーカー)|Backlog|
|菊|ベロシティーを元に次週に完了するIssueか確認|Backlog|
|菊|Issueの優先順位を付ける|Backlog|
|菊|金曜日に`To Do`に`Backlog`のIssueを移動|To Do|

## `1 Iteration`の定義
### 期間
1週間
### 含まれる工程
ストーリ(issue)別に、
- 設計
- 設計レビュー
- 実装
- テスト
- リファクタリング
- 実装・テストのレビュー

原則、各工程の成果物、エビデンスの全てはストーリにログを残しておく。且つ、ドキュメントに反映する。

### 各工程の流れ
|誰が？|何をする？|どこで？どこに？(Waffle)|
|:--|:--|:--|
|各自|`To Do`にあるIssueで自分がやりたいもの(上から優先的に)を`In Progress`に移動|In Progress|
|各自|設計を行う|In Progress|
|各自|メンバーに設計のレビューを依頼|In Progress|
|レビュアー|レビューを行う|In Progress|
|各自|実装|In Progress|
|各自|テスト|In Progress|
|各自|メンバーにPRのレビュー依頼|In Progress|
|レビュアー|レビューを行う|In Progress|
|各自|レビューが問題無ければReadyに移動|Ready|
|菊|Issue完了判断、マージ|Done|
|菊|ステージングで動作確認|Done|

----

次のドキュメントへ進んでください。  
[運用ガイドライン](./operations.md)

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
