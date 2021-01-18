<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Service', 'GroupService');
App::import('Service', 'MemberGroupService');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');
App::import('Service', 'ImageStorageService');
App::import('Policy', 'GroupPolicy');
App::import('Utility', 'CustomLogger');

use Goalous\Exception as GlException;

class GroupsController extends BasePagingController
{
    use TranslationNotificationTrait;

    public $components = [
        'NotifyBiz',
    ];

    public function get_list()
    {
        $policy = new GroupPolicy($this->getUserId(), $this->getTeamId());
        $scope = $policy->scope('manage');
        // @var Group $Group;
        $Group = ClassRegistry::init("Group");
        $results = $Group->findGroupsWithMemberCount($scope);
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
        $requestData['team_id'] = $this->getTeamId();

        try {
            $this->authorize('create', $requestData);
            $this->validateGroupParams($requestData);
        } catch (Exception $e) {
            return $this->generateResponseIfException($e);
        }

        // @var GroupService $GroupService
        $GroupService = ClassRegistry::init("GroupService");

        try {
            $newGroup = $GroupService->createGroup($requestData);
        } catch (Exception $e) {
            return $this->generateResponseIfException($e);
        }

        return ApiResponse::ok()->withData($newGroup)->getResponse();
    }

    public function put(int $groupId)
    {
        $requestData = $this->getRequestJsonBody();

        try {
            $group = $this->findGroup($groupId);
            $this->authorize('update', $group);
            $this->validateUpdate($group, $requestData);

            // @var GroupService $GroupService
            $GroupService = ClassRegistry::init("GroupService");
            $ret = $GroupService->editGroup($groupId, $requestData);
        } catch (Exception $e) {
            return $this->generateResponseIfException($e);
        }

        return ApiResponse::ok()->withData($ret)->getResponse();
    }

    public function post_verify_members(int $groupId)
    {
        try {
            $group = $this->findGroup($groupId);
            $this->authorize('update', $group);
        } catch (Exception $e) {
            return $this->generateResponseIfException($e);
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
        try {
            $group = $this->findGroup($groupId);
            $this->authorize('update', $group);
        } catch (Exception $e) {
            return $this->generateResponseIfException($e);
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
        // @var MemberGroupService $MemberGroupService
        $MemberGroupService = ClassRegistry::init("MemberGroupService");
        $requestData = $this->getRequestJsonBody();

        try {
            $group = $this->findGroup($groupId);
            $this->authorize('update', $group);
            $MemberGroupService->removeGroupMember($groupId, $requestData['memberId']);
        } catch (Exception $e) {
            return $this->generateResponseIfException($e);
        }

        return ApiResponse::ok()->withData([])->getResponse();
    }

    private function validateGroupParams(array $data)
    {
        $this->loadModel("Group");
        $this->Group->set($data);

        if (!$this->Group->validates()) {
            $errMsgs = [];
            foreach ($this->Group->validationErrors as $field => $errors) {
                $errMsgs[$field] = array_shift($errors);
            }
            GoalousLog::error("Invalid group paramters", $errMsgs);
            throw new GlException\GoalousValidationException(__("Invalid group parameters"));
        }
        return null;
    }

    private function validateUpdate(array $group, array $data)
    {
        $this->loadModel('Group');
        $this->loadModel('Team');
        $team = $this->Team->findById($this->getTeamId());

        if (!$team['Team']['groups_enabled_flg']) {
            return null;
        }

        // a group with public visiblity turned OFF must have at least one non-archived group
        if ($data["archived_flg"] === true) {
            $groupsPresent = $this->Group->hasAny([
                'team_id' => $this->getTeamId(),
                'archived_flg' => false,
                'id !=' => $group['id']
            ]);

            if (!$groupsPresent) {
                throw new GlException\GoalousValidationException(__("You need at least one non-archived group."));
            }
        }
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

        switch ($method) {
            case 'read':
                if (!$policy->read($group)) {
                    throw new GlException\Auth\AuthFailedException(__("You don't have permission to access this group"));
                }
                break;
            case 'create':
                if (!$policy->create($group)) {
                    throw new GlException\Auth\AuthFailedException(__("You are not authorized to create groups for this team"));
                }
                break;
            case 'update':
                if (!$policy->update($group)) {
                    throw new GlException\Auth\AuthFailedException(__("You are not authorized to update this group"));
                }
                break;
        }
    }
}
