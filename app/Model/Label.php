<?php
App::uses('AppModel', 'Model');

/**
 * Label Model
 *
 * @property Team      $Team
 * @property GoalLabel $GoalLabel
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
            'maxLength' => [
                'rule' => ['maxLength', 128]
            ],
            'notEmpty'  => [
                'rule'     => ['notEmpty'],
                'required' => 'create',
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

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'GoalLabel',
    ];

    /**
     * タグのリストをゴール件数とともに返す
     * このデータはキャッシュされている
     *
     * @return array
     */
    public function getListWithGoalCount()
    {
        $res = Cache::read($this->getCacheKey(CACHE_KEY_LABEL), 'team_info');
        if ($res !== false) {
            return $res;
        }
        $res = $this->find('all', [
            'fields' => [
                'id',
                'name',
                'goal_label_count',
            ]
        ]);
        Cache::write($this->getCacheKey(CACHE_KEY_LABEL), $res, 'team_info');
        return $res;
    }
}
