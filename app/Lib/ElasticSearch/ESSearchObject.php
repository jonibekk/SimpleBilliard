<?php
/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 11/20/2018
 * Time: 6:56 PM
 */

class ESSearchObject
{
    /**
     * Current page number
     *
     * @var int
     */
    private $pageNumber;

    /**
     * Maximum number of record per page
     *
     * @var int
     */
    private $recordNumber;

    /**
     * Total number of possible search hits
     *
     * @var int
     */
    private $totalResultCount;

    /**
     * List of search results in current page
     *
     * @var array
     */
    private $searchResult;

    public function __construct(array $data)
    {
        $this->pageNumber = $data['pn'];
        $this->recordNumber = $data['rn'];
        $this->totalResultCount = $data['total'];
        $this->searchResult = $data['results'];
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function getRecordNumber(): int
    {
        return $this->recordNumber;
    }

    public function getTotalResultCount(): int
    {
        return $this->totalResultCount;
    }

    public function getSearchResult(): array
    {
        return $this->searchResult;
    }

    /**
     * Check whether there is more search result after current page.
     * Case 1: Search result has no total count. Check by comparing result count with record number
     * Case 2: Search result has total count.
     *
     * @return bool
     */
    public function hasMore(): bool
    {
        if (empty($this->pageNumber) || empty($this->recordNumber) || is_null($this->totalResultCount)) {
            return false;
        }

        return ($this->totalResultCount === 0 && count($this->searchResult) === $this->recordNumber)
            || ($this->totalResultCount > $this->pageNumber * $this->recordNumber);
    }

    /**
     * Convert this object to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $result['pn'] = $this->pageNumber;
        $result['rn'] = $this->recordNumber;
        $result['total'] = $this->totalResultCount;
        $result['results'] = $this->searchResult;

        return $result;
    }
}