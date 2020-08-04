<?php
App::import('Lib/ElasticSearch', "ESSearchResponse");

/**
 * Client for calling Elastic Search backend
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 11/20/2018
 * Time: 5:57 PM
 */

use GuzzleHttp\Client;

class ESClient
{
    const SEARCH_URL = "/api/search";
    const UPDATE_URL = "/api/update";
    const DELETE_URL = "/api/delete";

    public function __construct()
    {
        if (empty(ES_API_BASE_URL)) {
            throw new RuntimeException("Missing ES base URL");
        }
    }

    /**
     * Search data from ES node
     *
     * @param string $query
     * @param int    $teamId
     * @param array  $params
     *
     * @return ESSearchResponse
     */
    public function search(string $query, int $teamId, array $params): ESSearchResponse
    {
        $requestBody['query'] = $query;
        $requestBody['team_id'] = $teamId;
        $requestBody['params'] = $params;

        return new ESSearchResponse($this->post(ES_API_BASE_URL . self::SEARCH_URL, $requestBody));
    }

    /**
     * Update cached data in ES node
     *
     * @param array $params
     * @param int   $type
     *
     * @return array
     */
    public function update(array $params, int $type)
    {
        $requestBody['params'] = $params;
        $requestBody['type'] = $type;

        return $this->post(ES_API_BASE_URL . self::UPDATE_URL, $requestBody);

    }

    /**
     * Delete cached data in ES node
     *
     * @param array $params
     * @param int   $type
     *
     * @return array
     */
    public function delete(array $params, int $type)
    {
        $requestBody['params'] = $params;
        $requestBody['type'] = $type;

        return $this->post(ES_API_BASE_URL . self::DELETE_URL, $requestBody);
    }

    /**
     * General function
     *
     * @param string $url
     * @param array  $requestBody
     *
     * @return array
     */
    private function post(string $url, array $requestBody): array
    {
        $client = new Client();

        try {
            $response = $client->request('POST', $url, ['json' => $requestBody]);
            $json = json_decode($response->getBody()->getContents(), true);
            return $json ?: [];
        } catch (Exception $exception) {
            GoalousLog::error($exception->getMessage(), $exception->getTrace());
            return [];
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            GoalousLog::error($e->getMessage(), $e->getTrace());
            return [];
        }
    }
}
