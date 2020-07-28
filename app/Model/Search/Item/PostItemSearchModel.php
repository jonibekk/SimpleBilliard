<?php

App::import('Model/Search/Item', 'BaseItemSearchModel');

/**
 * Class PostItemSearchModel
 */
class PostItemSearchModel extends BaseItemSearchModel
{
    /** @var string */
    public $content;
    /** @var string */
    public $dateTime;
    /** @var string */
    public $type;
    /** @var string */
    public $userImageUrl;
    /** @var string */
    public $userName;
}
