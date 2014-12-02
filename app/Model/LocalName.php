<?php
App::uses('AppModel', 'Model');

/**
 * LocalName Model
 *
 * @property User $User
 */
class LocalName extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'language' => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        //        'first_name' => [
        //            'notEmpty' => [
        //                'rule' => ['notEmpty'],
        //            ],
        //        ],
        //        'last_name'  => [
        //            'notEmpty' => [
        //                'rule' => ['notEmpty'],
        //            ],
        //        ],
        'del_flg'  => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User'
    ];

    /**
     * ローカル名を1件返却
     *
     * @param $uid
     * @param $lang
     *
     * @return array|null
     */
    public function getName($uid, $lang)
    {
        $options = [
            'conditions' => [
                'user_id'  => $uid,
                'language' => $lang
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    /**
     * ローカル名を複数件返却(user_idをkeyにした配列)ただし、見つからなかったuser_idはkeyのみで空の配列
     * 例：
     * [1] => Array
     * (
     * [id] => 1
     * [user_id] => 1
     * [language] => jpn
     * [first_name] => テスト
     * [last_name] => アドミン
     * [del_flg] =>
     * [deleted] =>
     * [created] => 1415812609
     * [modified] => 1416935283
     * )
     *
     * @param $uids
     * @param $lang
     *
     * @return array|null
     */
    public function getNames($uids, $lang)
    {
        if (empty($uids)) {
            return [];
        }
        $options = [
            'conditions' => [
                'user_id'  => $uids,
                'language' => $lang
            ]
        ];

        $res = $this->find('all', $options);
        $res = Hash::combine($res, '{n}.LocalName.user_id', '{n}.LocalName');
        //IDが見つからなかったものは空の配列で格納する
        foreach ($uids as $uid) {
            if (!array_key_exists($uid, $res)) {
                $res[$uid] = [];
            }
        }
        return $res;
    }
}
