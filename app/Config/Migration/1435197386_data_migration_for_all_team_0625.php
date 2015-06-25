<?php

class DataMigrationForAllTeam0625 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'data_migration_for_all_team_0625';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = [
        'up'   => [
        ],
        'down' => [
        ],
    ];

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        if ($direction == 'up') {
            /**
             * @var Team $Team
             */
            $Team = ClassRegistry::init('Team');
            /**
             * @var Circle $Circle
             */
            $Circle = ClassRegistry::init('Circle');

            //key: team_id, val: team_id
            $all_teams = $Team->find('list', ['fields' => 'id,id']);
            $options = [
                'conditions' => [
                    'team_id'      => $all_teams,
                    'team_all_flg' => true
                ],
                'fields'     => ['team_id', 'id']
            ];
            //key: team_id, val: circle_id
            $exists_all_team_circles = $Circle->findWithoutTeamId('list', $options);
            debug($exists_all_team_circles);
            //merge circle_id
            foreach ($all_teams as $key => $team_id) {
                if (array_key_exists($team_id, $exists_all_team_circles)) {
                    $all_teams[$key] = ['circle_id' => $exists_all_team_circles[$team_id]];
                }
                else {
                    //team_allの登録
                    $Circle->create();
                    $Circle->save(
                        [
                            'team_id'      => $team_id,
                            'name'         => __d('gl', "チーム全体"),
                            'description'  => __d('gl', "チーム全体"),
                            'team_all_flg' => true,
                            'public_flg'   => true,
                        ]
                    );
                    $all_teams[$key] = ['circle_id' => $Circle->getLastInsertID()];
                }
            }
            debug($all_teams);

            //team_membersへのユーザの登録

            //投稿データの移行
        }
        if ($direction == 'down') {

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
