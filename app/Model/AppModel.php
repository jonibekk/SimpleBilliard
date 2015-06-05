<?php
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 *                @method findById()
 *                @method findByUserId()
 *                @method findByEmail()
 */
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
        'eng',
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

    }

    /**
     * トランザクション開始
     */
    function begin()
    {
        $db = ConnectionManager::getDataSource($this->useDbConfig);
        $db->begin($this);
    }

    /**
     * トランザクションコミット
     */
    function commit()
    {
        $db = ConnectionManager::getDataSource($this->useDbConfig);
        $db->commit($this);
    }

    /**
     * トランザクションロールバック
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
     * @param int $length Token Length
     *
     * @return string
     */
    public function generateToken($length = 22)
    {
        $possible = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
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
        }
        else {
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
        }
        else {
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
            'callbacks'  => false
        ));
    }

    /**
     * this method is about calling find method without WithTeamIdBehavior.
     *
     * @param string $type
     * @param array  $query
     *
     * @return array|null
     */
    public function findWithoutTeamId($type = 'first', $query = [])
    {
        $enable_containable = false;
        if ($this->Behaviors->loaded('ExtContainable')) {
            $enable_containable = true;
        }
        if (!$this->Behaviors->loaded('WithTeamId')) {
            throw new RuntimeException(__d('exception', "Please enable WithTeamIdBehavior!"));
        }
        $this->Behaviors->disable('WithTeamId');
        if ($enable_containable) {
            $this->Behaviors->load('ExtContainable', array('with_team_id' => false));
        }
        $res = $this->find($type, $query);
        $this->Behaviors->enable('WithTeamId');
        if ($enable_containable) {
            $this->Behaviors->load('ExtContainable', array('with_team_id' => true));
        }
        return $res;
    }

}
