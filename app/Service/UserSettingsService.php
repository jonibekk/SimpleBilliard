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

        return $user;
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
    public function updateAccountSettingsData(int $userId, UserAccountDTO $accountData): bool
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $data = array(
            'User' => [
                'id' => $userId,
                'email' => $accountData->email,
                'default_team_id' => $accountData->defTeamId,
                'language' => $accountData->language,
                'timezone' => $accountData->timezone,
                'update_email_flg' => $accountData->updateEmailFlag
            ]
        );

        try {
            $User->save($data, false);
        } catch (Exception $e) {
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
    public function updateProfileAndCoverPhoto(int $userId, int $teamId, $profileUuid, $coverUuid): bool
    {
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init("UserService");
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init("UploadService");
        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init("AttachedFileService");

        $user = $UserService->getUserData($userId);

        try {
            if (isset($profileUuid) && strlen($profileUuid) > 0) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $UploadService->getBuffer($userId, $teamId, $profileUuid);
                /** @var AttachedFileEntity $attachedFile */
                $AttachedFileService->add($userId, $teamId, $uploadedFile, AttachedModelType::TYPE_MODEL_ACTION_RESULT());

                // Delete old Profile photo
                if (!empty($user) && !empty($user['User']['photo_file_name'])) {
                    $this->deleteImage($userId, $teamId, $user['User']['photo_file_name']);
                }

                $this->setProfilePhotoName($uploadedFile->getFileName());
                $UploadService->saveWithProcessing("User", $userId, 'photo', $uploadedFile);
            }
            if (isset($coverUuid) && strlen($coverUuid) > 0) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $UploadService->getBuffer($userId, $teamId, $coverUuid);
                /** @var AttachedFileEntity $attachedFile */
                $AttachedFileService->add($userId, $teamId, $uploadedFile, AttachedModelType::TYPE_MODEL_ACTION_RESULT());

                // Delete old Cover photo
                if (!empty($user) && !empty($user['User']['cover_photo_file_name'])) {
                    $this->deleteImage($userId, $teamId, $user['User']['cover_photo_file_name']);
                }

                $this->setCoverPhotoName($uploadedFile->getFileName());
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

    // Delete Profile and/or Cover Photo
    private function deleteImage(int $userId, int $teamId, $fileName)
    {
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');

        $option = [
            'conditions' => [
                'AttachedFile.user_id' => $userId,
                'AttachedFile.team_id' => $teamId,
                'AttachedFile.del_flg'    => false,
                'AttachedFile.model_type'    => AttachedModelType::TYPE_MODEL_ACTION_RESULT(),
                'AttachedFile.attached_file_name'    => $fileName,
            ]
        ];

        $attFile = $AttachedFile->find('first', $option);
        if (!empty($attFile)) {
            $attFile['AttachedFile']['del_flg'] = true;
            $attFile['AttachedFile']['deleted'] = GoalousDateTime::now()->getTimestamp();
            $AttachedFile->useType()->useEntity()->save($attFile, false);
        } else GoalousLog::error('Empty Attached File!');
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


    private function setProfilePhotoName($profilePhoto): void
    {
        $this->profilePhoteFileName = $profilePhoto;
    }

    public function getProfilePhotoName()
    {
        return $this->profilePhoteFileName ? $this->profilePhoteFileName : null;
    }

    private function setCoverPhotoName($coverPhoto): void
    {
        $this->coverPhoteFileName = $coverPhoto;
    }

    public function getCoverPhotoName()
    {
        return $this->coverPhoteFileName ? $this->coverPhoteFileName : null;
    }

    // Profile image file name.
    private $profilePhoteFileName;

    // Cover image file name.
    private $coverPhoteFileName;
}
