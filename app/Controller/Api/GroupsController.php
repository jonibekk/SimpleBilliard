<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Service', 'GroupService');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');
App::import('Service', 'ImageStorageService');

use Goalous\Exception as GlException;

class GroupsController extends BasePagingController
{
    use TranslationNotificationTrait;

    public $components = [
        'NotifyBiz',
    ];

    public function get_list()
    {
        $teamId = $this->getTeamId();
        // @var Group $Group;
        $Group = ClassRegistry::init("Group");
        $results = $Group->findGroupsWithMemberCount($teamId);
        $ret = Hash::extract($results, '{n}.Group');

        return ApiResponse::ok()->withData($ret)->getResponse();
    }

    public function get_detail(int $groupId)
    {
        try {
            $group = $this->findGroup($groupId);
            $this->authorize('read', $group);
        } catch (Exception $e) {
            return $this->generateResponseIfException($e);
        }

        $members = $this->Group->findMembers($groupId);

        $group['members'] = array_map(
            function ($row) {
                // @var ImageStorageService $ImageStorageService;
                $ImageStorageService = ClassRegistry::init("ImageStorageService");

                $member = $row["User"];
                $member['profile_img_url'] = $ImageStorageService->getImgUrlEachSize($member, 'User');

                return $member;
            },
            $members
        );

        return ApiResponse::ok()->withData($group)->getResponse();
    }

    public function post()
    {
        $requestData = $this->getRequestJsonBody();

        $validationError = $this->validateCreate($requestData);
        if ($validationError !== null) {
            return $validationError;
        }

        // @var GroupService $GroupService
        $GroupService = ClassRegistry::init("GroupService");

        try {
            $data = $requestData;
            $data['team_id'] = $this->getTeamId();
            $newGroup = $GroupService->createGroup($data);
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()
                ->withMessage(__($e->getMessage()))
                ->getResponse();
        }

        return ApiResponse::ok()->withData($newGroup)->getResponse();
    }

    public function put(int $groupId)
    {
        $requestData = $this->getRequestJsonBody();

        $validationError = $this->validateUpdate($groupId);
        if ($validationError !== null) {
            return $validationError;
        }

        // @var GroupService $GroupService
        $GroupService = ClassRegistry::init("GroupService");
        $ret = $GroupService->editGroup($groupId, $requestData);

        return ApiResponse::ok()->withData($ret)->getResponse();
    }

    public function post_verify_members(int $groupId)
    {
        $validationError = $this->validateUpdate($groupId);
        if ($validationError !== null) {
            return $validationError;
        }

        $file = Hash::get($this->request->params, 'form.file');

        // @var GroupService $GroupService
        $GroupService = ClassRegistry::init("GroupService");
        $result = $GroupService->parseMembers(
            $groupId,
            $this->getTeamId(),
            $file["tmp_name"]
        );

        return ApiResponse::ok()->withData($result)->getResponse();
    }

    public function post_members(int $groupId)
    {
        $validationError = $this->validateUpdate($groupId);
        if ($validationError !== null) {
            return $validationError;
        }

        $requestData = $this->getRequestJsonBody();

        // @var GroupService $GroupService
        $GroupService = ClassRegistry::init("GroupService");
        $result = $GroupService->addMembers(
            $groupId,
            $this->getTeamId(),
            $requestData['user_ids']
        );

        return ApiResponse::ok()->withData($result)->getResponse();
    }

    public function post_remove_member(int $groupId)
    {
        $validationError = $this->validateUpdate($groupId);
        if ($validationError !== null) {
            return $validationError;
        }

        $requestData = $this->getRequestJsonBody();

        $this->loadModel("MemberGroup");
        $this->MemberGroup->deleteAll([
            'user_id' => $requestData['memberId'],
            'group_id' => $groupId
        ]);

        return ApiResponse::ok()->withData([])->getResponse();
    }

    private function validateCreate($data)
    {
        try {
            $this->validateTeamAdmin();
            $this->validateGroupParams($data);
            return null;
        } catch (Exception $e) {
            return ErrorResponse::badRequest()
                ->withMessage(__($e->getMessage()))
                ->getResponse();
        }
    }

    private function validateUpdate($groupId)
    {
        $this->loadModel("Group");
        $group = $this->Group->findById($groupId);

        $this->loadModel("TeamMember");
        $result = $this->TeamMember->hasAny([
            "user_id" => $this->getUserId(),
            "team_id" => $this->getTeamId(),
            "admin_flg" => true
        ]);

        if (!$result || $group['Group']['team_id'] != $this->getTeamId()) {
            return ErrorResponse::badRequest()
                ->withMessage(__("You are not authorized to manage this group"))
                ->getResponse();
        }
        return null;
    }

    private function findGroup(int $groupId): array
    {
        /** @var Group $Group */
        $Group = ClassRegistry::init("Group");
        $group = $Group->useType()->getById($groupId);

        if (empty($group)) {
            throw new GlException\GoalousNotFoundException(__("This group doesn't exist."));
        }

        return $group;
    }

    public function authorize(string $method, array $group): void
    {
        $policy = new GroupPolicy($this->getUserId(), $this->getTeamId());
        $authorized = $policy->{$method}($group);

        if (!$authorized) {
            switch ($method) {
                case 'read':
                    throw new GlException\Auth\AuthFailedException(__("You don't have permission to access this group"));
                case 'create':
                    throw new GlException\Auth\AuthFailedException(__("You are not authorized to create groups for this team"));
                case 'update':
                    throw new GlException\Auth\AuthFailedException(__("You are not authorized to update this group"));
            }
        }
    }
}
