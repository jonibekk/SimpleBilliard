<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/18
 * Time: 15:53
 */

class BinaryTree implements Tree
{
    /**
     * Value of this node
     *
     * @var mixed
     */
    protected $value;

    /**
     * @var BinaryTree
     */
    protected $left;

    /**
     * @var BinaryTree
     */
    protected $right;

    /**
     * @var int
     */
    protected $depth;

    /**
     * BinaryNode constructor.
     *
     * @param mixed $value
     * @param mixed $left
     * @param mixed $right
     */
    public function __construct($value = null, $left = null, $right = null)
    {
        $this->value = $value;

        if (!empty($left)) {
            $this->left = ($left instanceof self) ? $left : new self($left);
        }
        if (!empty($right)) {
            $this->right = ($right instanceof self) ? $right : new self($right);
        }

        $this->depth = $this->calculateDepth($this);
    }

    /**
     * Get the value of this node
     *
     * @return mixed|null
     */
    public function &getValue()
    {
        return $this->value;
    }

    /**
     * Get the node of left child
     *
     * @return BinaryTree | null
     */
    public function &getLeft()
    {
        return $this->left;
    }

    /**
     * Get the node of right child
     *
     * @return BinaryTree | null
     */
    public function &getRight()
    {
        return $this->right;
    }

    /**
     * Set the left child of this node
     *
     * @param BinaryTree $left
     */
    public function setLeft(BinaryTree $left)
    {
        $this->left = $left;
    }

    /**
     * Set the right child of this node
     *
     * @param BinaryTree $right
     */
    public function setRight(BinaryTree $right)
    {
        $this->right = $right;
    }

    /**
     * Set the value of this node
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Check whether this node has left child
     *
     * @return bool
     */
    public function hasLeft()
    {
        return !empty($this->left);
    }

    /**
     * Check whether this node has right child
     *
     * @return bool
     */
    public function hasRight()
    {
        return !empty($this->right);
    }

    /**
     * Check whether this node is a leaf
     * Leaf node is a node without any children
     *
     * @return bool
     */
    public function isLeaf(): bool
    {
        return !$this->hasLeft() && !$this->hasRight();
    }

    /**
     * Check whether this node has value
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    /**
     * Search the tree using depth-first algorithm.
     * Return the first node with matching value
     *
     * @param                 $targetValue
     * @param Tree            $node
     * @param callable        $comparator
     *
     * @return BinaryTree|null
     */
    public function &searchTree($targetValue, Tree &$node = null, callable $comparator = null): BinaryTree
    {
        if (!($node instanceof BinaryTree)) {
            throw new InvalidArgumentException("Invalid tree type");
        }
        if (empty($node)) {
            if (empty($this->root)) {
                $null = null;
                return $null;
            }
            $node = $this->root;
        }

        //If comparator not given, use default one.
        if (empty($comparator)) {
            $comparator = function ($currentValue, $targetValue) {
                return $currentValue === $targetValue;
            };
        }

        if ($comparator($node->getValue(), $targetValue)) {
            $return = &$node;
            return $return;
        }

        if ($node->hasLeft()) {
            $left = &$node->getLeft();
            $result = &$this->searchTree($targetValue, $left, $comparator);
            if (!empty($result)) {
                return $result;
            }
        }
        if ($node->hasRight()) {
            $right = &$node->getRight();
            $result = &$this->searchTree($targetValue, $right, $comparator);
            if (!empty($result)) {
                return $result;
            }
        }
        $null = null;
        return $null;
    }

    /**
     * Create a BinaryTree from 1-Dimensional Array.
     * Array count must be 2^(depth + 1) - 1
     *
     * @param array $sourceArray
     */
    public function generateTree(array $sourceArray)
    {
        $node = new BinaryTree();
        $this->decodeArray($sourceArray, $node);
        $this->__construct($node);
    }

    /**
     * Decode an array to a tree structure
     *
     * @param array      $sourceArray
     * @param BinaryTree $node
     */
    private function decodeArray(array &$sourceArray, BinaryTree &$node)
    {
        if (count($sourceArray) % 2 == 0) {
            throw new InvalidArgumentException("Invalid array");
        }

        if (empty($node)) {
            $node = new BinaryTree();
        }

        $value = array_shift($sourceArray);

        $node->setValue($value);

        $leftHalf = array_slice($sourceArray, 0, count($sourceArray) / 2);
        $rightHalf = array_slice($sourceArray, count($sourceArray) / 2);

        if (!empty($leftHalf[0])) {
            if (count($leftHalf) == 1) {
                $node->setLeft(new BinaryTree($leftHalf[0]));
            } else {
                $node->setLeft(new BinaryTree());
                $this->decodeArray($leftHalf, $node->getLeft());
            }
        }

        if (!empty($rightHalf[0])) {
            if (count($rightHalf) == 1) {
                $node->setRight(new BinaryTree($rightHalf[0]));
            } else {
                $node->setRight(new BinaryTree());
                $this->decodeArray($rightHalf, $node->getRight());
            }
        }
    }

    /**
     * Get the 1-dimensional representation of this tree
     *
     * @param bool $fillTree Whether the tree is made complete first before making array. MUST be used for creating pointer
     *
     * @return array
     */
    public function toArray(bool $fillTree = true): array
    {
        if ($this->isEmpty()) {
            throw new RuntimeException("Tree can't be empty");
        }
        if ($fillTree) {
            $this->completeTree($this->value);
        }
        return $this->flattenArray($this->value);
    }

    /**
     * Update the depth of current tree
     */
    public function updateDepth()
    {
        $this->depth = $this->calculateDepth($this);
    }

    /**
     * Calculate the depth of this tree
     *
     * @param BinaryTree $node
     *
     * @return int
     */
    protected function calculateDepth(BinaryTree $node): int
    {
        if (empty($node)) {
            return 0;
        }
        if ($node->isLeaf()) {
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
     * @param BinaryTree $node
     * @param int        $currentDepth
     */
    public function completeTree(BinaryTree &$node, int $currentDepth = 0)
    {
        if (empty($node)) {
            throw new RuntimeException("Tree can't be empty");
        }

        if ($currentDepth >= $this->depth) {
            return;
        }

        if (!$node->hasLeft()) {
            $node->setLeft(new BinaryTree());
        }
        $this->completeTree($node->getLeft(), $currentDepth + 1);

        if (!$node->hasRight()) {
            $node->setRight(new BinaryTree());
        }
        $this->completeTree($node->getRight(), $currentDepth + 1);
    }

    /**
     * Flatten the tree to 1-dimensional array
     *
     * @param BinaryTree $node
     *
     * @return array
     */
    private function flattenArray(BinaryTree $node = null): array
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
        $this->completeTree($this);
    }

    /**
     * Check whether the tree is complete.
     * Complete tree means tree is full & all deepest nodes have the same depth
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->checkComplete($this);
    }

    private function checkComplete(BinaryTree $node = null, int $currentDepth = 0): bool
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
        return $this->checkFull($this);
    }

    private function checkFull(BinaryTree $node = null): bool
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