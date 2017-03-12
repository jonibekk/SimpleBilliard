<?php
App::uses('ApiController', 'Controller/Api');
/** @noinspection PhpUndefinedClassInspection */

/**
 * Class TopicsController
 */
class TopicsController extends ApiController
{

    /**
     * Get topic detail
     * url: GET /api/v1/topics/{topic_id}
     *
     * @param int $topicId
     *
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BGET%5D+Topic+detail+page
     * TODO: This is mock! We have to implement it!
     */
    function get_detail(int $topicId)
    {
        $retMock = [];
        $retMock['data'] = [
            "topic"    => [
                "id"              => 1,
                "tilte"           => "", // タイトル変更時に変更前タイトル表示用に利用
                "display_title"   => "大樹,翔平,厚平,将之", //　表示用タイトル(トピック名が無い場合はメンバー名の羅列にする)
                "read_count"      => 3,
                "members_count"   => 4,
                "can_leave_topic" => true,
            ],
            'messages' => [
                [
                    'id'              => 123,
                    'body'            => 'あついなー。',
                    'created'         => 1438585548,
                    'display_created' => '03/09 13:51',
                    'type'            => 1,
                    'user'            => [
                        'id'               => 2,
                        'img_url'          => '/img/no-image.jpg',
                        'display_username' => '佐伯 翔平',
                    ]
                ],
                [
                    'id'              => 124,
                    'body'            => 'そうかなー。',
                    'created'         => 1438585558,
                    'display_created' => '03/09 13:52',
                    'type'            => 1,
                    'user'            => [
                        'id'               => 4,
                        'img_url'          => '/img/no-image.jpg',
                        'display_username' => '吉田 将之',
                    ]
                ],
                [
                    'id'              => 125,
                    'body'            => '全然あつくないでしょ。恋でもしてるの？',
                    'created'         => 1438585568,
                    'display_created' => '03/09 13:53',
                    'type'            => 1,
                    'user'            => [
                        'id'               => 1,
                        'img_url'          => '/img/no-image.jpg',
                        'display_username' => '平形 大樹',
                    ]
                ],
                [
                    'id'              => 126,
                    'body'            => '利尻いってこい。涼しいぞ。',
                    'created'         => 1438585578,
                    'display_created' => '03/09 13:54',
                    'type'            => 1,
                    'user'            => [
                        'id'               => 3,
                        'img_url'          => '/img/no-image.jpg',
                        'display_username' => '菊池 厚平',
                    ],

                ],
            ]
        ];
        $retMock['paging'] = [
            'next' => "/api/v1/topics/123/messages?cursor=11111&limit=10",
        ];

        return $this->_getResponsePagingSuccess($retMock);
    }

    /**
     * Get Messages on topic
     * url: GET /api/v1/topics/{topic_id}/messages
     *
     * @param int $topicId
     *
     * @queryParam int $cursor optional
     * @queryParam int $limit optional
     * @return CakeResponse
     * @link       https://confluence.goalous.com/display/GOAL/%5BGET%5D+Topic+message+list
     *             TODO: This is mock! We have to implement it!
     */
    function get_messages(int $topicId)
    {
        $cursor = $this->request->query('cursor');
        $limit = $this->request->query('limit');

        $retMock = [];
        $retMock['data'] = [
            [
                'id'              => 123,
                'body'            => 'あついなー。',
                'created'         => 1438585548,
                'display_created' => '03/09 13:51',
                'type'            => 1,
                'user'            => [
                    'id'               => 2,
                    'img_url'          => '/img/no-image.jpg',
                    'display_username' => '佐伯 翔平',
                ],
                'attached_files'  => [
                    [
                        'id'            => 1,
                        'ext'           => 'jpg',
                        'type'          => 1,
                        'download_url'  => '/img/no-image.jpg',
                        'preview_url'   => '',
                        'thumbnail_url' => '/img/no-image.jpg',
                    ],
                ]
            ],
            [
                'id'              => 124,
                'body'            => 'そうかなー。',
                'created'         => 1438585558,
                'display_created' => '03/09 13:52',
                'type'            => 1,
                'user'            => [
                    'id'               => 4,
                    'img_url'          => '/img/no-image.jpg',
                    'display_username' => '吉田 将之',
                ]
            ],
            [
                'id'              => 125,
                'body'            => '全然あつくないでしょ。恋でもしてるの？',
                'created'         => 1438585568,
                'display_created' => '03/09 13:53',
                'type'            => 1,
                'user'            => [
                    'id'               => 1,
                    'img_url'          => '/img/no-image.jpg',
                    'display_username' => '平形 大樹',
                ]
            ],
            [
                'id'              => 126,
                'body'            => '利尻いってこい。涼しいぞ。',
                'created'         => 1438585578,
                'display_created' => '03/09 13:54',
                'type'            => 1,
                'user'            => [
                    'id'               => 3,
                    'img_url'          => '/img/no-image.jpg',
                    'display_username' => '菊池 厚平',
                ],

            ],
        ];
        $retMock['paging'] = [
            'next' => "/api/v1/topics/123/messages?cursor=11111&limit=10",
        ];
        return $this->_getResponsePagingSuccess($retMock);
    }

