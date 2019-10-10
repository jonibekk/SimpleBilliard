<?php

use Goalous\Enum\Model\Team\ServiceUseStatus;

App::uses('GoalousTestCase', 'Test');
App::uses('Email', 'Model');
App::uses('User', 'Model');
App::uses('Team', 'Model');
App::uses('Circle', 'Model');
App::uses('GlEmailComponent', 'Controller/Component');
App::import('Service', 'TeamMemberBulkRegisterService');

/**
 * Class TeamMemberBulkRegisterServiceTest
 * @property Email $Email
 * @property User $User
 * @property Team $Team
 * @property TeamMember $TeamMember
 * @property Circle $Circle
 * @property CircleMember $CircleMember
 * @property int $freeTrialTeamId
 * @property int $otherFreeTrialTeamId1
 * @property float $commonTeamTimeZone
 * @property int $notFreeTrialTeamId
 * @property int $teamAllCircleId
 * @property int $other1TeamAllCircleId
 */
class TeamMemberBulkRegisterServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.email',
        'app.user',
        'app.team',
        'app.team_member',
        'app.circle',
        'app.circle_member',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Email = ClassRegistry::init('Email');
        $this->User = ClassRegistry::init('User');
        $this->Team = ClassRegistry::init('Team');
        $this->Circle = ClassRegistry::init('Circle');
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->CircleMember = ClassRegistry::init('CircleMember');

        $this->Email->getDataSource()->truncate('emails');
        $this->User->getDataSource()->truncate('users');
        $this->Team->getDataSource()->truncate('teams');
        $this->Circle->getDataSource()->truncate('circles');
        $this->TeamMember->getDataSource()->truncate('team_members');
        $this->CircleMember->getDataSource()->truncate('circle_members');

        $this->freeTrialTeamId = 1;
        $this->notFreeTrialTeamId = 2;
        $this->otherFreeTrialTeamId1 = 3;

        $this->commonTeamTimeZone = Hash::get($this->createFreeTrialTeam($this->freeTrialTeamId), 'Team.timezone');
        $this->createNotFreeTrialTeam($this->notFreeTrialTeamId);
        $this->createFreeTrialTeam($this->otherFreeTrialTeamId1);
        $this->teamAllCircleId = Hash::get($this->createTeamAllCircle($this->freeTrialTeamId), 'Circle.id');
        $this->other1TeamAllCircleId = Hash::get($this->createTeamAllCircle($this->otherFreeTrialTeamId1), 'Circle.id');
    }

    /**
     * @return array
     */
    public function dataProviderExecuteValidateAbnormal(): array
    {
        return [
            'Parameter file_name does not exist' => [
                'params' => [
                    'team_id' => rand(1, 100),
                ],
                'doesBucketExist' => true,
                'doesObjectExist' => true,
                'confirmTeamName' => 'yes'
            ],
            'The log s3 bucket does not exist.' => [
                'params' => [
                    'team_id' => rand(1, 100),
                    'file_name' => 'hoge.csv',
                ],
                'doesBucketExist' => false,
                'doesObjectExist' => true,
                'confirmTeamName' => 'yes'
            ],
            'The log s3 csv file does not exist.' => [
                'params' => [
                    'team_id' => rand(1, 100),
                    'file_name' => 'hoge.csv',
                ],
                'doesBucketExist' => true,
                'doesObjectExist' => false,
                'confirmTeamName' => 'yes'
            ],
            'The team_id parameter is invalid.' => [
                'params' => [
                    'team_id' => 'hoge',
                    'file_name' => 'hoge.csv',
                ],
                'doesBucketExist' => true,
                'doesObjectExist' => true,
                'confirmTeamName' => 'yes'
            ],
            'Target team ID does not exist.' => [
                'params' => [
                    'team_id' => 1000000000,
                    'file_name' => 'hoge.csv',
                ],
                'doesBucketExist' => true,
                'doesObjectExist' => true,
                'confirmTeamName' => 'yes'
            ],
            'The target team is a plan in which users cannot be registered in bulk.' => [
                'params' => [
                    'team_id' => 2, // notFreeTrialTeamId
                    'file_name' => 'hoge.csv',
                ],
                'doesBucketExist' => true,
                'doesObjectExist' => true,
                'confirmTeamName' => 'yes'
            ],
            'Exit by user input' => [
                'params' => [
                    'team_id' => 1, // freeTrialTeamId
                    'file_name' => 'hoge.csv',
                ],
                'doesBucketExist' => true,
                'doesObjectExist' => true,
                'confirmTeamName' => 'no'
            ],
        ];
    }

    /**
     * @group validParameter
     * @param array $params
     * @param bool $doesBucketExist
     * @param bool $doesObjectExist
     * @param string $confirmTeamName
     * @dataProvider dataProviderExecuteValidateAbnormal
     * @expectedException \RuntimeException
     */
    public function testValidParameter(
        array $params,
        bool $doesBucketExist,
        bool $doesObjectExist,
        string $confirmTeamName
    ) {
        // TODO
        $this->markTestIncomplete('Somehow an error occurs in CLI.');
        $teamId = (int) Hash::get($params, 'team_id');
        $fileName = Hash::get($params, 'file_name', '');
        $dryRun = array_key_exists('dry-run', $params);

        $serviceMock = \Mockery::mock(TeamMemberBulkRegisterService::class, [$teamId, $fileName, $dryRun])
            ->shouldAllowMockingProtectedMethods()->makePartial();
        $serviceMock->shouldReceive('doesBucketExist')->andReturn($doesBucketExist);
        $serviceMock->shouldReceive('doesObjectExist')->andReturn($doesObjectExist);
        $serviceMock->shouldReceive('confirmTeamName')->andReturn($confirmTeamName);
        $serviceMock->shouldReceive('validParameter')->passthru();

        $serviceMock->validParameter();
    }

    /**
     * @return array
     */
    public function dataProviderExecute(): array
    {
        return [[
            'records' => [
                ['email' => 'hoge@email.com', 'first_name' => 'Hoge', 'last_name' => 'Yamada', 'language' => 'jpn', 'admin_flg' => 'on'],
                ['email' => 'fuga@email.com', 'first_name' => 'Fuga', 'last_name' => 'John', 'language' => 'eng', 'admin_flg' => 'off'],
                ['email' => 'piyo@email.com', 'first_name' => 'Piyo', 'last_name' => 'Yamada', 'language' => 'jpn', 'admin_flg' => 'on'],
                ['email' => 'foo@email.com', 'first_name' => 'Foo', 'last_name' => 'John', 'language' => 'eng', 'admin_flg' => 'off'],
                ['email' => 'bar@email.com', 'first_name' => 'Bar', 'last_name' => 'Yamada', 'language' => 'jpn', 'admin_flg' => 'on'],
            ],
        ]];
    }

    /**
     * @group execute
     * @param array $records
     * @dataProvider dataProviderExecute
     */
    public function testExecuteDryRun(array $records)
    {
        $fileName = 'hoge.csv';
        $dryRun = true;

        // Do not send email
        $glMailMock = \Mockery::mock(GlEmailComponent::class);
        $glMailMock->shouldReceive('sendMailTeamMemberBulkRegistration')->times(0);

        $serviceMock = $this->createServiceMock($this->freeTrialTeamId, $fileName, $dryRun, $records, $glMailMock);
        $serviceMock->execute();

        $this->assertSame(count($records), $serviceMock->getAggregate()->getNewUserCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getExistUserCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getFailedCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getExcludedCount());

        $csvDataEmailMap = [];
        foreach ($records as $record) {
            $csvDataEmailMap[$record['email']] = $record;
        }

        $registedCount = $this->Email->find('count', [
            'conditions' => ['Email.email' => array_keys($csvDataEmailMap)],
            'contain'    => ['User']
        ]);

        $this->assertSame(0, $registedCount);
    }

    /**
     * @group execute
     * @param array $records
     * @dataProvider dataProviderExecute
     */
    public function testExecuteAllNewUser(array $records)
    {
        $fileName = 'hoge.csv';
        $dryRun = false;

        // Send email to all CSV users
        $glMailMock = \Mockery::mock(GlEmailComponent::class);
        $glMailMock->shouldReceive('sendMailTeamMemberBulkRegistration')->times(count($records));

        $serviceMock = $this->createServiceMock($this->freeTrialTeamId, $fileName, $dryRun, $records, $glMailMock);
        $serviceMock->execute();

        $this->assertSame(count($records), $serviceMock->getAggregate()->getNewUserCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getExistUserCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getFailedCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getExcludedCount());

        $this->checkSavedData($records);
    }

    /**
     * @group execute
     * @param array $records
     * @dataProvider dataProviderExecute
     */
    public function testExecuteAllExistUserJoinedOtherTeam(array $records)
    {
        $fileName = 'hoge.csv';
        $dryRun = false;

        // Email all teams to all CSV users
        $glMailMock = \Mockery::mock(GlEmailComponent::class);
        $glMailMock->shouldReceive('sendMailTeamMemberBulkRegistration')->times(count($records) * 2);

        // Join the first team
        $serviceMock = $this->createServiceMock($this->otherFreeTrialTeamId1, $fileName, $dryRun, $records, $glMailMock);
        $serviceMock->execute();

        // Join the second team
        $serviceMock = $this->createServiceMock($this->freeTrialTeamId, $fileName, $dryRun, $records, $glMailMock);
        $serviceMock->execute();

        $this->assertSame(0, $serviceMock->getAggregate()->getNewUserCount());
        $this->assertSame(count($records), $serviceMock->getAggregate()->getExistUserCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getFailedCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getExcludedCount());

        $this->checkSavedData($records);
    }

    /**
     * @group execute
     * @param array $records
     * @dataProvider dataProviderExecute
     */
    public function testExecuteAllExistUserAlreadyJoinedSameTeam(array $records)
    {
        $fileName = 'hoge.csv';
        $dryRun = false;

        // Email first team to all CSV users
        $glMailMock = \Mockery::mock(GlEmailComponent::class);
        $glMailMock->shouldReceive('sendMailTeamMemberBulkRegistration')->times(count($records));

        // Join the first team
        $serviceMock = $this->createServiceMock($this->freeTrialTeamId, $fileName, $dryRun, $records, $glMailMock);
        $serviceMock->execute();

        // Join the first team again
        $serviceMock = $this->createServiceMock($this->freeTrialTeamId, $fileName, $dryRun, $records, $glMailMock);
        $serviceMock->execute();

        $this->assertSame(0, $serviceMock->getAggregate()->getNewUserCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getExistUserCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getFailedCount());
        $this->assertSame(count($records), $serviceMock->getAggregate()->getExcludedCount());

        $this->checkSavedData($records);
    }

    /**
     * @group execute
     * @param array $records
     * @dataProvider dataProviderExecute
     */
    public function testExecuteMixedNewUserAndExistUser(array $records)
    {
        $fileName = 'hoge.csv';
        $dryRun = false;

        $totalCsvUserCount = count($records);
        $preRegistryCount = 2;
        $preRegistryRecords = array_slice($records , 0, $preRegistryCount);

        // Send email to 2 users registered in the first team
        // Send email to the second team to all CSV users
        $glMailMock = \Mockery::mock(GlEmailComponent::class);
        $glMailMock->shouldReceive('sendMailTeamMemberBulkRegistration')->times($totalCsvUserCount + $preRegistryCount);

        // Join the first team
        $serviceMock = $this->createServiceMock($this->otherFreeTrialTeamId1, $fileName, $dryRun, $preRegistryRecords, $glMailMock);
        $serviceMock->execute();

        $newRegistryCount = count($records) - $preRegistryCount;

        // Join the second team
        $serviceMock = $this->createServiceMock($this->freeTrialTeamId, $fileName, $dryRun, $records, $glMailMock);
        $serviceMock->execute();

        $this->assertSame($newRegistryCount, $serviceMock->getAggregate()->getNewUserCount());
        $this->assertSame($preRegistryCount, $serviceMock->getAggregate()->getExistUserCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getFailedCount());
        $this->assertSame(0, $serviceMock->getAggregate()->getExcludedCount());

        $this->checkSavedData($records);
    }

    private function checkSavedData(array $records)
    {
        $csvDataEmailMap = [];
        foreach ($records as $record) {
            $csvDataEmailMap[$record['email']] = $record;
        }

        $registedEmailWithUsers = $this->Email->find('all', [
            'conditions' => ['Email.email' => array_keys($csvDataEmailMap)],
            'contain'    => ['User']
        ]);

        foreach ($registedEmailWithUsers as $registedEmailWithUser) {
            $userId = Hash::get($registedEmailWithUser, 'User.id');
            $firstName = Hash::get($registedEmailWithUser, 'User.first_name');
            $lastName = Hash::get($registedEmailWithUser, 'User.last_name');
            $language = Hash::get($registedEmailWithUser, 'User.language');
            $defaultTeamId = (int) Hash::get($registedEmailWithUser, 'User.default_team_id');
            $timezone = (float) Hash::get($registedEmailWithUser, 'User.timezone');
            $password = Hash::get($registedEmailWithUser, 'User.password');
            $activeFlg = (int) Hash::get($registedEmailWithUser, 'User.active_flg');
            $email = Hash::get($registedEmailWithUser, 'Email.email');
            $emailVerified = (int) Hash::get($registedEmailWithUser, 'Email.email_verified');

            $csvDataRecord = $csvDataEmailMap[$email] ?? [];
            $adminFlg = Hash::get($csvDataRecord, 'admin_flg') === 'on' ? 1 : 0;

            $this->assertSame(Hash::get($csvDataRecord, 'email'), $email);
            $this->assertSame(TeamMemberBulkRegisterService::EMAIL_VERIFIED_YES, $emailVerified);
            $this->assertSame(Hash::get($csvDataRecord, 'last_name'), $lastName);
            $this->assertSame(Hash::get($csvDataRecord, 'first_name'), $firstName);
            $this->assertSame(Hash::get($csvDataRecord, 'language'), $language);
            $this->assertSame($this->freeTrialTeamId, $defaultTeamId);
            $this->assertSame($this->commonTeamTimeZone, $timezone);
            $this->assertNotEmpty($password);
            $this->assertSame(TeamMemberBulkRegisterService::ACTIVE_FLG_YES, $activeFlg);

            $teamMemberCount = $this->TeamMember->find('count', [
                'conditions' => [
                    'user_id' => $userId,
                    'team_id' => $this->freeTrialTeamId,
                    'admin_flg' => $adminFlg,
                    'status'  => TeamMember::USER_STATUS_ACTIVE
                ]
            ]);
            $this->assertSame(1, $teamMemberCount);

            $circleMemberCount = $this->CircleMember->find('count', [
                'conditions' => [
                    'user_id'   => $userId,
                    'circle_id' => $this->teamAllCircleId
                ]
            ]);

            $this->assertSame(1, $circleMemberCount);
        }
    }

    private function createServiceMock(int $teamId, string $fileName, bool $dryRun, array $records, $glMailMock)
    {
        $serviceMock = \Mockery::mock(TeamMemberBulkRegisterService::class, [$teamId, $fileName, $dryRun])
            ->shouldAllowMockingProtectedMethods()->makePartial();
        $serviceMock->shouldReceive('execute')->passthru();
        $serviceMock->shouldReceive('validParameter')->once();
        $serviceMock->shouldReceive('getCsvRecords')->andReturn($records);
        $serviceMock->shouldReceive('getGlMail')->andReturn($glMailMock);

        return $serviceMock;
    }

    /**
     * @param int $teamId
     * @return array|mixed
     * @throws Exception
     */
    private function createNotFreeTrialTeam(int $teamId)
    {
        $this->Team->id = $teamId;
        return $this->Team->save([
            'name' => 'NotFreeTrialTeam',
            'timezone' => 9.0,
            'service_use_status' => ServiceUseStatus::CANNOT_USE,
        ]);
    }

    /**
     * @param int $teamId
     * @return array|mixed
     * @throws Exception
     */
    private function createFreeTrialTeam(int $teamId)
    {
        $this->Team->id = $teamId;
        return $this->Team->save([
            'name' => 'FreeTrialTeam' . $teamId,
            'timezone' => 9.0,
            'service_use_status' => ServiceUseStatus::FREE_TRIAL,
        ]);
    }

    /**
     * @param int $teamId
     * @return array|mixed
     * @throws Exception
     */
    private function createTeamAllCircle(int $teamId)
    {
        $this->Circle->create();
        return $this->Circle->save([
            'team_id' => $teamId,
            'name' => 'FreeTrialTeamTeamAllCircle' . $teamId,
            'team_all_flg' => true,
            'description' => 'Test Free Trial Team TeamAllCircle' . $teamId
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
    }
}
