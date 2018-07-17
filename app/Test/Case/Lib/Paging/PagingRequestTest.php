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
    public function test_decodePaging_success()
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

    public function test_decodePagingToObject_success()
    {

        $conditions = [
            'name'    => 'test',
            'team_id' => 2
        ];

        $pointer = ['count' => ['>', 100]];
        $order = ['count' => 'asc'];

        $encodedString = PagingRequest::createPageCursor($conditions, $pointer, $order);

        $decodedObject = PagingRequest::decodeCursorToObject($encodedString);

        $this->assertEquals($conditions['team_id'], $decodedObject->getConditions()['team_id']);
        //TODO
        $this->assertEquals($pointer, $decodedObject->getPointers());
        $this->assertEquals($order, $decodedObject->getOrders()[0]);
    }

    public function test_addingResource_success()
    {
        $resourceKey = "test";
        $resourceValue = 1;

        $pagingRequest = new PagingRequest();
        $pagingRequest->addResource($resourceKey, $resourceValue);

        $this->assertEmpty($pagingRequest->getConditions());
        $this->assertEquals($resourceValue, $pagingRequest->getConditions(true)[$resourceKey]);

        $encodedString = $pagingRequest->returnCursor();

        $this->assertEmpty($encodedString);

        try {
            $decodedArray = PagingRequest::decodeCursorToObject($encodedString);
            $this->assertNull($decodedArray);
        } catch (InvalidArgumentException $e) {

        } catch (Exception $e) {
            $this->fail();
        }
    }
}