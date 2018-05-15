<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/10
 * Time: 12:11
 */

trait PagingServiceTrait
{
    /**
     * Method for reading data from DB, based on the parameters
     *
     * @param array  $conditions Conditions for paging query
     * @param mixed  $pivotValue Starting ID for the search
     * @param int    $limit      Number of records to be read
     * @param string $order      Order of query
     * @param string $direction  Direction of query
     *
     * @return array Query result
     */
    abstract public function readData($conditions, $pivotValue, $limit, $order, $direction): array;

    /**
     * Count the number of data matching conditions provided
     *
     * @param array $conditions
     *
     * @return int
     */
    abstract public function countData($conditions): int;

    /**
     * Method to be called before reading data from db.
     * Override to use
     */
    public function beforeRead()
    {
        return true;
    }

    /**
     * Method to be called after reading data from db
     * Override to use
     */
    public function afterRead()
    {
        return true;
    }

    /**
     * Extend result arrays with additional contents
     * Override to use
     *
     * @param array $resultArray Content to be extended
     * @param array $flags       Extension flags
     *
     * @return array
     */
    public function extendPagingResult(&$resultArray, $flags = [])
    {
        return $resultArray;
    }

    /**
     * Get pivot value to define beginning point of next page
     * Default to using id
     *
     * @param array $lastElement The array of result array's last element
     *
     * @return mixed
     */
    public function getPivotValue($lastElement)
    {
        return $lastElement['id'];
    }

}