<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CircleListPagingService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('CirclePin', 'Model');

/**
 * @property CircleListPagingService $CircleListPagingService
 * @property CirclePin $CirclePin
 * @property CircleMember $CircleMember
 */
class CircleListPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.circle_pin',
        'app.experiment',
        'app.post_share_circle'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->CircleListPagingService = ClassRegistry::init('CircleListPagingService');
        $this->CirclePin = ClassRegistry::init('CirclePin');
        $this->CircleMember = ClassRegistry::init('CircleMember');
    }

    public function test_getCircleList_success()
    {
        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(13);

        $res = $this->CircleListPagingService->getDataWithPaging($pagingRequest, 3);
        $this->assertCount(3, $res['data']);
        $this->assertEquals(Hash::get($res, 'data.0.id'), 16);
        $this->assertEquals(Hash::get($res, 'data.1.id'), 17);
        $this->assertEquals(Hash::get($res, 'data.2.id'), 15);
        $this->assertEmpty($res['cursor']);
        $this->assertEquals($res['count'], 3);
    }

    /**
     * [Note]
     * ↓ cases can't be executed because SQLite doesn't suppoet `ORDER BY FIELD` syntax and this error happens `PDOException: SQLSTATE[HY000]: General error: 1 no such function: FIELD`
     */
