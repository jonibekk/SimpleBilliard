<?php

/**
 * Class SearchApiRequestDto
 */
class SearchApiRequestDto {
    /** @var null|string */
    public $keyword;
    /** @var int */
    public $limit;
    /** @var int */
    public $pageNumber;
    /** @var null|int */
    public $teamId;
    /** @var null|string */
    public $type;
    /** @var null|int */
    public $userId;

    /**
     * SearchApiRequestDto constructor.
     */
    public function __construct()
    {
        $this->limit = 3;
        $this->pageNumber = 1;
    }
}
