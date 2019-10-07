<?php

use Goalous\Enum\Model\User\ActiveFlg;
use Goalous\Enum\Model\User\UpdateEmailFlg;
use Goalous\Enum\Model\Email\EmailVerified;

App::uses('User', 'Model');
App::uses('Email', 'Model');
App::uses('TransactionManager', 'Model');
App::uses('TeamMemberBulkRegisterValidator', 'Validator/Csv');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');
App::import('Model/Csv', 'TeamMemberBulkRegister');
App::import('Model/User', 'UserSignUpFromCsv');
App::import('Service/User/Team', 'UserTeamJoiningService');
App::import('Service/User/Circle', 'UserCircleJoiningService');
App::import('Service/User', 'UserRegistererService');

class TeamMemberBulkRegisterService
{
    /** @var TeamMemberBulkRegister */
    private $register_model;
    /** @var TransactionManager */
    private $TransactionManager;
    /** @var User */
    private $User;
    /** @var Email */
    private $Email;
    /** @var Circle */
    private $Circle;
    /** @var UserTeamJoiningService */
    private $user_team_joining_service;
    /** @var UserCircleJoiningService */
    private $user_circle_joining_service;
    /** @var UserRegistererService */
    private $registerer_service;
    /** @var GlEmailComponent */
    private $GlEmail;
    /** * @var array */
    private $log = [];

    /**
     * TeamMemberBulkRegisterService constructor.
     * @param TeamMemberBulkRegister $register_model
     */
    public function __construct(TeamMemberBulkRegister $register_model) {
        $this->register_model = $register_model;
        $this->TransactionManager = ClassRegistry::init("TransactionManager");
        $this->user_team_joining_service = new UserTeamJoiningService();
        $this->user_circle_joining_service = new UserCircleJoiningService();
        $this->registerer_service = new UserRegistererService();
        $this->User = ClassRegistry::init('User');
        $this->Email = ClassRegistry::init('Email');
        $this->Circle = ClassRegistry::init('Circle');
        $this->GlEmail = new GlEmailComponent(new ComponentCollection());
        $this->GlEmail->startup(new AppController());
    }

    /**
     * @return TeamMemberBulkRegisterService
     */
    public function execute(): self
    {
        foreach ($this->getRegisterModel()->getRecords() as $record) {
            try {
                $this->addLog(str_repeat('-=', 40));
                $this->TransactionManager->begin();

                $this->executeRecord($record);

                if ($this->getRegisterModel()->isDryRun()) {
                    $this->TransactionManager->rollback();
                } else {
                    $this->TransactionManager->commit();
                }

                $this->addLog('Succeeded.');
            } catch (\Throwable $e) {
                $this->TransactionManager->rollback();
                $this->addLog('Failed. ' . $e->getMessage());
                $this->addLog($e->getTraceAsString());
            } finally {
                $this->addRecordLog($record);
            }
        }

        return $this;
    }

    /**
     * @param array $record
     * @throws Exception
     */
    private function executeRecord(array $record): void
    {
        TeamMemberBulkRegisterValidator::createDefaultValidator()->validate($record);

        $email = $record['email'];
        $language = $record['language'];
        $password = null;

        $user_id = $this->getRegisterModel()->getExistUserId($email);
        if ($user_id === null) {
            $this->addLog($email . ' is new user.');
            $sign_up_model = $this->createUserSignUpFromCsvModel($record);
            $password = $sign_up_model->getPassword();
            $user_id = $this->getRegisterer()->signUpFromCsv($sign_up_model);
        } else {
            $this->addLog($email . ' is exist user.');
        }

        $admin_flg = $record['admin_flg'] === 'on' ? 1 : 0;
        $this->joinTeam($user_id, $email, $admin_flg)->joinCircle($user_id, $email);

        if (!$this->getRegisterModel()->isDryRun()) {
            $this->GlEmail->sendMailTeamMemberBulkRegistration(
                $user_id,
                $this->getRegisterModel()->getTeamId(),
                $this->getRegisterModel()->getTeamName(),
                $language,
                $email,
                $password
            );
        }
    }

    /**
     * @param array $record
     * @return UserSignUpFromCsv
     */
    private function createUserSignUpFromCsvModel(array $record): UserSignUpFromCsv
    {
        return (new UserSignUpFromCsv())->setFirstName($record['first_name'])
            ->setLastName($record['last_name'])
            ->setLanguage($record['language'])
            ->setDefaultTeamId($this->getRegisterModel()->getTeamId())
            ->setPassword($this->randomPassword())
            ->setUpdateEmailFlg(UpdateEmailFlg::YES)
            ->setTimezone($this->getRegisterModel()->getTeamTimezone())
            ->setAgreedTermsOfServiceId($this->getRegisterModel()->getAgreedTermsOfServiceId())
            ->setActiveFlg(ActiveFlg::YES)
            ->setEmail($record['email'])
            ->setEmailVerified(EmailVerified::YES);
    }

    private function joinTeam(string $user_id, string $email, bool $admin_flg): self
    {
        $team_id = $this->getRegisterModel()->getTeamId();
        if ($this->getUserTeamJoiningService()->isJoined($user_id, $team_id)) {
            throw new \RuntimeException('Already registered as a team member.');
        }

        $result = !!$this->getUserTeamJoiningService()->addMember([
            'user_id' => $user_id,
            'team_id' => $team_id,
            'admin_flg' => $admin_flg
        ]);

        if ($result === false) {
            throw new \RuntimeException('Failed to add member to team.');
        }

        return $this;
    }

    private function joinCircle(string $user_id, string $email): self
    {
        $team_id = $this->getRegisterModel()->getTeamId();
        $circle_id = $this->getRegisterModel()->getTeamAllCircleId();
        if ($this->getTeamCircleService()->isJoined($circle_id, $user_id)) {
            throw new \RuntimeException('Already registered as a circle member.');
        }

        $result = !!$this->getTeamCircleService()->addMember([
            'circle_id' => $circle_id,
            'team_id' => $team_id,
            'user_id' => $user_id
        ]);

        if ($result === false) {
            throw new \RuntimeException('Failed to add member to circle.');
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getLog(): array
    {
        return $this->log;
    }

    private function addLog(string $message): void
    {
        $this->log[] = $message;
    }

    /**
     * @param array $record
     */
    private function addRecordLog(array $record): void
    {
        foreach ($record as $key => $value) {
            $this->addLog($key . ': ' . $value);
        }
    }

    /**
     * @return string
     */
    private function randomPassword(): string
    {
        $password_length = 8;
        $number_digits = rand(1, 4);
        $str_digits = $password_length - $number_digits;

        $str = substr(str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'), 0, $str_digits);
        $number = substr(str_shuffle('123456789'), 0, $number_digits);

        return str_shuffle($str . $number);
    }

    /**
     * @return UserTeamJoiningService
     */
    private function getUserTeamJoiningService(): UserTeamJoiningService
    {
        return $this->user_team_joining_service;
    }

    /**
     * @return UserCircleJoiningService
     */
    private function getTeamCircleService(): UserCircleJoiningService
    {
        return $this->user_circle_joining_service;
    }

    /**
     * @return TeamMemberBulkRegister
     */
    private function getRegisterModel(): TeamMemberBulkRegister
    {
        return $this->register_model;
    }

    /**
     * @return UserRegistererService
     */
    private function getRegisterer(): UserRegistererService
    {
        return $this->registerer_service;
    }
}
