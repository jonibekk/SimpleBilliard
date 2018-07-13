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
        var_dump($condition);
    }

    private function createDefaultTree():PointerTree
    {
        $node1 = new BinaryNode(['id', '<', 2]);
        $node2 = new BinaryNode('AND', ['time', '>=', 100], ['del_flg', '=', true]);
        $node3 = new BinaryNode('OR', $node1, $node2);

        return new PointerTree($node3);
    }

}