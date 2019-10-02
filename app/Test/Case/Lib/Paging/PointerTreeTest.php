<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/Paging', 'PointerTree');
App::import('Lib/DataStructure', 'BinaryNode');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/13
 * Time: 16:55
 */
class PointerTreeTest extends GoalousTestCase
{
    public function test_treeToCondition_success()
    {
        $defaultTree = $this->createDefaultTree();
        $condition = $defaultTree->toCondition();

        $this->assertInternalType('array', $condition);
        $this->assertEquals('AND', key($condition));
        $this->assertEquals("id < 2", $condition['AND'][0][0]);
        $this->assertEquals('OR', key($condition['AND'][1]));
    }

    /**
     * @expectedException RuntimeException
     */
    public function test_treeToConditionBadTree_failed()
    {
        $tree = new PointerTree('AND');
        $tree->toCondition();
    }

    public function test_getPointer_success()
    {
        $defaultTree = $this->createDefaultTree();
        $actualPointer = $defaultTree->searchTree("time")->getValue();
        $expectedPointer = ['time', '>=', 100];
        $this->assertEquals($expectedPointer, $actualPointer);
    }

    public function test_addNewPointerFromEmpty_success()
    {
        $newPointer = ['id', '<', 1];

        $tree = new PointerTree();
        $tree->addPointer($newPointer);

        $this->assertEquals($newPointer, $tree->getValue());
    }

    public function test_addNewPointerRootAnd_success()
    {
        $newPointer = ['id', '<', 1];

        $tree = new PointerTree('AND', 'OR');
        $tree->addPointer($newPointer);

        $this->assertEquals($newPointer, $tree->getRight()->getValue());
    }

    public function test_addNewPointerBothChildPointer_success()
    {
        $newPointer = ['id', '<', 1];
        $leftPointer = ['id', '<', 2];
        $rightPointer = ['id', '<', 3];

        $tree = new PointerTree('AND', $leftPointer, $rightPointer);
        $tree->addPointer($newPointer);

        $this->assertEquals('AND', $tree->getLeft()->getValue());
        $this->assertEquals($newPointer, $tree->getLeft()->getLeft()->getValue());
    }

    public function test_addNewPointerComplexTree_success()
    {
        $newPointer1 = ['id', '<', 1];
        $newPointer2 = ['created', '<', 123456];
        $tree = $this->createDefaultTree();
        $tree->addPointer($newPointer1);

        $this->assertEquals('AND', $tree->getLeft()->getValue());
        $this->assertEquals($newPointer1, $tree->getLeft()->getLeft()->getValue());
        $this->assertInternalType('array', $tree->getLeft()->getRight()->getValue());

        $tree->addPointer($newPointer2);

        $this->assertEquals('AND', $tree->getLeft()->getValue());
        $this->assertEquals('AND', $tree->getLeft()->getLeft()->getValue());
        $this->assertEquals($newPointer1, $tree->getLeft()->getLeft()->getRight()->getValue());
        $this->assertEquals($newPointer2, $tree->getLeft()->getLeft()->getLeft()->getValue());
    }

    /**
     * @expectedException RuntimeException
     */
    public function test_addNewPointerInvalidStructure_failed()
    {
        $newPointer = ['id', '<', 1];
        $tree = new PointerTree('AND', 'OR', 'OR');
        $tree->addPointer($newPointer);
    }

    /**
     * @expectedException RuntimeException
     */
    public function test_addNewPointerRootOr_failed()
    {
        $newPointer = ['id', '<', 1];
        $tree = new PointerTree('OR');
        $tree->addPointer($newPointer);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_addBadPointer_failed()
    {
        $newPointer = ['horrible'];
        $tree = new PointerTree('AND');
        $tree->addPointer($newPointer);
    }

    private function createDefaultTree(): PointerTree
    {
        $node1 = new PointerTree(['id', '<', 2]);
        $node2 = new PointerTree('OR', ['time', '>=', 100], ['del_flg', '=', true]);

        return new PointerTree('AND', $node1, $node2);
    }

}