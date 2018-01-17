<?php
App::uses('GoalousTestCase', 'Test');
App::uses('SavedPost', 'Model');
App::import('Service/Api', 'ApiSavedPostService');

/**
 * Class ApiSavedPostServiceTest
 *
 * @property ApiSavedPostService $ApiSavedPostService
 */
class ApiSavedPostServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.saved_post'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ApiSavedPostService = ClassRegistry::init('ApiSavedPostService');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApiSavedPostService);
        parent::tearDown();
    }


    function test_search()
    {
        //TODO: it should be written later.
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_convertResponseForApi()
    {
        //TODO: it should be written later.
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_extend()
    {
        //TODO: it should be written later.
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_setPaging()
    {
        //TODO: it should be written later.
        $this->markTestIncomplete('testClear not implemented.');
    }
}
