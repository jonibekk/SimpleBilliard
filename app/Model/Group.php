<?php
App::uses('AppModel', 'Model');

/**
 * Group Model
 *
 * @property Team       $Team
 * @property Group      $ParentGroup
 * @property Group      $ChildGroup
 * @property TeamMember $TeamMember
 */
class Group extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'team_id'    => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'name'       => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
            ),
        ),
        'active_flg' => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'del_flg'    => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team',
        'ParentGroup' => ['className' => 'Group', 'foreignKey' => 'parent_id',]
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'ChildGroup' => ['className' => 'Group', 'foreignKey' => 'parent_id',],
        'TeamMember',
    ];

}
