<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/15
 * Time: 10:05
 */

trait ControllerPaginator
{
    protected $conditions;
    protected $order;
    protected $pivot;
    protected $direction;

    abstract protected function getPagingConditions(CakeRequest $request): array;

    protected function setResourceIdToCondition($key, $value)
    {
        $condition[$key] = $value;
    }

    private function processCursor(CakeRequest $request)
    {
        if (isset($request['paging'][RequestPaging::PAGE_DIR_NEXT])) {
            $this->direction = RequestPaging::PAGE_DIR_NEXT;
            $cursor = $request['paging'][RequestPaging::PAGE_DIR_NEXT];
        } elseif (isset($request['paging'][RequestPaging::PAGE_DIR_PREV])) {
            $this->direction = RequestPaging::PAGE_DIR_PREV;
            $cursor = $request['paging'][RequestPaging::PAGE_DIR_PREV];
        }
        if (empty($cursor)) {
            return false;
        }

        $processedCursor = RequestPaging::decodeCursor($cursor);

        $this->conditions = $processedCursor['conditions'];
        $this->order = $processedCursor['order'];
        $this->pivot = $processedCursor['pivot'];

        return true;
    }

    protected function readData(CakeRequest $request, ApiPagingInterface $implementer, int $limit)
    {
        if (empty($implementer) || empty ($request)) {
            return [];
        }

        if (!$this->processCursor($request)) {
            $this->getPagingConditions($request);
        }

        return $implementer->readData($this->conditions, $this->pivot, $limit, $this->order, $this->direction);
    }
}