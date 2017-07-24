<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Topic', 'Model');
App::import('Service/Api', 'ApiTopicService');

/**
 * Class ApiTopicServiceTest
 *
 * @property ApiTopicService $ApiTopicService
 */
class ApiTopicServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.topic'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ApiTopicService = ClassRegistry::init('ApiTopicService');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApiTopicService);
        parent::tearDown();
    }

    function test_process()
    {
        $myUserId = 1;
        $topicByModel = [
            [
                'Topic'         => ['id' => 1, 'title' => null],
                'LatestMessage' => [
                    'id'                  => 1,
                    'body'                => 'test',
                    'attached_file_count' => 0,
                    'sender_user_id'      => 1,
                    'created'             => '1456811206'
                ],
                'TopicMember'   => [
                    [
                        'last_read_message_id' => 1,
                        'user_id'              => 1,
                        'User'                 => [
                            'id'                 => 1,
                            'display_username'   => '佐伯 翔平',
                            'display_first_name' => '翔平'
                        ]
                    ],
                    [
                        'last_read_message_id' => 2,
                        'user_id'              => 2,
                        'User'                 => [
                            'id'                 => 2,
                            'display_username'   => '菊池 厚平',
                            'display_first_name' => '厚平'
                        ]
                    ],
                    [
                        'last_read_message_id' => 1,
                        'user_id'              => 3,
                        'User'                 => [
                            'id'                 => 3,
                            'display_username'   => '深瀬 元彦',
                            'display_first_name' => '元彦'

                        ]
                    ],
                ]
            ]
        ];
        $expected = [
            [
                'id'              => 1,
                'title'           => null,
                'is_unread'       => false,
                'latest_message'  => [
                    'id'                  => 1,
                    'body'                => 'You : test',
                    'attached_file_count' => 0,
                    'sender_user_id'      => 1,
                    'created'             => '1456811206',
                    'display_created'     => 'Mar  1 2016'
                ],
                'members_count'   => 3,
                'can_leave_topic' => true,
                'display_title'   => '厚平, 元彦',
                'read_count'      => 1,
                'users'           => [
                    [
                        'id'                 => 2,
                        'display_username'   => '菊池 厚平',
                        'display_first_name' => '厚平'
                    ],
                    [
                        'id'                 => 3,
                        'display_username'   => '深瀬 元彦',
                        'display_first_name' => '元彦'
                    ],
                ]
            ]
        ];
        $res = $this->ApiTopicService->process($topicByModel, $myUserId);
        $this->assertEqual($res, $expected);

        // only attached files
        $topicByModel[0]['LatestMessage']['body'] = "";
        $topicByModel[0]['LatestMessage']['attached_file_count'] = 1;
        $expected[0]['latest_message']['body'] = "You : Sent file(s).";
        $expected[0]['latest_message']['attached_file_count'] = 1;
        $res = $this->ApiTopicService->process($topicByModel, $myUserId);
        $this->assertEqual($res, $expected);
    }

    function test_calcReadCount()
    {
        //TODO: it should be written later.
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_findTopicDetailInitData()
    {
        //TODO: it should be written later.
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_getDisplayTitle()
    {
        //TODO: it should be written later.
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_findReadMembers()
    {
        //TODO: it should be written later.
        $this->markTestIncomplete('testClear not implemented.');
    }
}