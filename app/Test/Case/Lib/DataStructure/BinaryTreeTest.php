<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/DataStructure', 'BinaryTree');
App::import('Lib/DataStructure', 'BinaryNode');

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
        $node = $tree->searchNode(6);

        $this->assertEquals(6, $node->getValue());
        $this->assertEquals(4, $node->getLeft()->getValue());
        $this->assertEquals(7, $node->getRight()->getValue());
        $this->assertEmpty($node->getLeft()->getLeft());
        $this->assertEmpty($node->getLeft()->getRight());
        $this->assertEmpty($node->getRight()->getRight());
        $this->assertEmpty($node->getRight()->getLeft());

        $node1 = $tree->searchNode(8);

        $this->assertEquals(8, $node1->getValue());
        $this->assertEquals(3, $node1->getLeft()->getValue());
        $this->assertEquals(10, $node1->getRight()->getValue());
        $this->assertEquals(1, $node1->getLeft()->getLeft()->getValue());
        $this->assertEquals(6, $node1->getLeft()->getRight()->getValue());
        $this->assertEmpty($node1->getRight()->getLeft());
        $this->assertEquals(14, $node1->getRight()->getRight()->getValue());
    }

    public function test_getFlattenArray_success()
    {
        $tree = $this->createDefaultTree();
        $resultArray = $tree->generateArray();
        $targetArray = [8, 3, 1, null, null, 6, 4, 7, 10, null, null, null, 14, 13, null];

        $this->assertCount(15, $resultArray);
        $this->assertEquals($targetArray,$resultArray);
    }

    public function test_convertFromArray_success()
    {
        $sourceArray = [8, 3, 1, null, null, 6, 4, 7, 10, null, null, null, 14, 13, null];

        $tree = new BinaryTree();
        $tree->generateTree($sourceArray);

        $this->assertEquals(3, $tree->getDepth());

        $node1 = $tree->getRoot();

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
        $tree = $this->createDefaultTree();

        $this->assertEquals(3, $tree->getDepth());
    }

    private function createDefaultTree(): BinaryTree
    {
        //Tree visualization https://i.stack.imgur.com/7VxZe.png
        $node1 = new BinaryNode(6, 4, 7);
        $node2 = new BinaryNode(14, 13);
        $node3 = new BinaryNode(3, 1, $node1);
        $node4 = new BinaryNode(10, null, $node2);
        $node5 = new BinaryNode(8, $node3, $node4);

        return new BinaryTree($node5);
    }

}