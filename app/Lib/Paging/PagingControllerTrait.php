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
     * @return PagingCursor
     */
    abstract protected function getPagingConditionFromRequest(): PagingCursor;

    /**
     * Based on model's DB query condition
     */
    abstract protected function getResourceIdForCondition(): array;

    /**
     * Get the limit of paging
     *
     * @param CakeRequest $request
     *
     * @return int
     */
    protected function getPagingLimit(CakeRequest $request)
    {
        return $request['limit'] ?? PagingCursor::DEFAULT_PAGE_LIMIT;
    }

    /**
     * Method for getting paging parameters from request
     *
     * @param CakeRequest $request
     * @param bool        $skipCursor Force to get parameters from request, instead of given cursor
     *
     * @return PagingCursor
     */
    protected function getPagingParameters(CakeRequest $request, bool $skipCursor = false)
    {
        if (empty ($request)) {
            return new PagingCursor();
        }

        $pagingCursor = $this->getPagingConditionFromCursor($request);

        //If skipping using cursor, or cursor is empty, replace it with parameters from request
        if ($skipCursor || $pagingCursor->isEmpty()) {
            $pagingCursor = $this->getPagingConditionFromRequest($request);
        }

        //Merge existing condition with resource ID embedded in URL
        $pagingCursor->addCondition($this->getResourceIdForCondition());

        return $pagingCursor;
    }

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
}