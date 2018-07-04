<?php
App::uses('Model', 'Model');
App::uses('Sanitize', 'Utility');

/**
 * Application model for Cake.
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 * @method findById($id)
 * @method findByUserId($id)
 * @method findByEmail($email)
 * @method findByName($name)
 */

use Goalous\Enum\DataType\DataType as DataType;

class AppModel extends Model
{

    var $actsAs = [
        'ExtAddValidationRule',
        'ExtValidationErrorI18n',
        'ExtValidationPatterns',
        'SoftDeletable' => [
            'field'      => 'del_flg',
            'field_date' => 'deleted',
        ],
        'WithTeamId',
        'ExtContainable',
        'Trim',
    ];

    //全てのモデルでデフォルトで再起的にjoinするのをやめる。個別に指定する。
    public $recursive = -1;
    /**
     * 自分のユーザ情報を保持(コントローラ側からセッションの値をセットする)
     *
     * @var array
     */
    public $me = [];

    /**
     * 自分のuser_id
     *
     * @var int
     */
    public $my_uid = null;

    /**
     * 現在のチームID
     *
     * @var int
     */
    public $current_team_id = null;

    /**
     * save系のメソッドで扱うuser_id
     *
     * @var null
     */
    public $uid = null;
    /**
     * save系のメソッドで扱うteam_id
     *
     * @var null
     */
    public $team_id = null;

    public $support_lang_codes = [
        'jpn',
    ];

    /**
     * Flag whether data type conversion will be done
     *
     * @var bool
     */
    private $conversionFlag = false;

    /**
     * Default conversion table
     *
     * @var array
     */
    private $defaultConversionTable = [
        'id'       => DataType::INT,
        'created'  => DataType::INT,
        'modified' => DataType::INT,
        'deleted'  => DataType::INT,
        'del_flg'  => DataType::BOOL
    ];

    /**
     * Conversion table for model
     *
     * @var array
     */
    protected $modelConversionTable = [];

    public $model_key_map = [
        'key_result_id'    => 'KeyResult',
        'action_result_id' => 'ActionResult',
        'comment_id'       => 'Comment',
        'post_id'          => 'Post',
        'goal_id'          => 'Goal',
        'team_member_id'   => 'TeamMember',
        'circle_id'        => 'Circle',
        'evaluate_term_id' => 'Term',
        'team_id'          => 'Team',
        'user_id'          => 'User',
        'team_vision_id'   => 'TeamVision',
        'group_vision_id'  => 'GroupVision',
    ];

