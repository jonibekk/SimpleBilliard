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
        $order = ['count' => 'asc'];

        $encodedString = PagingRequest::createPageCursor($conditions, $pointer, $order);

        $decodedArray = PagingRequest::decodeCursorToArray($encodedString);

        $this->assertEquals($conditions['team_id'], $decodedArray['conditions']['team_id']);
        $this->assertEquals($pointer, $decodedArray['pointer'][0]);
        $this->assertEquals($order, $decodedArray['order']);
    }

    public function test_decodePagingToObject_success()
    {

        $conditions = [
            'name'    => 'test',
            'team_id' => 2
        ];

        $pointer = ['count', '>', 100];
        $order = ['count' => 'asc'];

        $encodedString = PagingRequest::createPageCursor($conditions, $pointer, $order);

        $decodedObject = PagingRequest::decodeCursorToObject($encodedString);

        $this->assertEquals($conditions['team_id'], $decodedObject->getConditions()['team_id']);
        $this->assertEquals("$pointer[0] $pointer[1] $pointer[2]",
            $decodedObject->getPointersAsQueryOption()[0]);
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

    public function test_addingOrder_success()
    {
        $pagingRequest = new PagingRequest();
        $pagingRequest->addOrder('id');
        $this->assertEquals([['id' => 'desc']], $pagingRequest->getOrders());

        $this->assertTrue($pagingRequest->addOrder('a'));
        $this->assertCount(2, $pagingRequest->getOrders());
        $this->assertEquals('a', key($pagingRequest->getOrders()[1]));
        $this->assertEquals('id', key($pagingRequest->getOrders()[0]));

        $this->assertFalse($pagingRequest->addOrder('a'));
        $this->assertCount(2, $pagingRequest->getOrders());

        $this->assertTrue($pagingRequest->addOrder('b'));
        $this->assertCount(3, $pagingRequest->getOrders());
        $this->assertEquals('b', key($pagingRequest->getOrders()[2]));
    }
}