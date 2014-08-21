<?php

class AddSomeColumnsToNotifications0815 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = '';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'notifications' => array(
                    'model_id'  => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'モデルID(feedならpost_id,circleならcircle_id)', 'after' => 'body'),
                    'url_data'  => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'URLデータ(json)', 'charset' => 'utf8', 'after' => 'model_id'),
                    'count_num' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メッセージ内で利用する件数', 'after' => 'url_data'),
                    'indexes'   => array(
                        'modified' => array('column' => 'modified', 'unique' => 0),
                    ),
                ),
            ),
            'alter_field'  => array(
                'notifications' => array(
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知を更新した日付時刻'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'  => array(
                'notifications' => array('model_id', 'url_data', 'count_num', 'indexes' => array('modified')),
            ),
            'alter_field' => array(
                'notifications' => array(
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '通知を更新した日付時刻'),
                ),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
