<?php
App::uses('AppModel', 'Model');

/**
 * RecoveryCode Model
 *
 * @property User $User
 */
class RecoveryCode extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'code'          => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'available_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'       => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
    ];

    public function beforeSave($options = [])
    {
        // code カラムを暗号化
        if (isset($this->data[$this->alias]['code'])) {
            list($encrypted_data, $salt) = $this->_encrypt($this->data[$this->alias]['code']);
            $this->data[$this->alias]['code'] = base64_encode($salt . $encrypted_data);
        }
        return true;
    }

    public function afterFind($results, $primary = false)
    {
        // code カラムを復号化
        foreach ($results as $k => $v) {
            if (isset($v[$this->alias]['code']) && $v[$this->alias]['code']) {
                $data = base64_decode($v[$this->alias]['code']);
                $salt = substr($data, 0, 16);
                $encrypted_data = substr($data, 16);
                $results[$k][$this->alias]['code'] = $this->_decrypt($encrypted_data, $salt);
            }
        }
        return $results;
    }

    /**
     * 指定ユーザーのリカバリコードを全て返す。
     * 使用済みのコードも含まれる。
     * 補足：
     * available_flg == 1 のものが、現在有効なコード（画面に表示されるコード）
     * その中で used !== NULL のものが、使用済コード
     * available_flg == 0 のものは、無効になったコード
     * （テーブル掃除による物理削除を防ぐため、del_flg でなく、available_flg で判別する）
     *
     * @param $user_id
     *
     * @return array
     */
    public function getAll($user_id)
    {
        $options = [
            'conditions' => [
                'RecoveryCode.user_id'       => $user_id,
                'RecoveryCode.available_flg' => true,
            ],
            'order'      => ['RecoveryCode.id' => 'ASC'],
        ];
        return $this->find('all', $options);
    }

    /**
     * 指定ユーザーのリカバリコードを全て利用不可にする
     *
     * @param $user_id
     *
     * @return bool 成功時 true
     */
    public function invalidateAll($user_id)
    {
        return $this->updateAll(['RecoveryCode.available_flg' => false],
            ['RecoveryCode.user_id' => $user_id]);
    }

    /**
     * 指定ユーザーの現在のリカバリコードを全て利用不可にし、新しいコードを登録する
     *
     * @param $user_id
     *
     * @return bool 成功時 true
     */
    public function regenerate($user_id)
    {
        $this->begin();
        $res = [];
        $res[] = $this->invalidateAll($user_id);
        for ($i = 0; $i < 10; $i++) {
            $this->create();
            $res[] = $this->save([
                'user_id'       => $user_id,
                'code'          => $this->_generateCode(),
                'available_flg' => true
            ]);
        }
        if (array_search(false, $res) !== false) {
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * $code が指定したユーザーの現在利用可能なコードに含まれていればそのデータを返す
     *
     * @param $user_id
     * @param $code
     *
     * @return array|bool
     */
    public function findUnusedCode($user_id, $code)
    {
        $recovery_codes = $this->User->RecoveryCode->getAll($user_id);
        foreach ($recovery_codes as $v) {
            // 使用済み
            if ($v['RecoveryCode']['used']) {
                continue;
            }

            // 使用出来るコードが見つかった場合
            if ($v['RecoveryCode']['code'] === $code) {
                return $v;
            }
        }
        // 使用出来るコードが見つからなかった場合
        return false;
    }

    /**
     * コードを使用済にする
     *
     * @param $id
     *
     * @return bool
     */
    public function useCode($id)
    {
        $recovery_code = $this->findById($id);
        if (!$recovery_code) {
            return false;
        }
        $this->id = $recovery_code['RecoveryCode']['id'];
        $res = $this->saveField('used', REQUEST_TIMESTAMP);
        return $res ? true : false;
    }

    /**
     * リカバリコードを生成する
     *
     * @return string
     */
    protected function _generateCode()
    {
        while ($code = $this->generateToken(8, '0123456789abcdefghijklmnopqrstuvwxyz')) {
            // 数字と英字が混ざっているか確認
            // （テストカバレッジが毎回同じになるように書く）
            if (!(strlen(str_replace(range(0, 9), '', $code, $count)) && $count)) {
                continue;
            }
            break;
        }
        return $code;
    }

    /**
     * $data を暗号化する
     *
     * @param string $data 暗号化するデータ
     *
     * @return array
     * @see http://blog.ohgaki.net/encrypt-decrypt-using-openssl
     */
    protected function _encrypt($data)
    {
        $secret = substr(Configure::read('Security.salt'), -16);
        $salt = openssl_random_pseudo_bytes(16);
        list($key, $ivec) = $this->_makeKeyAndIV($secret, $salt);
        $encrypted_data = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $ivec);
        return [$encrypted_data, $salt];
    }

    /**
     * データを複合化する
     *
     * @param string $encrypted_data 暗号化されたデータ
     * @param string $salt           ソルト
     *
     * @return string
     */
    protected function _decrypt($encrypted_data, $salt)
    {
        $secret = substr(Configure::read('Security.salt'), -16);
        list($key, $ivec) = $this->_makeKeyAndIV($secret, $salt);
        return openssl_decrypt($encrypted_data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $ivec);
    }

    /**
     * $secret と $salt から暗号化用のキーと IV を作成して返す
     *
     * @param $secret
     * @param $salt
     *
     * @return array
     */
    protected function _makeKeyAndIV($secret, $salt)
    {
        $salted = '';
        $hash = '';
        while (strlen($salted) < 48) {
            $hash = hash('sha256', $hash . $secret . $salt, true);
            $salted .= $hash;
        }

        $key = substr($salted, 0, 32);
        $ivec = substr($salted, 32, 16);
        return [$key, $ivec];
    }
}
