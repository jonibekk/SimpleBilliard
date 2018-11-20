<?php
App::import('Lib/ElasticSearch', 'ESSearchObject');

/**
 * Object to contain search result from elastic search
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 11/20/2018
 * Time: 6:52 PM
 */
class ESSearchResponse
{
    /** @var string */
    private $errorMessage;
    /** @var int */
    private $errorNumber;
    /** @var ESSearchObject[] */
    private $searchData;

    public function __construct(array $rawData)
    {
        $this->errorMessage = $rawData['errmsg'];
        $this->errorNumber = $rawData['errno'];
        foreach ($rawData['data'] as $model => $data) {
            $this->searchData[$model] = new ESSearchObject($data);
        }
    }

    /**
     * Get search response
     *
     * @param string $model
     *
     * @return ESSearchObject
     */
    public function getData(string $model): ESSearchObject
    {
        return $this->searchData[$model];
    }

    /**
     * Get all search result
     *
     * @return ESSearchObject[]
     */
    public function getAllData(): array
    {
        return $this->searchData;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getErrorNumber(): int
    {
        return $this->errorNumber;
    }
}