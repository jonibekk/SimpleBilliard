<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/ElasticSearch', "ESClient");
App::import('Lib/ElasticSearch', "ESSearchResponse");

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 11/21/2018
 * Time: 11:21 AM
 */
class ESClientTest extends GoalousTestCase
{
    public function test_getSearchResult_success()
    {
        $client = new ESClient();

        $params['post'] = [
            'pn'          => 1,
            'rn'          => 10,
            'file_name'   => 0,
            'circle'      => [],
            'circle_only' => 0
        ];

        $result = $client->search("test", 1, $params);

        $this->assertNotEmpty($result);

        $this->assertNotEmpty($result->getData('post')->getSearchResult());
    }
}