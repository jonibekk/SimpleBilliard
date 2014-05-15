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
    public $validate = [
        'user_id'    => ['uuid' => ['rule' => ['uuid']]],
        'team_id'    => ['uuid' => ['rule' => ['uuid']]],
        'name'       => ['notEmpty' => ['rule' => ['notEmpty']]],
        'active_flg' => ['boolean' => ['rule' => ['boolean']]],
        'del_flg'    => ['boolean' => ['rule' => ['boolean']]],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
        'Image',
    ];
}
