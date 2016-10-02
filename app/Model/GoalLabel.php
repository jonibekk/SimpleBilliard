<?php
App::uses('AppModel', 'Model');

/**
 * GoalLabel Model
 *
 * @property Team  $Team
 * @property Goal  $Goal
 * @property Label $Label
 */
class GoalLabel extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
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
    public $belongsTo = array(
        'Team',
        'Goal',
        'Label' => [
            "counterCache" => true,
            'counterScope' => ['GoalLabel.del_flg' => false],
            'type'         => 'INNER'
        ],
    );

    /**
     * ラベルの保存とゴール関連付け処理
     * 説明
     * - ゴールの新規保存、更新の両方で使用可能。
     * - すべてのラベルの関連を解除した上で再登録するのではなく差分だけ保存、関連削除する(レコード増えまくる問題の回避)
     * 処理
     * - すでにゴールが持っているラベルを取得
     * - 新規登録するラベルを抽出(すでに持っているラベルは除外)
     * - 関連を解除するラベルを抽出
     * - もし変更がない場合は何もせずここでreturn
     * - 解除対象のラベルの関連付けを解除
     * - 既存ラベル全件取得(key:label_id,value:name)
     * - 新規ラベルの抽出
     * - 新規ラベルの保存(及びゴール関連付け)
     * - まだ持っていない既存ラベルをゴールに関連づける
     * - ラベルのキャッシュを削除
     *
     * @param       $goalId
     * @param array $postedLabels
     *
     * @return bool
     */
    function saveLabels($goalId, $postedLabels)
    {
        if(!is_array($postedLabels)){
            return false;
        }

        //すでに持っているラベルを取得
        $goalLabelsExistList = $this->getLabelList($goalId);
        //関連付けるラベルを抽出(すでに持っているラベルは除外)
        $attachLabels = array_diff($postedLabels, $goalLabelsExistList);
        //関連を解除するラベルを抽出
        $detachLabels = array_diff($goalLabelsExistList, $postedLabels);
        //変更なしの場合は何もせずreturn
        if (empty($attachLabels) && empty($detachLabels)) {
            return true;
        }
        //追加対象のラベルの関連づけ
        $isSuccess = (bool)$this->attachLabels($goalId, $attachLabels);
        //解除対象のラベルの関連付けを解除
        $isSuccess = $isSuccess && (bool)$this->detachLabels($goalId, $detachLabels);
        //ラベルのキャッシュを削除
        Cache::delete($this->getCacheKey(CACHE_KEY_LABEL), 'team_info');
        return $isSuccess;
    }

    /**
     * ゴールが持っているラベルをkey:label_id, value:nameの形式で返す
     *
     * @param $goalId
     *
     * @return array|null
     */
    function getLabelList($goalId)
    {
        $res = $this->find('all', [
            'conditions' => ['GoalLabel.goal_id' => $goalId],
            'fields'     => ['GoalLabel.goal_id'],
            'contain'    => [
                'Label' => [
                    'fields' => ['Label.id', 'Label.name']
                ]
            ]
        ]);
        $res = Hash::combine($res, '{n}.Label.id', '{n}.Label.name');
        return $res;
    }

    /**
     * 登録データの作成:まだ存在しないラベルの保存及び関連づけ
     *
     * @param $goalId
     * @param $newLabels
     *
     * @return array
     */
    function _buildSaveDataLabelNotExists($goalId, $newLabels)
    {
        $newData = [];
        foreach ($newLabels as $name) {
            $newData[] = [
                'goal_id' => $goalId,
                'team_id' => $this->current_team_id,
                'Label'   => [
                    'name'    => $name,
                    'team_id' => $this->current_team_id,
                ]
            ];
        }
        return $newData;
    }

    /**
     * 登録データの作成:すでに存在しているラベルの関連づけ
     *
     * @param $goalId
     * @param $existLabelsNotAttached (key:label_id,value:name)
     *
     * @return array
     */
    function _buildSaveDataLabelExists($goalId, $existLabelsNotAttached)
    {
        $saveData = [];
        foreach ($existLabelsNotAttached as $labelId => $name) {
            $saveData[] = [
                'goal_id'  => $goalId,
                'label_id' => $labelId,
                'team_id'  => $this->current_team_id,
            ];
        }
        return $saveData;
    }

    /**
     * ラベルの関連づけを行う
     * - 存在しているラベルはゴールへの関連づけのみ行う。
     * - 存在していないラベルは新規保存及びゴールへの関連づけを行う。
     *
     * @param       $goalId
     * @param array $attachLabels
     *
     * @return bool
     */
    function attachLabels($goalId, $attachLabels)
    {
        if (empty($attachLabels)) {
            return true;
        }
        //既存ラベル全件取得(key:label_id,value:name)
        $allLabels = Hash::combine($this->Label->getListWithGoalCount(false, false), '{n}.Label.id', '{n}.Label.name');
        //新規ラベルの抽出
        $newLabels = array_diff($attachLabels, $allLabels);
        //まだ持っていない既存ラベルを抽出
        $existLabelsNotAttached = array_intersect($allLabels, $attachLabels);
        //saveデータをビルド
        $saveData = array_merge_recursive(
            $this->_buildSaveDataLabelExists($goalId, $existLabelsNotAttached),
            $this->_buildSaveDataLabelNotExists($goalId, $newLabels)
        );
        $this->create();
        $res = $this->saveAll($saveData, ['deep' => true]);
        return (bool)$res;
    }

    /**
     * ラベルの関連付けを解除する
     * - カウンターキャッシュ更新
     *
     * @param       $goalId
     * @param array $detachLabels key:label_id,val:name
     *
     * @return bool
     */
    function detachLabels($goalId, $detachLabels)
    {
        if (empty($detachLabels)) {
            return true;
        }
        $labelIds = array_keys($detachLabels);
        $this->deleteAll(['GoalLabel.goal_id' => $goalId, 'GoalLabel.label_id' => $labelIds]);
        foreach ($labelIds as $labelId) {
            $this->updateCounterCache(['label_id' => $labelId]);
        }
        return true;
    }

    function findByGoalId($goalId)
    {
        $res = $this->find('all', [
            'fields' => ['Label.id','Label.name'],
            'conditions' => [
                'GoalLabel.team_id' => $this->current_team_id,
                'GoalLabel.goal_id' => $goalId
            ],
            'joins' => [
                [
                    'type' => 'INNER',
                    'table' => 'labels',
                    'alias' => 'Label',
                    'conditions' => [
                        'GoalLabel.label_id = Label.id',
                    ]
                ],
            ]
        ]);
        return $res;
    }

}
