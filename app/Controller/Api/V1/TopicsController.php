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
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::import('Lib/ElasticSearch', 'ESPagingRequest');
App::import('Service/Paging/Search', 'TopicSearchPagingService');
App::import('Service/Paging/Search', 'MessageSearchPagingService');

use Goalous\Enum as Enum;

/**
 * Class TopicsController
 */
class TopicsController extends ApiController
{
    public function get_list()
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init("Topic");
        /** @var ApiTopicService $ApiTopicService */
        $ApiTopicService = ClassRegistry::init("ApiTopicService");

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
        //       for unifying with other controller logic.
        if (count($topics) > $limit) {
            $basePath = '/api/v1/topics';
            $response['paging'] = $ApiTopicService->generatePaging($basePath, $limit, $offset, compact('keyword'));
            array_pop($topics);
        }
        $response['data'] = $topics;
        return $this->_getResponsePagingSuccess($response);
    }

    public function get_search()
    {
        $query = $this->request->query;
        $limit = $this->request->query('limit');
        $cursor = $this->request->query('cursor');
        $teamId = $this->current_team_id;
        $userId = $this->Auth->user('id');

        if (empty($cursor)) {
            $pagingRequest = new ESPagingRequest();
            $pagingRequest->setQuery($query);
            $pagingRequest->addCondition('pn', 1);
            $pagingRequest->addCondition('limit', $limit);
            $pagingRequest->addCondition('category', 1);
        } else {
            $pagingRequest = ESPagingRequest::convertBase64($cursor);
        }

        $pagingRequest->addTempCondition('team_id', $teamId);
        $pagingRequest->addTempCondition('user_id', $userId);

        /** @var TopicSearchPagingService $TopicSearchPagingService */
        $TopicSearchPagingService = ClassRegistry::init('TopicSearchPagingService');
        $searchResult = $TopicSearchPagingService->getDataWithPaging($pagingRequest);

        return ApiResponse::ok()->withBody($searchResult)->getResponse();
    }

    public function get_search_messages(int $topicId)
    {
        $error = $this->validateSearchMessage($topicId);

        if (!empty($error)) {
            return $error;
        }

        $query = $this->request->query;
        $limit = $this->request->query('limit');
        $cursor = $this->request->query('cursor');
        $teamId = $this->current_team_id;

        if (empty($cursor)) {
            $pagingRequest = new ESPagingRequest();
            $pagingRequest->setQuery($query);
            $pagingRequest->addCondition('pn', 1);
            $pagingRequest->addCondition('limit', $limit);
            $pagingRequest->addCondition('topic_id', $topicId);
        } else {
            $pagingRequest = ESPagingRequest::convertBase64($cursor);
        }

        $pagingRequest->addTempCondition('team_id', $teamId);
        $pagingRequest->addTempCondition('user_id', $this->Auth->user('id'));

        /** @var MessageSearchPagingService $MessageSearchPagingService */
        $MessageSearchPagingService = ClassRegistry::init('MessageSearchPagingService');
        $searchResult = $MessageSearchPagingService->getDataWithPaging($pagingRequest);

        return ApiResponse::ok()->withBody($searchResult)->getResponse();
    }

    /**
     * Get topic detail
     * url: GET /api/v1/topics/{topic_id}
     *
     * @param int $topicId
     *
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BGET%5D+Topic+detail+page
     */
    function get_detail(int $topicId)
    {
        $messageId = $this->request->query('message_id');

        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');

        $loginUserId = $this->Auth->user('id');

        if (!$TopicMember->isMember($topicId, $loginUserId)) {
            return $this->_getResponseBadFail(__("You cannot access the topic"));
        }

        /** @var ApiTopicService $ApiTopicService */
        $ApiTopicService = ClassRegistry::init('ApiTopicService');
        $ret = $ApiTopicService->findTopicDetailInitData($topicId, $loginUserId, $messageId);

        // updating notification for message
        $this->NotifyBiz->removeMessageNotification($topicId);
        $this->NotifyBiz->updateCountNewMessageNotification();

        return $this->_getResponseSuccess($ret);
    }

    /**
     * Get topic detail
     * url: GET /api/v1/topics/{topic_id}
     *
     * @param int $topicId
     *
     * @return CakeResponse
     */
    function get_init_search_messages(int $topicId)
    {
        $error = $this->validateSearchMessage($topicId);
        if (!empty($error)) {
            return $error;
        }

        /** @var ApiTopicService $ApiTopicService */
        $ApiTopicService = ClassRegistry::init('ApiTopicService');

        $query = $this->request->query;
        $loginUserId = $this->Auth->user('id');
        $teamId = $this->current_team_id;
        $ret = $ApiTopicService->findInitSearchMessages($topicId, $loginUserId, $teamId, $query);

        return ApiResponse::ok()->withBody($ret)->getResponse();
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
     */
    function get_messages(int $topicId)
    {
        $cursor = $this->request->query('cursor');
        $limit = $this->request->query('limit');
        $queryDirection = $this->request->query('direction');
        $direction = Enum\Model\Message\MessageDirection::isValid($queryDirection) ? $queryDirection : Enum\Model\Message\MessageDirection::OLD;
        $loginUserId = $this->Auth->user('id');

        /** @var ApiMessageService $ApiMessageService */
        $ApiMessageService = ClassRegistry::init("ApiMessageService");
        // checking max limit
        if (!$ApiMessageService->checkMaxLimit((int)$limit)) {
            return $this->_getResponseBadFail(__("Get count over the upper limit"));
        }
        $response = $ApiMessageService->findMessages($topicId, $loginUserId, $cursor, $limit, $direction);

        // updating notification for message
        $this->NotifyBiz->removeMessageNotification($topicId);
        $this->NotifyBiz->updateCountNewMessageNotification();

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
//    function get_members(int $topicId)
//    {
//        $retMockData = [
//            'users'        => [
//                [
//                    'id'               => '1',
//                    "img_url"          => "https://goalous-release2-assets.s3.amazonaws.com/users/1/843c2194af311ce15624357b5eb85a4a_small.JPG?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1488936809&Signature=NoPa3dWbYu0vZ5kNqIz%2BT%2F003i0%3D",
//                    "display_username" => "Daiki Hirakata",
//                ],
//                [
//                    'id'               => '2',
//                    "img_url"          => "https://goalous-release2-assets.s3.amazonaws.com/users/1/843c2194af311ce15624357b5eb85a4a_small.JPG?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1488936809&Signature=NoPa3dWbYu0vZ5kNqIz%2BT%2F003i0%3D",
//                    "display_username" => "Kohei Kikuchi",
//                ],
//            ],
//            'member_count' => 2,
//        ];
//        $retMockHtml = <<<HTML
//<div class="modal-dialog">
//    <div class="modal-content">
//        <div class="modal-header">
//            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
//                    class="close-icon">×</span></button>
//            <h4 class="modal-title font_18px font_bold">会話のメンバー (6)</h4>
//        </div>
//        <div class="modal-body without-footer">
//            <div class="row borderBottom">
//                <div class="col col-xxs-12 mpTB0">
//                    <img src="/img/no-image.jpg"
//                         class="comment-img" alt="">
//                    <div class="comment-body modal-comment">
//                        <div class="font_12px font_bold modalFeedTextPadding">
//                            西田 昂弘 440点&nbsp;
//                        </div>
//
//                    </div>
//                </div>
//                <div class="col col-xxs-12 mpTB0">
//                    <img src="/img/no-image.jpg"
//                         class="comment-img" alt="">
//                    <div class="comment-body modal-comment">
//                        <div class="font_12px font_bold modalFeedTextPadding">
//                            吉田 将之&nbsp;
//                        </div>
//
//                    </div>
//                </div>
//                <div class="col col-xxs-12 mpTB0">
//                    <img src="/img/no-image.jpg"
//                         class="comment-img" alt="">
//                    <div class="comment-body modal-comment">
//                        <div class="font_12px font_bold modalFeedTextPadding">
//                            佐伯 翔平&nbsp;
//                        </div>
//
//                    </div>
//                </div>
//                <div class="col col-xxs-12 mpTB0">
//                    <img src="/img/no-image.jpg"
//                         class="comment-img" alt="">
//                    <div class="comment-body modal-comment">
//                        <div class="font_12px font_bold modalFeedTextPadding">
//                            古山 浩志&nbsp;
//                        </div>
//
//                    </div>
//                </div>
//                <div class="col col-xxs-12 mpTB0">
//                    <img src="/img/no-image.jpg"
//                         class="comment-img" alt="">
//                    <div class="comment-body modal-comment">
//                        <div class="font_12px font_bold modalFeedTextPadding">
//                            平形 大樹 565-&gt;625&nbsp;
//                        </div>
//
//                    </div>
//                </div>
//                <div class="col col-xxs-12 mpTB0">
//                    <img src="/img/no-image.jpg"
//                         class="comment-img" alt="">
//                    <div class="comment-body modal-comment">
//                        <div class="font_12px font_bold modalFeedTextPadding">
//                            平野 愛&nbsp;
//                        </div>
//
//                    </div>
//                </div>
//            </div>
//        </div>
//    </div>
//</div>
//HTML;
//        return $this->_getResponseSuccess($retMockData, $retMockHtml);
//
//    }

    /**
     * Get read member list
     * url: GET /api/v1/topics/{topic_id}/read_members
     *
     * @param int $topicId
     *
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BGET%5D+Read+member+list
     */
    function get_read_members(int $topicId)
    {
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init("TopicMember");
        /** @var ApiTopicService $ApiTopicService */
        $ApiTopicService = ClassRegistry::init("ApiTopicService");

        // permission check
        $loginUserId = $this->Auth->user('id');
        if (!$TopicMember->isMember($topicId, $loginUserId)) {
            return $this->_getResponseBadFail(__("You cannot access the topic"));
        }

        $red_users = $ApiTopicService->findReadMembers($topicId);

        $ret = [
            'users'        => $red_users,
            'member_count' => count($red_users),
        ];

        return $this->_getResponseSuccess($ret);
    }

    /**
     * Create a topic and first message
     * url: POST /api/v1/topics
     *
     * @data array $user_ids required
     * @data string $message required
     * @data array $file_ids optional
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
        $createRes = $TopicService->create($postedData, $userId, $toUserIds);
        if ($createRes === false) {
            return $this->_getResponseInternalServerError();
        }
        $topicId = $createRes['topicId'];
        $messageId = $createRes['messageId'];

        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MESSAGE, $messageId);

        // push event
        $MessageService->execPushMessageEvent($topicId);

        /** @var ApiTopicService $ApiTopicService */
        $ApiTopicService = ClassRegistry::init("ApiTopicService");
        $topic = $ApiTopicService->get($topicId, $userId);
        return $this->_getResponseSuccess(['topic' => $topic]);
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

        $socketId = $this->request->data('socket_id');
        if (!$TopicService->addMembers($topicId, $loginUserId, $userIds, $socketId)) {
            return $this->_getResponseInternalServerError();
        }

        $topic = $TopicService->findTopicDetail($topicId, $loginUserId);
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
        /** @var ApiMessageService $ApiMessageService */
        $ApiMessageService = ClassRegistry::init("ApiMessageService");

        // Get request data
        $requestData = $this->request->input('json_decode', true) ?? [];
        $updateData = AppUtil::filterWhiteList($requestData, ['title']);
        $updateData['id'] = $topicId;
        // Validation
        if (!$Topic->validates($updateData)) {
            $validationErrors = $Topic->_validationExtract($Topic->validationErrors);
            return $this->_getResponseValidationFail($validationErrors);
        }

        // Check permission
        $userId = $this->Auth->user('id');
        if (!$TopicMember->isMember($topicId, $userId)) {
            return $this->_getResponseBadFail(__("You cannot access the topic"));
        }

        // Update
        $socketId = Hash::get($requestData, 'socket_id');
        if (!$TopicService->update($updateData, $userId, $socketId)) {
            return $this->_getResponseInternalServerError();
        }

        $topic = $TopicService->findTopicDetail($topicId, $userId);
        $messages = $ApiMessageService->findMessages($topicId, $userId, null, 1);
        $latestMessage = Hash::extract($messages, 'data.0');
        return $this->_getResponseSuccess(
            [
                'topic'          => $topic,
                'latest_message' => $latestMessage
            ]
        );
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

    /**
     * @param int $topicId
     *
     * @return BaseApiResponse|null
     */
    private function validateSearchMessage(int $topicId)
    {
        $userId = $this->Auth->user('id');

        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');

        if (!$TopicMember->isMember($topicId, $userId)) {
            return ErrorResponse::forbidden()->withMessage(__("You cannot access the topic"))->getResponse();
        }

        return null;
    }
}
