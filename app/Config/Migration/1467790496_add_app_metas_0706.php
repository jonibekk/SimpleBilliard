<?php

class AddAppMetas0706 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_app_metas_0706';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'app_metas' => array(
                    'id'              => array(
                        'type'     => 'integer',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary',
                        'comment'  => 'メタID'
                    ),
                    'key_name'        => array(
                        'type'    => 'string',
                        'null'    => false,
                        'default' => null,
                        'length'  => 20,
                        'collate' => 'utf8mb4_general_ci',
                        'comment' => 'キーの名前',
                        'charset' => 'utf8mb4'
                    ),
                    'value'           => array(
                        'type'    => 'string',
                        'null'    => false,
                        'default' => null,
                        'length'  => 128,
                        'collate' => 'utf8mb4_general_ci',
                        'comment' => '値',
                        'charset' => 'utf8mb4'
                    ),
                    'del_flg'         => array(
                        'type'    => 'boolean',
                        'null'    => false,
                        'default' => '0',
                        'comment' => '削除フラグ'
                    ),
                    'deleted'         => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '削除した日付時刻'
                    ),
                    'created'         => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '追加した日付時刻'
                    ),
                    'modified'        => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '更新した日付時刻'
                    ),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array(
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'app_metas'
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        if($direction == 'up'){
            //insert default records
            /**
             * @var AppMeta $AppMeta
             */
            $AppMeta = ClassRegistry::init('AppMeta');
            $AppMeta->saveAll([
                [
                    'key_name' => 'iOS_version',
                    'value'    => '1.0.0',
                ],
                [
                    'key_name' => 'iOS_install_url',
                    'value'    => 'https://itunes.apple.com/jp/app/goalous-business-sns/id1060474459?l=en&mt=8',
                ],
                [
                    'key_name' => 'android_version',
                    'value'    => '1.0.0',
                ],
                [
                    'key_name' => 'android_install_url',
                    'value'    => 'https://play.google.com/store/apps/details?id=jp.co.isao.android.goalous&hl=ja',
                ],
            ]);
        }

        return true;
    }
}
