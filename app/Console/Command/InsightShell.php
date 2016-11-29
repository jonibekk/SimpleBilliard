<?php

/**
 * 各種データ集計用バッチ
 * Console/cake insight -d YYYY-MM-DD -t timezone
 *
 * @property Team          $Team
 * @property TeamMember    $TeamMember
 * @property MemberGroup   $MemberGroup
 * @property Circle        $Circle
 * @property CircleMember  $CircleMember
 * @property TeamInsight   $TeamInsight
 * @property GroupInsight  $GroupInsight
 * @property CircleInsight $CircleInsight
 * @property GlRedis       $GlRedis
 * @property AccessUser    $AccessUser
 */
class InsightShell extends AppShell
{
    public $uses = array(
        'Team',
        'TeamMember',
        'MemberGroup',
        'Circle',
        'CircleMember',
        'TeamInsight',
        'GroupInsight',
        'CircleInsight',
        'AccessUser',
        'GlRedis',
    );

    public function startup()
    {
        parent::startup();
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $options = [
            'date'     => ['short' => 'd', 'help' => '集計日(YYYY-MM-DD)', 'required' => true,],
            'timezone' => ['short' => 't', 'help' => 'タイムゾーン', 'required' => true,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        // パラメータ
        $target_date = $this->params['date'];
        $timezone = $this->params['timezone'];

        // validate
        list($y, $m, $d) = explode('-', $target_date);
        if (strlen($target_date) != 10 || !checkdate(intval($m), intval($d), intval($y))) {
            $this->error('Invalid parameter', $this->_usageString());
        }
        if (!is_numeric($timezone)) {
            $this->error('Invalid parameter', $this->_usageString());
        }

        $this->Team->begin();

        // 全チーム
        $teams = $this->Team->find('all', [
            'fields' => [
                'Team.id',
            ]
        ]);
        foreach ($teams as $team) {
            // モデルに current_team_id をセット
            $this->_setupModels($team['Team']['id']);

            ///////////////////////////////////////////
            // チームメンバー数
            ///////////////////////////////////////////
            $member_count = $this->TeamMember->countActiveMembersByTeamId($team['Team']['id']);
            // 同じ日のデータが存在しない場合だけ登録する
            $row = $this->TeamInsight->find('first', [
                'fields'     => [
                    'TeamInsight.id',
                ],
                'conditions' => [
                    'TeamInsight.team_id'     => $team['Team']['id'],
                    'TeamInsight.target_date' => $target_date,
                    'TeamInsight.timezone'    => $timezone,
                ]
            ]);
            if (!$row) {
                $data = [
                    'team_id'     => $team['Team']['id'],
                    'target_date' => $target_date,
                    'timezone'    => $timezone,
                    'user_count'  => $member_count,
                ];
                $this->TeamInsight->create();
                $res = $this->TeamInsight->save($data);
                if (!$res) {
                    $this->Team->rollback();
                    $this->log("TeamInsight::save() failed.\n" . var_export($data, true));
                    $this->error('TeamInsight::save() failed.');
                }
            }

            ///////////////////////////////////////////
            // グループメンバー数
            ///////////////////////////////////////////
            $group_list = $this->Team->Group->getAllGroupList($team['Team']['id']);
            foreach ($group_list as $group_id => $name) {
                $group_member_list = $this->MemberGroup->getGroupMemberUserId($team['Team']['id'], $group_id);
                // 同じ日のデータが存在しない場合だけ登録する
                $row = $this->GroupInsight->find('first', [
                    'fields'     => [
                        'GroupInsight.id',
                    ],
                    'conditions' => [
                        'GroupInsight.team_id'     => $team['Team']['id'],
                        'GroupInsight.group_id'    => $group_id,
                        'GroupInsight.target_date' => $target_date,
                        'GroupInsight.timezone'    => $timezone,
                    ]
                ]);
                if (!$row) {
                    $data = [
                        'team_id'     => $team['Team']['id'],
                        'group_id'    => $group_id,
                        'target_date' => $target_date,
                        'timezone'    => $timezone,
                        'user_count'  => count($group_member_list),
                    ];
                    $this->GroupInsight->create();
                    $res = $this->GroupInsight->save($data);
                    if (!$res) {
                        $this->Team->rollback();
                        $this->log("GroupInsight::save() failed.\n" . var_export($data, true));
                        $this->error('GroupInsight::save() failed.');
                    }
                }
            }

            ///////////////////////////////////////////
            // サークルメンバー数
            ///////////////////////////////////////////
            $circles = $this->Circle->getList();
            foreach ($circles as $circle_id => $circle_name) {
                // 同じ日のデータが存在しない場合だけ登録する
                $row = $this->CircleInsight->find('first', [
                    'fields'     => [
                        'CircleInsight.id',
                    ],
                    'conditions' => [
                        'CircleInsight.team_id'     => $team['Team']['id'],
                        'CircleInsight.circle_id'   => $circle_id,
                        'CircleInsight.target_date' => $target_date,
                        'CircleInsight.timezone'    => $timezone,
                    ]
                ]);
                if (!$row) {
                    // 登録者数
                    $circle_member_count = $this->CircleMember->getActiveMemberCount($circle_id, false);
                    $data = [
                        'team_id'     => $team['Team']['id'],
                        'circle_id'   => $circle_id,
                        'target_date' => $target_date,
                        'timezone'    => $timezone,
                        'user_count'  => $circle_member_count,
                    ];
                    $this->CircleInsight->create();
                    $res = $this->CircleInsight->save($data);
                    if (!$res) {
                        $this->Team->rollback();
                        $this->log("CircleInsight::save() failed.\n" . var_export($data, true));
                        $this->error('CircleInsight::save() failed.');
                    }
                }
            }

            ///////////////////////////////////////////
            // アクセスユーザー数
            ///////////////////////////////////////////
            $access_user_list = $this->AccessUser->getUserList($team['Team']['id'], $target_date, $timezone);
            $user_ids = $this->GlRedis->getAccessUsers($team['Team']['id'], $target_date, $timezone);
            foreach ($user_ids as $user_id) {
                // 既存データが存在しない場合だけ登録する
                if (!isset($access_user_list[$user_id])) {
                    $data = [
                        'team_id'     => $team['Team']['id'],
                        'user_id'     => $user_id,
                        'access_date' => $target_date,
                        'timezone'    => $timezone,
                    ];
                    $this->AccessUser->create();
                    $res = $this->AccessUser->save($data);
                    if (!$res) {
                        $this->Team->rollback();
                        $this->log("AccessUser::save() failed.\n" . var_export($data, true));
                        $this->error('AccessUser::save() failed.');
                    }
                }
            }
        }

        $this->Team->commit();

        // redis にあるユーザーアクセスデータを削除
        foreach ($teams as $team) {
            $this->GlRedis->delAccessUsers($team['Team']['id'], $target_date, $timezone);
        }

    }

    protected function _usageString()
    {
        return 'Usage: cake insight YYYY-MM-DD time_offset';
    }

    protected function _setupModels($team_id)
    {
        foreach ($this->uses as $model) {
            $this->{$model}->current_team_id = $team_id;
        }
    }

}
