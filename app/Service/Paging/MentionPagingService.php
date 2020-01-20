<?php

App::import('Lib/Paging', 'BaseGetAllService');
App::import('Lib/Paging', 'PagingRequest');
App::import('Service', 'UserService');
App::import('Service', 'CircleService');
App::import('Service', 'ImageStorageService');

use Goalous\Enum as Enum;

class MentionPagingService extends BaseGetAllService
{
    const MAIN_MODEL = 'User';

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $conditions = $pagingRequest->getConditions();
        $keyword = Hash::get($conditions, 'keyword') ?? "";

        $teamId = $pagingRequest->getCurrentTeamId();
        $userId = $pagingRequest->getCurrentUserId();
        $resourceId = $pagingRequest->getQuery('resource_id');
        $resourceType = $pagingRequest->getQuery('resource_type');

        /** @var UserService $UserService */
        $UserService = ClassRegistry::init('UserService');
        $users = $UserService->findMentionItems($keyword, $teamId, $userId, $limit, $resourceId, $resourceType);
        $processedUsers = $this->processUsers($users);

        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');
        $circles = $CircleService->findMentionItems($keyword, $teamId, $userId, $limit, $resourceId, $resourceType);
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
                'url'   => "/users/view_goals/user_id:".$user['id'],
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
                'url'   => "/circles/".$circle['id']."/posts",
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
