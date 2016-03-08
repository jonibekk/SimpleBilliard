<?php
App::uses('AppModel', 'Model');

/**
 * EvaluateScore Model
 *
 * @property Team       $Team
 * @property Evaluation $Evaluation
 */
class EvaluateScore extends AppModel
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
        'name'        => [
            'isString'   => [
                'rule' => ['isString'],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 200]],
        ],
        'description' => [
            'isString'   => [
                'rule' => ['isString'],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 2000]],
        ],
        'index_num'   => [
            'maxLength' => ['rule' => ['maxLength', 5]],
            'numeric'   => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'     => [
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
        'Team'
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Evaluation'
    ];

    public function getScoreList($teamId)
    {
        $options = [
            'conditions' => [
                'team_id'    => $teamId,
                'active_flg' => true,
            ],
            'fields'     => [
                'id',
                'name',
            ],
            'order'      => [
                'index_num' => 'asc'
            ]
        ];
        $res = $this->find('list', $options);
        return [null => __("Please select")] + $res;
    }

    function getScore($teamId)
    {
        $options = [
            'conditions' => [
                'team_id'    => $teamId,
                'active_flg' => true,
            ],
            'order'      => [
                'index_num' => 'asc'
            ]
        ];
        $res = $this->find('all', $options);
        $res = ['EvaluateScore' => Hash::extract($res, '{n}.EvaluateScore')];
        return $res;
    }

    function saveScores($datas, $team_id)
    {
        if (empty($datas)) {
            return false;
        }
        $datas = Hash::insert($datas, '{n}.team_id', $team_id);
        $res = $this->saveAll($datas);
        return $res;
    }

    function setToInactive($id)
    {
        $this->id = $id;
        return $this->saveField('active_flg', false);
    }

}
