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
     * @param array  $conditions
     * @param mixed  $pivotValue
     * @param int    $limit
     * @param string $order
     * @param string $direction
     * @param array  $extendFlags
     *
     * @return array
     */
    public function getDataWithPaging(
        $conditions = [],
        $pivotValue = null,
        $limit = RequestPaging::DEFAULT_PAGE_LIMIT,
        $order = RequestPaging::PAGE_ORDER_DESC,
        $direction = RequestPaging::PAGE_DIR_NEXT,
        $extendFlags = []
    ) {

        $finalResult = [
            'data'   => [],
            'paging' => [
                'next' => '',
                'prev' => ''
            ],
            'count'  => 0
        ];

        $this->beforeRead();

        $finalResult['count'] = $this->countData($conditions);

        $queryResult = $this->readData($conditions, $pivotValue, $limit + 1, $order, $direction);

        //If there is further result
        if (count($queryResult) > $limit) {
            array_pop($queryResult);

            //Get the last element pivot value
            $newPivotValue = $this->getPivotValue($queryResult);

            if ($direction == RequestPaging::PAGE_DIR_NEXT || $direction == RequestPaging::PAGE_DIR_PREV) {
                $queryResult['paging'][$direction] = RequestPaging::createPageCursor($newPivotValue, $order,
                    $conditions,
                    $direction);
            }
        }

        if (!empty($extendFlags) && !empty($queryResult)) {
            $this->extendPagingResult($queryResult, $extendFlags);
        }

        $this->afterRead();

        $finalResult['data'] = $queryResult;

        return $finalResult;
    }

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
    abstract protected function readData($conditions, $pivotValue, $limit, $order, $direction): array;

    /**
     * Count the number of data matching conditions provided
     *
     * @param array $conditions
     *
     * @return int
     */
    abstract protected function countData($conditions): int;

    /**
     * Method to be called before reading data from db.
     * Override to use
     */
    protected function beforeRead()
    {
        return true;
    }

    /**
     * Method to be called after reading data from db
     * Override to use
     */
    protected function afterRead()
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
    protected function extendPagingResult(&$resultArray, $flags = [])
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
    protected function getPivotValue($lastElement)
    {
        return $lastElement['id'];
    }

}