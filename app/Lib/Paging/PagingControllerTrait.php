<?php

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/15
 * Time: 10:05
 */
trait PagingControllerTrait
{

    /**
     * Get paging conditions from request
     *
     * @param CakeRequest $request
     *
     * @return PagingCursor
     */
    abstract protected function getPagingConditionFromRequest(CakeRequest $request): PagingCursor;

    /**
     * Based on model's DB query condition
     */
    abstract protected function getResourceIdForCondition(): array;

    /**
     * Process paging parameters from passed cursor
     *
     * @param CakeRequest $request
     *
     * @return PagingCursor
     */
    private function getPagingConditionFromCursor(CakeRequest $request)
    {
        $cursor = $request->query['cursor'];

        if (empty($cursor)) {
            return new PagingCursor();
        }

        return PagingCursor::decodeCursorToObject($cursor);
    }

    /**
     * Method for reading data from db using paging
     *
     * @param CakeRequest            $request
     * @param PagingServiceInterface $pagingService
     * @param int                    $limit
     * @param bool                   $skipCursor  Skip using cursor, force using request as paging parameters
     * @param array                  $extendFlags Data extension flags
     *
     * @return array
     */
    protected function readData(
        CakeRequest $request,
        PagingServiceInterface $pagingService,
        int $limit,
        bool $skipCursor = false,
        array $extendFlags
    ) {
        if (empty($pagingService) || empty ($request)) {
            return [];
        }

        $pagingCursor = $this->getPagingConditionFromCursor($request);

        //If skipping using cursor, or cursor is empty, replace it with parameters from request
        if ($skipCursor || $pagingCursor->isEmpty()) {
            $pagingCursor = $this->getPagingConditionFromRequest($request);
        }

        //Merge existing condition with resource ID embedded in URL
        $pagingCursor->addCondition($this->getResourceIdForCondition());

        return $pagingService->getDataWithPaging(
            $pagingCursor,
            $limit,
            $extendFlags);
    }
}