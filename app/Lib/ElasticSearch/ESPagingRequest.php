<?php
/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 11/20/2018
 * Time: 8:09 PM
 */

class ESPagingRequest
{
    /**
     * HTTP request URL query
     *
     * @var array
     */
    private $query = [];

    /**
     * Paging request condition
     *
     * @var array
     *      [$key => $value]
     */
    private $condition = [];

    /**
     * Temporary paging request condition.
     * Will not be included to the paging cursor
     *
     * @var array
     *      [$key => $value]
     */
    private $tempCondition;

    public function __construct(array $condition = [])
    {
        $this->condition = $condition;
    }

    public function setQuery(array $query)
    {
        $this->query = $query;
    }

    /**
     * Add new condition
     *
     * @param string $key
     * @param        $value
     * @param bool   $overwrite Overwrite existing same key
     */
    public function addCondition(string $key, $value, bool $overwrite = false)
    {
        if ($overwrite || empty($this->condition[$key])) {
            $this->condition[$key] = $value;
        }
    }

    /**
     * Add HTTP query into request ocndition
     *
     * @param string $key
     * @param bool   $overwrite Overwrite existing key if exist
     * @param mixed  $default   Default value if given key doesn't exist in query
     */
    public function addQueryToCondition(string $key, bool $overwrite = false, $default = null)
    {
        if ($overwrite || empty($this->condition[$key])) {
            $this->condition[$key] = Hash::get($this->query, $key, $default);
        }
    }

    /**
     * Add new temporary condition
     *
     * @param string $key
     * @param        $value
     * @param bool   $overwrite Overwrite existing same key
     */
    public function addTempCondition(string $key, $value, bool $overwrite = false)
    {
        if ($overwrite || empty($this->tempCondition[$key])) {
            $this->tempCondition[$key] = $value;
        }
    }

    /**
     * Replace condition
     *
     * @param array $condition
     */
    public function setCondition(array $condition)
    {
        $this->condition = $condition;
    }

    /**
     * Get saved condition based on key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getCondition(string $key)
    {
        return Hash::get($this->condition, $key, null);
    }

    /**
     * Get saved temporary condition based on key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getTempCondition(string $key)
    {
        return Hash::get($this->tempCondition, $key, null);
    }

    /**
     * Get all saved conditions
     *
     * @param $includeTemp bool Include temporary condition
     *
     * @return array
     */
    public function getAllConditions(bool $includeTemp = false): array
    {
        if ($includeTemp) {
            return am($this->condition, $this->tempCondition);
        }
        return $this->condition;
    }

    /**
     * Convert condition to JSON
     *
     * @return string
     */
    public function getJSON(): string
    {
        return json_encode($this->condition);
    }

    /**
     * Convert condition to base64 encoded string
     *
     * @return string
     */
    public function getBase64(): string
    {
        return base64_encode($this->getJSON());
    }

    /**
     * Convert JSON encoded string into paging request
     *
     * @param string $rawData
     *
     * @return ESPagingRequest
     */
    public static function convertJSON(string $rawData): self
    {
        try {
            $condition = json_decode($rawData, true);
        } catch (Exception $exception) {
            $condition = [];
        }
        return new self($condition);
    }

    /**
     * Convert Base64 encoded string into paging request
     *
     * @param string $rawData
     *
     * @return ESPagingRequest
     */
    public static function convertBase64(string $rawData): self
    {
        try {
            $jsonDecoded = base64_decode($rawData, true);
            $condition = json_decode($jsonDecoded, true);
        } catch (Exception $exception) {
            $condition = [];
        }
        return new self($condition);
    }
}