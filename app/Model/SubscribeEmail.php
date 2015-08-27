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
    public $validate = [
        'email'   => [
            'email' => [
                'rule' => ['email'],
            ],
        ],
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * @param $postData
     *
     * @return mixed
     * @throws Exception
     */
    function add($postData)
    {
        if (!$email = viaIsSet($postData['SubscribeEmail']['email'])) {
            throw new RuntimeException(__d('gl', "送信されたデータが正しくありません。"));
        }
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        if ($this->findByEmail($email)) {
            throw new RuntimeException(__d('gl', "既に登録済のメールアドレスです。"));
        }
        return $this->save($postData);
    }
}
