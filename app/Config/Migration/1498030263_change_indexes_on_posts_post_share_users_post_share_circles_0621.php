<?php

class ChangeIndexesOnPostsPostShareUsersPostShareCircles0621 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_indexes_on_posts_post_share_users_post_share_circles_0621';

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
            $this->db->query("ALTER TABLE posts DROP PRIMARY KEY , ADD PRIMARY KEY (id, created);");
            $this->db->query("ALTER TABLE post_share_users DROP PRIMARY KEY , ADD PRIMARY KEY (id, created);");
            $this->db->query("ALTER TABLE post_share_circles DROP PRIMARY KEY , ADD PRIMARY KEY (id, created);");
        } else {
            $this->db->query("ALTER TABLE posts DROP PRIMARY KEY , ADD PRIMARY KEY (id, modified);");
            $this->db->query("ALTER TABLE post_share_users DROP PRIMARY KEY , ADD PRIMARY KEY (id, modified);");
            $this->db->query("ALTER TABLE post_share_circles DROP PRIMARY KEY , ADD PRIMARY KEY (id, modified);");

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
