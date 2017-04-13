<?php

class AddLastMessageReadDatetime0413 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_last_read_message_datetime_0413';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'topic_members' => array(
                    'last_read_message_datetime' => array(
                        'type'     => 'integer',
                        'null'     => true,
                        'default'  => null,
                        'unsigned' => true,
                        'key'      => 'index',
                        'comment'  => 'It\'s update when read message.',
                        'after'    => 'last_read_message_id'
                    ),
                    'indexes'                    => array(
                        'last_read_message_datetime' => array('column' => 'last_read_message_datetime', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'topic_members' => array(
                    'last_read_message_datetime',
                    'indexes' => array('last_read_message_datetime')
                ),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        if ($direction == 'up') {
            // update all topic_members.
            /** @var TopicMember $TopicMember */
            $TopicMember = ClassRegistry::init('TopicMember');
            $TopicMember->cacheQueries = false;
            $TopicMember->unbindModel(['belongsTo' => ['Topic', 'User']]);
            $TopicMember->updateAll(
                ['TopicMember.last_read_message_datetime' => 'TopicMember.modified']
            );
        }

        return true;
    }
}
