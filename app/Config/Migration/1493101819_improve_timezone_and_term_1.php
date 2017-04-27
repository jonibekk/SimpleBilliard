<?php

class ImproveTimezoneAndTerm1 extends CakeMigration
{
    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'improve_timezone_and_term_1';

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
        $Team = ClassRegistry::init('Team');
        if ($direction == 'up') {
            $Team->query($this->getDDLForUp());
        } elseif ($direction == 'down') {
            $Team->query($this->getDDLForDown());
        }
        return true;
    }

    private function getDDLForUp()
    {
        $sql = <<<SQL
        
ALTER TABLE `goals`
DROP INDEX `start_date`,
DROP INDEX `end_date`,
CHANGE COLUMN `start_date` `old_start_date` int(11) unsigned DEFAULT NULL COMMENT '開始日',
CHANGE COLUMN `end_date` `old_end_date` int(11) unsigned DEFAULT NULL COMMENT '終了日',
ADD COLUMN `start_date` date NOT NULL COMMENT '開始日' AFTER `description`,
ADD COLUMN `end_date` date NOT NULL COMMENT '終了日' AFTER `start_date`,
ADD INDEX `start_date`(`start_date`),
ADD INDEX `end_date`(`end_date`);


ALTER TABLE `key_results`
DROP INDEX `start_date`,
DROP INDEX `end_date`,
CHANGE COLUMN `start_date` `old_start_date` int(11) unsigned DEFAULT NULL COMMENT '開始日',
CHANGE COLUMN `end_date` `old_end_date` int(11) unsigned DEFAULT NULL COMMENT '終了日',
ADD COLUMN `start_date` date NOT NULL COMMENT '開始日' AFTER `description`,
ADD COLUMN `end_date` date NOT NULL COMMENT '終了日' AFTER `start_date`,
ADD INDEX `start_date`(`start_date`),
ADD INDEX `end_date`(`end_date`);        
        
CREATE TABLE `terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `team_id` bigint(20) unsigned NOT NULL COMMENT 'チームID',
  `start_date` date NOT NULL COMMENT '期開始日',
  `end_date` date NOT NULL COMMENT '期終了日',
  `evaluate_status` int(11) NOT NULL DEFAULT '0' COMMENT '評価ステータス(0 = 評価開始前, 1 = 評価中,2 = 評価凍結中, 3 = 最終評価終了)',
  `del_flg` tinyint(1) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  `deleted` int(11) unsigned DEFAULT NULL COMMENT '削除した日付時刻',
  `created` int(11) unsigned DEFAULT NULL COMMENT '追加した日付時刻',
  `modified` int(11) unsigned DEFAULT NULL COMMENT '更新した日付時刻',
  PRIMARY KEY (`id`),
  KEY `team_id` (`team_id`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SQL;
        return $sql;

    }

    private function getDDLForDown()
    {
        $sql = <<<SQL
        
ALTER TABLE `goals`
DROP INDEX `start_date`,
DROP INDEX `end_date`,
DROP COLUMN `start_date`,
DROP COLUMN `end_date`,
CHANGE COLUMN `old_start_date` `start_date` int(11) unsigned DEFAULT NULL COMMENT '開始日',
CHANGE COLUMN `old_end_date` `end_date` int(11) unsigned DEFAULT NULL COMMENT '終了日',
ADD INDEX `start_date`(`start_date`),
ADD INDEX `end_date`(`end_date`);


ALTER TABLE `key_results`
DROP INDEX `start_date`,
DROP INDEX `end_date`,
DROP COLUMN `start_date`,
DROP COLUMN `end_date`,
CHANGE COLUMN `old_start_date` `start_date` int(11) unsigned DEFAULT NULL COMMENT '開始日',
CHANGE COLUMN `old_end_date` `end_date` int(11) unsigned DEFAULT NULL COMMENT '終了日',
ADD INDEX `start_date`(`start_date`),
ADD INDEX `end_date`(`end_date`);
        
DROP TABLE `terms`;
SQL;
        return $sql;

    }
}
