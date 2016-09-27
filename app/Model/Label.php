<?php
App::uses('AppModel', 'Model');

/**
 * Label Model
 *
 * @property Team      $Team
 * @property GoalLabel $GoalLabel
 */
class Label extends AppModel
{

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'    => [
            'maxLength' => [
                'rule' => ['maxLength', 128]
            ],
            'notEmpty'  => [
                'rule'     => ['notEmpty'],
                'required' => 'create',
            ],
            'isUnique'  => [
                'rule' => ['isUnique', ['name', 'team_id'], false],
            ],
        ],
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
    public $belongsTo = [
        'Team',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'GoalLabel',
    ];

    /**
     * タグのリストをゴール件数とともに返す
     * - 抽出条件はgoal_label_countが0以外のもの
     * - ソート条件はgoal_label_countの降順
     * - このデータはキャッシュされている
     *
     * @return array
     */
    public function getListWithGoalCount()
    {
        $res = Cache::read($this->getCacheKey(CACHE_KEY_LABEL), 'team_info');
        if ($res !== false) {
            return $res;
        }
        $option = [
            'conditions' => ['NOT' => ['goal_label_count' => 0]],
            'fields'     => [
                'id',
                'name',
                'goal_label_count',
            ],
            'order'      => ['goal_label_count DESC'],
        ];
        $res = $this->find('all', $option);

        Cache::write($this->getCacheKey(CACHE_KEY_LABEL), $res, 'team_info');
        return $res;
    }

    /**
     * ラベルバリデーション
     *
     * @param $data
     *
     * @return array
     * @internal param $validationErrors
     */
    public function validationLabelNames($data)
    {
        $labelNames = Hash::get($data, 'labels');
        // 未入力チェック
        if (empty($labelNames)) {
            return __("Input is required.");
        }

        $labels = [];
        foreach ($labelNames as $labelName) {
            array_push($labels, ['name' => $labelName]);
        }
        $this->log(compact('labels'));
        // 複数レコードのバリデーション
        if (!$this->saveAll($labels, ['validate' => 'only'])) {
            // 最初のエラーメッセージのみを抽出
            return reset(Hash::flatten($this->validationErrors));
        }
        return "";
    }
}
