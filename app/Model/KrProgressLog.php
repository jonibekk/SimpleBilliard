<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'KrProgressLogEntity');

use Goalous\Enum\DataType\DataType as DataType;

/**
 * KrProgressLog Model
 *
 * @property ActionResult $ActionResult
 */
class KrProgressLog extends AppModel
{
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'ActionResult',
    ];

    public $modelConversionTable = [
        'team_id'          => DataType::INT,
        'goal_id'          => DataType::INT,
        'user_id'          => DataType::INT,
        'key_result_id'    => DataType::INT,
        'action_result_id' => DataType::INT,
        'value_unit'       => DataType::INT,
        'before_value'     => DataType::FLOAT,
        'change_value'     => DataType::FLOAT,
        'target_value'     => DataType::FLOAT,
    ];

    /**
     * KRに紐づく全てのログを論理削除
     *
     * @param int $krId
     *
     * @return bool
     */
    function deleteByKrId(int $krId): bool
    {
        $now = time();
        return $this->updateAll(
            [
                'KrProgressLog.modified' => $now,
                'KrProgressLog.deleted'  => $now,
                'KrProgressLog.del_flg'  => true
            ],
            ['KrProgressLog.key_result_id' => $krId, 'KrProgressLog.del_flg' => false]
        );
    }

    public function getByActionResultId(int $actionResultId): ?KrProgressLogEntity
    {
        $option = [
            'conditions' => [
                'action_result_id' => $actionResultId
            ]
        ];

        /** @var KrProgressLogEntity $result */
        $result = $this->useType()->useEntity()->find('first', $option);

        if (empty($result)) {
            return null;
        }

        return $result;
    }
}
