<?php
App::uses('AppModel', 'Model');

/**
 * CommentMention Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class CommentMention extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'post_id' => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'user_id' => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'team_id' => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'del_flg' => array(
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
        'Post',
        'User',
        'Team',
    ];
}
