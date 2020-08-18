<?php

App::import('Model/Dto/Search/Item', 'BaseItemSearchDto');

/**
 * Class PostItemSearchDto
 */
class PostItemSearchDto extends BaseItemSearchDto
{
    /** @var string */
    public $content;
    /** @var string */
    public $dateTime;
    /** @var string */
    public $type;
    /** @var int */
    public $userId;
    /** @var string */
    public $userImageUrl;
    /** @var string */
    public $userName;
}
