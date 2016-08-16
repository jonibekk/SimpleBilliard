<?php
App::uses('AppModel', 'Model');

/**
 * TopicMember Model
 *
 * @property Topic $Topic
 * @property User  $User
 * @property Team  $Team
 */
class TopicMember extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'del_flg' => array(
            'boolean' => array(
                'rule' => array('boolean'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
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
        'Topic' => array(
            'className'  => 'Topic',
            'foreignKey' => 'topic_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'User'  => array(
            'className'  => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'Team'  => array(
            'className'  => 'Team',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        )
    );
}