    /**
     * Get topic members
     * url: GET /api/v1/topics/{topic_id}/members
     *
     * @param int $topicId
     *
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BGET%5D+Topic+member+list
     * TODO: This is mock! We have to implement it!
     */
    function get_members(int $topicId)
    {
        $retMockData = [
            'users'        => [
                [
                    'id'               => '1',
                    "img_url"          => "https://goalous-release2-assets.s3.amazonaws.com/users/1/843c2194af311ce15624357b5eb85a4a_small.JPG?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1488936809&Signature=NoPa3dWbYu0vZ5kNqIz%2BT%2F003i0%3D",
                    "display_username" => "Daiki Hirakata",
                ],
                [
                    'id'               => '2',
                    "img_url"          => "https://goalous-release2-assets.s3.amazonaws.com/users/1/843c2194af311ce15624357b5eb85a4a_small.JPG?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1488936809&Signature=NoPa3dWbYu0vZ5kNqIz%2BT%2F003i0%3D",
                    "display_username" => "Kohei Kikuchi",
                ],
            ],
            'member_count' => 2,
        ];
        $retMockHtml = <<<HTML
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">×</span></button>
            <h4 class="modal-title font_18px font_bold">会話のメンバー (6)</h4>
        </div>
        <div class="modal-body without-footer">
            <div class="row borderBottom">
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            西田 昂弘 440点&nbsp;
                        </div>

                    </div>
                </div>
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            吉田 将之&nbsp;
                        </div>

                    </div>
                </div>
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            佐伯 翔平&nbsp;
                        </div>

                    </div>
                </div>
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            古山 浩志&nbsp;
                        </div>

                    </div>
                </div>
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            平形 大樹 565-&gt;625&nbsp;
                        </div>

                    </div>
                </div>
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            平野 愛&nbsp;
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
HTML;
        return $this->_getResponseSuccess($retMockData, $retMockHtml);

    }

    /**
     * Get read member list
     * url: GET /api/v1/topics/{topic_id}/read_members
     *
     * @param int $topicId
     *
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BGET%5D+Read+member+list
     * TODO: This is mock! We have to implement it!
     */
    function get_read_members(int $topicId)
    {
        $retMockData = [
            'users'        => [
                [
                    'id'                => '1',
                    "img_url"           => "/img/no-image.jpg",
                    "display_username"  => "Daiki Hirakata",
                    "display_read_date" => "Now",
                    "read_date"         => "1438585548",
                ],
                [
                    'id'                => '2',
                    "img_url"           => "/img/no-image.jpg",
                    "display_username"  => "Kohei Kikuchi",
                    "display_read_date" => "Dec 1st",
                    "read_date"         => "1438585548",
                ],
            ],
            'member_count' => 2,
        ];
        $retMockHtml = <<<HTML
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">×</span></button>
            <h4 class="modal-title">
                既読 (5)
            </h4>
        </div>
        <div class="modal-body without-footer">
            <div class="row borderBottom">
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            古山 浩志&nbsp;
                        </div>

                        <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
                            <span title="2017年 2月28日 07:16"> 2月28日 07:16</span></div>
                    </div>
                </div>
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            平形 大樹 565-&gt;625&nbsp;
                        </div>

                        <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
                            <span title="2017年 2月28日 02:16"> 2月28日 02:16</span></div>
                    </div>
                </div>
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            西田 昂弘 440点&nbsp;
                        </div>

                        <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
                            <span title="2017年 2月27日 18:39"> 2月27日 18:39</span></div>
                    </div>
                </div>
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            吉田 将之&nbsp;
                        </div>

                        <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
                            <span title="2017年 2月27日 18:20"> 2月27日 18:20</span></div>
                    </div>
                </div>
                <div class="col col-xxs-12 mpTB0">
                    <img src="/img/no-image.jpg"
                         class="comment-img" alt="">
                    <div class="comment-body modal-comment">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            佐伯 翔平&nbsp;
                        </div>

                        <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
                            <span title="2017年 2月27日 18:20"> 2月27日 18:20</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
HTML;
        return $this->_getResponseSuccess($retMockData, $retMockHtml);
    }

