<?php
/**
 * AllTests file
 * PHP 5
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Test.Case
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * AllWebTests
 * This test group will run all tests.
 *
 * @package GoalousWebTest
 * @version 2016/03/22
 */
class AllWebTests extends PHPUnit_Framework_TestSuite
{

    /**
     * Suite define the tests for this suite
     *
     * @return CakeTestSuite
     */
    public static function suite()
    {
        $files = [
            'LoginWebTest.php',
            'CircleWebTest.php',
            'HomeWebTest.php',
            'CommentWebTest.php',
            'MessageWebTest.php',
        ];
        ini_set('memory_limit', '2G');
        $suite = new CakeTestSuite('All Application Web Test');
        array_map(function ($file) use ($suite) {
            $suite->addTestFile(APP_TEST_CASES . DS . 'Web/' . $file);
        }, $files);

        return $suite;
    }

}
