<?php
App::uses('AppModel', 'Model');

/**
 * MessageFile Model
 *
 * @property Topic        $Topic
 * @property Message      $Message
 * @property AttachedFile $AttachedFile
 * @property Team         $Team
 */
class MessageFile extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'index_num' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                //'message' => 'Your custom message here',
                //'allowEmpty' => false,
                //'required' => false,
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'del_flg'   => array(
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
        'Topic'        => array(
            'className'  => 'Topic',
            'foreignKey' => 'topic_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'Message'      => array(
            'className'  => 'Message',
            'foreignKey' => 'message_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'AttachedFile' => array(
            'className'  => 'AttachedFile',
            'foreignKey' => 'attached_file_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'Team'         => array(
            'className'  => 'Team',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        )
    );
}
