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
}
