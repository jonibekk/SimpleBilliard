<?php

App::import('Model/Search/Item', 'BaseItemSearchModel');

/**
 * Class TypeSearchModel
 */
class TypeSearchModel
{
    /** @var array|BaseItemSearchModel[] */
    public $items;
    /** @var int */
    public $totalItemsCount;

    /**
     * TypeSearchModel constructor.
     */
    public function __construct()
    {
        $this->items = [];
        $this->totalItemsCount = 0;
    }
}
