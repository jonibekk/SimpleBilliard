<?php
App::uses('AppModel', 'Model');

/**
 * Group Model
 *
 * @property Team        $Team
 * @property MemberGroup $MemberGroup
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
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'MemberGroup'
    ];

}
