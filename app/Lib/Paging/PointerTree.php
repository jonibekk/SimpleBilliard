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
    /**
     * Generate a SQL query condition array from this pointer tree
     *
     * @return array
     */
    public function toCondition()
    {
        if (!empty($this->getRoot())) {
            return $this->flattenTreeForCondition($this->getRoot());
        } else {
            return [];
        }
    }

    private function flattenTreeForCondition(BinaryNode $tree): array
    {
        $result = [];

        //Only leaf contains pointer
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
    public function &searchNode($targetValue, BinaryNode &$node = null, callable $comparator = null)
    {
        if (empty($comparator)) {
            $comparator = function ($node, string $target) {
                if (empty($target)) {
                    throw new InvalidArgumentException('Missing parameter');
                }
                if (is_array($node) && count($node) == 3) {
                    return $node[0] === $target;
                } else {
                    return false;
                }
            };
        }

        $result = &parent::searchNode($targetValue, $node, $comparator);
        return $result;
    }

    private function convertPointerToString(array $pointer): string
    {
        if (count($pointer) != 3) {
            throw new RuntimeException("Wrong array size");
        }
        return "$pointer[0] $pointer[1] $pointer[2]";
    }

    /**
     * Add multiple pointers to tree
     *
     * @param array $pointers
     *
     * @throws InvalidArgumentException
     */
    public function addManyPointers(array $pointers)
    {
        foreach ($pointers as $pointer) {
            $this->addPointer($pointer);
        }
    }

    /**
     * Add a pointer to the tree
     *
     * @param array $pointer
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function addPointer(array $pointer): bool
    {
        if (empty($this->root)) {
            $this->setRoot(new BinaryNode($pointer));
            return true;
        }
        if (count($pointer) != 3 && !is_string($pointer[0])) {
            throw new InvalidArgumentException("Invalid pointer array");
        }

        $node = new BinaryNode();
        $this->findDeepestAndNode($this->root, $node);

        if (empty($node)) {
            return false;
        }

        if ($node->isLeaf()) {
            $node->setValue($pointer);
        } elseif (!$node->hasLeft()) {
            $node->setLeft(new BinaryNode($pointer));
        } elseif (!$node->hasRight()) {
            $node->setRight(new BinaryNode($pointer));
        } else {
            $rightPointer = $node->getRight();
            $node->setRight(new BinaryNode('AND', $rightPointer, $pointer));
        }

        return true;
    }

    private function &findDeepestAndNode(
        BinaryNode &$node,
        BinaryNode &$result,
        int $currentDepth = 0,
        int $latestDepth = 0
    ) {
        if ($node->getValue() === 'AND') {

            if ($currentDepth > $latestDepth) {
                $latestDepth = $currentDepth;
                $result = $node;
            }

            if ($node->hasLeft()) {
                $left = &$node->getLeft();
                $this->findDeepestAndNode($left, $result, $currentDepth + 1, $latestDepth);
            }
            if ($node->hasRight()) {
                $right = &$node->getRight();
                $this->findDeepestAndNode($right, $result, $currentDepth + 1, $latestDepth);
            }
        }
    }
}