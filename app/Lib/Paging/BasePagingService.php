<?php
App::import('Lib/Paging', 'PagingServiceInterface');
App::import('Lib/DataStructure', 'BinaryNode');
App::import('Lib/Paging', 'PointerTree');

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
            'paging' => '',
            'count'  => 0
        ];

        //If only 1 flag is given, make it an array
        if (!is_array($extendFlags)) {
            $extendFlags = [$extendFlags];
        }

        $this->beforeRead($pagingRequest);
        $pagingRequest = $this->addDefaultValues($pagingRequest);

        $queryResult = $this->readData($pagingRequest, $limit + 1);

        //If there is further result
        if (count($queryResult) > $limit) {
            $nextHead = array_pop($queryResult);

            //Set end pointer values
            $pagingRequest->setPointer($this->createPointer($queryResult[--$limit], $nextHead, $pagingRequest));

            $finalResult['paging'] = $pagingRequest->returnCursor();
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
     * @param array         $lastElement     The array of result array's last element
     * @param array         $headNextElement The first element of the next page
     * @param PagingRequest $pagingRequest
     *
     * @return PointerTree
     */
    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree {
        return new PointerTree([static::MAIN_MODEL.'.id', ">", $lastElement['id']]);
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
        $pagingRequest->addOrder(static::MAIN_MODEL.'.id');
        return $pagingRequest;
    }

}
