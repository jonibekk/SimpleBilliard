<?php
/**
 * CakeTestCase file
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.TestSuite
 * @since         CakePHP(tm) v 1.2.0.4667
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('CakeFixtureManager', 'TestSuite/Fixture');
App::uses('CakeTestFixture', 'TestSuite/Fixture');

/**
 * CakeTestCase class
 *
 * @package       Cake.TestSuite
 */
abstract class GoalousTestCase extends CakeTestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Cache::config('team_info', ['prefix' => 'test_cache_team_info:']);
        Cache::config('user_data', ['prefix' => 'test_cache_user_data:']);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->_clearCache();
        parent::tearDown();
    }

    function _clearCache()
    {
        Cache::clear(false, 'team_info');
        Cache::clear(false, 'user_data');
    }

}
