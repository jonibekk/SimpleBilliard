<?php
App::uses('AppModel', 'Model');

/**
 * JobCategory Model
 *
 * @property Team       $Team
 * @property TeamMember $TeamMember
 */
class JobCategory extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'       => ['notEmpty' => ['rule' => ['notEmpty'],],],
        'active_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'del_flg'    => ['boolean' => ['rule' => ['boolean'],],],
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
        'TeamMember',
    ];

}
