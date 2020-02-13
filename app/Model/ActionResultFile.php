<?php
App::uses('AppModel', 'Model');

/**
 * ActionResultFile Model
 *
 * @property ActionResult $ActionResult
 * @property AttachedFile $AttachedFile
 * @property Team         $Team
 */
class ActionResultFile extends AppModel
{
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'ActionResult',
        'AttachedFile',
        'Team',
    ];
}
