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

        $pivotValue = 100;
        $order = 'asc';
        $direction = 'next';

        $encodedString = RequestPaging::createPageCursor($pivotValue, $conditions, $order, $direction);

        $decodedArray = RequestPaging::decodeCursor($encodedString);

        $this->assertEquals($pivotValue, $decodedArray['pivot']);
        $this->assertEquals($order, $decodedArray['order']);
        $this->assertEquals($direction, $decodedArray['direction']);
        $this->assertEquals($conditions['team_id'], $decodedArray['conditions']['team_id']);
    }
}