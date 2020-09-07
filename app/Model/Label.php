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
    // ゴールラベル登録の上限数
    const MAX_SAVE_GOAL_LABEL_COUNT = 20;

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
                'rule' => ['maxLength', 20]
            ],
            'notBlank'  => [
                'rule'     => ['notBlank'],
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
     * @param bool $isExistGoalLabel
     * @param bool $useCache
     *
     * @return array
     */
    public function getListWithGoalCount($isExistGoalLabel = true, $useCache = true)
    {
        if ($useCache) {
            $res = Cache::read($this->getCacheKey(CACHE_KEY_LABEL), 'team_info');
            if ($res !== false) {
                return $res;
            }
        }
        $option = [
            'fields'     => [
                'id',
                'name',
                'goal_label_count',
            ],
            'order'      => ['goal_label_count DESC'],
        ];
        if ($isExistGoalLabel) {
            $option['conditions'] = ['NOT' => ['goal_label_count' => 0]];
        }
        $res = $this->find('all', $option);

        Cache::write($this->getCacheKey(CACHE_KEY_LABEL), $res, 'team_info');
        return $res;
    }

    /**
     * タグのリストをゴール件数とともに返す
     * - 抽出条件はgoal_label_countが0以外のもの
     * - ソート条件はgoal_label_countの降順
     * - このデータはキャッシュされている
     *
     * @param $names
     *
     * @return array
     * @internal param bool $isExistGoalLabel
     * @internal param bool $useCache
     */
    public function findIdsByNames($names)
    {
        if (empty($names)) {
            return [];
        }
        $option = [
            'fields'     => [
                'id',
                'name',
                'goal_label_count',
            ],
            'conditions' => [
                'name' => $names,
                'team_id' => $this->current_team_id
            ]
        ];
        $ret = $this->find('all', $option);
        return Hash::extract($ret, '{n}.Label.id');
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
        if (empty($labelNames) || !is_array($labelNames)) {
            return __("Input is required.");
        }

        // ラベル数上限チェック
        if (count($labelNames) > self::MAX_SAVE_GOAL_LABEL_COUNT) {
            return __('Label must be at least %d count.', 20);
        }

        $labels = [];
        foreach ($labelNames as $labelName) {
            array_push($labels, ['name' => $labelName]);
        }

        // 複数レコードのバリデーション
        if (!$this->saveAll($labels, ['validate' => 'only'])) {
            // 最初のエラーメッセージのみを抽出
            return reset(Hash::flatten($this->validationErrors));
        }
        return "";
    }
}
