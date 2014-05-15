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
    public $validate = [
        'user_id'        => ['uuid' => ['rule' => ['uuid']]],
        'type'           => ['numeric' => ['rule' => ['numeric']]],
        'item_file_name' => ['notEmpty' => ['rule' => ['notEmpty']]],
        'del_flg'        => ['boolean' => ['rule' => ['boolean']]],
    ];

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
