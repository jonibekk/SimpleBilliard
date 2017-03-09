<?php
App::uses('ApiController', 'Controller/Api');
/** @noinspection PhpUndefinedClassInspection */

/**
 * Class UsersController
 */
class UsersController extends ApiController
{

    /**
     * Search users
     * url: GET /api/v1/users/search
     *
     * @queryParam int $cursor optional
     * @queryParam int $limit optional
     * @queryParam string $keyword optional
     * @queryParam array $exclude_user_ids optional
     * @return CakeResponse
     * @link       https://confluence.goalous.com/display/GOAL/%5BGET%5D+Search+users
     */
    function get_search()
    {
        $cursor = $this->request->query('cursor');
        $limit = $this->request->query('limit');
        $keyword = $this->request->query('keyword');
        $excludeUserIds = $this->request->query('exclude_user_ids');

        $retMock = [];
        $retMock['data'] = [
            [
                'id'               => 1,
                'img_url'          => '/img/no-image.jpg',
                'display_username' => '平形 大樹(Daiki Hirakata)',
            ],
            [
                'id'               => 2,
                'img_url'          => '/img/no-image.jpg',
                'display_username' => '佐伯 翔平(Shohei Saeki)',
            ],
            [
                'id'               => 3,
                'img_url'          => '/img/no-image.jpg',
                'display_username' => '菊池 厚平(Kohei Kikuchi)',
            ],
            [
                'id'               => 4,
                'img_url'          => '/img/no-image.jpg',
                'display_username' => '吉田 将之(Masayuki Yoshida)',
            ],
        ];

        $retMock['pagenation'] = [
            'next' => "/api/v1/users/search?cursor=11111&limit=10&keyword=hoge",
        ];
        return $this->_getResponsePagingSuccess($retMock);
    }

}
