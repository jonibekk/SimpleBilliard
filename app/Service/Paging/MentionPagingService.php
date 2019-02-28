<?php

App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::import('Service', 'UserService');
App::import('Service', 'CircleService');
App::import('Service', 'ImageStorageService');

use Goalous\Enum as Enum;

class MentionPagingService extends BasePagingService
{
    const MAIN_MODEL = 'User';

    /**
     * Get all mentions and not including with paging data
     *
     * @param       $pagingRequest
     * @param array $extendFlags
     *
     * @return array
     */
    public function getAllData(
        $pagingRequest,
        $extendFlags = []
    ): array
    {
        // Check whether exist current user id and team id
        $this->validatePagingResource($pagingRequest);

        $finalResult = [
            'data'   => [],
            'paging' => '',
            'count'  => 0
        ];

        //If only 1 flag is given, make it an array
        if (!is_array($extendFlags)) {
            $extendFlags = [$extendFlags];
        }

        $this->beforeRead($pagingRequest);
        $pagingRequest = $this->addDefaultValues($pagingRequest);

        $queryResult = $this->readData($pagingRequest, 0);
        $finalResult['count'] = count($queryResult);

        if (!empty($extendFlags) && !empty($queryResult)) {
            $this->extendPagingResult($queryResult, $pagingRequest, $extendFlags);
        }

        $this->afterRead($queryResult, $pagingRequest);

        $finalResult['data'] = $queryResult;

        return $finalResult;
    }

    // this method is not called
    protected function countData(PagingRequest $request): int
    {
        return 0;
    }

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $conditions = $pagingRequest->getConditions();
        $keyword = Hash::get($conditions, 'keyword') ?? "";
        $postId = Hash::get($conditions, 'post_id');
        $teamId = $pagingRequest->getCurrentTeamId();
        $userId = $pagingRequest->getCurrentUserId();

        /** @var UserService $UserService */
        $UserService = ClassRegistry::init('UserService');
        $users = $UserService->findMentionItems($keyword, $teamId, $userId, $limit, $postId);
        $processedUsers = $this->processUsers($users);

        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');
        $circles = $CircleService->findMentionItems($keyword, $teamId, $userId, $limit, $postId);
        $processedCircles = $this->processCircles($circles);

        return $this->mixAndSortResult($processedUsers, $processedCircles);;
    }

    /**
     * Mix users and circles, then sort asc by label
     * @param array $users
     * @param array $circles
     * @return array
     */
    private function mixAndSortResult(array $users, array $circles): array
    {
        if (empty($users) || empty($circles)) {
            return empty($users) ? $circles : $users;
        }

        $res = array_merge($users, $circles);

        $sort = [];
        foreach ($res as $k => $v) {
            $sort[$k] = $v['label'];
        }

        array_multisort($sort, SORT_ASC, $res);
        return $res;
    }

    /**
     * Format users for mention response
     * @param array $users
     * @return array
     */
    private function processUsers(array $users): array
    {
        // Set profile image url each data
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        $res = [];
        foreach ($users as $user) {
            $user['profile_img_url'] = $ImageStorageService->getImgUrlEachSize($user, 'User');
            $res[] = [
                'type'    => 'user',
                'id'      => $user['id'],
                'label'   => $user['display_username'],
                'user' => $user
            ];
        }
        return $res;
    }

    /**
     * Format circles for mention response
     * @param array $circles
     * @return array
     */
    private function processCircles(array $circles): array
    {
        // Set profile image url each data
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        $res = [];
        foreach ($circles as $circle) {
            $circle['img_url'] = $ImageStorageService->getImgUrlEachSize($circle, 'Circle');
            $res[] = [
                'type'    => 'circle',
                'id'      => $circle['id'],
                'label'   => $circle['name'],
                'circle' => $circle
            ];
        }
        return $res;
    }

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        // Nothing
    }

    protected function beforeRead(PagingRequest $pagingRequest)
    {
        $pagingRequest->addQueriesToCondition(['keyword', 'post_id']);
        return $pagingRequest;
    }

    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        return $pagingRequest;
    }
}
