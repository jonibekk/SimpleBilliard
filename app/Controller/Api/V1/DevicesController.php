<?php
App::uses('ApiController', 'Controller/Api');
App::import("Service", "PushService");

use Goalous\Enum as Enum;

class DevicesController extends  ApiController
{
    public $components = array('RequestHandler');

    public function beforeFilter()
    {
        // Do not call parent class to prevent the validation of
        // authenticated users. The API should allow request even if
        // the users is not logged in
        //parent::beforeFilter();
        $this->Auth->allow();
        $this->Security->validatePost = false;
        $this->Security->csrfCheck = false;
    }

    /**
     * Accept a JSON with device information
     * Format:
     * {
     *    "installationId": "string",
     *    "version": "string"
     * }
     * 
     * @return CakeResponse
     */
    public function post()
    {
        $userId = $this->Auth->user('id');
        $requestJsonData = $this->request->input("json_decode", true);

        // Validate parameters
        if (empty($requestJsonData['installationId']) ||
            empty($requestJsonData['version'])) {
            return $this->_getResponseBadFail('Invalid Parameters');
        }
        $installationId = $requestJsonData['installationId'];
        $version = $requestJsonData['version'];

        // User not logged
        if ($userId === null) {
            // remove device info
            /** @var Device $Device */
            $Device = ClassRegistry::init('Device');

            if (!$Device->softDeleteAll(['Device.installation_id' => $installationId], false)) {
                GoalousLog::error("Failed to delete installation_id", ["installation_id" => $installationId]);
                return $this->_getResponseInternalServerError();
            }
            return $this->_getResponseSuccess();
        }

       // Check the request user
        if (!$this->User->exists($userId)) {
            GoalousLog::error("User id is invalid", ["user_id" => $userId]);
            return $this->_getResponseBadFail(__('Parameters were wrong'));
        }

        try {
            // Save the device
            $this->NotifyBiz->saveDeviceInfo($userId, $installationId, $version);

            // Update setup status
            $this->_updateSetupStatusIfNotCompleted();
        }
        catch (RuntimeException $e) {
            GoalousLog::error("Failed to save Device", ["Exception" => $e->getMessage()]);
            CakeLog::error($e->getTraceAsString());
            return $this->_getResponseInternalServerError();
        }
        return $this->_getResponseSuccess();
    }

    /**
     * Accept a JSON with device information
     * Format:
     * {
     *    "token": "string",
     *    "version": "string"
     *    "os": int
     * }
     *
     * @return CakeResponse
     */
    public function post_token()
    {
        $userId = $this->Auth->user('id');
        $requestJsonData = $this->request->input("json_decode", true);

        // Validate parameters
        if (empty($requestJsonData['token']) ||
            empty($requestJsonData['version']) ||
            !isset($requestJsonData['os'])) {
            return $this->_getResponseBadFail('Invalid Parameters');
        }
        $token = $requestJsonData['token'];
        $version = $requestJsonData['version'];
        $deviceType = new Enum\Devices\DeviceType($requestJsonData['os']);
        $deleteInstallationId = isset($requestJsonData['delete_installationId']) ? $requestJsonData['delete_installationId'] : "";
        $deleteToken = isset($requestJsonData['delete_token']) ? $requestJsonData['delete_token'] : "";

        /** @var PushService $PushService */
        $PushService = ClassRegistry::init('PushService');

        // User not logged, remove device to avoid push notification
        if ($userId === null) {
            $PushService->removeDevice($token);
            return $this->_getResponseSuccess(['action' => 'Unregistered']);
        }

        // Check the request user
        if (!$this->User->exists($userId)) {
            GoalousLog::error("User id is invalid", ["user_id" => $userId]);
            return $this->_getResponseBadFail('Invalid Parameters');
        }

        // Save device token only if the installation id is deleted first
        if (!empty($deleteInstallationId)) {
            if (!$PushService->removeInstallationId($deleteInstallationId)) {
                return $this->_getResponseSuccess(['action' => 'Unregistered']);
            }
        }

        // Delete old token first
        if (!empty($deleteToken)) {
            if (!$PushService->removeDevice($deleteToken)) {
                return $this->_getResponseSuccess(['action' => 'Unregistered']);
            }
        }

        // Save device
        if (!$PushService->saveDeviceToken($userId, $token, $deviceType, $version)) {
            return $this->_getResponseInternalServerError();
        }

        // Update setup status
        $this->_updateSetupStatusIfNotCompleted();

        return $this->_getResponseSuccess(['action' => 'Registered']);
    }
}
