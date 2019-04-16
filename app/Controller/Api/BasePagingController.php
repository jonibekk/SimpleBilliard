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
    const DEFAULT_PAGE_LIMIT = 7;
    const MAX_PAGE_LIMIT = 100;

    /**
     * Get the limit of paging. If limit not given or above maximum amount, use default page limit
     *
     * @param int $defaultLimit Override the global default limit if needed
     *
     * @return int
     */
    protected function getPagingLimit(int $defaultLimit = 0)
    {
        $limit = $this->request->query('limit');

        if (empty($limit)) { //If not given, use default
            return (empty($defaultLimit)) ? self::DEFAULT_PAGE_LIMIT : $defaultLimit;
        }
        if ($limit > self::MAX_PAGE_LIMIT) { //If larger than max limit, return max
            return self::MAX_PAGE_LIMIT;
        }
        return $limit;
    }

    /**
     * Get extension options for paging
     *
     * @return array
     */
    protected function getExtensionOptions(): array
    {
        $stringOption = $this->request->query('extoption');

        $options = explode(',', $stringOption);

        if (empty($options[0])) {
            return [];
        }

        return $options;
    }

    /**
     * Method for getting paging parameters from request
     *
     * @param bool $skipCursor Force to get parameters from request, instead of given cursor
     *
     * @return PagingRequest
     */
    protected function getPagingParameters(bool $skipCursor = false)
    {
        if (empty ($this->request)) {
            return new PagingRequest();
        }

        if ($skipCursor) {
            $pagingRequest = $this->generatePagingRequest();
        } else {
            $pagingRequest = $this->generatePagingRequest($this->getPagingConditionFromCursor());
        }

        return $pagingRequest;
    }

    /**
     * Generate paging request
     *
     * @param PagingRequest If given, will insert to it instead
     *
     * @return PagingRequest
     */
    private function generatePagingRequest(PagingRequest $pagingRequest = null): PagingRequest
    {
        if (empty($pagingRequest)) {
            $pagingRequest = new PagingRequest();
        }

        //Add resource ID
        if (!empty($this->request->params['id'])) {
            $pagingRequest->setResourceId($this->request->params['id']);
        }
        $pagingRequest->setCurrentUserId($this->getUserId());
        $pagingRequest->setCurrentTeamId($this->getTeamId());

        $queries = array_filter($this->request->query, function ($queryKey) {
            if ($queryKey == "limit") {
                return false;
            }
            if ($queryKey == "extoption") {
                return false;
            }
            if ($queryKey == "cursor") {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_KEY);

        $pagingRequest->addQueries($queries);

        return $pagingRequest;
    }

    /**
     * Process paging parameters from passed cursor
     *
     * @return PagingRequest
     */
    private function getPagingConditionFromCursor()
    {
        $cursor = $this->request->query('cursor');

        if (empty($cursor)) {
            return new PagingRequest();
        }
        try {
            $pagingRequest = PagingRequest::decodeCursorToObject($cursor);
        } catch (RuntimeException $r) {
            throw $r;
        } catch (Exception $e) {
            $pagingRequest = new PagingRequest();
        }
        return $pagingRequest;
    }
}
