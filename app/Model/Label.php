<?php
App::uses('AppModel', 'Model');

/**
 * Label Model
 *
 * @property Team $Team
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
            'notEmpty' => [
                'rule' => ['notEmpty'],
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
}
