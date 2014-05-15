<?php
App::uses('AppModel', 'Model');

/**
 * Badge Model
 *
 * @property User  $User
 * @property Team  $Team
 * @property Image $Image
 */
class Badge extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
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
    public $belongsTo = array(
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
        ),
        'Image' => array(
            'className'  => 'Image',
            'foreignKey' => 'image_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
    );
}
