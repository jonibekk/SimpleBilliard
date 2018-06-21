<?php
App::uses('BaseApiController', 'Controller/Api');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/15
 * Time: 10:05
 */
abstract class  BasePagingController extends BaseApiController
{
    /**
     * Get paging conditions from request
     *
     * @return PagingCursor
     */
    abstract protected function getPagingConditionFromRequest(): PagingCursor;

    /**
     * Get the limit of paging
     *
     * @return int
     */
    protected function getPagingLimit()
    {
        return $this->request->query('limit') ?? PagingCursor::DEFAULT_PAGE_LIMIT;
    }

    /**
     * Get extension options for paging
     *
     * @return array
     */
    protected function getExtensionOptions(): array
    {
        $stringOption = $this->request->query('extoption');

        $res = explode(',', $stringOption);

        return $res ?? [];
    }

    /**
     * Method for getting paging parameters from request
     *
     * @param bool $skipCursor Force to get parameters from request, instead of given cursor
     *
     * @return PagingCursor
     */
    protected function getPagingParameters(bool $skipCursor = false)
    {
        if (empty ($this->request)) {
            return new PagingCursor();
        }

        $pagingCursor = $this->getPagingConditionFromCursor();

        //If skipping using cursor, or cursor is empty, replace it with parameters from request
        if ($skipCursor || $pagingCursor->isEmpty()) {
            $pagingCursor = $this->getPagingConditionFromRequest();
        }

        return $pagingCursor;
    }

    /**
     * Process paging parameters from passed cursor
     *
     * @return PagingCursor
     */
    private function getPagingConditionFromCursor()
    {
        $cursor = $this->request->query('cursor');

        if (empty($cursor)) {
            return new PagingCursor();
        }

        return PagingCursor::decodeCursorToObject($cursor);
    }
}