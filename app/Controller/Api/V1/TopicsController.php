<?php
App::uses('ApiController', 'Controller/Api');
App::uses('Topic', 'Model');
App::uses('TopicMember', 'Model');
App::uses('Message', 'Model');
App::uses('TopicSearchKeyword', 'Model');
App::import('Service', 'TopicService');
App::import('Service', 'MessageService');
App::import('Service/Api', 'ApiMessageService');
/** @noinspection PhpUndefinedClassInspection */
App::import('Service/Api', 'ApiTopicService');
App::uses('AppUtil', 'Util');

/**
 * Class TopicsController
 */
class TopicsController extends ApiController
{
    /**
     * Init and search data for topic list page
     * - path '/api/v1/topics/search'
     * - also used as getting init list page data api
     *
     * @queryParam int $limit optional
     * @queryParam int $offset optional
     * @queryParam int $keyword optional
     * @return CakeResponse
     */
    function get_search()
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init("Topic");
        /** @var ApiTopicService $ApiTopicService */
        $ApiTopicService = ClassRegistry::init("ApiTopicService");
        /** @var TopicSearchKeyword $TopicSearchKeyword */
        $TopicSearchKeyword = ClassRegistry::init("TopicSearchKeyword");

        // get query params
        $limit = $this->request->query('limit') ?? ApiTopicService::DEFAULT_TOPICS_NUM;
        $offset = $this->request->query('offset') ?? 0;
        $keyword = $this->request->query('keyword') ?? '';
        $userId = $this->Auth->user('id');

        // check limit param under max
        if (!$ApiTopicService->checkMaxLimit($limit)) {
            return $this->_getResponseBadFail(__("Get count over the upper limit"));
        }

        // define response data
        $response = [
            'data'   => [],
            'paging' => [
                'next' => ''
            ]
        ];

        $topics = $Topic->findLatest($userId, $offset, $limit + 1, $keyword);
        $topics = $ApiTopicService->process($topics, $userId);

        // Set paging text
        // TODO: should move setting paging to service.
        //       for unifying with other controller logic.
        if (count($topics) > $limit) {
            $basePath = '/api/v1/topics/search';
            $response['paging'] = $ApiTopicService->generatePaging($basePath, $limit, $offset, compact('keyword'));
            array_pop($topics);
        }

        $response['data'] = $topics;

