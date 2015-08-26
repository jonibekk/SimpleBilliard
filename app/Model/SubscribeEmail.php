<?php
App::uses('AppModel', 'Model');

/**
 * SubscribeEmail Model
 */
class SubscribeEmail extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'email'   => array(
            'email' => array(
                'rule' => array('email'),
            ),
        ),
        'del_flg' => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
    );
}
