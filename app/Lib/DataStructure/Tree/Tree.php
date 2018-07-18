<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/18
 * Time: 15:53
 */

interface Tree
{
    public function getValue();

    public function getDepth(): int;

    public function isEmpty(): bool;

    public function isLeaf(): bool;

    public function searchTree($targetValue, Tree &$tree, callable $comparator = null);

    public function toArray(): array;
}