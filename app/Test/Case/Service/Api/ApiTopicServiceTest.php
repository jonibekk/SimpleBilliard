<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Topic', 'Model');
App::import('Service/Api', 'ApiTopicService');

/**
 * Class ApiTopicServiceTest
 *
 * @property ApiTopicService $ApiTopicService
 * @property Message $Message
 * @property Topic $Topic
 * @property TopicMember $TopicMember
 */
class ApiTopicServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.topic',
        'app.message',
        'app.user',
        'app.message_file',
        'app.team',
        'app.topic_member',
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
        $this->Message = ClassRegistry::init('Message');
        $this->Topic = ClassRegistry::init('Topic');
        $this->TopicMember = ClassRegistry::init('TopicMember');
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
                    'display_created'     => 'Mar  1, 2016'
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

    function test_findTopicDetailInitData_fromUnExisting()
    {
        $r = $this->ApiTopicService->findTopicDetailInitData(1, 1);
        $this->assertCount(0, $r['topic']);
        $this->assertCount(0, $r['messages']['data']);
    }

    function test_findTopicDetailInitData_fromLatest()
    {
        $data = $this->createMessageTestData();
        $r = $this->ApiTopicService->findTopicDetailInitData($data["topic_id"], $data["user_id"]);
        $this->assertEquals('text100', end($r['messages']['data'])['body']);

        // new
        $this->assertNull($r['messages']['paging']['new']);

        // old
        $this->assertContains('limit=20', $r['messages']['paging']['old']);
        $this->assertContains('cursor=80', $r['messages']['paging']['old']);
        $this->assertContains('direction=old', $r['messages']['paging']['old']);
    }

    function test_findTopicDetailInitData_fromMiddle()
    {
        $data = $this->createMessageTestData();
        $r = $this->ApiTopicService->findTopicDetailInitData($data["topic_id"], $data["user_id"], 50);
        $this->assertEquals('text50', end($r['messages']['data'])['body']);

        // new
        $this->assertContains('limit=20', $r['messages']['paging']['new']);
        $this->assertContains('cursor=51', $r['messages']['paging']['new']);
        $this->assertContains('direction=new', $r['messages']['paging']['new']);

        // old
        $this->assertContains('limit=20', $r['messages']['paging']['old']);
        $this->assertContains('cursor=30', $r['messages']['paging']['old']);
        $this->assertContains('direction=old', $r['messages']['paging']['old']);
    }

    function test_findTopicDetailInitData_fromBeginning()
    {
        $data = $this->createMessageTestData();
        $r = $this->ApiTopicService->findTopicDetailInitData($data["topic_id"], $data["user_id"], 5);
        $this->assertEquals('text5', end($r['messages']['data'])['body']);

        // new
        $this->assertContains('limit=20', $r['messages']['paging']['new']);
        $this->assertContains('cursor=6', $r['messages']['paging']['new']);
        $this->assertContains('direction=new', $r['messages']['paging']['new']);

        // old
        $this->assertNull($r['messages']['paging']['old']);
    }

    private function createMessageTestData(): array
    {
        $userId = 1;
        $teamId = 1;
        $this->Message->my_uid = $userId;
        $topic = $this->Topic->save([
            'creator_user_id' => $userId,
            'team_id' => $teamId,
            'title' => "TestTopic",
        ]);
        $topicId = $topic['Topic']['id'];
        $this->TopicMember->save([
            'topic_id' => $topicId,
            'user_id' => $userId,
            'team_id' => $teamId,
        ]);

        $message = null;
        for ($i =1; $i <= 100; $i++) {
            $this->Message->create();
            $message = $this->Message->save([
                'topic_id' => $topicId,
                'sender_user_id' => $userId,
                'body' => "text" . $i,
                'type' => 1,
            ]);
        }
        $this->Topic->save([
            'id' => $topicId,
            'latest_message_id' => $message['Message']['id']
        ]);

        return [
            'user_id' => $userId,
            'topic_id' => $topicId,
        ];
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
