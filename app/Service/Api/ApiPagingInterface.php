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
     * @param int    $currentId Starting ID for the search
     * @param int    $limit     Number of records to be read
     * @param string $direction Direction of query
     *
     * @return array Query result
     */
    public function readData($currentId, $limit, $direction): array;

    /**
     * Method to be called before reading data from db
     */
    public function beforeRead();

    /**
     * Method to be called after reading data from db
     */
    public function afterRead();

    /**
     * Set db query parameters into private variables
     *
     * @param array $parameters
     */
    public function setPagingParameters(array $parameters);

    /**
     * Read private query parameters and return them as multi-dimensional array
     *
     * @return array
     */
    public function getPagingParameters(): array;
}