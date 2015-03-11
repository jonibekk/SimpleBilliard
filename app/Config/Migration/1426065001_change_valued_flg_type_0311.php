<?php

class ChangeValuedFlgType0311 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_valued_flg_type_0311';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field' => array(
                'collaborators' => array(
                    'valued_flg' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '価値フラグ(0 = 処理前,1 = 承認, 2 = 保留,3 = 修正依頼, 4 = 差し戻し)'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'collaborators' => array(
                    'valued_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '価値フラグ'),
                ),
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
        return true;
    }
}
