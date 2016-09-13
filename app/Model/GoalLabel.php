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
            'type'         => ' INNER'
        ],
    );

    /**
     * @param       $goal_id
     * @param array $postedLabels
     *
     * @return bool
     */
    function saveLabels($goal_id, $postedLabels)
    {
        //すでに持っているラベルを取得
        $goalLabelsExistList = $this->getLabelList($goal_id);

        //新規登録するラベル(すでに持っているラベルは除外)
        $addLabels = array_diff($postedLabels, $goalLabelsExistList);

        //関連を解除するラベル
        $removeLabels = array_diff($goalLabelsExistList, $postedLabels);
        //変更なしの場合は何もせずreturn
        if (empty($addLabels) && empty($removeLabels)) {
            return true;
        }

        //ラベルの関連付けを解除
        if (!empty($removeLabels)) {
            $this->deleteAll(['GoalLabel.goal_id' => $goal_id, 'GoalLabel.label_id' => array_keys($removeLabels)]);
        }

        //既存ラベル(key:label_id,value:name)
        $labels = Hash::combine($this->Label->getListWithGoalCount(), '{n}.Label.id', '{n}.Label.name');

        //新規ラベルの抽出
        $newLabels = array_diff($addLabels, $labels);
        //新規ラベルの保存(ゴール関連付け)
        $this->saveNewLabelsWithGoal($goal_id, $newLabels);

        //まだ持っていない既存ラベルをゴールに関連づける
        $existLabelsNotHave = array_intersect($labels, $addLabels);
        $this->associateLabelsAndGoal($goal_id, $existLabelsNotHave);

        //ラベルのキャッシュを削除
        Cache::delete($this->getCacheKey(CACHE_KEY_LABEL), 'team_info');

        return true;
    }

    /**
     * ゴールが持っているラベルをkey:label_id, value:nameの形式で返す
     *
     * @param $goal_id
     *
     * @return array|null
     */
    function getLabelList($goal_id)
    {
        $res = $this->find('all', [
            'conditions' => ['GoalLabel.goal_id' => $goal_id],
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
     * まだ存在しないラベルを保存しかつゴールに関連付ける
     *
     * @param       $goal_id
     * @param array $newLabels
     *
     * @return bool|mixed
     */
    function saveNewLabelsWithGoal($goal_id, $newLabels)
    {
        if (empty($newLabels)) {
            return true;
        }

        $newData = [];
        foreach ($newLabels as $name) {
            $newData[] = [
                'goal_id' => $goal_id,
                'Label'   => ['name' => $name]
            ];
        }
        $this->create();
        $res = $this->saveAll($newData);
        return $res;
    }

    /**
     * すでに存在するラベルをゴールに関連付ける
     *
     * @param       $goal_id
     * @param array $existLabelsNotHave
     *
     * @return mixed
     */
    function associateLabelsAndGoal($goal_id, $existLabelsNotHave)
    {
        if (empty($existLabelsNotHave)) {
            return true;
        }

        $saveData = [];
        foreach ($existLabelsNotHave as $label_id => $name) {
            $saveData[] = [
                'goal_id'  => $goal_id,
                'label_id' => $label_id
            ];
        }
        $this->create();
        $res = $this->saveAll($saveData);
        return $res;
    }
}
