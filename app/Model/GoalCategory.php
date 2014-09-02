<?php
App::uses('AppModel', 'Model');

/**
 * GoalCategory Model
 *
 * @property Team $Team
 * @property Goal $Goal
 */
class GoalCategory extends AppModel
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

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Goal',
    ];

    function getCategoryList()
    {
        $res = $this->find('list', ['conditions' => ['team_id' => $this->current_team_id]]);
        if (empty($res)) {
            $this->saveDefaultCategory();
            $res = $this->find('list', ['conditions' => ['team_id' => $this->current_team_id]]);
        }
        return $res;
    }

    function saveDefaultCategory()
    {
        $data = [
            [
                'name'    => __d('gl', "職務"),
                'team_id' => $this->current_team_id,
            ],
            [
                'name'    => __d('gl', "成長"),
                'team_id' => $this->current_team_id,
            ],
        ];
        $res = $this->saveAll($data);
        return $res;
    }

}
