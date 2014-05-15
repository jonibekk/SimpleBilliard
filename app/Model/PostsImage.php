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
    public $validate = [
        'post_id'  => ['uuid' => ['rule' => ['uuid'],],],
        'image_id' => ['uuid' => ['rule' => ['uuid'],],],
        'del_flg'  => ['boolean' => ['rule' => ['boolean'],],],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Post',
        'Image',
    ];
}
