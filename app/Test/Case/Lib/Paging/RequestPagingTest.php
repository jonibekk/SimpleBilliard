<?php
App::uses('GoalousTestCase', 'Test');
App::uses('RequestPaging', 'Lib/Paging');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/09
 * Time: 17:38
 */
class RequestPagingTest extends GoalousTestCase
{
    public function test_NextPaging_success()
    {
        $conditions = [
            'name'    => 'test',
            'team_id' => 2
        ];

        $startId = 10;
        $limit = 5;

        $encodedString = RequestPaging::createNextPageCursor($conditions, $startId, $limit);

        $decodedArray = RequestPaging::decodeCursor($encodedString);

        $this->assertEquals($startId, $decodedArray['start']);
        $this->assertEquals($limit, $decodedArray['limit']);
        $this->assertEquals($conditions['team_id'], $decodedArray['conditions']['team_id']);
    }

    public function test_PrevPaging_success()
    {
        $conditions = [
            'name'    => 'test',
            'team_id' => 2
        ];

        $endId = 10;
        $limit = 5;

        $encodedString = RequestPaging::createPrevPageCursor($conditions, $endId, $limit);

        $decodedArray = RequestPaging::decodeCursor($encodedString);

        $this->assertEquals($endId, $decodedArray['end']);
        $this->assertEquals($limit, $decodedArray['limit']);
        $this->assertEquals($conditions['team_id'], $decodedArray['conditions']['team_id']);
    }
}