        return $this->_getResponsePagingSuccess($response);
    }

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
//TODO: it will be removed after writing test cases.
//        $retMock = [];
//        $retMock['data'] = [
//            "topic"    => [
//                "id"              => 1,
//                "tilte"           => "", // タイトル変更時に変更前タイトル表示用に利用
//                "display_title"   => "大樹,翔平,厚平,将之", //　表示用タイトル(トピック名が無い場合はメンバー名の羅列にする)
//                "read_count"      => 3,
//                "members_count"   => 4,
//                "can_leave_topic" => true,
//            ],
//            'messages' => [
//                'data'   => [
//                    [
//                        'id'              => 123,
//                        'body'            => 'あついなー。',
//                        'created'         => 1438585548,
//                        'display_created' => '03/09 13:51',
//                        'type'            => 1,
//                        'user'            => [
//                            'id'               => 2,
//                            'medium_img_url'          => '/img/no-image.jpg',
//                            'display_username' => '佐伯 翔平',
//                        ]
//                    ],
//                    [
//                        'id'              => 124,
//                        'body'            => 'そうかなー。',
//                        'created'         => 1438585558,
//                        'display_created' => '03/09 13:52',
//                        'type'            => 1,
//                        'user'            => [
//                            'id'               => 4,
//                            'medium_img_url'          => '/img/no-image.jpg',
//                            'display_username' => '吉田 将之',
//                        ]
//                    ],
//                    [
//                        'id'              => 125,
//                        'body'            => '全然あつくないでしょ。恋でもしてるの？',
//                        'created'         => 1438585568,
//                        'display_created' => '03/09 13:53',
//                        'type'            => 1,
//                        'user'            => [
//                            'id'               => 1,
//                            'medium_img_url'          => '/img/no-image.jpg',
//                            'display_username' => '平形 大樹',
//                        ]
//                    ],
//                    [
//                        'id'              => 126,
//                        'body'            => '利尻いってこい。涼しいぞ。',
//                        'created'         => 1438585578,
//                        'display_created' => '03/09 13:54',
//                        'type'            => 1,
//                        'user'            => [
//                            'id'               => 3,
//                            'medium_img_url'          => '/img/no-image.jpg',
//                            'display_username' => '菊池 厚平',
//                        ],
//
//                    ],
//                ],
//                'paging' => [
////                    'next' => "/api/v1/topics/123/messages?cursor=11111&limit=10",
//'next' => "",
//                ]
//            ]
//        ];

        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');

        $loginUserId = $this->Auth->user('id');

        if (!$TopicMember->isMember($topicId, $loginUserId)) {
            return $this->_getResponseBadFail(__("You cannot access the topic"));
        }

        /** @var ApiTopicService $ApiTopicService */
        $ApiTopicService = ClassRegistry::init('ApiTopicService');
        $ret = $ApiTopicService->findTopicDetailInitData($topicId, $loginUserId);

        return $this->_getResponseSuccess($ret);
    }

    /**
     * Get Messages on topic
     * url: GET /api/v1/topics/{topic_id}/messages
     *
     * @param int $topicId
     *
     * @queryParam int $cursor optional
     * @queryParam int $limit optional
     * @queryParam string $direction optional. "old" or "new" for getting older than cursor or newer
     * @return CakeResponse
     * @link       https://confluence.goalous.com/display/GOAL/%5BGET%5D+Topic+message+list
     *             TODO: This is mock! We have to implement it!
     */
    function get_messages(int $topicId)
    {
        $cursor = $this->request->query('cursor');
        $limit = $this->request->query('limit');
        $direction = $this->request->query('direction') ?? Message::DIRECTION_OLD;
        $loginUserId = $this->Auth->user('id');

        /** @var ApiMessageService $ApiMessageService */
        $ApiMessageService = ClassRegistry::init("ApiMessageService");
        // checking max limit
        if (!$ApiMessageService->checkMaxLimit((int)$limit)) {
            return $this->_getResponseBadFail(__("Get count over the upper limit"));
        }
        $response = $ApiMessageService->findMessages($topicId, $loginUserId, $cursor, $limit, $direction);
//TODO: This is for only reference. It should be removed. after writing test cases.
//        $retMock = [];
//        $retMock['data'] = [
//            [
//                'id'              => 123,
//                'body'            => 'あついなー。',
//                'created'         => 1438585548,
//                'display_created' => '03/09 13:51',
//                'type'            => 1,
//                'user'            => [
//                    'id'               => 2,
//                    'medium_img_url'          => '/img/no-image.jpg',
//                    'display_username' => '佐伯 翔平',
//                ],
//                'attached_files'  => [
//                    [
//                        'id'            => 1,
//                        'file_ext'           => 'jpg',
//                        'file_type'          => 1,
//                        'download_url'  => '/img/no-image.jpg',
//                        'preview_url'   => '',
//                        'thumbnail_url' => '/img/no-image.jpg',
//                    ],
//                ]
//            ],
//            [
//                'id'              => 124,
//                'body'            => 'そうかなー。',
//                'created'         => 1438585558,
//                'display_created' => '03/09 13:52',
//                'type'            => 1,
//                'user'            => [
//                    'id'               => 4,
//                    'medium_img_url'          => '/img/no-image.jpg',
//                    'display_username' => '吉田 将之',
//                ]
//            ],
//            [
//                'id'              => 125,
//                'body'            => '全然あつくないでしょ。恋でもしてるの？',
//                'created'         => 1438585568,
//                'display_created' => '03/09 13:53',
//                'type'            => 1,
//                'user'            => [
//                    'id'               => 1,
//                    'medium_img_url'          => '/img/no-image.jpg',
//                    'display_username' => '平形 大樹',
//                ]
//            ],
//            [
//                'id'              => 126,
//                'body'            => '利尻いってこい。涼しいぞ。',
//                'created'         => 1438585578,
//                'display_created' => '03/09 13:54',
//                'type'            => 1,
//                'user'            => [
//                    'id'               => 3,
//                    'medium_img_url'          => '/img/no-image.jpg',
//                    'display_username' => '菊池 厚平',
//                ],
//
//            ],
//        ];
//        $retMock['paging'] = [
//            'next' => "/api/v1/topics/123/messages?cursor=11111&limit=10",
//        ];
        return $this->_getResponsePagingSuccess($response);
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
     * Create a topic and first message
     * url: POST /api/v1/topics
     *
     * @data array $user_ids required
     * @data string $message required
     * @data array $file_ids optional
     *
     * @return CakeResponse|null
     */
    function post()
    {
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init("TopicService");
        /** @var MessageService $MessageService */
        $MessageService = ClassRegistry::init("MessageService");

        $userId = $this->Auth->user('id');

        // filter fields
        $postedData = AppUtil::filterWhiteList($this->request->data, ['body', 'file_ids']);
        $toUserIds = Hash::get($this->request->data, ['to_user_ids']);

        // validation
        $validationResult = $TopicService->validateCreate($postedData, $toUserIds);
        if ($validationResult !== true) {
            return $this->_getResponseValidationFail($validationResult);
        }

        // create
        $topicId = $TopicService->create($postedData, $userId, $toUserIds);
        if ($topicId === false) {
            return $this->_getResponseBadFail(null);
        }

        // TODO: フロント実装後に繋ぎこみ実装
        $socketId = "test";
        $MessageService->execPushMessageEvent($topicId, $socketId);

        return $this->_getResponseSuccess(['topic_id' => $topicId]);
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
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init('TopicService');
        /** @var ApiMessageService $ApiMessageService */
        $ApiMessageService = ClassRegistry::init('ApiMessageService');

        $loginUserId = $this->Auth->user('id');
        $userIds = $this->request->data('user_ids');
        // checking 403 or 404
        $errResponse = $this->_validatePostMembers($topicId, $loginUserId, $userIds);
        if ($errResponse !== true) {
            return $errResponse;
        }

        //TODO pusherのsocket_idをフォームで渡してもらう必要がある。これはapiからのつなぎこみ時に行う。
        $socketId = "test";
        if (!$TopicService->addMembers($topicId, $loginUserId, $userIds, $socketId)) {
            return $this->_getResponseInternalServerError();
        }

        $topic = $TopicService->findTopicDetail($topicId);
        $messages = $ApiMessageService->findMessages($topicId, $loginUserId, null, 1);
        $latestMessage = Hash::extract($messages, 'data.0');
        return $this->_getResponseSuccess(
            [
                'topic'          => $topic,
                'latest_message' => $latestMessage
            ]
        );

    }

    /**
     * Validate for post_members func
     * $addUserIds is a request parameter before validation execution, type is not specified.
     *
     * @param int $topicId
     * @param int $loginUserId
     * @param     $addUserIds
     *
     * @return bool|CakeResponse|true
     */
    private function _validatePostMembers(int $topicId, int $loginUserId, $addUserIds)
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');

        // checking 403 or 404
        $errResponse = $this->_validateForbiddenOrNotFound($topicId, $loginUserId);
        if ($errResponse !== true) {
            return $errResponse;
        }

        // Check not empty and array
        if (empty($addUserIds) || !is_array($addUserIds)) {
            return $this->_getResponseBadFail(
                __("Parameter is invalid.")
            );
        }
        // Check numeric
        if (!Hash::numeric($addUserIds)) {
            return $this->_getResponseBadFail(__("Parameter is invalid."));
        }

        // Check duplicate
        if (count($addUserIds) !== count(array_unique($addUserIds))) {
            return $this->_getResponseBadFail(__("Parameter is invalid."));
        }

        // Check users active
        if (!$User->isActiveUsers($addUserIds)) {
            return $this->_getResponseBadFail(__("Parameter is invalid."));
        }

        // Check can join member
        $existTopicMemberCount = $TopicMember->find('count',
            ['conditions' => ['user_id' => $addUserIds, 'topic_id' => $topicId]]);
        if ($existTopicMemberCount > 0) {
            return $this->_getResponseBadFail(__("Some users who already joined the topic are included in the specified users. Try again from the start."));
        }
        return true;
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
     */
    function put(int $topicId)
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init("Topic");
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init("TopicService");

        // Get request data
        $requestData = $this->request->input('json_decode', true) ?? [];
        $requestData = AppUtil::filterWhiteList($requestData, ['title']);
        $requestData['id'] = $topicId;
        // Validation
        if (!$Topic->validates($requestData)) {
            $validationErrors = $Topic->_validationExtract($Topic->validationErrors);
            return $this->_getResponseValidationFail($validationErrors);
        }

        // Check permission
        $userId = $this->Auth->user('id');
        if (!$TopicMember->isMember($topicId, $userId)) {
            return $this->_getResponseBadFail(__("You cannot access the topic"));
        }

        // Update
        if (!$TopicService->update($requestData)) {
            return $this->_getResponseInternalServerError();
        }

        $topic = $TopicService->findTopicDetail($topicId);

        return $this->_getResponseSuccess(compact('topic'));
    }

    /**
     * Leave me from Topic
     * url: DELETE /api/v1/topics/{topic_id}/leave_me
     *
     * @param int $topicId
     *
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BDELETE%5D+Leave+me
     */
    function delete_leave_me(int $topicId)
    {
        /** @var MessageService $MessageService */
        $MessageService = ClassRegistry::init('MessageService');
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init('TopicService');

        $userId = $this->Auth->user('id');

        // checking 403 or 404
        $errResponse = $this->_validateForbiddenOrNotFound($topicId, $userId);
        if ($errResponse !== true) {
            return $errResponse;
        }

        // validation
        $validationMsg = $TopicService->validateLeaveMe($topicId);
        if ($validationMsg !== true) {
            return $this->_getResponseBadFail($validationMsg);
        }

        // saving datas
        $isSuccessToSave = $TopicService->leaveMe($topicId, $userId);
        if (!$isSuccessToSave) {
            return $this->_getResponseInternalServerError();
        }

        // push event
        $MessageService->execPushMessageEvent($topicId);

        $ret = ['topic_id' => $topicId];
        return $this->_getResponseSuccessSimple($ret);
    }

    /**
     * validation for POST, PUT, UPDATE, DELETE
     * - if not found, it will return 404 response
     * - if not have permission, it will return 403 response
     *
     * @param $topicId
     * @param $userId
     *
     * @return CakeResponse|true
     */
    private function _validateForbiddenOrNotFound($topicId, $userId)
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init("Topic");
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init("TopicMember");

        // topic is exists?
        if (!$Topic->exists($topicId)) {
            return $this->_getResponseNotFound();
        }
        // is topic member?
        $isMember = $TopicMember->isMember($topicId, $userId);
        if (!$isMember) {
            return $this->_getResponseForbidden();
        }
        return true;
    }

}
