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
