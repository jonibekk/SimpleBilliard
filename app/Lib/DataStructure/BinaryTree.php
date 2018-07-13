<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/12
 * Time: 13:25
 */

class BinaryTree
{
    /** @var BinaryNode */
    private $root;

    /** @var int */
    private $depth;

    public function __construct(BinaryNode $node = null)
    {
        $this->root = $node;

        if (!empty($node)) {
            $this->depth = $this->getMinimumDepth($node);
        }
    }

    public function setRoot(BinaryNode $node)
    {
        $this->root = $node;
        $this->depth = $this->getMinimumDepth($node);
    }

    public function &getRoot(): BinaryNode
    {
        return $this->root;
    }

    /**
     * Search the tree using depth-first algorithm.
     * Return the first node with matching value
     *
     * @param                 $value
     * @param BinaryNode      $node
     * @param callable        $comparator
     *
     * @return BinaryNode|null
     */
    public function searchNode($value, BinaryNode $node = null, callable $comparator = null)
    {
        if (empty($node)) {
            if (empty($this->root)) {
                return null;
            }
            $node = $this->root;
        }

        if (empty($comparator)) {
            $comparator = function ($arg1, $arg2) {
                return $arg1 == $arg2;
            };
        }

        if ($comparator($node->getValue(), $value)) {
            return $node;
        }

        if ($node->hasLeft()) {
            $result = $this->searchNode($value, $node->getLeft(), $comparator);
            if (!empty($result)) {
                return $result;
            }
        }
        if ($node->hasRight()) {
            $result = $this->searchNode($value, $node->getRight(), $comparator);
            if (!empty($result)) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Create a BinaryTree from 1-Dimensional Array
     *
     * @param array $sourceArray
     */
    public function generateTree(array $sourceArray)
    {
        $node = new BinaryNode();
        $this->decodeArray($sourceArray, $node);

        $this->__construct($node);
    }

    private function decodeArray(array &$sourceArray, BinaryNode &$node)
    {
        if (count($sourceArray) % 2 == 0) {
            throw new InvalidArgumentException("Invalid array");
        }

        if (empty($node)) {
            $node = new BinaryNode();
        }

        $value = array_shift($sourceArray);

        $node->setValue($value);

        $leftHalf = array_slice($sourceArray, 0, count($sourceArray) / 2);
        $rightHalf = array_slice($sourceArray, count($sourceArray) / 2);

        if (!empty($leftHalf[0])) {
            if (count($leftHalf) == 1) {
                $node->setLeft(new BinaryNode($leftHalf[0]));
            } else {
                $node->setLeft(new BinaryNode());
                $this->decodeArray($leftHalf, $node->getLeft());
            }
        }

        if (!empty($rightHalf[0])) {
            if (count($rightHalf) == 1) {
                $node->setRight(new BinaryNode($rightHalf[0]));
            } else {
                $node->setRight(new BinaryNode());
                $this->decodeArray($rightHalf, $node->getRight());
            }
        }
    }

    /**
     * Get the 1-dimensional representation of this tree
     *
     * @return array
     */
    public function generateArray(): array
    {
        $completed = $this->getRoot();
        $this->completeTree($completed);
        return $this->flattenArray($completed);
    }

    /**
     * Flatten the tree to 1-dimensional array
     *
     * @param BinaryNode $node
     *
     * @return array
     */
    private function flattenArray(BinaryNode $node = null): array
    {
        if (empty($node)) {
            return [];
        }

        $return[] = $node->getValue();

        if ($node->isLeaf()) {
            return $return;
        }

        if ($node->hasLeft()) {
            $return = array_merge($return, $this->flattenArray($node->getLeft()));
        }

        if ($node->hasRight()) {
            $return = array_merge($return, $this->flattenArray($node->getRight()));
        }

        return $return;
    }

    /**
     * Get the depth of this tree
     *
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * Calculate the depth of this tree
     *
     * @param BinaryNode $node
     *
     * @return int|mixed
     */
    private function getMinimumDepth(BinaryNode $node)
    {
        if (empty($node)) {
            return 0;
        }

        if ($node->isLeaf()) {
            return 1;
        }

        if (!$node->hasLeft()) {
            return $this->getMinimumDepth($node->getRight()) + 1;
        }

        if (!$node->hasRight()) {
            return $this->getMinimumDepth($node->getLeft()) + 1;
        }

        return min($this->getMinimumDepth($node->getLeft()), $this->getMinimumDepth($node->getRight())) + 1;
    }

    /**
     * Make this tree complete
     */
    public function makeComplete()
    {
        $this->completeTree($this->root);
    }

    /**
     * Make a binary tree complete
     *
     * @param BinaryNode $node
     * @param int        $currentDepth
     */
    public function completeTree(BinaryNode &$node, int $currentDepth = 0)
    {
        if (empty($node)) {
            throw new RuntimeException("Tree can't be empty");
        }

        if ($currentDepth >= $this->depth) {
            return;
        }

        if (!$node->hasLeft()) {
            $node->setLeft(new BinaryNode());
        }
        $this->completeTree($node->getLeft(), $currentDepth + 1);

        if (!$node->hasRight()) {
            $node->setRight(new BinaryNode());
        }
        $this->completeTree($node->getRight(), $currentDepth + 1);

    }

}