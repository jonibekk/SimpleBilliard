<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/10
 * Time: 12:11
 */

interface ApiPagingInterface
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
    public function readData($conditions, $pivotValue, $limit, $order, $direction): array;

    /**
     * Count the number of data matching conditions provided
     *
     * @param array $conditions
     *
     * @return int
     */
    public function countData($conditions): int;

    /**
     * Method to be called before reading data from db
     */
    public function beforeRead();

    /**
     * Method to be called after reading data from db
     */
    public function afterRead();

    /**
     * Extend result arrays with additional contents
     *
     * @param array $resultArray Content to be extended
     * @param array $flags       Extension flags
     *
     * @return mixed
     */
    public function extendPagingResult(&$resultArray, $flags);

    /**
     * Get pivot value to define beginning point of next page
     *
     * @param array $resultElement The array of result array's last element
     *
     * @return mixed
     */
    public function getPivotValue($resultElement);

}