<?php

App::uses('AppModel', 'Model');

/**
 * Experiment Model
 * 実験を管理するテーブル
 * nameは自由に追加可能。
 * 例：フィードの通知設定を非表示にする実験(以下が対象チーム数分存在する)
 *   name: 'DisappearNotificationSettingsForFeed'
 *   team_id: 対象のチームID
 */
class Experiment extends AppModel
{
    //現存する実験の種類。不要になったら削除。
    const NAME_CIRCLE_DEFAULT_SETTING_ON = 'CircleDefaultSettingOn';
    const NAME_ENABLE_EVALUATION_FEATURE = 'EnableEvaluationFeature';

    /**
     * Video feature experiment
     *
     * @see https://confluence.goalous.com/pages/viewpage.action?pageId=13861014
     */
    const NAME_ENABLE_VIDEO_POST_TRANSCODING = 'EnableVideoPostTranscoding';
    const NAME_ENABLE_VIDEO_POST_PLAY        = 'EnableVideoPostPlay';

    /**
     * SSO Login feature
     *
     * @see https://confluence.goalous.com/x/BIDUAw
     */
    const NAME_ENABLE_SSO_LOGIN = 'EnableSSOLogin';

    const NAME_ENABLE_GROUPS_MANAGEMENT = 'EnableGroupsManagement';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'    => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * 定義済みの実験を取得
     *
     * @param $name
     *
     * @return array
     */
    function findExperiment($name)
    {
        $options = [
            'conditions' => [
                'name'    => $name,
                'team_id' => $this->current_team_id
            ]
        ];
        $ret = $this->find('first', $options);
        return $ret;
    }

    /**
     * Check whether given team has experiment setting enabled
     *
     * @param int    $teamId
     * @param string $experimentName
     *
     * @return bool
     */
    public function hasExperimentSetting(int $teamId, string $experimentName): bool
    {
        $options = [
            'conditions' => [
                'name'    => $experimentName,
                'team_id' => $teamId
            ]
        ];
        /** @var int $count */
        $count = $this->find('count', $options);
        return $count > 0;
    }
}
