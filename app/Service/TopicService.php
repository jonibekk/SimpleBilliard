<?php
App::import('Service', 'AppService');
App::uses('Topic', 'Model');
App::uses('Message', 'Model');
App::uses('TopicMember', 'Model');

/**
 * Class TopicService
 */
class TopicService extends AppService
{
    /* Default number of topics displaying */
    const DFAULT_TOPICS_NUM = 10;

    /**
     * Get initial topics
     * @return [type] [description]
     */
    function getInit($cursor)
    {

    }

    function process($dataByModel)
    {

    }

}
