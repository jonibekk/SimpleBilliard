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
     * @param string      $direction
     *
     * @return array
     */
    abstract protected function getPagingConditionFromRequest(CakeRequest $request, string $direction): array;

    /**
     * Based on model's DB query condition
     */
    abstract protected function getResourceIdForCondition(): array;

    /**
     * Process paging parameters from passed cursor
     *
     * @param CakeRequest $request
     * @param string      $direction
     *
     * @return array
     */
    private function getPagingConditionFromCursor(CakeRequest $request, string $direction)
    {
        $cursor = $request['paging']['direction'];

        if (empty($cursor)) {
            return [];
        }

        $processedCursor = RequestPaging::decodeCursor($cursor);

        return [
            'conditions' => Hash::get($processedCursor, 'conditions'),
            'pivot' => Hash::get($processedCursor, 'pivot'),
            'order' => Hash::get($processedCursor, 'order'),

            $direction
        ];
    }

    /**
     * Method for reading data from db using paging
     *
     * @param CakeRequest        $request
     * @param PagingServiceTrait $pagingService
     * @param int                $limit
     * @param string             $direction
     * @param array              $extendFlags Data extension flags
     *
     * @return array
     */
    protected function readData(
        CakeRequest $request,
        PagingServiceTrait $pagingService,
        int $limit,
        string $direction,
        array $extendFlags
    ) {
        if (empty($pagingService) || empty ($request)) {
            return [];
        }

        $parameters = $this->getPagingConditionFromCursor($request, $direction) ??
            $this->getPagingConditionFromRequest($request, $direction);

        return $pagingService->getDataWithPaging(
            am($parameters['conditions'], $this->getResourceIdForCondition()),
            Hash::get($parameters, 'pivot'),
            $limit,
            Hash::get($parameters, 'order'),
            Hash::get($parameters, 'direction'),
            $extendFlags);
    }
}