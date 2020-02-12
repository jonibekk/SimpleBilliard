<?php

App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');
App::uses('UnreadCirclePost', 'Model');
App::uses('CircleMember', 'Model');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsClient');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsKey');
App::import('Model/Redis/UnreadPosts', 'UnreadPostsData');

use Goalous\Enum as Enum;

class AddUnreadCirclePostsCacheTable extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'AddUnreadCirclePostsCacheTable';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'cache_unread_circle_posts' => array(
                    'id'              => array(
                        'type'     => 'biginteger',
                        'null'     => false,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'primary'
                    ),
                    'team_id'         => array(
                        'type'     => 'biginteger',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '= teams.id'
                    ),
                    'circle_id'       => array(
                        'type'     => 'biginteger',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '= circles.id'
                    ),
                    'user_id'         => array(
                        'type'     => 'biginteger',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => '= users.id'
                    ),
                    'post_id'         => array(
                        'type'     => 'biginteger',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'comment'  => '= posts.id'
                    ),
                    'del_flg'         => array(
                        'type'    => 'boolean',
                        'null'    => false,
                        'default' => '0',
                        'comment' => '削除フラグ'
                    ),
                    'deleted'         => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'created'         => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'modified'        => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true
                    ),
                    'indexes'         => array(
                        'PRIMARY'      => array('column' => 'id', 'unique' => 1),
                        'circle_user'  => array('column' => array('circle_id', 'user_id'), 'unique' => 0),
                        'circle_post'  => array('column' => array('circle_id', 'post_id'), 'unique' => 0),
                        'paging_index' => array('column' => array('team_id', 'user_id', 'id'), 'unique' => 0),
                        'tuple'        => array(
                            'column' => array('team_id', 'circle_id', 'user_id', 'post_id'),
                            'unique' => 1
                        ),
                    ),
                    'tableParameters' => array(
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_general_ci',
                        'engine'  => 'InnoDB'
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'cache_unread_circle_posts'
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
        if ($direction === 'down') {
            return true;
        }

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        $CircleMember->updateAll(['unread_count' => 0]);

        return true;
    }

    /**
     * Get team ids of all trial and paid teams
     *
     * @return int[]
     */
    private function getRelevantTeamIds(): array
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        $option = [
            'conditions' => [
                'service_use_status' => [
                    Enum\Model\Team\ServiceUseStatus::FREE_TRIAL,
                    Enum\Model\Team\ServiceUseStatus::PAID
                ],
                'del_flg'            => false
            ],
            'fields'     => ['id']
        ];

        $teams = $Team->find('all', $option);

        return Hash::extract($teams, '{n}.Team.id');
    }
}
