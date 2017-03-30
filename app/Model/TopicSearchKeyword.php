<?php
App::uses('AppModel', 'Model');

/**
 * TopicSearchKeyword Model

 */
class TopicSearchKeyword extends AppModel
{

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Topic'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'keywords' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'del_flg'  => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * add keywords record for search
     *
     * @param  int $topicId
     *
     * @return bool
     */
    function add(int $topicId, $keywords): bool
    {
        $data = [
            'team_id'  => $this->current_team_id,
            'topic_id' => $topicId,
            'keywords' => $keywords
        ];
        $res = (bool)$this->save($data);
        return $res;
    }

    /**
     * update keyword records for search by user id
     * - update all topics related by user
     * - in super exceptional, writing sql directory
     *  - For Reducing query cost
     *
     * @param  int $userId
     * @return bool
     */
    function updateByUserId($userId): bool
    {
        $query = <<<SQL
UPDATE topic_search_keywords tsk,
(SELECT
    t.id as topic_id,
    CONCAT(
        '\n',
        GROUP_CONCAT(DISTINCT(u.last_name) SEPARATOR '\n'),
        '\n',
        GROUP_CONCAT(DISTINCT(u.first_name) SEPARATOR '\n'),
        '\n',
        ifnull(GROUP_CONCAT(DISTINCT(ln.last_name) SEPARATOR '\n'), ""),
        '\n',
        ifnull(GROUP_CONCAT(DISTINCT(ln.first_name) SEPARATOR '\n'), "")
    ) AS keywords
    FROM topics t
    INNER JOIN topic_members tpm ON
        t.id = tpm.topic_id
        AND tpm.del_flg = 0
    INNER JOIN users u ON
        tpm.user_id = u.id
        AND u.active_flg = 1
        AND u.del_flg = 0
    INNER JOIN team_members tm ON
        tpm.user_id = tm.user_id
        AND tpm.team_id = tm.team_id
        AND tm.active_flg = 1
        AND tm.del_flg = 0
    LEFT JOIN local_names ln ON
        ln.user_id = u.id
        AND ln.del_flg = 0
    WHERE t.id in (
      SELECT tpm2.topic_id from topic_members as tpm2
      WHERE tpm2.user_id = $userId
    )
    GROUP BY t.id
) tsk2
SET tsk.keywords = tsk2.keywords
WHERE tsk.topic_id = tsk2.topic_id
      AND tsk.del_flg = 0
SQL;
        $this->query($query);
        return true;
    }

    /**
     * update keyword records for search by topic id
     *
     * @param  int    $topicId
     * @param  string $keywords
     *
     * @return bool
     */
    function updateByTopicId(int $topicId, string $keywords): bool
    {
        $record = $this->findByTopicId($topicId, ['id']);
        if (!$record) {
            $this->log(sprintf("Failed to get topic search keywords by topicId. topicId:%s", $topicId));
            return false;
        }
        $id = Hash::get($record, 'TopicSearchKeyword.id');

        $data = [
            'id'       => $id,
            'team_id'  => $this->current_team_id,
            'keywords' => $keywords
        ];
        return (bool)$this->save($data);
    }
}
