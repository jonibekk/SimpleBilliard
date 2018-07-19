<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/DataStructure/Tree', 'BinaryTree');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/12
 * Time: 17:57
 */
class BinaryTreeTest extends GoalousTestCase
{
    public function test_getValue_success()
    {
        $tree = $this->createDefaultTree();
        $node = $tree->searchTree(6);

        $this->assertEquals(6, $node->getValue());
        $this->assertEquals(4, $node->getLeft()->getValue());
        $this->assertEquals(7, $node->getRight()->getValue());
        $this->assertEmpty($node->getLeft()->getLeft());
        $this->assertEmpty($node->getLeft()->getRight());
        $this->assertEmpty($node->getRight()->getRight());
        $this->assertEmpty($node->getRight()->getLeft());

        $node1 = $tree->searchTree(8);

        $this->assertEquals(8, $node1->getValue());
        $this->assertEquals(3, $node1->getLeft()->getValue());
        $this->assertEquals(10, $node1->getRight()->getValue());
        $this->assertEquals(1, $node1->getLeft()->getLeft()->getValue());
        $this->assertEquals(6, $node1->getLeft()->getRight()->getValue());
        $this->assertEmpty($node1->getRight()->getLeft());
        $this->assertEquals(14, $node1->getRight()->getRight()->getValue());
    }

    public function test_getDepth_success()
    {
        $singleTree = new BinaryTree(1);
        $this->assertEquals(0, $singleTree->getDepth());

        $defaultTree = $this->createDefaultTree();
        $this->assertEquals(3, $defaultTree->getDepth());

        $fullTree = $this->createFullTree();
        $this->assertEquals(2, $fullTree->getDepth());

        $completeTree = $this->createCompleteTree();
        $this->assertEquals(2, $completeTree->getDepth());
    }

    public function test_getFlattenArray_success()
    {
        $tree = $this->createDefaultTree();
        $resultArray = $tree->toArray();
        $targetArray = [8, 3, 1, null, null, 6, 4, 7, 10, null, null, null, 14, 13, null];

        $this->assertCount(15, $resultArray);
        $this->assertEquals($targetArray, $resultArray);
    }

    public function test_convertFromArray_success()
    {
        $sourceArray = [8, 3, 1, null, null, 6, 4, 7, 10, null, null, null, 14, 13, null];

        $node1 = new BinaryTree();
        $node1->populateTree($sourceArray);

        $this->assertEquals(3, $node1->getDepth());

        $this->assertEquals(8, $node1->getValue());
        $this->assertEquals(3, $node1->getLeft()->getValue());
        $this->assertEquals(10, $node1->getRight()->getValue());
        $this->assertEquals(1, $node1->getLeft()->getLeft()->getValue());
        $this->assertEquals(6, $node1->getLeft()->getRight()->getValue());
        $this->assertEmpty($node1->getRight()->getLeft());
        $this->assertEquals(14, $node1->getRight()->getRight()->getValue());
    }

    public function test_isFull_success()
    {
        $singleTree = new BinaryTree(1);
        $this->assertTrue($singleTree->isFull());

        $defaultTree = $this->createDefaultTree();
        $this->assertFalse($defaultTree->isFull());

        $fullTree = $this->createFullTree();
        $this->assertTrue($fullTree->isFull());

        $completeTree = $this->createCompleteTree();
        $this->assertTrue($completeTree->isFull());
    }

    public function test_isComplete_success()
    {
        $singleTree = new BinaryTree(1);
        $this->assertTrue($singleTree->isComplete());

        $defaultTree = $this->createDefaultTree();
        $this->assertFalse($defaultTree->isComplete());

        $fullTree = $this->createFullTree();
        $this->assertFalse($fullTree->isComplete());

        $completeTree = $this->createCompleteTree();
        $this->assertTrue($completeTree->isComplete());
    }

    private function createDefaultTree(): BinaryTree
    {
        //Tree visualization https://i.stack.imgur.com/7VxZe.png
        $node1 = new BinaryTree(6, 4, 7);
        $node2 = new BinaryTree(14, 13);
        $node3 = new BinaryTree(3, 1, $node1);
        $node4 = new BinaryTree(10, null, $node2);
        $node5 = new BinaryTree(8, $node3, $node4);

        return $node5;
    }

    private function createFullTree(): BinaryTree
    {
        $node1 = new BinaryTree(1, 2, 3);
        $node2 = new BinaryTree(4, $node1, 5);

        return $node2;
    }

    private function createCompleteTree(): BinaryTree
    {
        $node1 = new BinaryTree(1, 2, 3);
        $node2 = new BinaryTree(5, 6, 7);
        $node3 = new BinaryTree(4, $node1, $node2);

        return $node3;
    }

}