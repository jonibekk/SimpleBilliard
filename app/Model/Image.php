<?php
App::uses('AppModel', 'Model');

/**
 * Image Model
 *
 * @property User  $User
 * @property Badge $Badge
 * @property Team  $Team
 * @property Post  $Post
 */
class Image extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'user_id'        => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'type'           => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'item_file_name' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
            ),
        ),
        'del_flg'        => array(
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
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Badge',
        'Team',
    ];

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = [
        'Post' => ['unique' => 'keepExisting',],
    ];

}
