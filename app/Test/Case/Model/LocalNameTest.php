<?php App::uses('GoalousTestCase', 'Test');
App::uses('LocalName', 'Model');

/**
 * LocalName Test Case
 *
 * @property LocalName $LocalName
 */
class LocalNameTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
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
        $this->LocalName = ClassRegistry::init('LocalName');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->LocalName);

        parent::tearDown();
    }

    function testGetName()
    {
        $this->LocalName->save(['user_id' => 1, 'language' => 'jpn', 'first_name' => 'test', 'last_name' => 'test']);
        $actual = $this->LocalName->getName(1, 'jpn');
        $this->assertNotEmpty($actual);
        $actual = $this->LocalName->getName(1, 'eng');
        $this->assertEmpty($actual);
    }

    function testGetAllByUserId()
    {
        $local_names = $this->LocalName->getAllByUserId(12);
        $this->assertNotEmpty($local_names);
        $langs = [];
        foreach ($local_names as $v) {
            $langs[$v['LocalName']['language']] = true;
        }
        $this->assertTrue(isset($langs['jpn']));
        $this->assertTrue(isset($langs['eng']));
    }

}
