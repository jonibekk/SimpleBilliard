<?php

App::import('Model/Dto/Search/Item', 'SearchApiBaseItemDto');

/**
 * Class SearchResultsDto
 */
class SearchResultsDto
{
    /** @var array|BaseItemSearchDto[] */
    public $items;
    /** @var int */
    public $totalItemsCount;

    /**
     * SearchResultsDto constructor.
     */
    public function __construct()
    {
        $this->items = [];
        $this->totalItemsCount = 0;
    }
}
