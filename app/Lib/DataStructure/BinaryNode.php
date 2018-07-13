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
            if ($left instanceof self) {
                $this->left = $left;
            } else {
                $this->left = new self($left);
            }
        }
        if (!empty($right)) {
            if ($right instanceof self) {
                $this->right = $right;
            } else {
                $this->right = new self($right);
            }
        }
    }

    public function &getValue()
    {
        return $this->value;
    }

    /**
     * @return BinaryNode | null
     */
    public function &getLeft()
    {
        return $this->left;
    }

    /**
     * @return BinaryNode | null
     */
    public function &getRight()
    {
        return $this->right;
    }

    /**
     * @param BinaryNode $left
     */
    public function setLeft(BinaryNode $left)
    {
        $this->left = $left;
    }

    /**
     * @param BinaryNode $right
     */
    public function setRight(BinaryNode $right)
    {
        $this->right = $right;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function hasLeft()
    {
        return !empty($this->left);
    }

    public function hasRight()
    {
        return !empty($this->right);
    }

    public function isLeaf(): bool
    {
        return !$this->hasLeft() && !$this->hasRight();
    }

}