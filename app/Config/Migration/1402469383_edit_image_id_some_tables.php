<?php

class EditImageIdSomeTables extends CakeMigration
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
                'badges' => array(
                    'photo' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ画像', 'charset' => 'utf8', 'after' => 'description'),
                ),
                'teams'  => array(
                    'photo' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'チームロゴ画像', 'charset' => 'utf8', 'after' => 'name'),
                ),
                'users'  => array(
                    'photo' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'プロフィール画像', 'charset' => 'utf8', 'after' => 'no_pass_flg'),
                ),
            ),
            'drop_field'   => array(
                'badges' => array('image_id',),
                'teams'  => array('image_id',),
                'users'  => array('profile_image_id',),
            ),
            'alter_field'  => array(
                'cake_sessions' => array(
                    'id' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'badges' => array('photo',),
                'teams'  => array('photo',),
                'users'  => array('photo',),
            ),
            'create_field' => array(
                'badges' => array(
                    'image_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ画像ID(hasOneでImageモデルに関連)', 'charset' => 'utf8'),
                ),
                'teams'  => array(
                    'image_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームロゴ画像ID(hasOneでImageモデルに関連)', 'charset' => 'utf8'),
                ),
                'users'  => array(
                    'profile_image_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'プロフィール画像ID(hasOneでImageモデルに関連)', 'charset' => 'utf8'),
                ),
            ),
            'alter_field'  => array(
                'cake_sessions' => array(
                    'id' => array('type' => 'string', 'null' => false, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
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
