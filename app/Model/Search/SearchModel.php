<?php

App::import('Model/Search', 'TypeSearchModel');

use Goalous\Enum\SearchEnum;

/**
 * Class SearchModel
 */
class SearchModel
{
    /** @var TypeSearchModel */
    public $actions;
    /** @var TypeSearchModel */
    public $circles;
    /** @var TypeSearchModel */
    public $members;
    /** @var TypeSearchModel */
    public $posts;

    /**
     * SearchModel constructor.
     */
    public function __construct()
    {
        $this->actions = new TypeSearchModel();
        $this->circles = new TypeSearchModel();
        $this->members = new TypeSearchModel();
        $this->posts = new TypeSearchModel();
    }
}
