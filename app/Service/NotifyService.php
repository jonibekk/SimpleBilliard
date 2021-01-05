<?php

App::import('Service', 'AppService');
App::uses('NotifySetting', 'Model');


class NotifyService extends AppService
{
    public function get($userID): array
    {
        /** @var NotifySetting $NotifySetting */
        $NotifySetting = ClassRegistry::init("NotifySetting");

        $options = [
            'conditions' => ['NotifySetting.user_id' => $userID,],
        ];

        try {
            $data = $NotifySetting->find('first', $options);
        } catch (Exception $e) {
            GoalousLog::error('Failed to get notify data.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $userID
            ]);
            return null;
        }

        return $data;
    }

    public function put($userID, array $data): bool
    {
        /** @var NotifySetting $NotifySetting */
        $NotifySetting = ClassRegistry::init("NotifySetting");

        try {
            $NotifySetting->save($data, false);
        } catch (Exception $e) {
            GoalousLog::error('Failed to update notify data.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $userID
            ]);
            return false;
        }

        return true;
    }
}
