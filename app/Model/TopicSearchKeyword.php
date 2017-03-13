<?php
App::uses('AppModel', 'Model');

/**
 * TopicSearchKeyword Model

 */
class TopicSearchKeyword extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'keywords' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'del_flg'  => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
}
