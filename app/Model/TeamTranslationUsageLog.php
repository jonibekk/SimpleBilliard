<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'TeamTranslationUsageLogEntity');

use Goalous\Enum\DataType\DataType as DataType;
use Respect\Validation\Validator as Validator;

class TeamTranslationUsageLog extends AppModel
{
    public $modelConversionTable = [
        'team_id' => DataType::INT
    ];

    /**
     * Get latest log for that team
     *
     * @param int $teamId
     *
     * @return TeamTranslationUsageLogEntity | null
     */
    public function getLatestLog(int $teamId)
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId
            ],
            'order'     => [
                'TeamTranslationUsageLog.id DESC'
            ]
        ];

        /** @var TeamTranslationUsageLogEntity $queryResult */
        $queryResult = $this->useType()->useEntity()->find('first', $option);

        return $queryResult;
    }

    /**
     * Save log data
     *
     * @param int    $teamId    Team Id
     * @param string $startDate Start date of the log
     * @param string $endDate   End date of the log
     * @param string $logJson   Log content. JSON-formatted string
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function saveLog(int $teamId, string $startDate, string $endDate, string $logJson)
    {
        if (!Validator::json()->validate($logJson)) {
            throw new InvalidArgumentException('Invalid string format for log');
        }

        $newData = [
            'team_id'         => $teamId,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'translation_log' => $logJson
        ];

        $this->create();
        $this->save($newData, false);
    }
}