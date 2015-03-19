### ローカル環境のアップデート
ホスト側(mac or windows)で
```shell
$ sh etc/local/update_app.sh
```

### DB Schemaのみアップデート
ゲスト側(ubuntu)で
```shell
vagrant@precise64:/vagrant/app$ ./Console/cake migrations.migration run all
```

### ローカルでテスト実行
#### Goalモデルなら
ゲスト側(ubuntu)で
```shell
vagrant@precise64:/vagrant/app$ ./Console/cake test app Model/Goal
```

#### Goalsコントローラなら
ゲスト側(ubuntu)で
```shell
vagrant@precise64:/vagrant/app$ ./Console/cake test app Controller/GoalsController
```

#### メソッド単体で実行(GoalモデルのtestGetXxxのみ実行)
ゲスト側(ubuntu)で
```
vagrant@precise64:/vagrant/app$ ./Console/cake test app Model/Goal --filter testGetXxx
```

### DB migrationの作成
ゲスト側(ubuntu)で実際のDBの修正をした後に、以下を実行
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
