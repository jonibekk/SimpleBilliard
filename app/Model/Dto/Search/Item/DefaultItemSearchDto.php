<?php

App::import('Model/Dto/Search/Item', 'BaseItemSearchDto');

/**
 * Class DefaultItemSearchDto
 */
class DefaultItemSearchDto extends BaseItemSearchDto
{
    /** @var string */
    public $name;
}
