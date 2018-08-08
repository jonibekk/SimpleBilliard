<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/18
 * Time: 15:53
 */

interface Tree
{
    /**
     * Get the current value of this node
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Get the depth of this tree
     *
     * @return int
     */
    public function getDepth(): int;

    /**
     * Check whether this node has value
     *
     * @return bool True = no value
     */
    public function isEmpty(): bool;

    /**
     * Check whether this node doesn't have any child
     *
     * @return bool True = no child
     */
    public function isLeaf(): bool;

    /**
     * Search the node containing targeted value
     *
     * @param               $targetValue
     * @param Tree          $tree
     * @param callable|null $comparator Custom comparator to compare targeted & actual value
     *
     * @return mixed
     */
    public function searchTree($targetValue, Tree &$tree, callable $comparator = null): Tree;

    /**
     * Convert this tree to array
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Fill this tree with values in the array
     *
     * @param array $source
     *
     * @return mixed
     */
    public function populateTree(array $source);
}