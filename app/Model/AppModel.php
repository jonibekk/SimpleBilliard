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
     * Entity class to encapsulate a query result
     *
     * @var BaseEntity
     */
    private $entityWrapperClass;

    /**
     * List of functions that will be executed on resulting array
     *
     * @var array
     */
    protected $postProcessFunctions = [];

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
    )
    {
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
     * @param bool $checkDeleted
     * @param int|null $teamId
     *
     * @return bool
     */
    public function exists($id = null, bool $checkDeleted = false, ?int $teamId = null)
    {
        if ($id === null) {
            $id = $this->getID();
        }

        if ($id === false) {
            return false;
        }

        $conditions = [
            'conditions' => [
                $this->alias . '.' . $this->primaryKey => $id
            ],
            'recursive'  => -1,
            'callbacks'  => false
        ];

        if (!empty($checkDeleted)) {
            $conditions['conditions']['del_flg'] = false;
        }

        if ($teamId !== null) {
            $conditions['conditions']['team_id'] = $teamId;
        }

        return (bool)$this->find('count', $conditions);
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
    ): bool
    {
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

    /**
     * Override save() function. Do post-processing
     *
     * @param null  $data
     * @param bool  $validate
     * @param array $fieldList
     *
     * @return array|mixed
     * @throws Exception
     */
    public function save($data = null, $validate = true, $fieldList = array())
    {
        //parent::save delete the postProcessFunctions array
        $functions = $this->postProcessFunctions;

        $result = parent::save($data, $validate, $fieldList);

        $this->postProcessFunctions = $functions;

        if (is_array($result)) {
            $result = $this->postProcess($result);
        }

        return $result;
    }

    /**
     * Override saveAll() function. Do post-processing
     *
     * @param array  $data
     * @param array  $opotions
     *
     * @return bool
     * @throws Exception
     */
    public function saveAll($data = array(), $options = array())
    {
        $functions = $this->postProcessFunctions;

        $result = parent::saveAll($data, $options);

        $this->postProcessFunctions = $functions;

        if (is_array($result)) {
            $result = $this->postProcess($result);
        }

        return $result;
    }
    /**
     * Override afterFind(). Will process find() result
     *
     * @param mixed $results
     * @param bool  $primary
     *
     * @return array|mixed
     */
    public function afterFind($results, $primary = false)
    {
        $result = parent::afterFind($results, $primary);

        if (is_array($result) && $primary) {
            $result = $this->postProcess($result);
        }

        return $result;
    }

    /**
     * Execute all registered function on result array after find() or save()
     *
     * @param array $data
     *
     * @return array | BaseEntity
     */
    private function postProcess($data = [])
    {
        foreach ($this->postProcessFunctions as $callable) {
            if (!is_callable($callable)) {
                throw new RuntimeException("Inserted element is not a callable");
            }
            $data = $callable($data);
        }

        //Reset functions after each processing
        $this->postProcessFunctions = [];

        return $data;
    }

    /**
     * Add entity conversion process to post process
     *
     * @return AppModel
     */
    public function useEntity(): self
    {
        $this->postProcessFunctions['entity'] = function (array $data) {
            return $this->convertEntity($data);
        };

        return $this;
    }

    public function useReset(): self
    {
        $this->postProcessFunctions = [];
        return $this;
    }

    /**
     * Add type conversion process to post process
     *
     * @return AppModel
     */
    public function useType(): self
    {
        $this->postProcessFunctions['type'] = function ($data) {
            return $this->convertType($data);
        };

        return $this;
    }

    /**
     * Convert data from string to configured ones
     *
     * @param array | BaseEntity $data
     *
     * @return array | BaseEntity
     */
    protected function convertType($data)
    {
        $conversionTable = array_merge($this->defaultConversionTable, $this->modelConversionTable);

        $this->traverseArray($data, $conversionTable);

        return $data;
    }

    /**
     * Recursively traverse an array and convert their data types from string to configured one
     *
     * @param array | BaseEntity $data
     * @param array              $conversionTable
     */
    private function traverseArray(&$data, array $conversionTable)
    {
        foreach ($data as $key => $value) {
            if (is_string($value) && key_exists($key, $conversionTable)) {
                switch ($conversionTable[$key]) {
                    case (DataType::INT):
                        $data[$key] = intval($value);
                        break;
                    case (DataType::BOOL):
                        $data[$key] = boolval($value);
                        break;
                    case (DataType::JSON):
                        $data[$key] = json_decode($value, true);
                        break;
                }
            }
            if (is_numeric($key) || is_array($value)) {
                $this->traverseArray($data[$key], $conversionTable);
            }
        }
    }

    /**
     * Convert an array to its respective Entity wrapper class
     *
     * @param array  $data
     * @param string $className Entity wrapper class name
     *
     * @return BaseEntity[] | BaseEntity
     */
    protected function convertEntity(array $data, string $className = null)
    {
        if (empty($this->entityWrapperClass)) {
            $this->initializeEntityClass($className);
        }
        if (empty($data)) {
            return null;
        }
        if (!is_int(array_keys($data)[0])) {
            return new $this->entityWrapperClass($data);
        }
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = new $this->entityWrapperClass($value);
        }
        return $result;
    }

    /**
     * Initialize the wrapper class name. By default will use the Model's name + 'Entity'
     * e.g. User -> UserEntity
     *
     * @param string|null $className
     */
    protected function initializeEntityClass(string $className = null)
    {
        if (empty($className)) {
            $className = get_class($this) . 'Entity';
        }

        $object = new $className;

        if (!($object instanceof BaseEntity)) {
            throw new RuntimeException("Entity class does not exist :" . $className);
        }

        $this->entityWrapperClass = $object;
    }

    /**
     * Get an entity based on its primary id
     *
     * @param int      $id             Primary id of the model
     * @param string[] $columns        Specify which columns to query from database
     * @param bool     $excludeDeleted Check del_flg
     *
     * @return BaseEntity | null
     */
    public final function getEntity(int $id, array $columns = [], bool $excludeDeleted = true)
    {
        $conditions = [
            'conditions' => [
                $this->alias . '.id' => $id
            ]
        ];
        if ($excludeDeleted) {
            $conditions['conditions']['del_flg'] = false;
        }
        if (!empty($columns)) {
            $conditions['fields'] = $columns;
        }

        /** @var BaseEntity $return */
        $return = $this->useType()->useEntity()->find('first', $conditions);

        if (empty($return)) return null;

        return $return;
    }

    /**
     * Soft delete all entries by their team id
     *
     * @param int $teamId
     */
    public function softDeleteAllByTeamId(int $teamId)
    {
        if (!$this->softDeleteAll([$this->alias . '.team_id' => $teamId], false)) {
            throw new RuntimeException();
        }
    }

}
