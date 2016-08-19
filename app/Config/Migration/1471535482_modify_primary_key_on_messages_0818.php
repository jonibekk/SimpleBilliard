<?php

class ModifyPrimaryKeyOnMessages0818 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'modify_primary_key_on_messages_0818';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(),
        'down' => array(),
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
        if ($direction === 'up') {
            $this->db->query("ALTER TABLE messages DROP PRIMARY KEY , ADD PRIMARY KEY (id, created);");
        } else {
            $this->db->query("ALTER TABLE messages DROP PRIMARY KEY , ADD PRIMARY KEY (id);");

        }
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
