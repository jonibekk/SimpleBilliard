<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CircleMemberPagingService');
App::import('Lib/Paging', 'PagingRequest');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/23
 * Time: 15:08
 */

use Goalous\Enum as Enum;

class CircleMemberPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.experiment',
        'app.post_share_circle',
        'app.team_member',
    ];

    public function test_getCircleMembers_success()
    {
        /** @var CircleMemberPagingService $CircleMemberPagingService */
        $CircleMemberPagingService = ClassRegistry::init('CircleMemberPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setResourceId(1);

        $result = $CircleMemberPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertNotEmpty($result['data']);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
        $this->assertNotEmpty($result['data'][0]['user_id']);
        $this->assertNotEmpty($result['data'][0]['last_posted']);
    }

    public function test_getCircleMembersWithCursor_success()
    {
        /** @var CircleMemberPagingService $CircleMemberPagingService */
        $CircleMemberPagingService = ClassRegistry::init('CircleMemberPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setResourceId(1);

        $result = $CircleMemberPagingService->getDataWithPaging($pagingRequest, 1);

        $cursor = $result['cursor'];

        $nextRequest = PagingRequest::decodeCursorToObject($cursor);
        $nextRequest->setCurrentTeamId(1);
        $nextRequest->setCurrentUserId(1);
        $nextRequest->setResourceId(1);

        $result = $CircleMemberPagingService->getDataWithPaging($nextRequest, 1);

        $this->assertNotEmpty($result['data']);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
        $this->assertNotEmpty($result['data'][0]['user_id']);
        $this->assertNotEmpty($result['data'][0]['last_posted']);
    }

    public function test_getCircleMembersWithUserExtension_success()
    {
        /** @var CircleMemberPagingService $CircleMemberPagingService */
        $CircleMemberPagingService = ClassRegistry::init('CircleMemberPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setResourceId(1);

        $result = $CircleMemberPagingService->getDataWithPaging($pagingRequest, 1,
            CircleMemberExtender::EXTEND_USER);

        $this->assertNotEmpty($result['data']);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
        $this->assertNotEmpty($result['data'][0]['user_id']);
        $this->assertNotEmpty($result['data'][0]['last_posted']);
        $this->assertNotEmpty($result['data'][0]['user']);
        $this->assertNotEmpty($result['data'][0]['user']['id']);
    }

    public function test_getCircleMembersOrders_success()
{
    $circleId = 202;
    $teamId = 909;
    $userId = 24;

    $pageLimit = 3;

    $this->createCircleMember($circleId, $teamId, $userId);

    /** @var CircleMember $CircleMember */
    $CircleMember = ClassRegistry::init('CircleMember');
    /** @var CircleMemberPagingService $CircleMemberPagingService */
    $CircleMemberPagingService = ClassRegistry::init('CircleMemberPagingService');

    /**
     * Create randomized data
     */
    for ($i = 0; $i < 30; $i++) {
        $options = [
            'admin_flg'   => (bool)($i % 3),
            'last_posted' => (int)($i / 4)
        ];
        $generatedUserId = 1000 + $i;
        $this->createCircleMember($circleId, $teamId, $generatedUserId, $options);
        $this->createTeamMember($teamId, $generatedUserId);
    }

    $manualCondition = [
        'fields'     => [
            'CircleMember.id',
            'CircleMember.user_id',
            'CircleMember.last_posted',
            'CircleMember.admin_flg'
        ],
        'conditions' => [
            'CircleMember.team_id'   => $teamId,
            'CircleMember.circle_id' => $circleId
        ],
        'joins'      => [
            [
                'type'       => 'INNER',
                'table'      => 'team_members',
                'alias'      => 'TeamMember',
                'conditions' => [
                    'TeamMember.team_id = CircleMember.team_id',
                    'TeamMember.user_id = CircleMember.user_id',
                    'TeamMember.del_flg' => false,
                    'TeamMember.status'  => Enum\Model\TeamMember\Status::ACTIVE,
                ]
            ]
        ],
        'order'      => [
            'CircleMember.last_posted' => 'desc',
            'CircleMember.id'          => 'desc'
        ]
    ];

    $members = $CircleMember->useType()->find('all', $manualCondition);

    $pagingRequest = null;
    $result = [];

    for ($i = 0; $i < count($members); $i++) {

        $pageDataIndex = $i % $pageLimit;

        if ($pageDataIndex === 0) {
            if (empty($pagingRequest)) {
                $pagingRequest = new PagingRequest();
            } else {
                $pagingRequest = PagingRequest::decodeCursorToObject($result['cursor']);
            }

            $pagingRequest->setCurrentTeamId($teamId);
            $pagingRequest->setCurrentUserId($userId);
            $pagingRequest->setResourceId($circleId);

            $result = $CircleMemberPagingService->getDataWithPaging($pagingRequest, $pageLimit);
        }

        $this->assertEquals($members[$i]['CircleMember']['id'], $result['data'][$pageDataIndex]['id']);
        $this->assertEquals($members[$i]['CircleMember']['last_posted'], $result['data'][$pageDataIndex]['last_posted']);
        $this->assertEquals($members[$i]['CircleMember']['admin_flg'], $result['data'][$pageDataIndex]['admin_flg']);
    }
}

    public function test_getCircleMembersOrdersWithAdmin_success()
    {
        $circleId = 202;
        $teamId = 909;
        $userId = 24;

        $pageLimit = 3;

        $this->createCircleMember($circleId, $teamId, $userId);

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        /** @var CircleMemberPagingService $CircleMemberPagingService */
        $CircleMemberPagingService = ClassRegistry::init('CircleMemberPagingService');

        /**
         * Create randomized data
         */
        for ($i = 0; $i < 30; $i++) {
            $options = [
                'admin_flg'   => (bool)($i % 3),
                'last_posted' => (int)($i / 4)
            ];
            $generatedUserId = 1000 + $i;
            $this->createCircleMember($circleId, $teamId, $generatedUserId, $options);
            $this->createTeamMember($teamId, $generatedUserId);
        }

        $manualCondition = [
            'fields'     => [
                'CircleMember.id',
                'CircleMember.user_id',
                'CircleMember.last_posted',
                'CircleMember.admin_flg'
            ],
            'conditions' => [
                'CircleMember.team_id'   => $teamId,
                'CircleMember.circle_id' => $circleId
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.team_id = CircleMember.team_id',
                        'TeamMember.user_id = CircleMember.user_id',
                        'TeamMember.del_flg' => false,
                        'TeamMember.status'  => Enum\Model\TeamMember\Status::ACTIVE,
                    ]
                ]
            ],
            'order'      => [
                'CircleMember.admin_flg'   => 'desc',
                'CircleMember.last_posted' => 'desc',
                'CircleMember.id'          => 'desc'
            ]
        ];

        $members = $CircleMember->useType()->find('all', $manualCondition);

        $pagingRequest = null;
        $result = [];

        for ($i = 0; $i < count($members); $i++) {

            $pageDataIndex = $i % $pageLimit;

            if ($pageDataIndex === 0) {
                if (empty($pagingRequest)) {
                    $pagingRequest = new PagingRequest();
                    $pagingRequest->addQueries(['admin_first' => true]);
                } else {
                    $pagingRequest = PagingRequest::decodeCursorToObject($result['cursor']);
                }

                $pagingRequest->setCurrentTeamId($teamId);
                $pagingRequest->setCurrentUserId($userId);
                $pagingRequest->setResourceId($circleId);

                $result = $CircleMemberPagingService->getDataWithPaging($pagingRequest, $pageLimit);
            }

            $this->assertEquals($members[$i]['CircleMember']['id'], $result['data'][$pageDataIndex]['id']);
            $this->assertEquals($members[$i]['CircleMember']['last_posted'], $result['data'][$pageDataIndex]['last_posted']);
            $this->assertEquals($members[$i]['CircleMember']['admin_flg'], $result['data'][$pageDataIndex]['admin_flg']);
        }
    }
}
