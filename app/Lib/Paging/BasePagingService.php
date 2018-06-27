<?php
App::import('Lib/Paging', 'PagingServiceInterface');
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/10
 * Time: 12:11
 */

abstract class BasePagingService implements PagingServiceInterface
{
    /**
     * Implement interface PagingServiceInterface, should this trait used in a class
     *
     * @param PagingCursor $pagingCursor
     * @param int          $limit
     * @param array        $extendFlags
     *
     * @return array
     */
    public final function getDataWithPaging(
        $pagingCursor,
        $limit = PagingCursor::DEFAULT_PAGE_LIMIT,
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

        $finalResult['count'] = $this->countData($pagingCursor->getConditions());

        $pointerValues = $pagingCursor->getPointers();

        $queryResult = $this->readData($pagingCursor, $limit + 1);

        //If there is further result
        if (count($queryResult) > $limit) {
            array_pop($queryResult);

            //Set end pointer values
            $pagingCursor->addPointerArray($this->getEndPointerValue($queryResult[--$limit]));

            $finalResult['paging']['next'] = $pagingCursor->returnCursor();
        }

        //If there is previous result
        //Non-empty pointers means not the first page
        if (count($queryResult) > 0 && !empty($pointerValues)) {

            //Set start pointer value
            $pagingCursor->setPointer($this->getStartPointerValue($queryResult[0]));

            $finalResult['paging']['prev'] = $pagingCursor->returnCursor();
        }

        if (!empty($extendFlags) && !empty($queryResult)) {
            $this->extendPagingResult($queryResult, $pagingCursor->getConditions(), $extendFlags);
        }

        $this->afterRead();

        $finalResult['data'] = $queryResult;

        return $finalResult;
    }

    /**
     * Method to be called before reading data from db.
     * Override to use
     */
    protected function beforeRead()
    {
        return true;
    }

    /**
     * Get pointer value to define beginning point of next page
     * Default to using id
     *
     * @param array $lastElement The array of result array's last element
     *
     * @return array
     */
    protected function getEndPointerValue($lastElement)
    {
        return ['id', ">", $lastElement['id']];
    }

    /**
     * Get pointer value to define end point of previous page
     * Default to using id
     *
     * @param array $firstElement The array of result array's last element
     *
     * @return array
     */
    protected function getStartPointerValue($firstElement)
    {
        return ['id', "<", $firstElement['id']];
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
     * Method for reading data from DB, based on the parameters
     *
     * @param PagingCursor $pagingCursor Conditions for paging query
     * @param int          $limit        Number of records to be read
     *
     * @return array Query result
     */
    abstract protected function readData(PagingCUrsor $pagingCursor, int $limit): array;

    /**
     * Count the number of data matching conditions provided
     *
     * @param array $conditions
     *
     * @return int
     */
    abstract protected function countData(array $conditions): int;

    /**
     * Extend result arrays with additional contents
     * Override to use
     *
     * @param array $resultArray Content to be extended
     * @param array $conditions  Conditions used for getting the result
     * @param array $options     Extension options
     *
     * @return array
     */
    protected function extendPagingResult(&$resultArray, $conditions, $options = [])
    {
        return $resultArray;
    }

}