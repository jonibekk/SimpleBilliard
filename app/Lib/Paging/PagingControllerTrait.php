<?php
App::uses('RequestPaging', 'Lib/Paging');

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
     * @return array
     */
    abstract protected function getPagingConditionFromRequest(CakeRequest $request): array;

    /**
     * Based on model's DB query condition
     */
    abstract protected function getResourceIdForCondition(): array;

    /**
     * Process paging parameters from passed cursor
     *
     * @param CakeRequest $request
     *
     * @return array
     */
    private function getPagingConditionFromCursor(CakeRequest $request)
    {
        $direction = '';
        if (isset($request['paging'][RequestPaging::PAGE_DIR_NEXT])) {
            $direction = RequestPaging::PAGE_DIR_NEXT;
            $cursor = $request['paging'][RequestPaging::PAGE_DIR_NEXT];
        } elseif (isset($request['paging'][RequestPaging::PAGE_DIR_PREV])) {
            $direction = RequestPaging::PAGE_DIR_PREV;
            $cursor = $request['paging'][RequestPaging::PAGE_DIR_PREV];
        }
        if (empty($cursor)) {
            return [];
        }

        $processedCursor = RequestPaging::decodeCursor($cursor);

        return [
            'conditions' => $processedCursor['conditions'],
            'pivot'      => $processedCursor['pivot'],
            'order'      => $processedCursor['order'],
            'direction'  => $direction
        ];
    }

    /**
     * Method for reading data from db using paging
     *
     * @param CakeRequest        $request
     * @param PagingServiceTrait $pagingService
     * @param int                $limit
     * @param array              $extendFlags Data extension flags
     *
     * @return array
     */
    protected function readData(CakeRequest $request, PagingServiceTrait $pagingService, int $limit, array $extendFlags)
    {
        if (empty($pagingService) || empty ($request)) {
            return [];
        }

        $parameters = $this->getPagingConditionFromCursor($request) ?? $this->getPagingConditionFromRequest($request);

        $requestPaging = new RequestPaging();

        return $requestPaging->getWithPaging(
            $pagingService,
            am($parameters['conditions'], $this->getResourceIdForCondition()),
            $parameters['pivot'],
            $limit,
            $parameters['order'],
            $parameters['direction'],
            $extendFlags);
    }
}