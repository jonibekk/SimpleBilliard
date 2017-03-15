<?php
App::uses('AppModel', 'Model');

/**
 * Topic Model
 *
 * @property User        $CreatorUser
 * @property MessageFile $MessageFile
 * @property Message     $Message
 * @property TopicMember $TopicMember
 */
class Topic extends AppModel
{
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
            'boolean' => ['rule' => ['boolean'],],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'CreatorUser' => [
            'className'  => 'User',
            'foreignKey' => 'creator_user_id',
        ],
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'MessageFile',
        'Message',
        'TopicMember',
    ];

    /**
     * get one topic.
     *
     * @param int $topicId
     *
     * @return array
     */
    public function get(int $topicId): array
    {
        $options = [
            'conditions' => ['id' => $topicId],
            'fields'     => [
                'id',
                'title',
                'latest_message_id',
            ],
        ];
        $ret = $this->find('first', $options);
        $ret = Hash::extract($ret, 'Topic');
        return (array)$ret;
    }

}
