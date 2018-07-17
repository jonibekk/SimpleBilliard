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
    protected $root;

    /** @var int */
    private $depth;

    /**
     * Search the tree using depth-first algorithm.
     * Return the first node with matching value
     *
     * @param                 $targetValue
     * @param BinaryNode      $node
     * @param callable        $comparator
     *
     * @return BinaryNode|null
     */
    public function &searchNode($targetValue, BinaryNode &$node = null, callable $comparator = null)
    {
        if (empty($node)) {
            if (empty($this->root)) {
                $null = null;
                return $null;
            }
            $node = $this->root;
        }

        if (empty($comparator)) {
            $comparator = function ($arg1, $arg2) {
                return $arg1 === $arg2;
            };
        }

        if ($comparator($node->getValue(), $targetValue)) {
            return $node;
        }

        if ($node->hasLeft()) {
            $left = &$node->getLeft();
            $result = &$this->searchNode($targetValue, $left, $comparator);
            if (!empty($result)) {
                return $result;
            }
        }
        if ($node->hasRight()) {
            $right = &$node->getRight();
            $result = &$this->searchNode($targetValue, $right, $comparator);
            if (!empty($result)) {
                return $result;
            }
        }
        $null = null;
        return $null;
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

    public function __construct(BinaryNode $node = null)
    {
        $this->root = $node;
        if (!empty($node)) {
            $this->depth = $this->calculateDepth($node);
        }
    }

    /**
     * Get the 1-dimensional representation of this tree
     *
     * @param bool $fillTree Whether the tree is made complete first before making array. MUST be used for creating pointer
     *
     * @return array
     */
    public function generateArray(bool $fillTree = true): array
    {
        $completed = $this->getRoot();
        if (empty($completed)){
            throw new RuntimeException("Tree can't be empty");
        }
        if ($fillTree) {
            $this->completeTree($completed);
        }
        return $this->flattenArray($completed);
    }

    /**
     * Get the root of this tree.
     *
     * @return BinaryNode | null
     */
    public function &getRoot()
    {
        return $this->root;
    }

    /**
     * Set the root node of this tree
     *
     * @param BinaryNode $node
     */
    public function setRoot(BinaryNode $node)
    {
        $this->root = $node;
        $this->depth = $this->calculateDepth($node);
    }

    /**
     * Update the depth of current tree
     */
    public function updateDepth()
    {
        $this->depth = $this->calculateDepth($this->root);
    }

    /**
     * Calculate the depth of this tree
     *
     * @param BinaryNode $node
     *
     * @return int
     */
    protected function calculateDepth(BinaryNode $node)
    {
        if (empty($node) || $node->isLeaf()) {
            return 0;
        }
        if (!$node->hasLeft()) {
            return $this->calculateDepth($node->getRight()) + 1;
        }
        if (!$node->hasRight()) {
            return $this->calculateDepth($node->getLeft()) + 1;
        }
        return max($this->calculateDepth($node->getLeft()), $this->calculateDepth($node->getRight())) + 1;
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
     * Make this tree complete
     */
    public function makeComplete()
    {
        $this->completeTree($this->root);
    }

    /**
     * Check whether the tree is complete.
     * Complete tree means tree is full & all deepest nodes have the same depth
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->checkComplete($this->root);
    }

    private function checkComplete(BinaryNode $node = null, int $currentDepth = 0): bool
    {
        if (empty($node)) {
            return false;
        }

        if ($node->hasLeft() xor $node->hasRight()) {
            return false;
        }

        if ($node->isLeaf()) {
            if ($currentDepth == $this->depth) {
                return true;
            } else {
                return false;
            }
        }

        return $this->checkComplete($node->getLeft(), $currentDepth + 1) &&
            $this->checkComplete($node->getRight(), $currentDepth + 1);
    }

    /**
     * Check if a the tree is full.
     * Full tree means each node either have no children or both children
     *
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->checkFull($this->root);
    }

    private function checkFull(BinaryNode $node = null): bool
    {
        if (empty($node)) {
            return false;
        }

        if ($node->isLeaf()) {
            return true;
        }

        if ($node->hasLeft() xor $node->hasRight()) {
            return false;
        }

        return $this->checkFull($node->getLeft()) && $this->checkFull($node->getRight());
    }

}