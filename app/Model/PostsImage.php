<?php
App::uses('AppModel', 'Model');

/**
 * PostsImage Model
 *
 * @property Post  $Post
 * @property Image $Image
 */
class PostsImage extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'post_id'  => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'image_id' => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'del_flg'  => array(
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
        'Post'  => array(
            'className'  => 'Post',
            'foreignKey' => 'post_id',
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
        )
    );
}
