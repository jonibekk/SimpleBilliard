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
        $limit = BasePagingController::DEFAULT_PAGE_LIMIT,
        $extendFlags = []
    ): array
    {

        // Check whether exist current user id and team id
        $this->validatePagingResource($pagingRequest);

        $finalResult = [
            'data'   => [],
            'cursor' => '',
            'count'  => 0
        ];

        //If only 1 flag is given, make it an array
        if (!is_array($extendFlags)) {
            $extendFlags = [$extendFlags];
        }

        $pagingRequest = $this->beforeRead($pagingRequest);
        $pagingRequest = $this->addDefaultValues($pagingRequest);

        $queryResult = $this->readData($pagingRequest, $limit + 1);

        //If there is further result
        if (count($queryResult) > $limit) {
            $nextHead = array_pop($queryResult);

            //Set end pointer values
            $pagingRequest->setPointer($this->createPointer($queryResult[--$limit], $nextHead, $pagingRequest));

            $finalResult['cursor'] = $pagingRequest->returnCursor();
        }

        $finalResult['count'] = $this->countData($pagingRequest);

        if (!empty($extendFlags) && !empty($queryResult)) {
            $this->extendPagingResult($queryResult, $pagingRequest, $extendFlags);
        }

        $queryResult = $this->afterRead($queryResult, $pagingRequest);

        $finalResult['data'] = $queryResult;

        return $finalResult;
    }

    /**
     * Check whether exist current user id and team id in paging request
     *
     * @param PagingRequest $pagingRequest
     */
    protected final function validatePagingResource(PagingRequest $pagingRequest)
    {
        if (empty($pagingRequest->getCurrentUserId())) {
            GoalousLog::error("Missing current user id");
            throw new UnexpectedValueException("Missing current user id");
        }
        if (empty($pagingRequest->getCurrentTeamId())) {
            GoalousLog::error("Missing current team id");
            throw new UnexpectedValueException("Missing current team id");
        }
    }

    /**
     * Method to be called before reading data from db.
     * Override to use
     *
     * @param PagingRequest $pagingRequest
     *
     * @return PagingRequest
     */
    protected function beforeRead(PagingRequest $pagingRequest)
    {
        return $pagingRequest;
    }

    /**
     * Get pointer value to define beginning point of next page
     * Default to using id
     *
     * @return PointerTree
     */
    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree
    {
        return new PointerTree([static::MAIN_MODEL . '.id', ">", $lastElement['id']]);
    }

    /**
     * Method to be called after reading data from db
     * Override to use
     *
     * @param array         $queryResult
     * @param PagingRequest $pagingRequest
     *
     * @return array
     */
    protected function afterRead(array $queryResult, PagingRequest $pagingRequest): array
    {
        return $queryResult;
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
     * @param array         $data    Content to be extended
     * @param PagingRequest $request Conditions used for getting the result
     * @param array         $options Extension options
     *
     * @return array
     */
    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        return $data;
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
}
