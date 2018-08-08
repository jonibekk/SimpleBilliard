<?php
App::import('Lib/DataStructure/Tree', 'BinaryTree');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/12
 * Time: 13:23
 */
class PointerTree extends BinaryTree implements Tree
{
    /**
     * Generate a SQL query condition array from this pointer tree
     *
     * @return array
     */
    public function toCondition()
    {
        if (empty($this->getValue())) {
            return [];
        } else {
            return $this->flattenTreeForCondition($this);
        }
    }

    private function flattenTreeForCondition(BinaryTree $tree): array
    {
        $result = [];

        //Only leaf contains pointer
        if ($tree->isLeaf()) {
            $result[] = $this->valueToString($tree->value);
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
     * @param Tree|null       $node
     * @param callable|null   $comparator
     *
     * @return PointerTree|null
     */
    public function &searchTree($targetValue, Tree &$node = null, callable $comparator = null): Tree
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

        /** @var PointerTree $result */
        $result = &parent::searchTree($targetValue, $node, $comparator);

        return $result;
    }

    private function valueToString(array $value): string
    {
        if (empty($value)) {
            return "";
        }
        if (count($value) != 3) {
            var_dump($value);
            throw new RuntimeException("Wrong array size");
        }
        return "$value[0] $value[1] $value[2]";
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
        if (count($pointer) != 3 && !is_string($pointer[0])) {
            throw new InvalidArgumentException("Invalid pointer array");
        }

        if ($this->isEmpty()) {
            $this->setValue($pointer);
            return true;
        }

        $node = new PointerTree();
        $this->findDeepestAndNode($this, $node);

        if (empty($node)) {
            return false;
        }

        if ($node->isLeaf()) {
            $node->setValue($pointer);
        } elseif (!$node->hasLeft()) {
            $node->setLeft(new BinaryTree($pointer));
        } elseif (!$node->hasRight()) {
            $node->setRight(new BinaryTree($pointer));
        } else {
            $rightPointer = $node->getRight();
            $node->setRight(new BinaryTree('AND', $rightPointer, $pointer));
        }

        return true;
    }

    private function &findDeepestAndNode(
        BinaryTree &$node,
        BinaryTree &$result,
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