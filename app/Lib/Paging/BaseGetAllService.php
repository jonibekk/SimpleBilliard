<?php

abstract class BaseGetAllService
{

    /**
     * Get all mentions and not including with paging data
     *
     * @param       $pagingRequest
     * @param array $extendFlags
     *
     * @return array
     */
    public function getAllData(
        $pagingRequest,
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

        $this->beforeRead($pagingRequest);
        $pagingRequest = $this->addDefaultValues($pagingRequest);

        $queryResult = $this->readData($pagingRequest, 0);
        $finalResult['count'] = count($queryResult);

        if (!empty($extendFlags) && !empty($queryResult)) {
            $this->extendPagingResult($queryResult, $pagingRequest, $extendFlags);
        }

        $this->afterRead($queryResult, $pagingRequest);

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
