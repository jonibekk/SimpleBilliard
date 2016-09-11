<?php
App::uses('AppModel', 'Model');

/**
 * Label Model
 *
 * @property Team $Team
 */
class Label extends AppModel
{

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'    => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
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
        'Team',
    ];

    public function getListWithGoalCount()
    {
        $res = $this->find('all', [
            'fields' => [
                'id',
                'name',
                'goal_label_count',
            ]
        ]);
        return $res;
    }
}
