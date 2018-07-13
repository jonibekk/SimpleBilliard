<?php
App::import('Lib/DataStructure', 'BinaryTree');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/12
 * Time: 13:23
 */
class PointerTree extends BinaryTree
{
    public function toCondition()
    {
        return $this->flattenTreeForCondition($this->getRoot());
    }

    private function flattenTreeForCondition(BinaryNode $tree): array
    {
        $result = [];

        if ($tree->isLeaf()) {
            $result[] = $this->convertPointerToString($tree->getValue());
            return $result;
        }

        /** @var string $operator */
        $operator = $tree->getValue();

        if ($tree->hasLeft()) {
            $result[$operator][] = $this->flattenTreeForCondition($tree->getLeft());
        }
        if ($tree->hasRight()) {
            $result[$operator][] = $this->flattenTreeForCondition($tree->getRight());
        }

        return $result;
    }

    /**
     * Get the first pointer with the same key as inputted one
     *
     * @param                 $targetValue
     * @param BinaryNode|null $node
     * @param callable|null   $comparator
     *
     * @return BinaryNode|null
     */
    public function searchNode($targetValue, BinaryNode $node = null, callable $comparator = null)
    {
        $comparator = function (array $node, string $target) {
            if (count($node) != 3 || empty($target)) {
                throw new InvalidArgumentException('Missing parameter');
            }
            return $node[0] == $target;
        };

        return parent::searchNode($targetValue, $node, $comparator);
    }

    private function convertPointerToString(array $pointer): string
    {
        if (count($pointer) != 3) {
            throw new RuntimeException("Wrong array size");
        }
        return "$pointer[0] $pointer[1] $pointer[2]";
    }
}