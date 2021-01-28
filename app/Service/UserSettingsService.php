<?php

use Goalous\Enum\Model\AttachedFile\AttachedModelType;

App::import('Service', 'AppService');
App::import('Service', 'UploadService');
App::import('Service', 'AttachedFileService');
App::uses('NotifySetting', 'Model');
App::uses('User', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Email', 'Model');

App::import('Model/Dto/UserSettings', 'UserNotifyDTO');
App::import('Model/Dto/UserSettings', 'UserAccountDTO');
App::import('Model/Dto/UserSettings', 'UserProfileDTO');
App::import('Model/Dto/UserSettings', 'UserChangeEmailDTO');
App::import('Model/Dto/UserSettings', 'UserChangePasswordDTO');


class UserSettingsService extends AppService
{
    public function getUserData(int $userId): ?array
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $user = $User->getById($userId);
        if (empty($user)) {
            return null;
        }

        return array('User' => $user);
    }

    public function getTeamMemberData(int $userId, int $teamId): ?array
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $team = $TeamMember->getUnique($userId, $teamId);
        if (empty($team)) {
            return null;
        }

        return array('TeamMember' => $team);
    }

    // Update User Account
    public function updateAccountSettingsData(int $userId, int $teamId, UserAccountDTO $accountData): bool
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $userData = array(
            'User' => [
                'id' => $userId,
                'email' => $accountData->email,
                'default_team_id' => $accountData->defTeamId,
                'language' => $accountData->language,
                'timezone' => $accountData->timezone,
                'update_email_flg' => $accountData->updateEmailFlag
            ]
        );

        $team = $this->getTeamMemberData($userId, $teamId);
        $teamData = array(
            'TeamMember' => [
                'id' => $team['TeamMember']['id'],
                'default_translation_language' => $accountData->defaultTranslationLanguage
            ]
        );

        try {
            $this->TransactionManager->begin();
            $User->save($userData, false);
            $TeamMember->save($teamData, false);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Failed to update user data.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'USer data' => $userId
            ]);
            return false;
        }

        return true;
    }

    // Update User Profile
    public function updateProfileSettingsData(int $userId, UserProfileDTO $profileData): bool
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");

        $userData = array(
            'User' => [
                'id' => $userId,
                'first_name' => $profileData->firstName,
                'last_name' => $profileData->lastName,
                'gender_type' => $profileData->genderType,
                'birth_day' => $profileData->birthday,
                'hide_year_flg' => $profileData->hideBirthdayFlag,
                'hometown' => $profileData->homewotn,
            ]
        );
        if (isset($profileData->profilePhotoName) && strlen($profileData->profilePhotoName) > 0) {
            $userData['User']['photo_file_name'] = $profileData->profilePhotoName;
        }
        if (isset($profileData->coverPhotoName) && strlen($profileData->coverPhotoName) > 0) {
            $userData['User']['cover_photo_file_name'] = $profileData->coverPhotoName;
        }

        $teamMemberData = $this->getTeamMemberData($profileData->userId, $profileData->teamId);
        $teamMemberData['TeamMember']['comment'] = $profileData->comment;

        try {
            $this->TransactionManager->begin();
            $User->save($userData, false);
            $TeamMember->save($teamMemberData, false);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Failed to update user data.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'User data' => $userData
            ]);
            return false;
        }

        return true;
    }

    //Update User Notify Settings
    public function updateNotifySettingsData(int $userId, UserNotifyDTO $notifyInfo): bool
    {
        /** @var NotifySetting $NotifySetting */
        $NotifySetting = ClassRegistry::init("NotifySetting");

        $data = $notifyInfo->toArray();
        if (empty($data)) return false;

        try {
            $NotifySetting->save(array('NotifySetting' => $data), false);
        } catch (Exception $e) {
            GoalousLog::error('Failed to update user data.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'User' => $userId
            ]);
            return false;
        }

        return true;
    }

    // Update Profile and/or Cover Photo
    public function updateProfileAndCoverPhoto(int $userId, int $teamId, string $profileUuid = null, string $coverUuid = null, UserProfileDTO $profileInfo): bool
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init("UploadService");

        try {
            if (isset($profileUuid) && strlen($profileUuid) > 0) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $UploadService->getBuffer($userId, $teamId, $profileUuid);

                $profileInfo->profilePhotoName = $uploadedFile->getFileName();
                $UploadService->saveWithProcessing("User", $userId, 'photo', $uploadedFile);
            }
            if (isset($coverUuid) && strlen($coverUuid) > 0) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $UploadService->getBuffer($userId, $teamId, $coverUuid);

                $profileInfo->coverPhotoName = $uploadedFile->getFileName();
                $UploadService->saveWithProcessing("User", $userId, 'cover_photo', $uploadedFile);
            }
        } catch (Exception $e) {
            GoalousLog::error('Failed to save profile user settings data.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => $userId,
                'team_id' => $teamId
            ]);
            return false;
        }

        return true;
    }

    // Validate Password
    public function validatePassword(array $data): bool
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        return $User->validatePassword($data);
    }

    // Update Email Address
    public function updateEmailAddress(UserChangeEmailDTO $emailInfo)
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $result = $User->addEmail(array('User' => $emailInfo->toArray()), $emailInfo->userId);
        return $result;
    }

    // Update Passsword
    public function updatePassword(UserChangePasswordDTO $passInfo): bool
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        return $User->changePassword(array('User' => $passInfo->toArray()));
    }

    // Get Cache Key
    // returns String;
    public function getCacheKey($name, $isUserData = false, $userId = null, $withTeamId = true)
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        return $User->getCacheKey($name, $isUserData, $userId, $withTeamId);
    }

}
