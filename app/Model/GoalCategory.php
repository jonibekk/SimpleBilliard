<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'GoalCategoryEntity');

/**
 * GoalCategory Model
 *
 * @property Team $Team
 * @property Goal $Goal
 */

use Goalous\Enum\DataType\DataType as DataType;

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
        'name'        => [
            'notBlank'  => [
                'rule' => ['notBlank'],
            ],
            'maxLength' => [
                'rule' => ['maxLength', 200],
            ],
        ],
        'description' => [
            'isString'  => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
            'maxLength' => [
                'rule' => ['maxLength', 2000],
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


    public $modelConversionTable = [
        'team_id'    => DataType::INT,
        'active_flg' => DataType::BOOL
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

    function getCategories($fields = [])
    {
        $options = [
            'conditions' => [
                'active_flg' => true,
            ],
        ];
        if (!empty($fields)) {
            $options['fields'] = $fields;
        }
        $res = $this->find('all', $options);
        if (empty($res)) {
            $this->saveDefaultCategory();
            $res = $this->find('all', $options);
        }
        return $res;
    }

    function saveDefaultCategory()
    {
        $data = [
            [
                'name'    => __("Undefined"),
                'team_id' => $this->current_team_id,
            ],
        ];
        $res = $this->saveAll($data);
        return $res;
    }

    function saveGoalCategories($datas, $team_id)
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