    /**
     * Search topics
     * url: GET /api/v1/topics/search
     *
     * @queryParam int $cursor optional
     * @queryParam int $limit optional
     * @queryParam string $keyword optional
     * @return CakeResponse
     * @link       https://confluence.goalous.com/display/GOAL/%5BGET%5D+Search+topics
     *             TODO: This is mock! We have to implement it!
     */
    function get_search()
    {
        $cursor = $this->request->query('cursor');
        $limit = $this->request->query('limit');
        $keyword = $this->request->query('keyword');

        $retMock = [];
        $retMock['data'] = [
            "id"              => 1,
            "tilte"           => "", // タイトル変更時に変更前タイトル表示用に利用
            "display_title"   => "大樹,翔平,厚平,将之", //　表示用タイトル(トピック名が無い場合はメンバー名の羅列にする)
            "read_count"      => 3,
            "members_count"   => 4,
            "can_leave_topic" => true,
            "latest_message"  => [
                'body'            => '今日はいい天気だなー',
                'created'         => 1438585548,
                'display_created' => '2017年3月9日',
            ],
            "users"           => [
                [
                    'id'               => 1,
                    'img_url'          => '/img/no-image.jpg',
                    'display_username' => '平形 大樹',
                ],
                [
                    'id'               => 2,
                    'img_url'          => '/img/no-image.jpg',
                    'display_username' => '佐伯 翔平',
                ],
                [
                    'id'               => 3,
                    'img_url'          => '/img/no-image.jpg',
                    'display_username' => '菊池 厚平',
                ],
                [
                    'id'               => 4,
                    'img_url'          => '/img/no-image.jpg',
                    'display_username' => '吉田 将之',
                ],
            ],
        ];

        $retMock['paging'] = [
            'next' => "/api/v1/topics/search?cursor=11111&limit=10&keyword=hoge",
        ];
        return $this->_getResponsePagingSuccess($retMock);
    }

    /**
     * Create a topic
     * url: POST /api/v1/topics
     *
     * @data array $user_ids required
     * @data string $message required
     * @data array $file_ids optional
     * @return CakeResponse|null
     * @link https://confluence.goalous.com/display/GOAL/%5BPOST%5D+Create+a+topic
     *       TODO: This is mock! We have to implement it!
     */
    function post()
    {
        $userIds = $this->request->data('user_ids');
        $message = $this->request->data('message');
        $fileIds = $this->request->data('file_ids');

        $retMock = ['topic_id' => 1];
        return $this->_getResponseSuccessSimple($retMock);
    }

    /**
     * Add members to the topic
     * url: POST /api/v1/topics/{topic_id}/members
     *
     * @param int $topicId
     *
     * @data array $user_ids required
     * @return CakeResponse|null
     * @link https://confluence.goalous.com/display/GOAL/%5BPOST%5D+Add+members
     */
    function post_members(int $topicId)
    {
        $userIds = $this->request->data('user_ids');
        $retMock = [
            'topic_member_ids' => [1, 2, 3, 4]
        ];
        return $this->_getResponseSuccessSimple($retMock);
    }

    /**
     * Update the topic
     * url: PUT /api/v1/topics/{topic_id}
     *
     * @param int $topicId
     *
     * @data string $title required
     * @return CakeResponse|null
     * @link https://confluence.goalous.com/display/GOAL/%5BPUT%5D+Set+topic+title
     *       TODO: This is mock! We have to implement it!
     */
    function put(int $topicId)
    {
        $title = $this->request->data('title');
        return $this->_getResponseSuccessSimple();
    }

    /**
     * Leave me from Topic
     * url: GET /api/v1/topics/{topic_id}
     *
     * @param int $topicId
     *
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BDELETE%5D+Leave+me
     * TODO: This is mock! We have to implement it!
     */
    function delete_leave_me(int $topicId)
    {
        $retMock = ['topic_id' => $topicId];
        return $this->_getResponseSuccessSimple($retMock);
    }

}
