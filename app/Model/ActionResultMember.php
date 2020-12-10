<?php

App::uses('AppModel', 'Model');
App::uses('Translation', 'Model');

/**
 * ActionResultMember Model
 */
class ActionResultMember extends AppModel
{
    public $uses = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'team_id' => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => ['rule' => 'notBlank'],
        ],
        'user_id' => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => ['rule' => 'notBlank'],
        ],
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    public $modelConversionTable = [];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [];

    /**
     * hasOne associations
     */
    public $hasOne = [];

    public function getActionResultMembersByActionResultId(string $actionResultId): array
    {
        $r = $this->find('all', [
            'conditions' => [
                'action_result_id' => $actionResultId,
            ],
        ]);
        return Hash::extract($r, '{n}.ActionResultMember');
    }

    public function addMember(
        string $actionResultId,
        string $userId,
        string $teamId,
        bool $isActionCreator
    ) {
        $this->create();
        return $this->save([
            'action_result_id' => $actionResultId,
            'user_id' => $userId,
            'team_id' => $teamId,
            'is_action_creator' => $isActionCreator,
        ])['ActionResultMember'];
    }

    public function deleteAllByActionResultId(
        string $actionResultId
    ): bool {
        if (empty($actionResultId)) {
            return false;
        }
        return $this->deleteAll([
            'ActionResultMember.action_result_id' => $actionResultId,
        ]);
    }
}
