<?php
App::uses('AppModel', 'Model');

/**
 * GivenBadge Model
 *
 * @property User $User
 * @property User $GrantUser
 * @property Team $Team
 * @property Post $Post
 */
class GivenBadge extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'user_id'       => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'grant_user_id' => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'team_id'       => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'post_id'       => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'del_flg'       => array(
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
        'User',
        'GrantUser' => ['className' => 'User', 'foreignKey' => 'grant_user_id',],
        'Team',
        'Post',
    ];
}
