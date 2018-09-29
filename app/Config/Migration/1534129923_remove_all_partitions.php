<?php

class RemoveAllPartitions extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'remove_all_partitions';

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
            $migrateTbls = [
                'posts',
                'post_share_users',
                'post_share_circles'
            ];
            foreach ($migrateTbls as $tbl) {
                $this->migrateTable($tbl);
            }
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

    function migrateTable(string $table)
    {
        // posts table
        if ($this->isPartitioned($table)) {
            $this->db->query("ALTER TABLE ${table} REMOVE PARTITIONING;");
        }
        $this->db->query("ALTER TABLE ${table} DROP PRIMARY KEY , ADD PRIMARY KEY (id);");
        return false;
    }

    function isPartitioned(string $table)
    {
        $dbName = $this->db->getSchemaName();
        $res = $this->db->query("SELECT count(*) as count FROM INFORMATION_SCHEMA.PARTITIONS WHERE TABLE_SCHEMA = '{$dbName}' AND TABLE_NAME =  '{$table}'");
        if ($res[0][0]['count'] > 1) {
            return true;
        }
        return false;
    }

}
