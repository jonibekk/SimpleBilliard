<?php
App::uses('GoalousTestCase', 'Test');
App::uses('PagingRequest', 'Lib/Paging');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/09
 * Time: 17:38
 */
class PagingRequestTest extends GoalousTestCase
{
    public function test_NextPaging_success()
    {
        $conditions = [
            'name'    => 'test',
            'team_id' => 2
        ];

        $pointer = ['count', '>', 100];
        $order = ['count', 'asc'];

        $encodedString = PagingRequest::createPageCursor($conditions, $pointer, $order);

        $decodedArray = PagingRequest::decodeCursorToArray($encodedString);

        $this->assertEquals($conditions['team_id'], $decodedArray['conditions']['team_id']);
        $this->assertEquals($pointer, $decodedArray['pointer']);
        $this->assertEquals($order, $decodedArray['order']);
    }
}