<?php App::uses('GoalousTestCase', 'Test');
App::uses('RecoveryCode', 'Model');

/**
 * RecoveryCode Test Case
 *
 * @property RecoveryCode $RecoveryCode
 */
class RecoveryCodeTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.recovery_code',
        'app.user',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->RecoveryCode = ClassRegistry::init('RecoveryCode');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RecoveryCode);

        parent::tearDown();
    }

    function testGetAll()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->RecoveryCode->create();
            $this->RecoveryCode->save(['user_id' => 1, 'code' => 'test' . $i]);
            $this->RecoveryCode->create();
            $this->RecoveryCode->save(['user_id' => 2, 'code' => 'test' . $i]);
        }

        $available_codes = $this->RecoveryCode->getAll(1);
        $this->assertCount(10, $available_codes);
        foreach ($available_codes as $v) {
            $this->assertEquals(1, $v['RecoveryCode']['user_id']);
        }
    }

    function testInvalidateAll()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->RecoveryCode->create();
            $this->RecoveryCode->save(['user_id' => 1, 'code' => 'test' . $i]);
            $this->RecoveryCode->create();
            $this->RecoveryCode->save(['user_id' => 2, 'code' => 'test' . $i]);
        }
        $available_codes = $this->RecoveryCode->getAll(1);
        $this->assertCount(10, $available_codes);
        $available_codes2 = $this->RecoveryCode->getAll(2);
        $this->assertCount(10, $available_codes2);

        $res = $this->RecoveryCode->invalidateAll(1);
        $this->assertTrue($res);

        $available_codes = $this->RecoveryCode->getAll(1);
        $this->assertEmpty($available_codes);
        $available_codes3 = $this->RecoveryCode->getAll(2);
        $this->assertEquals($available_codes2, $available_codes3);
    }

    function testRegenerate()
    {
        $count1 = $this->RecoveryCode->find('count');
        $res = $this->RecoveryCode->regenerate(1);
        $this->assertTrue($res);
        $count2 = $this->RecoveryCode->find('count');
        $this->assertEquals($count1 + 10, $count2);
        $available_codes = $this->RecoveryCode->getAll(1);
        $this->assertCount(10, $available_codes);
    }

    function testRegenerateMultiTime()
    {
        $res = $this->RecoveryCode->regenerate(1);
        $this->assertTrue($res);
        $available_codes1 = $this->RecoveryCode->getAll(1);
        $res = $this->RecoveryCode->regenerate(1);
        $this->assertTrue($res);
        $available_codes2 = $this->RecoveryCode->getAll(1);
        $this->assertNotEquals($available_codes1, $available_codes2);
        $available_codes3 = $this->RecoveryCode->getAll(1);
        $this->assertEquals($available_codes2, $available_codes3);
    }


    function testRegenerateFailed()
    {
        $count1 = $this->RecoveryCode->find('count');
        $this->RecoveryCode = $this->getMockForModel('RecoveryCode', array('updateAll'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->RecoveryCode->expects($this->any())
                           ->method('updateAll')
                           ->will($this->returnValue(false));
        $res = $this->RecoveryCode->regenerate(1);
        $this->assertFalse($res);
        $count2 = $this->RecoveryCode->find('count');
        $this->assertEquals($count1, $count2);
    }

    function testRegenerateFailed2()
    {
        $count1 = $this->RecoveryCode->find('count');
        $this->RecoveryCode = $this->getMockForModel('RecoveryCode', array('save'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->RecoveryCode->expects($this->any())
                           ->method('save')
                           ->will($this->returnValue(false));
        $res = $this->RecoveryCode->regenerate(1);
        $this->assertFalse($res);
        $count2 = $this->RecoveryCode->find('count');
        $this->assertEquals($count1, $count2);
    }

    function testUseCode()
    {
        $res = $this->RecoveryCode->regenerate(1);
        $this->assertTrue($res);

        $recovery_codes = $this->RecoveryCode->getAll(1);
        $this->assertCount(10, $recovery_codes);
        $this->assertNull($recovery_codes[0]['RecoveryCode']['used']);

        $res = $this->RecoveryCode->useCode($recovery_codes[0]['RecoveryCode']['id']);
        $this->assertTrue($res);

        $recovery_codes2 = $this->RecoveryCode->getAll(1);
        $this->assertCount(10, $recovery_codes2);
        $this->assertEquals($recovery_codes[0]['RecoveryCode']['id'], $recovery_codes2[0]['RecoveryCode']['id']);
        $this->assertNotNull($recovery_codes2[0]['RecoveryCode']['used']);

        // invalid ID
        $res = $this->RecoveryCode->useCode(9999999);
        $this->assertFalse($res);
    }

    function testFindUnusedCode()
    {
        $res = $this->RecoveryCode->regenerate(1);
        $this->assertTrue($res);

        $recovery_codes = $this->RecoveryCode->getAll(1);
        $this->assertCount(10, $recovery_codes);
        $this->assertNull($recovery_codes[0]['RecoveryCode']['used']);

        $row = $this->RecoveryCode->findUnusedCode(1, $recovery_codes[0]['RecoveryCode']['code']);
        $this->assertEquals($recovery_codes[0]['RecoveryCode']['id'], $row['RecoveryCode']['id']);

        $res = $this->RecoveryCode->useCode($recovery_codes[0]['RecoveryCode']['id']);
        $this->assertTrue($res);

        $row = $this->RecoveryCode->findUnusedCode(1, $recovery_codes[0]['RecoveryCode']['code']);
        $this->assertFalse($row);

        $row = $this->RecoveryCode->findUnusedCode(1, $recovery_codes[1]['RecoveryCode']['code']);
        $this->assertEquals($recovery_codes[1]['RecoveryCode']['id'], $row['RecoveryCode']['id']);

    }
}
