<?php

class EditUserAutoLang extends CakeMigration
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
            'alter_field' => array(
                'users' => array(
                    'auto_language_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自動言語設定フラグ(Onの場合はブラウザから言語を取得する)'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'users' => array(
                    'auto_language_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自動言語設定フラグ(Onの場合はブラウザから言語を取得する)'),
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
