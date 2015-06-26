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
            ini_set('memory_limit', '2024M');
            /**
             * @var Team            $Team
             * @var TeamMember      $TeamMember
             * @var Circle          $Circle
             * @var CircleMember    $CircleMember
             * @var Post            $Post
             * @var PostShareCircle $PostShareCircle
             */
            $Team = ClassRegistry::init('Team');
            $TeamMember = ClassRegistry::init('TeamMember');
            $Circle = ClassRegistry::init('Circle');
            $Post = ClassRegistry::init('Post');
            $CircleMember = ClassRegistry::init('CircleMember');
            $PostShareCircle = ClassRegistry::init('PostShareCircle');

            //key: team_id, val: team_id
            $all_teams = $Team->find('list', ['fields' => 'id,created']);
            $team_ids = array_keys($all_teams);
            $options = [
                'conditions' => [
                    'team_id'      => $team_ids,
                    'team_all_flg' => true
                ],
                'fields'     => ['team_id', 'id']
            ];
            unset($team_ids);
            //key: team_id, val: circle_id
            $exists_all_team_circles = $Circle->findWithoutTeamId('list', $options);
            //merge circle_id
            foreach ($all_teams as $team_id => $created) {
                if (array_key_exists($team_id, $exists_all_team_circles)) {
                    //既にチーム全体サークルが存在する場合は除外
                    unset($all_teams[$team_id]);
                    continue;
                }
                //team_allの登録
                $Circle->create();
                $Circle->save(
                    [
                        'team_id'      => $team_id,
                        'name'         => __d('gl', "チーム全体"),
                        'description'  => __d('gl', "チーム全体"),
                        'team_all_flg' => true,
                        'public_flg'   => true,
                        'created'      => $created,
                    ]
                );
                $all_teams[$team_id] = ['circle_id' => $Circle->getLastInsertID()];
            }
            unset($exists_all_team_circles);

            //team_membersテーブルからユーザデータを取得
            foreach ($all_teams as $team_id => $val) {
                //1件ずつ処理
                $options = [
                    'conditions' => ['team_id' => $team_id],
                    'fields'     => ['user_id', 'admin_flg']
                ];
                if (empty($uid_list = $TeamMember->findWithoutTeamId('list', $options))) {
                    //ユーザが存在しない場合は除外
                    unset($all_teams[$team_id]);
                }
                else {
                    $all_teams[$team_id]['uid_list'] = $uid_list;
                }
            }
            $circle_members = [];
            //circle_membersへのユーザ登録の準備
            foreach ($all_teams as $team_id => $team) {
                foreach ($team['uid_list'] as $uid => $admin_flg) {
                    $circle_members[] = [
                        'team_id'   => $team_id,
                        'circle_id' => $team['circle_id'],
                        'user_id'   => $uid,
                        'admin_flg' => $admin_flg
                    ];
                }
            }
            //circle_membersへのユーザ登録
            if (!empty($circle_members)) {
                $CircleMember->saveAll($circle_members, ['validate' => false, 'deep' => false]);
            }

            //publicな全投稿idの取得
            foreach ($all_teams as $team_id => $team) {
                $options = [
                    'conditions' => ['team_id' => $team_id, 'public_flg' => true],
                    'fields'     => ['id', 'id']
                ];
                $all_teams[$team_id]['post_id_list'] = $Post->findWithoutTeamId('list', $options);
            }
            //post_share_circlesの投稿データ登録の準備
            $post_share_circles = [];
            //circle_membersへのユーザ登録の準備
            foreach ($all_teams as $team_id => $team) {
                foreach ($team['post_id_list'] as $post_id) {
                    $post_share_circles[] = ['team_id' => $team_id, 'circle_id' => $team['circle_id'], 'post_id' => $post_id];
                }
            }
            //post_share_circlesの投稿データ登録
            //circle_membersへのユーザ登録
            if (!empty($post_share_circles)) {
                $PostShareCircle->saveAll($post_share_circles, ['validate' => false, 'deep' => false]);
            }

            //全postのpublic_flgにfalseをセット
            $Post->updateAll(['Post.public_flg' => false]);

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
