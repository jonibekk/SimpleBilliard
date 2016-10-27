<?php
App::uses('AppModel', 'Model');

/**
 * AppMeta Model
 */
class AppMeta extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'key_name' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'value'    => [
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

    /**
     * アプリのメタ情報を返す
     * getting app meta.
     * return array(e.g.):
     * array(
     * 'iOS_version' => '1.0.0',
     * 'iOS_install_url' => 'http://ios',
     * 'android_version' => '1.0.0',
     * 'android_install_url' => 'http://android'
     * )
     *
     * @return array
     */
    public function getMetas()
    {
        $res = [];
        foreach (Hash::extract($this->find('all'), '{n}.AppMeta') as $v) {
            $res[$v['key_name']] = $v['value'];
        }
        return $res;
    }
}
