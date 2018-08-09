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
     * @param PagingRequest $pagingRequest
     * @param int           $limit
     * @param array         $extendFlags
     *
     * @return array
     */
    public final function getDataWithPaging(
        $pagingRequest,
        $limit = PagingRequest::DEFAULT_PAGE_LIMIT,
        $extendFlags = []
    ): array {

        $finalResult = [
            'data'   => [],
            'paging' => [
                'next' => '',
                'prev' => ''
            ],
            'count'  => 0
        ];

        //If only 1 flag is given, make it an array
        if (!is_array($extendFlags)) {
            $extendFlags = [$extendFlags];
        }

        $this->beforeRead($pagingRequest);
        $pagingRequest = $this->addDefaultValues($pagingRequest);

        $pointerValues = $pagingRequest->getPointers();

        $queryResult = $this->readData($pagingRequest, $limit + 1);

        //If there is further result
        if (count($queryResult) > $limit) {
            array_pop($queryResult);

            //Set end pointer values
            $pagingRequest->addPointerArray($this->getEndPointerValue($queryResult[--$limit]));

            $finalResult['paging']['next'] = $pagingRequest->returnCursor();
        }

        //If there is previous result
        //Non-empty pointers means not the first page
        if (count($queryResult) > 0 && !empty($pointerValues)) {

            //Set start pointer value
            $pagingRequest->setPointer($this->getStartPointerValue($queryResult[0]));

            $finalResult['paging']['prev'] = $pagingRequest->returnCursor();
        }

        $finalResult['count'] = $this->countData($pagingRequest);

        if (!empty($extendFlags) && !empty($queryResult)) {
            $this->extendPagingResult($queryResult, $pagingRequest, $extendFlags);
        }

        $this->afterRead($pagingRequest);

        $finalResult['data'] = $queryResult;

        return $finalResult;
    }

    /**
     * Method to be called before reading data from db.
     * Override to use
     *
     * @param PagingRequest $pagingRequest
     *
     * @return bool
     */
    protected function beforeRead(PagingRequest $pagingRequest)
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
        return [static::MAIN_MODEL . '.id', ">", $lastElement['id']];
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
        return [static::MAIN_MODEL . '.id', "<", $firstElement['id']];
    }

    /**
     * Method to be called after reading data from db
     * Override to use
     *
     * @param PagingRequest $pagingRequest
     *
     * @return bool
     */
    protected function afterRead(PagingRequest $pagingRequest)
    {
        return true;
    }

    /**
     * Method for reading data from DB, based on the parameters
     *
     * @param PagingRequest $pagingRequest Conditions for paging query
     * @param int           $limit         Number of records to be read
     *
     * @return array Query result
     */
    abstract protected function readData(PagingRequest $pagingRequest, int $limit): array;

    /**
     * Count the number of data matching conditions provided
     *
     * @param PagingRequest $request
     *
     * @return int
     */
    abstract protected function countData(PagingRequest $request): int;

    /**
     * Extend result arrays with additional contents
     * Override to use
     *
     * @param array         $resultArray Content to be extended
     * @param PagingRequest $request     Conditions used for getting the result
     * @param array         $options     Extension options
     *
     * @return array
     */
    protected function extendPagingResult(array &$resultArray, PagingRequest $request, array $options = [])
    {
        return $resultArray;
    }

    /**
     * Attach additional values to PagingRequest before usage
     *
     * @param PagingRequest $pagingRequest
     *
     * @return PagingRequest
     */
    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        $pagingRequest->addOrder(static::MAIN_MODEL . '.id');
        return $pagingRequest;
    }

    /**
     * Check whether ext options include target ext
     *
     * @param string $targetExt
     * @param array  $options
     *
     * @return bool
     */
    protected function includeExt(array $options, string $targetExt): bool
    {
        if (in_array(static::EXTEND_ALL, $options)) {
            return true;
        }
        if (in_array($targetExt, $options)) {
            return true;
        }
        return false;
    }

}
