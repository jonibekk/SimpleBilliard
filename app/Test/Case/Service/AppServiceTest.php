<?php App::uses('GoalousTestCase', 'Test');
App::import('Service', 'AppService');

/**
 * AccessUser Test Case
 *
 * @property AppService $AppService
 */
class AppServiceTest extends GoalousTestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->AppService = ClassRegistry::init('AppService');
    }

    function testValidationExtract()
    {
        $validationErrorsBefore = [
            'test1' => [
                'test1 message',
            ],
            'test2' => [
                'test2 message',
            ],
        ];
        $validationErrorsAfter = [
            'test1' => 'test1 message',
            'test2' => 'test2 message',
        ];
        $actual = $this->AppService->validationExtract($validationErrorsBefore);
        $this->assertEquals($validationErrorsAfter, $actual);
        $actual = $this->AppService->validationExtract($validationErrorsAfter);
        $this->assertEquals($validationErrorsAfter, $actual);
    }

    function test_getWithCache() {
        // behavior test cases are included in GoalServiceTest
    }

}
