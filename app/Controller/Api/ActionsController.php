<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Service', 'ActionService');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');
App::import('Lib/DataExtender', 'FeedPostExtender');

class ActionsController extends BasePagingController
{
    use TranslationNotificationTrait;

    public function post()
    {
        $this->loadModel("Goal");
        /** @var ActionService $ActionService */
        $ActionService = ClassRegistry::init("ActionService");
        $requestData = $this->getRequestJsonBody();

        $validationError = $this->validateCreate($requestData);
        if ($validationError !== null) {
            return $validationError;
        }

        try {
            $data = $requestData;
            $data['user_id'] = $this->getUserId();
            $data['team_id'] = $this->getTeamId();
            $newActionId = $ActionService->createAngular($data);
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()
                ->withMessage(__($e->getMessage()))
                ->getResponse();
        }

        $ret = $this->formatCreateResp($newActionId);
        return ApiResponse::ok()->withData($ret)->getResponse();
    }

    private function validateCreate($data)
    {
        try {
            $this->validateGoalExists($data);
            $this->validateGoalCollaborator($data);
            $this->validateUploadedFile($data);
            $this->validateActionParams($data);
            $this->validateKeyResult($data);
            return null;
        } catch (Exception $e) {
            return ErrorResponse::badRequest()
                ->withMessage(__($e->getMessage()))
                ->getResponse();
        }
    }

    private function validateGoalExists(array $data): void
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        $goalId = $data['goal_id'];
        $goal = $GoalService->get($goalId);
        if (empty($goal)) {
            throw new Exception("Goal does not exist");
        }
    }

    private function validateGoalCollaborator(array $data): void
    {
        $goalId = $data['goal_id'];

        if (!$this->Goal->GoalMember->isCollaborated($goalId)) {
            throw new Exception("Unauthorized to create actions for this goal");
        }
    }

    private function validateUploadedFile(array $data): void
    {
        $fileIds = $data['file_ids'];

        if (empty($fileIds) || !is_array($fileIds)) {
            throw new Exception("No images found");
        }
        // unable to perform previous GlRedis cached image check since angular images are uploaded to different controller
    }

    private function validateActionParams(array $data): void
    {
        $this->Goal->ActionResult->validate = $this->Goal->ActionResult->postValidate;
        $this->Goal->ActionResult->set($data);

        if (!$this->Goal->ActionResult->validates()) {
            $errMsgs = [];
            foreach ($this->Goal->ActionResult->validationErrors as $field => $errors) {
                $errMsgs[$field] = array_shift($errors);
            }
            GoalousLog::error("Invalid action paramters", $errMsgs);
            throw new Exception("Invalid action parameters");
        }
    }

    private function validateKeyResult(array $data): void
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        $krBeforeValue = Hash::get($data, "key_result_before_value");
        $krId = Hash::get($data, 'key_result_id');

        $kr = $KeyResultService->get($krId);
        $krCurrentValue = Hash::get($kr, 'current_value');
        if ($krBeforeValue != $krCurrentValue) {
            throw new Exception("KR has been updated by another user");
        }
    }

    // format into a response similar to api/me/get_feed
    private function formatCreateResp(int $actionId): array
    {
        /** @var FeedPostExtender $FeedPostExtender **/
        $FeedPostExtender = ClassRegistry::init('FeedPostExtender');
        $this->loadModel("Post");
        $post = $this->Post->find('first', [
            'conditions' => [
                'action_result_id' => $actionId
            ]
        ]);
        $formattedData =  $FeedPostExtender->extendMulti(
            [$post["Post"]],
            $this->getUserId(),
            $this->getTeamId(),
            [FeedPostExtender::EXTEND_ALL]
        );
        return $formattedData[0];
    }
}
