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
    public $belongsTo = array(
        'User' => array(
            'className'  => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Badge' => array(
            'className'    => 'Badge',
            'foreignKey'   => 'image_id',
            'dependent'    => false,
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'limit'        => '',
            'offset'       => '',
            'exclusive'    => '',
            'finderQuery'  => '',
            'counterQuery' => ''
        ),
        'Team'  => array(
            'className'    => 'Team',
            'foreignKey'   => 'image_id',
            'dependent'    => false,
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'limit'        => '',
            'offset'       => '',
            'exclusive'    => '',
            'finderQuery'  => '',
            'counterQuery' => ''
        )
    );

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Post' => array(
            'className'             => 'Post',
            'joinTable'             => 'posts_images',
            'foreignKey'            => 'image_id',
            'associationForeignKey' => 'post_id',
            'unique'                => 'keepExisting',
            'conditions'            => '',
            'fields'                => '',
            'order'                 => '',
            'limit'                 => '',
            'offset'                => '',
            'finderQuery'           => '',
        )
    );

}