    /**
     * @param bool $id
     * @param null $table
     * @param null $ds
     */
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setSessionVariable();
    }

    function _setSessionVariable()
    {
        App::uses('CakeSession', 'Model/Datasource');
        $Session = new CakeSession();

        $this->me = $Session->read('Auth.User');
        $this->current_team_id = $Session->read('current_team_id');
        $this->my_uid = $Session->read('Auth.User.id');
        if ($Session->read('Auth.User.language')) {
            Configure::write('Config.language', $Session->read('Auth.User.language'));
        }
    }

    /**
     * トランザクション開始
     *
     * @deprecated
     * TODO: Delete this method and use TransactionManager
     */
    function begin()
    {
        $db = ConnectionManager::getDataSource($this->useDbConfig);
        $db->begin($this);
    }

    /**
     * トランザクションコミット
     *
     * @deprecated
     * TODO: Delete this method and use TransactionManager
     */
    function commit()
    {
        $db = ConnectionManager::getDataSource($this->useDbConfig);
        $db->commit($this);
    }

    /**
     * トランザクションロールバック
     *
     * @deprecated
     * TODO: Delete this method and use TransactionManager
     */
    function rollback()
    {
        $db = ConnectionManager::getDataSource($this->useDbConfig);
        $db->rollback($this);
    }

    /**
     * SoftDeletableBehaviorの為に追加
     *
     * @param string $id
     * @param bool   $cascade
     */
    function _deleteDependent($id, $cascade)
    {
        parent::_deleteDependent($id, $cascade);
    }

    /**
     * SoftDeletableBehaviorの為に追加
     *
     * @param string $id
     */
    function _deleteLinks($id)
    {
        parent::_deleteLinks($id);
    }

    /**
     * コールバックでデータ形式のゆらぎを統一する
     *
     * @param         $results
     * @param         $callback
     * @method $callback
     */
    public function dataIter(&$results, $callback)
    {

        if (!$isVector = isset($results[0])) {
            $results = array(
                $results
            );
        }

        $modeled = array_key_exists($this->alias, $results[0]);

        foreach ($results as &$value) {
            if (!$modeled) {
                $value = array(
                    $this->alias => $value
                );
            }

            $continue = $callback($value, $this);

            if (!$modeled) {
                $value = $value[$this->alias];
            }

            if (!is_null($continue) && !$continue) {
                break;
            }
        }

        if (!$isVector) {
            $results = $results[0];
        }
    }

    /**
     * 所有者かどうかのチェック
     *
     * @param null   $id
     * @param string $uid
     *
     * @return bool
     */
    public function isOwner($uid, $id = null)
    {
        if ($id === null) {
            $id = $this->getID();
        }

        if ($id === false) {
            return false;
        }

        return (bool)$this->find('count', array(
            'conditions' => array(
                $this->alias . '.' . $this->primaryKey => $id,
                $this->alias . '.' . 'user_id'         => $uid,
            ),
            'recursive'  => -1,
            'callbacks'  => false
        ));
    }

    public function isBelongCurrentTeam($id, $team_id)
    {
        $options = [
            'conditions' => [
                $this->alias . '.' . $this->primaryKey => $id,
                $this->alias . '.' . 'team_id'         => $team_id,
            ],
            'fields'     => [
                'id'
            ]
        ];
        if ($this->find('first', $options)) {
            return true;
        }
        return false;
    }

    /**
     * Generate token used by the user registration system
     *
     * @param int    $length Token Length
     * @param string $possible
     *
     * @return string
     */
    public function generateToken(
        $length = 22,
        $possible = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ) {
        $token = "";
        $i = 0;

        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            if (!stristr($token, $char)) {
                $token .= $char;
                $i++;
            }
        }
        return $token;
    }

    /**
     * トークンの期限を返却
     *
     * @param int $interval
     *
     * @return string
     */
    public function getTokenExpire($interval = TOKEN_EXPIRE_SEC_REGISTER)
    {
        return REQUEST_TIMESTAMP + $interval;
    }

    /**
     * ユーザIDをセット
     *
     * @param null $uid
     */
    public function setUid($uid = null)
    {
        if (!$uid) {
            $this->uid = $this->my_uid;
        } else {
            $this->uid = $uid;
        }
    }

    /**
     * チームIDをセット
     *
     * @param null $team_id
     */
    public function setTeamId($team_id = null)
    {
        if (!$team_id) {
            $this->team_id = $this->current_team_id;
        } else {
            $this->team_id = $team_id;
        }
    }

    /**
     * ユーザIDとチームIDをセット
     *
     * @param null $uid
     * @param null $team_id
     */
    public function setUidAndTeamId($uid = null, $team_id = null)
    {
        $this->setUid($uid);
        $this->setTeamId($team_id);
    }

    /**
     * 基底クラスのオーバーライド
     * (SoftDeletableのコールバックが実行されない為)
     *
     * @param null $id
     *
     * @return bool
     */
    public function exists($id = null)
    {
        if ($id === null) {
            $id = $this->getID();
        }

        if ($id === false) {
            return false;
        }

        return (bool)$this->find('count', array(
            'conditions' => array(
                $this->alias . '.' . $this->primaryKey => $id
            ),
            'recursive'  => -1,
            //TODO callbacksはtrueに変更する。影響範囲がかなりデカイので慎重にテストした上で行う。
            'callbacks'  => false
        ));
    }

    /**
     * this method is about calling find method without WithTeamIdBehavior.
     *
     * @param string $type
     * @param array  $options
     *
     * @return array|null
     */
    public function findWithoutTeamId($type = 'first', $options = [])
    {
        $enable_containable = false;
        $enable_with_team_id = false;
        if ($this->Behaviors->loaded('ExtContainable')) {
            $enable_containable = true;
        }
        if ($this->Behaviors->loaded('WithTeamId')) {
            $enable_with_team_id = true;
        }
        if ($enable_with_team_id) {
            $this->Behaviors->disable('WithTeamId');
        }
        if ($enable_containable) {
            $this->Behaviors->load('ExtContainable', array('with_team_id' => false));
        }
        $res = $this->find($type, $options);
        if ($enable_with_team_id) {
            $this->Behaviors->enable('WithTeamId');
        }
        if ($enable_containable) {
            $this->Behaviors->load('ExtContainable', array('with_team_id' => true));
        }
        return $res;
    }

    /**
     * bulk insert method.
     * if $add_date is true then, adding save fields that `modified` and `created`.
     * usage about $update_counter_cache_fields:
     * $updateCounterCacheFields = [
     * 'foreign key', 'foreign key'
     * ];
     *
     * @param array $data
     * @param bool  $addDate
     * @param array $updateCounterCacheFields
     * @param int   $chunkSize to chunk records of saving data for reducing query size at once.
     *
     * @return bool
     */
    public function bulkInsert(
        array $data,
        bool $addDate = true,
        array $updateCounterCacheFields = [],
        int $chunkSize = 100
    ): bool {
        if (empty($data) || empty($data[0])) {
            return false;
        }
        $data = Sanitize::clean($data);
        if (isset($data[0][$this->name])) {
            $fields = array_keys($data[0][$this->name]);
        } else {
            $fields = array_keys($data[0]);
        }
        if ($addDate) {
            if (!in_array('modified', $fields)) {
                $fields[] = 'modified';
            }
            if (!in_array('created', $fields)) {
                $fields[] = 'created';
            }

            foreach ($data as $k => $v) {
                if (isset($v[$this->name])) {
                    if (empty($data[$k][$this->name]['modified'])) {
                        $data[$k][$this->name]['modified'] = REQUEST_TIMESTAMP;
                    }
                    if (empty($data[$k][$this->name]['created'])) {
                        $data[$k][$this->name]['created'] = REQUEST_TIMESTAMP;
                    }
                } else {
                    if (empty($data[$k]['modified'])) {
                        $data[$k]['modified'] = REQUEST_TIMESTAMP;
                    }
                    if (empty($data[$k]['created'])) {
                        $data[$k]['created'] = REQUEST_TIMESTAMP;
                    }
                }
            }
        }
        $chunkedDatas = array_chunk($data, $chunkSize);
        foreach ($chunkedDatas as $chunkedData) {
            $valueArray = [];
            foreach ($chunkedData as $value) {
                $value = isset($value[$this->name]) ? $value[$this->name] : $value;
                $valueArray[] = "('" . implode('\',\'', $value) . "')";
            }
            $sql = "INSERT INTO "
                . $this->table . " (" . implode(', ', $fields) . ") VALUES "
                . implode(',', $valueArray);
            $ret = $this->query($sql);
            foreach ($updateCounterCacheFields as $field) {
                foreach ($chunkedData as $value) {
                    $value = isset($value[$this->name][$field]) ? $value[$this->name][$field] : $value[$field];
                    $this->updateCounterCache([$field => $value]);
                }
            }
        }
        return true;
    }

    /**
     * キャッシュ用のキーを返却
     *
     * @param string     $name
     * @param bool|false $is_user_data
     * @param null       $user_id
     * @param bool       $with_team_id
     *
     * @return string
     */
    function getCacheKey($name, $is_user_data = false, $user_id = null, $with_team_id = true)
    {
        if ($with_team_id) {
            $name .= ":team:" . $this->current_team_id;
        }
        if ($is_user_data) {
            if (!$user_id) {
                $user_id = $this->my_uid;
            }
            $name .= ":user:" . $user_id;
        }
        return $name;
    }

    function concatValidationErrorMsg($break_line = true)
    {
        $msg_arr = [];
        foreach ($this->validationErrors as $field) {
            foreach ($field as $msg) {
                $msg_arr[] = $msg;
            }
        }
        $delimiter = $break_line ? "\n" : " ";
        $concat_msg = "";
        foreach ($msg_arr as $msg) {
            $concat_msg .= $msg;
            if ($msg !== end($msg_arr)) {
                $concat_msg .= $delimiter;
            }
        }
        return $concat_msg;
    }

    /**
     * TODO:モデルで行う処理では無いので将来的に他の適切な場所に移行すること
     * 画像のurlを取得
     * - パラメタ $photoStyles は取得するサムネイルの名前を指定。Uploadビヘイビアで設定済みのものが有効。指定しない場合はすべて取得する.
     * - パラメタ $photoStylesで存在しないスタイルを指定された場合はスキップ。
     *
     * @param array  $data
     * @param string $modelName
     * @param array  $photoStyles
     *
     * @return array
     */
    function attachImgUrl($data, $modelName, $photoStyles = [])
    {
        $upload = new UploadHelper(new View());
        $defaultStyles = array_keys($this->actsAs['Upload']['photo']['styles']);
        if (empty($photoStyles)) {
            $photoStyles = $defaultStyles;
            $photoStyles[] = 'original';
        }
        foreach ($photoStyles as $style) {
            if ($style != 'original' && !in_array($style, $defaultStyles)) {
                continue;
            }
            $data["{$style}_img_url"] = $upload->uploadUrl($data,
                "$modelName.photo",
                ['style' => $style]);
        }
        return $data;
    }

    /**
     * バリデーションメッセージの展開
     * key:valueの形にして1フィールド1メッセージにする
     * TODO: 将来的にはService基底クラスに移行する
     *
     * @param $validationErrors
     *
     * @return array
     */
    function _validationExtract($validationErrors)
    {
        $res = [];
        if (empty($validationErrors)) {
            return $res;
        }
        if ($validationErrors === true) {
            return $res;
        }
        foreach ($validationErrors as $k => $v) {
            $res[$k] = $v[0];
        }
        return $res;
    }

    /**
     * idによる単体情報取得
     *
     * @param $id
     *
     * @return array
     */
    function getById($id, array $fields = [])
    {
        if (empty($id)) {
            return [];
        }
        $ret = $this->findById($id, $fields);
        return reset($ret);
    }

    /**
     * find single record by teamId
     *
     * @param int   $teamId
     * @param array $fields
     *
     * @return array
     */
    function getByTeamId(int $teamId, array $fields = [])
    {
        $ret = $this->findByTeamId($teamId, $fields);
        return reset($ret);
    }

    /**
     * 論理削除
     *
     * @param int  $id
     * @param bool $withTeamId
     *
     * @return bool
     */
    function softDelete(int $id, bool $withTeamId = true): bool
    {
        if (empty($id)) {
            return false;
        }
        $this->id = $id;
        $data = [
            $this->alias . '.' . 'del_flg'  => true,
            $this->alias . '.' . 'deleted'  => REQUEST_TIMESTAMP,
            $this->alias . '.' . 'modified' => REQUEST_TIMESTAMP,
        ];
        $condition = [
            $this->alias . '.' . 'id'      => $id,
            $this->alias . '.' . 'del_flg' => false,
        ];

        if ($withTeamId) {
            $condition = am($condition, [
                $this->alias . '.' . 'team_id' => $this->current_team_id,
            ]);
        }
        // saveだと削除済みのレコードも更新してしまうため、updateAllを使用
        $ret = $this->updateAll($data, $condition);
        return !empty($ret);
    }

    /**
     * 論理削除
     *
     * @param array $condition
     * @param bool  $withTeamId
     *
     * @return bool
     */
    function softDeleteAll(array $condition, bool $withTeamId = true): bool
    {
        if (empty($condition)) {
            return false;
        }
        $condition = am($condition, [
            $this->alias . '.' . 'del_flg' => false,
        ]);

        if ($withTeamId) {
            $condition = am($condition, [
                $this->alias . '.' . 'team_id' => $this->current_team_id,
            ]);
        }

        $ret = $this->updateAll([
            $this->alias . '.' . 'del_flg'  => true,
            $this->alias . '.' . 'deleted'  => REQUEST_TIMESTAMP,
            $this->alias . '.' . 'modified' => REQUEST_TIMESTAMP,
        ], $condition);

        return !empty($ret);
    }

    public function beforeFind($query)
    {
        if (Hash::get($query, 'conversion', false)) {
            $this->conversionFlag = true;
        }
        if (Hash::get($query, 'object', false)) {
            $this->objectFlag = true;
        }

        return parent::beforeFind($query);
    }

    public function afterFind($results, $primary = false)
    {
        if ($this->conversionFlag) {
            $results = $this->convertType($results);
        }
        if ($this->objectFlag) {
            $results = $this->convertObject($results);
        }

        return parent::afterFind($results, $primary);
    }

    /**
     * Convert data from string to configured ones
     *
     * @param array $data
     *
     * @return array
     */
    private function convertType(array $data): array
    {
        $conversionTable = array_merge($this->defaultConversionTable, $this->modelConversionTable);

        $this->traverseArray($data, $conversionTable);

        return $data;
    }

    /**
     * Recursively traverse an array and convert their data types from string to configured one
     *
     * @param array $data
     * @param array $conversionTable
     */
    private function traverseArray(array &$data, array $conversionTable)
    {
        foreach ($data as $key => &$value) {

            if (is_string($value) && key_exists($key, $conversionTable)) {
                switch ($conversionTable[$key]) {
                    case (DataType::INT):
                        $data[$key] = intval($value);
                        break;
                    case (DataType::BOOL):
                        $data[$key] = boolval($value);
                        break;
                }
            }
            if (is_numeric($key) || is_array($value)) {
                $this->traverseArray($value, $conversionTable);
            }
        }
    }

    protected $objectFlag = false;

    /** @var BaseObject */
    private $encapsulatorObject;

    /**
     * @param array $data
     *
     * @return array | BaseObject
     */
    protected function convertObject(array $data)
    {
        if (empty($this->encapsulatorObject)) {
            $this->initializeEncapsulator();
        }
        if (empty($data)) {
            return null;
        }
        if (!is_int(array_keys($data)[0])) {
            return new $this->encapsulatorObject($data);
        }
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = new $this->encapsulatorObject($value);
        }
        return $result;
    }

    protected function setEncapsulator(string $className)
    {
        $this->initializeEncapsulator($className);
    }

    protected function initializeEncapsulator(string $className = null)
    {
        if (empty($className)) {
            $className = get_class($this) . 'Object';
        }

        $object = new $className;

        if (!($object instanceof BaseObject)) {
            throw new RuntimeException();
        }

        $this->encapsulatorObject = $object;
    }
}
