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
     * Get the limit of paging. If limit not given or above maximum amount, use default page limit
     *
     * @return int
     */
    protected function getPagingLimit()
    {
        $limit = $this->request->query('limit');

        if ($limit > PagingRequest::MAX_PAGE_LIMIT) { //If larger than max limit, return max
            return PagingRequest::MAX_PAGE_LIMIT;
        } elseif (empty($limit)) { //If not given, use default
            return PagingRequest::DEFAULT_PAGE_LIMIT;
        } else {
            return $limit;
        }
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

        if (!empty($this->request->params['id'])) {
            $pagingRequest->addResource('res_id', $this->request->params['id']);
        }
        $pagingRequest->addResource('current_user_id', $this->getUserId());
        $pagingRequest->addResource('current_team_id', $this->getTeamId());
        $queries = array_filter($this->request->query, function ($array) {
            if ($array == "limit") {
                return false;
            }
            if ($array == "extoption") {
                return false;
            }
            if ($array == "cursor") {
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