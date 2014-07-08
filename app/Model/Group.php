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
    public $validate = [
        'name'       => ['notEmpty' => ['rule' => ['notEmpty']]],
        'active_flg' => ['boolean' => ['rule' => ['boolean']]],
        'del_flg'    => ['boolean' => ['rule' => ['boolean']]],
    ];

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
