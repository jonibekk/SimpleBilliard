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

    /**
     * Flatten pointer tree for SQL query condition
     *
     * @param BinaryTree $tree
     *
     * @return array
     */
    private function flattenTreeForCondition(BinaryTree $tree): array
    {
        $result = [];

        //Only leaf contains pointer
        if ($tree->isLeaf()) {
            if (is_string($tree->getValue())) {
                throw new RuntimeException("Invalid tree structure");
            }
            $result[] = $this->valueToString($tree->getValue());

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
    public function searchTree($targetValue, Tree &$node = null, callable $comparator = null)
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
        $result = parent::searchTree($targetValue, $node, $comparator);

        return $result;
    }

    /**
     * Convert pointer to query string
     *
     * @param array $value
     *
     * @return string
     */
    private function valueToString(array $value): string
    {
        if (empty($value)) {
            return "";
        }
        if (count($value) != 3) {
            throw new RuntimeException("Wrong array size");
        }

        $string = "$value[0] $value[1] ";

        if (is_bool($value[2])) {
            return $string . (int)$value[2];
        }

        return $string . $value[2];
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
     * Add a pointer to the tree, chained as AND
     *
     * @param array $pointer
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function addPointer(array $pointer)
    {
        if (count($pointer) != 3 || !is_string($pointer[0])) {
            throw new InvalidArgumentException("Invalid pointer array");
        }

        if (!$this->traverseAdd($this, $pointer)) {
            throw new RuntimeException("Failed to add new pointer");
        }
    }

    /**
     * Traverse all 'AND' nodes, and append new value on the first empty spot.
     *
     * @param BinaryTree $tree
     * @param array      $newPointer
     *
     * @return bool True on successful addition.
     */
    private function traverseAdd(BinaryTree $tree, array $newPointer): bool
    {
        $value = $tree->getValue();

        if (empty($value)) {
            $tree->setValue($newPointer);
            return true;
        } elseif ($value === 'AND') {
            if ($tree->isLeaf()) {
                $tree->setLeft(new PointerTree($newPointer));
                return true;
            }
            if (!$tree->hasLeft()) {
                $tree->setLeft(new PointerTree(($newPointer)));
                return true;
            } elseif ($this->traverseAdd($tree->getLeft(), $newPointer)) {
                return true;
            }
            if (!$tree->hasRight()) {
                $tree->setRight(new PointerTree(($newPointer)));
                return true;
            } elseif ($this->traverseAdd($tree->getRight(), $newPointer)) {
                return true;
            }
            return false;
        } elseif (is_array($value) && count($value) === 3) {
            $tree->setValue('AND');
            $tree->setLeft(new PointerTree($newPointer));
            $tree->setRight(new PointerTree($value));
            return true;
        } else {
            return false;
        }
    }
}