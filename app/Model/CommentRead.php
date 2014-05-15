<?php
App::uses('AppModel', 'Model');

/**
 * CommentRead Model
 *
 * @property Comment $Comment
 * @property User    $User
 * @property Team    $Team
 */
class CommentRead extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'comment_id' => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'user_id'    => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'team_id'    => array(
            'uuid' => array(
                'rule' => array('uuid'),
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
    public $belongsTo = array(
        'Comment' => array(
            'className'  => 'Comment',
            'foreignKey' => 'comment_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'User'    => array(
            'className'  => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'Team'    => array(
            'className'  => 'Team',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        )
    );
}
