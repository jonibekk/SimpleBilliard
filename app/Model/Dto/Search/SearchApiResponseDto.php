<?php

App::import('Model/Dto/Search', 'SearchResultsDto');

/**
 * Class SearchApiResponseDto
 */
class SearchApiResponseDto
{
    /** @var SearchResultsDto */
    public $actions;
    /** @var SearchResultsDto */
    public $circles;
    /** @var SearchResultsDto */
    public $members;
    /** @var SearchResultsDto */
    public $posts;

    /**
     * SearchApiResponseDto constructor.
     */
    public function __construct()
    {
        $this->actions = new SearchResultsDto();
        $this->circles = new SearchResultsDto();
        $this->members = new SearchResultsDto();
        $this->posts = new SearchResultsDto();
    }
}
