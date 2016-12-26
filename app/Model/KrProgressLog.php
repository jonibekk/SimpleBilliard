<?php
App::uses('AppModel', 'Model');

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


    /**
     * KRに紐づく全てのログを論理削除
     *
     * @param int $krId
     *
     * @return bool
     */
    function deleteByKrId(int $krId) : bool
    {
        $now = time();
        return $this->updateAll(
            [
                'KrProgressLog.modified' => $now,
                'KrProgressLog.deleted' => $now,
                'KrProgressLog.del_flg' => true
            ],
            ['KrProgressLog.key_result_id' => $krId, 'KrProgressLog.del_flg' => false]
        );
    }

}
