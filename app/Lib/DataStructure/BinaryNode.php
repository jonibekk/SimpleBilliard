<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/12
 * Time: 13:36
 */

class BinaryNode
{
    /**
     * Value of this node
     *
     * @var mixed
     */
    private $value;

    /**
     * @var BinaryNode
     */
    private $left;

    /**
     * @var BinaryNode
     */
    private $right;

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
     * @return BinaryNode | null
     */
    public function &getLeft()
    {
        return $this->left;
    }

    /**
     * Get the node of right child
     *
     * @return BinaryNode | null
     */
    public function &getRight()
    {
        return $this->right;
    }

    /**
     * Set the left child of this node
     *
     * @param BinaryNode $left
     */
    public function setLeft(BinaryNode $left)
    {
        $this->left = $left;
    }

    /**
     * Set the right child of this node
     *
     * @param BinaryNode $right
     */
    public function setRight(BinaryNode $right)
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

}