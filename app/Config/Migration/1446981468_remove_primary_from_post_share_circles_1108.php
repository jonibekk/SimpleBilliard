<?php

class RemovePrimaryFromPostShareCircles1108 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'remove_primary_from_post_share_circles_1108';

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
            $this->db->query("ALTER TABLE post_share_circles DROP PRIMARY KEY , ADD PRIMARY KEY (id, modified);");
        }
        else {
            $this->db->query("ALTER TABLE post_share_circles DROP PRIMARY KEY , ADD PRIMARY KEY (id);");

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
