<?php

class EditCircles0729 extends CakeMigration
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
                'circle_members' => array(
                    'admin_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '管理者フラグ', 'after' => 'user_id'),
                    'indexes'   => array(
                        'admin_flg' => array('column' => 'admin_flg', 'unique' => 0),
                    ),
                ),
                'circles'        => array(
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サークルロゴ画像', 'charset' => 'utf8', 'after' => 'description'),
                ),
            ),
            'alter_field'  => array(
                'circles' => array(
                    'public_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '公開フラグ(公開の場合はチームメンバー全員にサークルの存在が閲覧可能)'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'  => array(
                'circle_members' => array('admin_flg', 'indexes' => array('admin_flg')),
                'circles'        => array('photo_file_name',),
            ),
            'alter_field' => array(
                'circles' => array(
                    'public_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => '公開フラグ(公開の場合はチームメンバー全員にサークルの存在が閲覧可能)'),
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
