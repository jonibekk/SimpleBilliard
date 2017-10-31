<?php

class CreateViewTablePricePlan extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'create_view_table_price_plan';

    /**
     * Actions to be performed
     * [Important]
     * Cakephp doesn't support MySQL View table migration.
     * Actually normal table was created, not view table after I create view table and then run command to generate db migration.
     * So instead of using db migration feature, plain SQL run directly.
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => [],
        'down' => [],
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
            $sql = <<<SQL
CREATE VIEW view_price_plans AS
  SELECT
    mpp.id,
    mpp.group_id,
    mpp.code,
    mpp.price,
    mpp.max_members,
    mppg.currency
  FROM
    mst_price_plans mpp
  INNER JOIN mst_price_plan_groups mppg ON
    mpp.group_id = mppg.id
    AND mppg.del_flg = 0
  WHERE mpp.del_flg = 0;
SQL;

        } else {
            $sql = "DROP VIEW view_price_plans;";
        }
        $this->db->query($sql);
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
