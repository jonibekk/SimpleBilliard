<?php
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 * Add your application-wide methods in the class below, your models
 * will inherit them.

 *
*@package       app.Model
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

}