//    public function test_getCircleListWithCursor_pinned()
//    {
//        // Prepare Test:Reset circle pin
//        $this->CirclePin->deleteAll(['user_id' => 1]);
//
//        $userId = 1;
//        $teamId = 1;
//        $teamAllCircleId = 3;
//        $pagingRequest = new PagingRequest();
//        $pagingRequest->setCurrentTeamId($teamId);
//        $pagingRequest->setCurrentUserId($userId);
//        $pagingRequest->addQueries(['pinned' => true]);
//
//        /* Case: Default circle is pinned as system, user can't change pin/unpin */
//        $res = $this->CircleListPagingService->getDataWithPaging($pagingRequest, 2);
//        $this->assertEquals($res['count'], 1);
//        $this->assertEquals(Hash::get($res, 'data.0.id'), $teamAllCircleId);
//        $this->assertEquals(Hash::get($res, 'data.0.team_all_flg'), 1);
//
//
//
//        /* Case: Get pinned circle ordered initially */
//        // Prepare Test:Update circle pins and shuffle
//        $joinedCircles = $this->CircleMember->getMyCircleList(null, $userId, $teamId);
//        unset($joinedCircles[$teamAllCircleId]);
//        $joinedCircleIds = array_keys($joinedCircles);
//        shuffle($joinedCircleIds);
//        $this->CirclePin->create();
//        $this->CirclePin->save([
//            'user_id' => $userId,
//            'team_id'      => $teamId,
//            'circle_orders'    => implode(',', $joinedCircleIds),
//        ], false);
//
//        $res = $this->CircleListPagingService->getDataWithPaging($pagingRequest, 3);
//        $this->assertEquals($res['count'], count($joinedCircleIds) + 1);
//        $this->assertCount(3, $res['data']);
//        $this->assertEquals(Hash::get($res, 'data.0.id'), $teamAllCircleId);
//        $this->assertEquals(Hash::get($res, 'data.1.id'), $joinedCircleIds[0]);
//        $this->assertEquals(Hash::get($res, 'data.2.id'), $joinedCircleIds[1]);
//        $this->assertNotEmpty($res['cursor']);
//
//        /* Case: Get pinned circle ordered with cursor */
//        $pagingRequest = PagingRequest::decodeCursorToObject($res['cursor']);
//        $pagingRequest->setCurrentTeamId( $teamId);
//        $pagingRequest->setCurrentUserId($userId);
//
//        $result = $this->CircleListPagingService->getDataWithPaging($pagingRequest, 3);
//        $this->assertEquals($res['count'], count($joinedCircleIds) + 1);
//        $this->assertCount(3, $result['data']);
//        $this->assertEquals(Hash::get($res, 'data.0.id'), $joinedCircleIds[2]);
//        $this->assertEquals(Hash::get($res, 'data.1.id'), $joinedCircleIds[3]);
//        $this->assertEquals(Hash::get($res, 'data.2.id'), $joinedCircleIds[4]);
//        $this->assertNotEmpty($res['cursor']);
//
//    }

    public function test_getCircleListWithCursor_notPinned()
    {
        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(13);

        $res = $this->CircleListPagingService->getDataWithPaging($pagingRequest, 2);
        $this->assertEquals($res['count'], 3);
        $this->assertEquals(Hash::get($res, 'data.0.id'), 16);
        $this->assertEquals(Hash::get($res, 'data.1.id'), 17);

        $pagingRequest = PagingRequest::decodeCursorToObject($res['cursor']);
        $pagingRequest->setCurrentTeamId( 1);
        $pagingRequest->setCurrentUserId(13);

        $res = $this->CircleListPagingService->getDataWithPaging($pagingRequest, 2);

        $this->assertCount(1, $res['data']);
        $this->assertEquals(Hash::get($res, 'data.0.id'), 15);
        $this->assertEmpty($res['cursor']);
        $this->assertEquals($res['count'], 3);
    }

    public function test_getCircleListWithMemberInfoExtension_success()
    {
        $cursor = new PagingRequest();
        $cursor->setCurrentTeamId(1);
        $cursor->setCurrentUserId(13);
        $result = $this->CircleListPagingService->getDataWithPaging($cursor, 1,
            [CircleExtender::EXTEND_MEMBER_INFO]);

        $data = $result['data'][0];
        $this->assertInternalType('int', $data['unread_count']);
        $this->assertInternalType('bool', $data['admin_flg']);
    }


    public function test_getCircleListWithIsMemberExtension()
    {
        /* All joined(Default) */
        $cursor = new PagingRequest();
        $cursor->setCurrentTeamId(1);
        $cursor->setCurrentUserId(13);
        $cursor->addOrder('latest_post_created');

        $result = $this->CircleListPagingService->getDataWithPaging($cursor, 2,
            [CircleExtender::EXTEND_IS_MEMBER]);

        $data = $result['data'];
        $this->assertTrue($data[0]['is_member']);
        $this->assertTrue($data[1]['is_member']);

        /* All joined(Specified) */
        $cursor = new PagingRequest();
        $cursor->setCurrentTeamId(1);
        $cursor->setCurrentUserId(13);
        $cursor->addOrder('latest_post_created');
        $cursor->addCondition(['joined' => true]);

        $result = $this->CircleListPagingService->getDataWithPaging($cursor, 2,
            [CircleExtender::EXTEND_IS_MEMBER]);

        $data = $result['data'];
        $this->assertTrue($data[0]['is_member']);
        $this->assertTrue($data[1]['is_member']);

        /* All not joined(Specified) */
        $cursor = new PagingRequest();
        $cursor->setCurrentTeamId(1);
        $cursor->setCurrentUserId(13);
        $cursor->addOrder('latest_post_created');
        $cursor->addCondition(['joined' => false]);

        $result = $this->CircleListPagingService->getDataWithPaging($cursor, 2,
            [CircleExtender::EXTEND_IS_MEMBER]);

        $data = $result['data'];
        $this->assertFalse($data[0]['is_member']);
        $this->assertFalse($data[1]['is_member']);
    }


    /**
     * Currently, if user is getting joined circles, it will skip pinned circles.
     * All of user 1's joined circles are pinned
     */
    public function test_getCircleListWithCursor_notJoined()
    {
        $userId = 13;
        $teamId = 1;
        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId($teamId);
        $pagingRequest->setCurrentUserId($userId);
        $pagingRequest->addQueries(['joined' => false]);
        $res = $this->CircleListPagingService->getDataWithPaging($pagingRequest, 2);
        $this->assertEquals(4, $res['count']);
        $this->assertCount(2, $res['data']);
        $this->assertEquals(Hash::get($res, 'data.0.id'), 5);
        $this->assertEquals(Hash::get($res, 'data.1.id'), 3);
        $this->assertNotEmpty($res['cursor']);


        $pagingRequest = PagingRequest::decodeCursorToObject($res['cursor']);
        $pagingRequest->setCurrentTeamId( $teamId);
        $pagingRequest->setCurrentUserId($userId);

        $res = $this->CircleListPagingService->getDataWithPaging($pagingRequest, 2);
        $this->assertEquals(4, $res['count']);
        $this->assertCount(2, $res['data']);
        $this->assertEquals(Hash::get($res, 'data.0.id'), 2);
        $this->assertEquals(Hash::get($res, 'data.1.id'), 1);
        $this->assertEmpty($res['cursor']);

        $this->CircleMember->deleteAll(['CircleMember.user_id' => $userId, 'CircleMember.team_id' => $teamId]);

        $res = $this->CircleListPagingService->getDataWithPaging($pagingRequest, 100);
        $this->assertEquals(7, $res['count']);
    }

    /**
     * filter new created circle from unjoined circle
     */
    public function test_getCircleListWithCursor_newCreated()
    {
        $userId = 13;
        $teamId = 1;
        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId($teamId);
        $pagingRequest->setCurrentUserId($userId);
        $pagingRequest->addQueries(['joined' => false]);
        $pagingRequest->addQueries(['newcreated' => true]);
        $res = $this->CircleListPagingService->getDataWithPaging($pagingRequest, 2);

        $this->assertCount(0, $res['data']);
    }

}
